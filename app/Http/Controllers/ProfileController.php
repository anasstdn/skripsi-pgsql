<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Traits\ActivityTraits;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    //
    use ActivityTraits;

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('permission:read-profile');
	}

	public function index()
	{
		if(cek_kelengkapan_data()['status']==true)
        {
            message(false,'',cek_kelengkapan_data()['pesan']);
            return redirect('/profile/edit/'.\Auth::user()->id);
        }
        else
        {
           $foto_profile=\App\Models\Profile::find(\Auth::user()->id_profile);
			$this->menuAccess(\Auth::user(),'Profile');
			return view('profile.index',compact('foto_profile'));
        }
	}

	public function edit(Request $request, $id)
	{
		$profile=null;
        // dd($id);
        if(\Auth::user()->id_profile!==null)
        {
            $profile=Profile::find(\Auth::user()->id_profile);
        }
        return view('profile.edit',compact('profile'));
	}

	public function store(Request $request)
	{
		$all_data=$request->all();

		$validation = Validator::make($request->all(), [
            'nama_depan'    		=> 'required',
            'nama_belakang' 		=> 'required',
            'nik'      				=> 'required',
            'id_jenis_kelamin' 		=> 'required',
            'id_agama'         		=> 'required',
            'tempat_lahir'      	=> 'required',
            'tgl_lahir'    			=> 'required',
            'id_status_perkawinan' 	=> 'required',
            'alamat_ktp'         	=> 'required',
            'id_kelurahan_ktp'      => 'required',
            'alamat_domisili'       => 'required',
            'id_kelurahan_domisili'	=> 'required',
            'no_telp'         		=> 'required',
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
                return redirect('/profile');
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

        	$dataProfile=Profile::find(\Auth::user()->id_profile);

        	$dataUser  = array(
        		'nik'          			=> $all_data['nik'] ,
        		'nama_depan'    		=> ucwords(strtolower($all_data['nama_depan'])),
             	'nama_belakang' 		=> ucwords(strtolower($all_data['nama_belakang'])),
        		'id_jenis_kelamin'  	=> $all_data['id_jenis_kelamin'] ,
        		'id_agama'  			=> $all_data['id_agama'] ,
        		'tempat_lahir'  		=> $all_data['tempat_lahir'] ,
        		'tgl_lahir'  			=> date('Y-m-d',strtotime($all_data['tgl_lahir'])) ,
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
        		$this->logUpdatedActivity(Auth::user(),$dataProfile->getAttributes(),$dataUser,'Profile','profile');
        		$insert=$dataProfile->update($dataUser);
        		$id_profile=$dataProfile->id;
        	}
        	else
        	{
        		$this->logCreatedActivity(Auth::user(),$dataUser,'Profile','profile');
        		$insert=Profile::create($dataUser);
        		$id_profile=$insert->id;
        	}

        	$dataUser1=array(
        		'name'          => ucwords(strtolower($all_data['nama_depan'])).' '.ucwords(strtolower($all_data['nama_belakang'])),
        		'id_profile'    => $id_profile,
        	);

        	$this->logUpdatedActivity(Auth::user(),User::find(\Auth::user()->id)->getAttributes(),$dataUser1,'Profile','users');
        	$update=User::find(\Auth::user()->id)->update($dataUser1);

        	if($insert==true && $update==true)
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

      return redirect('/profile');
	}
}
