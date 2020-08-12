<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Traits\ActivityTraits;
use Illuminate\Support\Facades\Auth;
use App\Models\KategoriTransaksi;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use Illuminate\Support\Facades\Validator;

class KategoriTransaksiController extends Controller
{
    //
     use ActivityTraits;

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('permission:read-kategori-transaksi');
	}

	public function index()
	{
		$this->menuAccess(\Auth::user(),'Kategori Transaksi');
		return view('master.kategori_transaksi.index');
	}

	public function getData()
	{
		$GLOBALS['nomor']=\Request::input('start',1)+1;
		$dataList = KategoriTransaksi::select('*')->get();
		if (request()->get('status') == 'trash') {
			$dataList->onlyTrashed();
		}
		return DataTables::of($dataList)
		->addColumn('nomor',function($kategori){
			return $GLOBALS['nomor']++;
		})
		->addColumn('nama_kategori',function($data){
			if(isset($data->nama_kategori)){
				return $data->nama_kategori;
			}else{
				return null;
			}
		})
		->addColumn('action', function ($data) {
			$edit=$data->id;
			$delete=url("kategori-transaksi/".$data->id)."/delete";
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
			'nama_kategori'   => 'required',
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
				return redirect('/kategori-transaksi');
			}
		}

		DB::beginTransaction();
		try {

			$data  = array(
				'nama_kategori'  => $all_data['nama_kategori'] ,
			);

			$this->logCreatedActivity(Auth::user(),$data,'Kategori Transaksi','kategori_transaksi');
			$act=KategoriTransaksi::create($data);

			message($act,'Data berhasil disimpan!','Data gagal disimpan!');


		} catch (Exception $e) {
			echo 'Message' .$e->getMessage();
			DB::rollback();
		}
		DB::commit();

		return redirect('/kategori-transaksi');
	}

	public function edit(Request $request, $id)
    {
    	$data=KategoriTransaksi::find($id);

    	$this->menuAccess(\Auth::user(),'Kategori Transaksi (Edit)');
    	return Response::json(array('data' => $data));  
    }

    public function update(Request $request)
    {
    	$all_data=$request->all();

    	$validation = Validator::make($request->all(), [
    		'nama_kategori'    => 'required',
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
    			return redirect('/kategori-transaksi');
    		}
    	}

    	DB::beginTransaction();
    	try {
    		$get=KategoriTransaksi::find($all_data['id']);

    		$data  = array(
    			'nama_kategori'    =>$all_data['nama_kategori'] ,
    		);

    		$this->logUpdatedActivity(Auth::user(),$get->getAttributes(),$data,'Kategori Transaksi','kategori_transaksi');

    		$act=$get->update($data);

    		message($act,'Data berhasil diupdate!','Data gagal diupdate!');

    	} catch (Exception $e) {
    		echo 'Message' .$e->getMessage();
    		DB::rollback();
    	}
    	DB::commit();

    	return redirect('/kategori-transaksi');
    }

    public function destroy(Request $request,$kode)
    {
    	$user=KategoriTransaksi::find($kode);
    	$act=false;
    	try {
    		$this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Kategori Transaksi','Kategori Transaksi','kategori_transaksi');
    		$act=$user->forceDelete();
    		message($act,'Data berhasil dihapus!','Data gagal dihapus!');
    	} catch (\Exception $e) {
    		$this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Katego Transaksi','Kategori Transaksi','kategori_transaksi');
    		$user=KategoriTransaksi::find($user->pk());
    		$act=$user->delete();
    		message($act,'Data berhasil dihapus!','Data gagal dihapus!');
    	}
    }
}
