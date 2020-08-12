<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Traits\ActivityTraits;
use Illuminate\Support\Facades\Auth;
use App\Models\Golongan;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use Illuminate\Support\Facades\Validator;

class GolonganController extends Controller
{
    //
    use ActivityTraits;

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('permission:read-golongan');
	}

	public function index()
	{
		$this->menuAccess(\Auth::user(),'Golongan');
		return view('master.golongan.index');
	}

	public function getData()
	{
		$GLOBALS['nomor']=\Request::input('start',1)+1;
		$dataList = Golongan::select('*')->get();
		if (request()->get('status') == 'trash') {
			$dataList->onlyTrashed();
		}
		return DataTables::of($dataList)
		->addColumn('nomor',function($kategori){
			return $GLOBALS['nomor']++;
		})
		->addColumn('golongan',function($data){
			if(isset($data->golongan)){
				return $data->golongan;
			}else{
				return null;
			}
		})
		->addColumn('action', function ($data) {
			$edit=$data->id;
			$delete=url("golongan/".$data->id)."/delete";
			$content = '';
			$content.="<a href='#' onclick='ubah_data(\"$edit\")' class='btn btn-primary btn-sm' data-original-title='Edit' title='Edit'><i class='fa fa-edit' aria-hidden='true'></i></a>";
			$content.="<a href='#' onclick='hapus(\"$delete\")' class='btn btn-danger btn-sm' data-original-title='Hapus' title='Hapus'><i class='fa fa-trash' aria-hidden='true'></i></a>";

			return $content;
		})
		->make(true);
	}

	public function store(Request $request)
	{
		$all_data=$request->all();

		$validation = Validator::make($request->all(), [
			'golongan'   => 'required',
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
				return redirect('/golongan');
			}
		}

		DB::beginTransaction();
		try {

			$data  = array(
				'golongan'  => $all_data['golongan'] ,
			);

			$this->logCreatedActivity(Auth::user(),$data,'Golongan','golongan');
			$act=Golongan::create($data);

			message($act,'Data berhasil disimpan!','Data gagal disimpan!');


		} catch (Exception $e) {
			echo 'Message' .$e->getMessage();
			DB::rollback();
		}
		DB::commit();

		return redirect('/golongan');
	}

	public function edit(Request $request, $id)
    {
    	$data=Golongan::find($id);

    	$this->menuAccess(\Auth::user(),'Golongan (Edit)');
    	return Response::json(array('data' => $data));  
    }

    public function update(Request $request)
    {
    	$all_data=$request->all();

    	$validation = Validator::make($request->all(), [
    		'golongan'    => 'required',
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
    			return redirect('/golongan');
    		}
    	}

    	DB::beginTransaction();
    	try {
    		$get=Golongan::find($all_data['id']);

    		$data  = array(
    			'golongan'    =>$all_data['golongan'] ,
    		);

    		$this->logUpdatedActivity(Auth::user(),$get->getAttributes(),$data,'Golongan','golongan');

    		$act=$get->update($data);

    		message($act,'Data berhasil diupdate!','Data gagal diupdate!');

    	} catch (Exception $e) {
    		echo 'Message' .$e->getMessage();
    		DB::rollback();
    	}
    	DB::commit();

    	return redirect('/golongan');
    }

    public function destroy(Request $request,$kode)
    {
    	$user=Golongan::find($kode);
    	$act=false;
    	try {
    		$this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Golongan','Golongan','golongan');
    		$act=$user->forceDelete();
    		message($act,'Data berhasil dihapus!','Data gagal dihapus!');
    	} catch (\Exception $e) {
    		$this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Golongan','Golongan','golongan');
    		$user=Golongan::find($user->pk());
    		$act=$user->delete();
    		message($act,'Data berhasil dihapus!','Data gagal dihapus!');
    	}
    }
}
