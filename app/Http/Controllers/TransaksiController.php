<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Traits\ActivityTraits;
use Illuminate\Support\Facades\Auth;
use App\Models\RawDatum;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{
    //
    use ActivityTraits;

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('permission:read-transaksi');
	}

	public function index()
	{
		$this->menuAccess(\Auth::user(),'Transaksi');
		return view('transaksi.index');
	}

	public function getData()
	{
		$GLOBALS['nomor']=\Request::input('start',1)+1;
		$tanggal_awal=\Request::input('tanggal_awal',null);
        $tanggal_akhir=\Request::input('tanggal_akhir',null);
        $no_nota=\Request::input('no_nota',null);
		$dataList = RawDatum::select('*')
		->where(function($q) use($tanggal_awal,$tanggal_akhir,$no_nota){
			if(!empty($tanggal_awal))
			{
				$q->whereDate('tgl_transaksi','>=',date('Y-m-d',strtotime($tanggal_awal)));
			}
			if(!empty($tanggal_akhir))
			{
				$q->whereDate('tgl_transaksi','<=',date('Y-m-d',strtotime($tanggal_akhir)));
			}
			if(!empty($no_nota))
			{
				if (env('DB_CONNECTION') == 'pgsql') {
					$q->where('no_nota','ILIKE','%'.$no_nota.'%');
				}
				else
				{
					$q->where('no_nota','LIKE','%'.$no_nota.'%');
				}
			}
		})
		->get();
		if (request()->get('status') == 'trash') {
			$dataList->onlyTrashed();
		}
		return DataTables::of($dataList)
		->addColumn('nomor',function($kategori){
			return $GLOBALS['nomor']++;
		})
		->addColumn('tgl_transaksi',function($data){
			if(isset($data->tgl_transaksi)){
				return date_indo(date('Y-m-d',strtotime($data->tgl_transaksi)));
			}else{
				return null;
			}
		})
		->addColumn('no_nota',function($data){
			if(isset($data->no_nota)){
				return $data->no_nota;
			}else{
				return null;
			}
		})
		->addColumn('pasir',function($data){
			if(isset($data->pasir)){
				return $data->pasir !== null ?	$data->pasir:0;
			}else{
				return null;
			}
		})
		->addColumn('gendol',function($data){
			if(isset($data->gendol)){
				return $data->gendol !== null ?	$data->gendol:0;
			}else{
				return null;
			}
		})
		->addColumn('abu',function($data){
			if(isset($data->abu)){
				return $data->abu !== null ?	$data->abu:0;
			}else{
				return null;
			}
		})
		->addColumn('split2_3',function($data){
			if(isset($data->split2_3)){
				return $data->split2_3 !== null ?	$data->split2_3:0;
			}else{
				return null;
			}
		})
		->addColumn('split1_2',function($data){
			if(isset($data->split1_2)){
				return $data->split1_2 !== null ?	$data->split1_2:0;
			}else{
				return null;
			}
		})
		->addColumn('lpa',function($data){
			if(isset($data->lpa)){
				return $data->lpa !== null ?	$data->lpa:0;
			}else{
				return null;
			}
		})
		->addColumn('action', function ($data) {
			$edit=url("transaksi/".$data->id)."/edit";
			$delete=url("transaksi/hapus/".$data->id);
			$content = '';
			$content.="<a href='#' onclick='show_modal(\"$edit\")' class='btn btn-primary btn-sm' data-original-title='Edit' title='Edit'><i class='fa fa-edit' aria-hidden='true'></i></a>";
			$content.="<a href='#' onclick='hapus(\"$delete\")' class='btn btn-danger btn-sm' data-original-title='Hapus' title='Hapus'><i class='fa fa-trash' aria-hidden='true'></i></a>";

			return $content;
		})
		->make(true);
	}

	public function create(Request $request)
	{
		$title='Form Tambah Transaksi Penjualan';
        $mode='create';
        $url=route('transaksi.store');
		$this->menuAccess(\Auth::user(),'Transaksi Penjualan');
        return view('transaksi.popup',compact('title','mode','url'));
	}

	public function store(Request $request)
	{
		$all_data=$request->all();

		$validation = Validator::make($request->all(), [
			'tgl_transaksi'   => 'required',
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
				return redirect('/transaksi');
			}
		}

		DB::beginTransaction();
		try {

			$data  = array(
				'tgl_transaksi'	=> date('Y-m-d',strtotime($all_data['tgl_transaksi'])) ,
				'no_nota'  		=> $all_data['no_nota'] ,
				'pasir'  		=> $all_data['pasir'] ,
				'gendol'  		=> $all_data['gendol'] ,
				'abu'  			=> $all_data['abu'] ,
				'split1_2'  	=> $all_data['split1_2'] ,
				'split2_3'  	=> $all_data['split2_3'] ,
				'lpa'  			=> $all_data['lpa'] ,
				'campur'  		=> isset($all_data['campur'])?'Y':'N' ,
			);


			$this->logCreatedActivity(Auth::user(),$data,'Transaksi Penjualan','raw_data');
			$act=RawDatum::create($data);

			message($act,'Data berhasil disimpan!','Data gagal disimpan!');


		} catch (Exception $e) {
			echo 'Message' .$e->getMessage();
			DB::rollback();
		}
		DB::commit();

		return redirect('/transaksi');
	}

	public function edit(Request $request, $id)
    {
    	$title='Form Edit Transaksi Penjualan';
        $mode='edit';
        $data=RawDatum::find($id);
        $url=action('TransaksiController@update', $id);
		$this->menuAccess(\Auth::user(),'Transaksi Penjualan');
        return view('transaksi.popup',compact('title','mode','id','url','data'));
    }

    public function update(Request $request,$id)
    {
    	$all_data=$request->all();

    	$validation = Validator::make($request->all(), [
    		'tgl_transaksi'    => 'required',
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
    			return redirect('/transaksi');
    		}
    	}

    	DB::beginTransaction();
    	try {
    		$get=RawDatum::find($id);

    		$data  = array(
				'tgl_transaksi'	=> date('Y-m-d',strtotime($all_data['tgl_transaksi'])) ,
				'no_nota'  		=> $all_data['no_nota'] ,
				'pasir'  		=> $all_data['pasir'] ,
				'gendol'  		=> $all_data['gendol'] ,
				'abu'  			=> $all_data['abu'] ,
				'split1_2'  	=> $all_data['split1_2'] ,
				'split2_3'  	=> $all_data['split2_3'] ,
				'lpa'  			=> $all_data['lpa'] ,
				'campur'  		=> isset($all_data['campur'])?'Y':'N' ,
			);

    		$this->logUpdatedActivity(Auth::user(),$get->getAttributes(),$data,'Transaksi Penjualan','raw_data');

    		$act=$get->update($data);

    		message($act,'Data berhasil diupdate!','Data gagal diupdate!');

    	} catch (Exception $e) {
    		echo 'Message' .$e->getMessage();
    		DB::rollback();
    	}
    	DB::commit();

    	return redirect('/transaksi');
    }

    public function hapus($id)
    {
    	$data = RawDatum::find($id);
    	$this->logDeletedActivity($data,'Delete data id='.$id.' di menu Transaksi','Transaksi','raw_data');
    	$data->delete();

    	message($data,'Data berhasil dihapus!','Data gagal dihapus!');
    }

    public function destroy(Request $request,$kode)
    {
    	$user=RawDatum::find($kode);
    	$act=false;
    	try {
    		$this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Transaksi','Transaksi','raw_data');
    		$act=$user->forceDelete();
    		message($act,'Data berhasil dihapus!','Data gagal dihapus!');
    	} catch (\Exception $e) {
    		$this->logDeletedActivity($user,'Delete data id='.$kode.' di menu Transaksi','Transaksi','raw_data');
    		$user=RawDatum::find($user->pk());
    		$act=$user->delete();
    		message($act,'Data berhasil dihapus!','Data gagal dihapus!');
    	}
    }
}
