<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Traits\ActivityTraits;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use App\Models\Anggotum;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use Illuminate\Support\Facades\Validator;

class AnggotaController extends Controller
{
    //
    use ActivityTraits;

	public function __construct()
	{
		$this->middleware('auth');
		// $this->middleware('permission:read-anggota');
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
				return view('anggota.index');
            }
        }
        else
        {
            $this->menuAccess(\Auth::user(),'Pegawai');
			return view('anggota.index');
        }
	}

    public function getData()
    {
        $GLOBALS['nomor']=\Request::input('start',1)+1;
        $dataList = Profile::select(\DB::raw('profile.*,anggota.tgl_bergabung,anggota.flag_keaktifan'))
                    ->join('anggota','anggota.id_profile','=','profile.id')
                    ->get();

        if (request()->get('status') == 'trash') {
            $dataList->onlyTrashed();
        }
        return DataTables::of($dataList)
        ->addColumn('nomor',function($kategori){
            return $GLOBALS['nomor']++;
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
        ->addColumn('tgl_bergabung',function($data){
            if(isset($data->tgl_bergabung)){
                return date_indo($data->tgl_bergabung);
            }else{
                return null;
            }
        })
         ->addColumn('flag_keaktifan',function($data){
            if(isset($data->flag_keaktifan)){
                return $data->flag_keaktifan;
            }else{
                return null;
            }
        })
        ->addColumn('action', function ($data) {
            $edit=url("anggota/edit/".$data->id);
            $delete=url("anggota/".$data->id)."/delete";

            $content = '';
            $content.="<a href='$edit' class='btn btn-primary btn-sm' data-original-title='Edit' title='Edit'><i class='fa fa-edit' aria-hidden='true'></i></a>";
            $content.="<a href='#' onclick='hapus(\"$delete\")' class='btn btn-danger btn-sm' data-original-title='Hapus' title='Hapus'><i class='fa fa-trash' aria-hidden='true'></i></a>";
            
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
                return view('anggota.form',compact('mode'));
            }
        }
        else
        {
            $mode='create';
            return view('anggota.form',compact('mode'));
        }
    }

    public function store(Request $request)
    {
        $all_data=$request->all();

        $validation = Validator::make($request->all(), [
            'nama_depan'        => 'required',
            'nama_belakang'     => 'required',
            'nik'               => 'required',
            'tgl_bergabung'     => 'required',
            'flag_aktif'        => 'required',
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
                return redirect('/anggota');
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
                'nik'                   => $all_data['nik'] ,
                'nama_depan'            => ucwords(strtolower($all_data['nama_depan'])),
                'nama_belakang'         => ucwords(strtolower($all_data['nama_belakang'])),
                'id_jenis_kelamin'      => $all_data['id_jenis_kelamin'] ,
                'id_agama'              => $all_data['id_agama'] ,
                'tempat_lahir'          => $all_data['tempat_lahir'] ,
                'tgl_lahir'             => isset($all_data['tgl_lahir']) && !empty($all_data['tgl_lahir'])?date('Y-m-d',strtotime($all_data['tgl_lahir'])):null ,
                'id_status_perkawinan'  => $all_data['id_status_perkawinan'] ,
                'alamat_ktp'            => $all_data['alamat_ktp'] ,
                'id_kelurahan_ktp'      => $all_data['id_kelurahan_ktp'] ,
                'alamat_domisili'       => $all_data['alamat_domisili'] ,
                'id_kelurahan_domisili' => $all_data['id_kelurahan_domisili'] ,
                'no_telp'               => $all_data['no_telp'] ,
                'foto'                  => $flag,
            );

            $this->logCreatedActivity(Auth::user(),$dataUser,'Anggota Koperasi','profile');
            $insert=Profile::create($dataUser);
            $id_profile=$insert->id;

            $dataAnggota=array(
                'id_profile'        =>  $id_profile,
                'flag_keaktifan'    =>  $all_data['flag_aktif'],
                'keterangan'        =>  $all_data['keterangan'],
                'tgl_bergabung'     =>  date('Y-m-d',strtotime($all_data['tgl_bergabung'])),
            );

            $this->logCreatedActivity(Auth::user(),$dataAnggota,'Anggota Koperasi','anggota');
            $insertAnggota=Anggotum::create($dataAnggota);

        }catch (Exception $e) {
            echo 'Message' .$e->getMessage();
            DB::rollback();
        }
        DB::commit();

        return redirect('/anggota');
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
                $profile=Profile::join('anggota','anggota.id_profile','=','profile.id')
                        ->where('profile.id',$id)
                        ->first();

                return view('anggota.form',compact('profile','id','mode'));
            }
        }
        else
        {
            $mode='edit';
            $profile=Profile::join('anggota','anggota.id_profile','=','profile.id')
                        ->where('profile.id',$id)
                        ->first();

            return view('anggota.form',compact('profile','id','mode'));
        }
    }

    public function update(Request $request, $id)
    {
        $all_data=$request->all();

        $validation = Validator::make($request->all(), [
            'nama_depan'        => 'required',
            'nama_belakang'     => 'required',
            'nik'               => 'required',
            'tgl_bergabung'     => 'required',
            'flag_aktif'        => 'required',
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
                return redirect('/anggota');
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
                'nik'                   => $all_data['nik'] ,
                'nama_depan'            => ucwords(strtolower($all_data['nama_depan'])),
                'nama_belakang'         => ucwords(strtolower($all_data['nama_belakang'])),
                'id_jenis_kelamin'      => $all_data['id_jenis_kelamin'] ,
                'id_agama'              => $all_data['id_agama'] ,
                'tempat_lahir'          => $all_data['tempat_lahir'] ,
                'tgl_lahir'             => isset($all_data['tgl_lahir']) && !empty($all_data['tgl_lahir'])?date('Y-m-d',strtotime($all_data['tgl_lahir'])):null ,
                'id_status_perkawinan'  => $all_data['id_status_perkawinan'] ,
                'alamat_ktp'            => $all_data['alamat_ktp'] ,
                'id_kelurahan_ktp'      => $all_data['id_kelurahan_ktp'] ,
                'alamat_domisili'       => $all_data['alamat_domisili'] ,
                'id_kelurahan_domisili' => $all_data['id_kelurahan_domisili'] ,
                'no_telp'               => $all_data['no_telp'] ,
                'foto'                  => $flag,
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


            $anggota=Anggotum::where('id_profile',$id_profile)->first();

            $dataAnggota=array(
                'id_profile'        =>  $id_profile,
                'flag_keaktifan'    =>  $all_data['flag_aktif'],
                'keterangan'        =>  $all_data['keterangan'],
                'tgl_bergabung'     =>  date('Y-m-d',strtotime($all_data['tgl_bergabung'])),
            );

            // $this->logUpdatedActivity(Auth::user(),$pegawai->getAttributes(),$dataPegawai,'Pegawai','pegawai');
            // $updatePegawai=$pegawai->update($dataPegawai);

            $updateAnggota=$this->createOrUpdate($dataAnggota,'\App\Models\Anggotum','id_profile',$id_profile,'Anggota');


            if($insert==true && $updateAnggota==true)
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

        return redirect('/anggota');
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

    public function destroy(Request $request,$kode)
    {
    $user=Profile::find($kode);
      $act=false;
      try {
         $this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Anggota Koperasi','Anggota Koperasi','anggota');
         $delRoleUser=Anggotum::where('id_profile',$kode)->forceDelete();
         $act=$user->forceDelete();
         message($act,'Data berhasil dihapus!','Data gagal dihapus!');
     } catch (\Exception $e) {
         $this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Anggota Koperasi','Anggota Koperasi','anggota');
         $user=Profile::find($user->pk());
         $delRoleUser=Anggotum::where('id_profile',$kode)->delete();
         $act=$user->delete();
         message($act,'Data berhasil dihapus!','Data gagal dihapus!');
     }
 }

}
