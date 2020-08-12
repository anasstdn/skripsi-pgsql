<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Traits\ActivityTraits;
use Illuminate\Support\Facades\Auth;
use App\Models\JenisTransaksi;
use App\Models\KategoriTransaksi;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use Illuminate\Support\Facades\Validator;

class JenisTransaksiController extends Controller
{
    //
    use ActivityTraits;

	public function __construct()
	{
		$this->middleware('auth');
		// $this->middleware('permission:read-jenis-transaksi');
	}

	public function index()
	{
		$kategori_transaksi=KategoriTransaksi::get();
		$this->menuAccess(\Auth::user(),'Jenis Transaksi');
		return view('master.jenis_transaksi.index',compact('kategori_transaksi'));
	}

	public function getData()
	{
		$GLOBALS['nomor']=\Request::input('start',1)+1;
		$dataList = JenisTransaksi::select(\DB::raw('jenis_transaksi.id, jenis_transaksi.jenis_transaksi,kategori_transaksi.nama_kategori,jenis_transaksi.flag_pemasukan,jenis_transaksi.flag_pengeluaran'))->join('kategori_transaksi','kategori_transaksi.id','=','jenis_transaksi.id_kategori_transaksi')->get();
		if (request()->get('status') == 'trash') {
			$dataList->onlyTrashed();
		}
		return DataTables::of($dataList)
		->addColumn('nomor',function($kategori){
			return $GLOBALS['nomor']++;
		})
		->addColumn('jenis_transaksi',function($data){
			if(isset($data->jenis_transaksi)){
				return $data->jenis_transaksi;
			}else{
				return null;
			}
		})
		->addColumn('nama_kategori',function($data){
			if(isset($data->nama_kategori)){
				return $data->nama_kategori;
			}else{
				return null;
			}
		})
		->addColumn('flag_pemasukan',function($data){
			if(isset($data->flag_pemasukan)){
				return $data->flag_pemasukan;
			}else{
				return null;
			}
		})
		->addColumn('flag_pengeluaran',function($data){
			if(isset($data->flag_pengeluaran)){
				return $data->flag_pengeluaran;
			}else{
				return null;
			}
		})
		->addColumn('action', function ($data) {
			$edit=$data->id;
			$delete=url("jenis-transaksi/".$data->id)."/delete";
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
			'jenis_transaksi'   	=> 'required',
			'flag_pemasukan'   		=> 'required',
			'flag_pengeluaran'  	=> 'required',
			'id_kategori_transaksi'	=> 'required',
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
				return redirect('/jenis-transaksi');
			}
		}

		DB::beginTransaction();
		try {

			$data  = array(
				'jenis_transaksi'  		=> $all_data['jenis_transaksi'] ,
				'flag_pemasukan'  		=> $all_data['flag_pemasukan'] ,
				'flag_pengeluaran'  	=> $all_data['flag_pengeluaran'] ,
				'id_kategori_transaksi' => $all_data['id_kategori_transaksi'] ,
			);

			$this->logCreatedActivity(Auth::user(),$data,'Jenis Transaksi','jenis_transaksi');
			$act=JenisTransaksi::create($data);

			message($act,'Data berhasil disimpan!','Data gagal disimpan!');


		} catch (Exception $e) {
			echo 'Message' .$e->getMessage();
			DB::rollback();
		}
		DB::commit();

		return redirect('/jenis-transaksi');
	}

	public function edit(Request $request, $id)
    {
    	$data=JenisTransaksi::find($id);

    	$this->menuAccess(\Auth::user(),'Jenis Transaksi (Edit)');
    	return Response::json(array('data' => $data));  
    }

    public function update(Request $request)
    {
    	$all_data=$request->all();

    	$validation = Validator::make($request->all(), [
    		'jenis_transaksi'   	=> 'required',
			'flag_pemasukan'   		=> 'required',
			'flag_pengeluaran'  	=> 'required',
			'id_kategori_transaksi'	=> 'required',
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
    			return redirect('/jenis-transaksi');
    		}
    	}

    	DB::beginTransaction();
    	try {
    		$get=JenisTransaksi::find($all_data['id']);

    		$data  = array(
    			'jenis_transaksi'  		=> $all_data['jenis_transaksi'] ,
				'flag_pemasukan'  		=> $all_data['flag_pemasukan'] ,
				'flag_pengeluaran'  	=> $all_data['flag_pengeluaran'] ,
				'id_kategori_transaksi' => $all_data['id_kategori_transaksi'] ,
    		);

    		$this->logUpdatedActivity(Auth::user(),$get->getAttributes(),$data,'Jenis Transaksi','jenis_transaksi');

    		$act=$get->update($data);

    		message($act,'Data berhasil diupdate!','Data gagal diupdate!');

    	} catch (Exception $e) {
    		echo 'Message' .$e->getMessage();
    		DB::rollback();
    	}
    	DB::commit();

    	return redirect('/jenis-transaksi');
    }

    public function destroy(Request $request,$kode)
    {
    	$user=JenisTransaksi::find($kode);
    	$act=false;
    	try {
    		$this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Jenis Transaksi','Jenis Transaksi','jenis_transaksi');
    		$act=$user->forceDelete();
    		message($act,'Data berhasil dihapus!','Data gagal dihapus!');
    	} catch (\Exception $e) {
    		$this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Jenis Transaksi','Jenis Transaksi','jenis_transaksi');
    		$user=JenisTransaksi::find($user->pk());
    		$act=$user->delete();
    		message($act,'Data berhasil dihapus!','Data gagal dihapus!');
    	}
    }
}
