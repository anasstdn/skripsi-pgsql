<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Traits\ActivityTraits;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use App\Models\Pegawai;
use App\Models\Anggotum;
use App\Models\User;
use App\Models\RoleUser;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use Illuminate\Support\Facades\Validator;

class PegawaiController extends Controller
{
    //
    use ActivityTraits;

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('permission:read-pegawai');
	}

	public function index()
	{
		 if(!in_array(\Auth::user()->roleUser->role_id, getConfigValues('ROLE_ADMIN')))
        {
            if(cek_kelengkapan_data()['status']==true)
            {
                message(false,'',cek_kelengkapan_data()['pesan']);
                return redirect('/profile/edit/'.\Auth::user()->id);
            }
            else
            {
                $this->menuAccess(\Auth::user(),'Pegawai');
				return view('pegawai.index');
            }
        }
        else
        {
            $this->menuAccess(\Auth::user(),'Pegawai');
			return view('pegawai.index');
        }
	}

	public function getData()
	{
		$GLOBALS['nomor']=\Request::input('start',1)+1;
		$dataList = Profile::select(\DB::raw('profile.*'))
                    ->join('users','users.id_profile','=','profile.id')
                    ->join('role_user','users.id','=','role_user.user_id')
                    ->whereNotIn('role_user.role_id',getConfigValues('ROLE_ADMIN'))
                    ->get();

		if (request()->get('status') == 'trash') {
			$dataList->onlyTrashed();
		}
		return DataTables::of($dataList)
		->addColumn('nomor',function($kategori){
			return $GLOBALS['nomor']++;
		})
		->addColumn('nip',function($data){
			$get=Pegawai::where('id_profile',$data->id)->first();
			if(isset($get->nip)){
				return $get->nip;
			}else{
				return null;
			}
		})
		->addColumn('nama_depan',function($data){
			if(isset($data->nama_depan)){
				return $data->nama_depan;
			}else{
				return null;
			}
		})
		->addColumn('nama_belakang',function($data){
			if(isset($data->nama_belakang)){
				return $data->nama_belakang;
			}else{
				return null;
			}
		})
		->addColumn('nama_jabatan',function($data){
			$get=Pegawai::where('id_profile',$data->id)->first();
			if(isset($get->jabatan->nama_jabatan)){
				return $get->jabatan->nama_jabatan;
			}else{
				return null;
			}
		})
		->addColumn('nama_departement',function($data){
			$get=Pegawai::where('id_profile',$data->id)->first();
			if(isset($get->departement->nama_departement)){
				return $get->departement->nama_departement;
			}else{
				return null;
			}
		})
		// ->addColumn('flag_resign', function ($data) {
  //   		if(isset($data->flag_resign))
  //   		{
  //               if($data->flag_resign=='Y')
  //               {
  //                   return array('url'=>url("pegawai/".$data->id)."/aktifkan",'flag_resign'=>$data->flag_resign);
  //               }
  //               else
  //               {
  //                   return array('url'=>url("pegawai/".$data->id)."/nonaktifkan",'flag_resign'=>$data->flag_resign);
  //               }
    			
  //   		}else
  //   		{
  //   			return null;
  //   		}
  //   	})
		->addColumn('action', function ($data) {
            $get=Pegawai::where('id_profile',$data->id)->first();
			$edit=url("pegawai/edit/".$data->id);
			$delete=url("pegawai/".$data->id)."/delete";
            if(isset($get))
            {
                if($get->flag_resign=='N')
                {
                    $aktif=url("pegawai/".$data->id)."/nonaktifkan";
                }
                else
                {
                    $aktif=url("pegawai/".$data->id)."/aktifkan";
                }
                
            }

            $content = '';
    
            $content.="<a href='$edit' class='btn btn-primary btn-sm' data-original-title='Edit' title='Edit'><i class='fa fa-edit' aria-hidden='true'></i></a>";
            $content.="<a href='#' onclick='hapus(\"$delete\")' class='btn btn-danger btn-sm' data-original-title='Hapus' title='Hapus'><i class='fa fa-trash' aria-hidden='true'></i></a>";
            if(isset($get))
            {
                if($get->flag_resign=='N')
                {
                    $content.="<a href='#' onclick='show_modal(\"$aktif\")' class='btn btn-warning btn-sm' data-original-title='Nonaktifkan' title='Hapus'><i class='fa fa-times' aria-hidden='true'></i></a>";
                }
                else
                {
                    $content.="<a href='#' onclick='show_modal(\"$aktif\")' class='btn btn-success btn-sm' data-original-title='Aktifkan' title='Hapus'><i class='fa fa-check' aria-hidden='true'></i></a>";
                }
            }
            
			
			return $content;
		})
		->make(true);
	}

	public function create()
	{
		if(!in_array(\Auth::user()->roleUser->role_id, getConfigValues('ROLE_ADMIN')))
        {
            if(cek_kelengkapan_data()['status']==true)
            {
                message(false,'',cek_kelengkapan_data()['pesan']);
                return redirect('/profile/edit/'.\Auth::user()->id);
            }
            else
            {
            	$mode='create';
                return view('pegawai.form',compact('mode'));
            }
        }
        else
        {
        	$mode='create';
            return view('pegawai.form',compact('mode'));
        }
	}

	public function store(Request $request)
	{
		$all_data=$request->all();

		$validation = Validator::make($request->all(), [
            'nama_depan'    	=> 'required',
            'nama_belakang' 	=> 'required',
            'nik'      			=> 'required',
            'nip' 				=> 'required',
            'tgl_bergabung'     => 'required',
            'id_jabatan'      	=> 'required',
            'id_departement'    => 'required',
            'roles'    			=> 'required',
        ]);


        if (!$validation->passes()){
            $count_err=count($validation->errors()->all());
            $i=0;
            foreach($validation->errors()->all() as $val)
            {
                message(false,'',$val);
                $i++;
            }
            if($count_err==$i)
            {
                return redirect('/pegawai');
            }
        }

        DB::beginTransaction();
        try {

        	$flag=isset($all_data['nama_foto'])?$all_data['nama_foto']:null;

        	if($request->hasFile('foto'))
        	{
        		$extension = $request->file('foto')->getClientOriginalExtension();
        		$dir = 'images/profile/';
        		$flag = uniqid() . '_' . time() . '.' . $extension;
        		$request->file('foto')->move($dir, $flag);
        	}


        	$dataUser  = array(
        		'nik'          			=> $all_data['nik'] ,
        		'nama_depan'    		=> ucwords(strtolower($all_data['nama_depan'])),
             	'nama_belakang' 		=> ucwords(strtolower($all_data['nama_belakang'])),
        		'id_jenis_kelamin'  	=> $all_data['id_jenis_kelamin'] ,
        		'id_agama'  			=> $all_data['id_agama'] ,
        		'tempat_lahir'  		=> $all_data['tempat_lahir'] ,
        		'tgl_lahir'  			=> isset($all_data['tgl_lahir']) && !empty($all_data['tgl_lahir'])?date('Y-m-d',strtotime($all_data['tgl_lahir'])):null ,
        		'id_status_perkawinan' 	=> $all_data['id_status_perkawinan'] ,
        		'alamat_ktp' 			=> $all_data['alamat_ktp'] ,
        		'id_kelurahan_ktp' 		=> $all_data['id_kelurahan_ktp'] ,
        		'alamat_domisili' 		=> $all_data['alamat_domisili'] ,
        		'id_kelurahan_domisili' => $all_data['id_kelurahan_domisili'] ,
        		'no_telp' 				=> $all_data['no_telp'] ,
        		'foto'        			=> $flag,
        	);

        	$this->logCreatedActivity(Auth::user(),$dataUser,'Pegawai','profile');
        	$insert=Profile::create($dataUser);
        	$id_profile=$insert->id;

        	$dataPegawai=array(
        		'id_profile'		=>	$id_profile,
        		'id_jabatan'		=>	$all_data['id_jabatan'],
        		'id_golongan'		=>	$all_data['id_golongan'],
        		'id_departement'	=>	$all_data['id_departement'],
        		'nip'				=>	$all_data['nip'],
        		'tgl_bergabung'		=>	date('Y-m-d',strtotime($all_data['tgl_bergabung'])),
        	);

        	$this->logCreatedActivity(Auth::user(),$dataUser,'Pegawai','pegawai');
        	$insertPegawai=Pegawai::create($dataPegawai);

        	$data  = array(
        		'name'         =>ucwords(strtolower($all_data['nama_depan'])).' '.ucwords(strtolower($all_data['nama_belakang'])) ,
        		'username'     =>strtolower($all_data['nama_depan']) ,
        		'email'        =>strtolower($all_data['nama_depan']).'@gmail.com' ,
        		'password'     =>bcrypt('password') ,
        		'id_profile'   =>$id_profile ,
        	);

        	$this->logCreatedActivity(Auth::user(),$data,'Pegawai','users');
        	$user=User::create($data);

        	$role=array(
        		'role_id'  =>intval($all_data['roles']),
        		'user_id'  =>$user->id,
        		'user_type'=>'App\User'
        	);

        	$this->logCreatedActivity(Auth::user(),[
        		'role_id'=>intval($all_data['roles']),
        		'user_id'=>$user->id,
        		'user_type'=>'App\User'
        	],'Pegawai','role_user');

        	$roleUser = DB::table('role_user')->insert($role);

        	if($insert==true && $insertPegawai==true && $user==true && $roleUser==true)
            {
                $act=true;
            }
            else
            {
                $act=false;
            }

            message($act,'Data berhasil disimpan!','Data gagal disimpan!');

        }catch (Exception $e) {
        	echo 'Message' .$e->getMessage();
        	DB::rollback();
        }
        DB::commit();

        return redirect('/pegawai');
	}

	public function edit(Request $request, $id)
	{
		if(!in_array(\Auth::user()->roleUser->role_id, getConfigValues('ROLE_ADMIN')))
        {
            if(cek_kelengkapan_data()['status']==true)
            {
                message(false,'',cek_kelengkapan_data()['pesan']);
                return redirect('/profile/edit/'.\Auth::user()->id);
            }
            else
            {
            	$mode='edit';
            	$profile=Profile::leftjoin('pegawai','profile.id','=','pegawai.id_profile')
            			->join('users','users.id_profile','=','profile.id')
            			->join('role_user','users.id','=','role_user.user_id')
            			->where('profile.id',$id)
            			->first();

                return view('pegawai.form',compact('profile','id','mode'));
            }
        }
        else
        {
        	$mode='edit';
        	$profile=Profile::leftjoin('pegawai','profile.id','=','pegawai.id_profile')
            			->join('users','users.id_profile','=','profile.id')
            			->join('role_user','users.id','=','role_user.user_id')
            			->where('profile.id',$id)
            			->first();

            return view('pegawai.form',compact('profile','id','mode'));
        }
	}

	public function update(Request $request, $id)
	{
		$all_data=$request->all();

		$validation = Validator::make($request->all(), [
            'nama_depan'    	=> 'required',
            'nama_belakang' 	=> 'required',
            'nik'      			=> 'required',
            'nip' 				=> 'required',
            'tgl_bergabung'     => 'required',
            'id_jabatan'      	=> 'required',
            'id_departement'    => 'required',
            'roles'    			=> 'required',
        ]);


        if (!$validation->passes()){
            $count_err=count($validation->errors()->all());
            $i=0;
            foreach($validation->errors()->all() as $val)
            {
                message(false,'',$val);
                $i++;
            }
            if($count_err==$i)
            {
                return redirect('/pegawai');
            }
        }

        DB::beginTransaction();
        try {
        	
        	$dataProfile=Profile::find($id);

            $flag=isset($all_data['nama_foto'])?$all_data['nama_foto']:null;

            if($request->hasFile('foto'))
            {
                $extension = $request->file('foto')->getClientOriginalExtension();
                $dir = 'images/profile/';
                $flag = uniqid() . '_' . time() . '.' . $extension;
                $request->file('foto')->move($dir, $flag);
            }

        	$dataUser  = array(
        		'nik'          			=> $all_data['nik'] ,
        		'nama_depan'    		=> ucwords(strtolower($all_data['nama_depan'])),
             	'nama_belakang' 		=> ucwords(strtolower($all_data['nama_belakang'])),
        		'id_jenis_kelamin'  	=> $all_data['id_jenis_kelamin'] ,
        		'id_agama'  			=> $all_data['id_agama'] ,
        		'tempat_lahir'  		=> $all_data['tempat_lahir'] ,
        		'tgl_lahir'  			=> isset($all_data['tgl_lahir']) && !empty($all_data['tgl_lahir'])?date('Y-m-d',strtotime($all_data['tgl_lahir'])):null ,
        		'id_status_perkawinan' 	=> $all_data['id_status_perkawinan'] ,
        		'alamat_ktp' 			=> $all_data['alamat_ktp'] ,
        		'id_kelurahan_ktp' 		=> $all_data['id_kelurahan_ktp'] ,
        		'alamat_domisili' 		=> $all_data['alamat_domisili'] ,
        		'id_kelurahan_domisili' => $all_data['id_kelurahan_domisili'] ,
        		'no_telp' 				=> $all_data['no_telp'] ,
        		'foto'        			=> $flag,
        	);

        	if(isset($dataProfile))
        	{
        		$this->logUpdatedActivity(Auth::user(),$dataProfile->getAttributes(),$dataUser,'Pegawai','profile');
        		$insert=$dataProfile->update($dataUser);
        		$id_profile=$dataProfile->id;
        	}
        	else
        	{
        		$this->logCreatedActivity(Auth::user(),$dataUser,'Pegawai','profile');
        		$insert=Profile::create($dataUser);
        		$id_profile=$insert->id;
        	}


        	$pegawai=Pegawai::where('id_profile',$id_profile)->first();

        	$dataPegawai=array(
                'id_profile'        =>  $id_profile,
        		'id_jabatan'		=>	$all_data['id_jabatan'],
        		'id_golongan'		=>	$all_data['id_golongan'],
        		'id_departement'	=>	$all_data['id_departement'],
        		'nip'				=>	$all_data['nip'],
        		'tgl_bergabung'		=>	date('Y-m-d',strtotime($all_data['tgl_bergabung'])),
        	);

        	// $this->logUpdatedActivity(Auth::user(),$pegawai->getAttributes(),$dataPegawai,'Pegawai','pegawai');
        	// $updatePegawai=$pegawai->update($dataPegawai);

            $updatePegawai=$this->createOrUpdate($dataPegawai,'\App\Models\Pegawai','id_profile',$id_profile,'Pegawai');


        	$flag=isset($all_data['nama_foto'])?$all_data['nama_foto']:null;

        	if($request->hasFile('foto'))
        	{
        		$extension = $request->file('foto')->getClientOriginalExtension();
        		$dir = 'images/profile/';
        		$flag = uniqid() . '_' . time() . '.' . $extension;
        		$request->file('foto')->move($dir, $flag);
        	}

        	$user=User::where('id_profile',$id_profile)->first();

        	$data  = array(
        		'name'         =>ucwords(strtolower($all_data['nama_depan'])).' '.ucwords(strtolower($all_data['nama_belakang'])) ,
        		'username'     =>strtolower($all_data['nama_depan']) ,
        		'email'        =>strtolower($all_data['nama_depan']).'@gmail.com' ,
        		'password'     =>bcrypt('password') ,
        		'id_profile'   =>$id_profile ,
        	);

        	if(isset($user))
        	{
        		// $this->logUpdatedActivity(Auth::user(),$user->getAttributes(),$data,'Pegawai','users');
        		// $insert=$user->update($data);
        		$id_user=$user->id;
        	}
        	else
        	{
        		$this->logCreatedActivity(Auth::user(),$user,'Pegawai','users');
        		$insert=User::create($data);
        		$id_user=$insert->id;
        	}

        	if(!empty(RoleUser::where('user_id',$id_user)->first())){
            $this->logDeletedActivity(RoleUser::where('user_id',$id_user)->first(),'Hapus Role User user_id='.$id_user.'','Pegawai','role_user');
            }

            $delRoleUser=RoleUser::where('user_id',$id_user)->forceDelete();

            $this->logCreatedActivity(Auth::user(),[
               'role_id'=>intval($all_data['roles']),
               'user_id'=>$id_user,
               'user_type'=>'App\User'
             ],'Pegawai','role_user');

            $role=array(
             'role_id'  =>intval($all_data['roles']),
             'user_id'  =>$id_user,
             'user_type'=>'App\User'
            );

            $roleUser = DB::table('role_user')->insert($role);

            if($updatePegawai==true && $roleUser==true)
            {
                $act=true;
            }
            else
            {
                $act=false;
            }

            message($act,'Data berhasil disimpan!','Data gagal disimpan!');

        }catch (Exception $e) {
        	echo 'Message' .$e->getMessage();
        	DB::rollback();
        }
        DB::commit();

        return redirect('/pegawai');
	}

    public function createOrUpdate($formatted_array,$model,$where,$id,$menu) {
        $row = $model::where($where,$id)->first();
        if ($row === null) {
            $this->logCreatedActivity(Auth::user(),$formatted_array,$menu,with(new $model)->getTable());
            $model::firstOrCreate($formatted_array);
        } else {
            $this->logUpdatedActivity(Auth::user(),$row->getAttributes(),$formatted_array,$menu,with(new $model)->getTable());
            $row->update($formatted_array);
        }
        $affected_row = $model::where($where,$id)->first();
        return $affected_row;
    }

    public function nonaktifkan(Request $request, $id)
    {
        $title='Form Resign';
        $mode='nonaktifkan';
        $this->menuAccess(\Auth::user(),'Pegawai (Nonaktifkan)');
        return view('pegawai.popup',compact('title','mode','id'));
    }

    public function aktifkan(Request $request, $id)
    {
        $title='Form Aktivasi';
        $mode='aktifkan';
        $this->menuAccess(\Auth::user(),'Pegawai (Aktifkan)');
        return view('pegawai.popup',compact('title','mode','id'));
    }

    public function status(Request $request, $id)
    {
        $all_data=$request->all();

        $validation = Validator::make($request->all(), [
            'tanggal'  => 'required',
            'mode'     => 'required',
        ]);


        if (!$validation->passes()){
            $count_err=count($validation->errors()->all());
            $i=0;
            foreach($validation->errors()->all() as $val)
            {
                message(false,'',$val);
                $i++;
            }
            if($count_err==$i)
            {
                return redirect('/pegawai');
            }
        }

        DB::beginTransaction();
        try {

            if($all_data['mode']=='nonaktifkan')
            {
                $data=array(
                    'tgl_resign'    =>  date('Y-m-d',strtotime($all_data['tanggal'])),
                    'flag_resign'   =>  'Y',
                );
            }
            else
            {
                $data=array(
                    'tgl_resign'    =>  null,
                    'flag_resign'   =>  'N',
                    'tgl_bergabung' =>  date('Y-m-d',strtotime($all_data['tanggal'])),
                );
            }

            $act=$this->createOrUpdate($data,'\App\Models\Pegawai','id_profile',$id,'Pegawai');

            message($act,'Data berhasil disimpan!','Data gagal disimpan!');

        }catch (Exception $e) {
            echo 'Message' .$e->getMessage();
            DB::rollback();
        }
        DB::commit();

        return redirect('/pegawai');
    }
}
