<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Traits\ActivityTraits;
use Illuminate\Support\Facades\Auth;
use App\Models\RawDatum;
use App\Models\User;
use App\Models\ConfigId;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use Illuminate\Support\Facades\Validator;

class RecycleBinController extends Controller
{
    //
	use ActivityTraits;

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('permission:read-recycle-bin');
	}

	public function index()
	{
		$this->menuAccess(\Auth::user(),'Recycle Bin');
		return view('recycle_bin.index');
	}

	public function loadConfig()
	{
		$GLOBALS['nomor']=\Request::input('start',1)+1;
		$created_at=\Request::input('created_at',null);
        $updated_at=\Request::input('updated_at',null);
        $deleted_at=\Request::input('deleted_at',null);
		$dataList = ConfigId::select('*')
		->where(function($q) use($created_at,$updated_at,$deleted_at){
			if(!empty($created_at))
			{
				$q->whereDate('created_at',date('Y-m-d',strtotime($created_at)));
			}
			if(!empty($updated_at))
			{
				$q->whereDate('updated_at',date('Y-m-d',strtotime($updated_at)));
			}
			if(!empty($deleted_at))
			{
				$q->whereDate('deleted_at',date('Y-m-d',strtotime($deleted_at)));
			}
		})
		->onlyTrashed()
		->get();

		if (request()->get('status') == 'trash') {
			$dataList->onlyTrashed();
		}
		return DataTables::of($dataList)
		->addColumn('nomor',function($kategori){
			return $GLOBALS['nomor']++;
		})
		->addColumn('action', function ($data) {
			$restore=url("recycle-bin/".$data->id)."/restore-config";
			$delete=url("recycle-bin/".$data->id)."/delete-config";
			$content = '';
			$content .= "<a onclick='restore(\"$restore\")' style='color:white' class='btn btn-primary btn-sm' data-toggle='tooltip' data-original-title='Restore'><i class='fa fa-history' aria-hidden='true'></i> Restore</a>";
			$content .= " <a onclick='hapus(\"$delete\")' style='color:white' class='btn btn-danger btn-sm' data-toggle='tooltip' data-original-title='Hapus'><i class='fa fa-trash' aria-hidden='true'></i> Delete</a>";

			return $content;
		})
		->make(true);
	}

	public function restoreConfig(Request $request, $id)
	{
		$data = ConfigId::onlyTrashed()->where('id',$id);
		$this->logRestoredActivity($data->first(),'Restore data id='.$id.' di menu Config ID','Config ID','config_ids');
    	$data->restore();

    	message($data,'Data berhasil dikembalikan!','Data gagal dikembalikan!');
	}

	public function deleteConfig(Request $request, $id)
	{
		$data = ConfigId::onlyTrashed()->where('id',$id);
		$this->logDeletedActivity($data->first(),'Delete permanen data id='.$id.' di menu Config ID','Recycle Bin','config_ids');
    	$data->forceDelete();

    	message($data,'Data berhasil dihapus!','Data gagal dihapus!');
	}

	public function restoreAllConfig()
	{
		$data = ConfigId::onlyTrashed();
		foreach($data->get() as $key=>$val)
		{
			$this->logRestoredActivity($val,'Restore data id='.$val->id.' di menu Config ID','Recycle Bin','config_ids');
		}
    	$data->restore();

    	message($data,'Data berhasil dikembalikan!','Data gagal dikembalikan!');
	}

	public function deleteAllConfig()
	{
		$data = ConfigId::onlyTrashed();
		foreach($data->get() as $key=>$val)
		{
			$this->logDeletedActivity($val,'Delete permanen data id='.$val->id.' di menu Config ID','Config ID','config_ids');
		}
    	$data->forceDelete();

    	message($data,'Data berhasil dihapus!','Data gagal dihapus!');
	}

	public function loadManajemenPengguna()
	{
		$GLOBALS['nomor']=\Request::input('start',1)+1;
		$created_at=\Request::input('created_at',null);
		$updated_at=\Request::input('updated_at',null);
		$deleted_at=\Request::input('deleted_at',null);
		$dataList = User::select('*')
		->where(function($q) use($created_at,$updated_at,$deleted_at){
			if(!empty($created_at))
			{
				$q->whereDate('created_at',date('Y-m-d',strtotime($created_at)));
			}
			if(!empty($updated_at))
			{
				$q->whereDate('updated_at',date('Y-m-d',strtotime($updated_at)));
			}
			if(!empty($deleted_at))
			{
				$q->whereDate('deleted_at',date('Y-m-d',strtotime($deleted_at)));
			}
		})
		->onlyTrashed()
		->get();

		if (request()->get('status') == 'trash') {
			$dataList->onlyTrashed();
		}
		return DataTables::of($dataList)
		->addColumn('nomor',function($kategori){
			return $GLOBALS['nomor']++;
		})
		->addColumn('action', function ($data) {
			$restore=url("recycle-bin/".$data->id)."/restore-manajemen-pengguna";
			$delete=url("recycle-bin/".$data->id)."/delete-manajemen-pengguna";
			$content = '';
			$content .= "<a onclick='restore(\"$restore\")' style='color:white' class='btn btn-primary btn-sm' data-toggle='tooltip' data-original-title='Restore'><i class='fa fa-history' aria-hidden='true'></i> Restore</a>";
			$content .= " <a onclick='hapus(\"$delete\")' style='color:white' class='btn btn-danger btn-sm' data-toggle='tooltip' data-original-title='Hapus'><i class='fa fa-trash' aria-hidden='true'></i> Delete</a>";

			return $content;
		})
		->make(true);
	}

	public function restoreManajemenPengguna(Request $request, $id)
	{
		$data = User::onlyTrashed()->where('id',$id);
		$this->logRestoredActivity($data->first(),'Restore data id='.$id.' di menu Manajemen Pengguna','Recycle Bin','users');
		$data->restore();

		message($data,'Data berhasil dikembalikan!','Data gagal dikembalikan!');
	}

	public function deleteManajemenPengguna(Request $request, $id)
	{
		$data = User::onlyTrashed()->where('id',$id);
		$this->logDeletedActivity($data->first(),'Delete permanen data id='.$id.' di menu Manajemen Pengguna','Recycle Bin','users');
		$data->forceDelete();

		message($data,'Data berhasil dihapus!','Data gagal dihapus!');
	}

	public function restoreAllManajemenPengguna()
	{
		$data = User::onlyTrashed();
		foreach($data->get() as $key=>$val)
		{
			$this->logRestoredActivity($val,'Restore data id='.$val->id.' di menu Manajemen Pengguna','Recycle Bin','users');
		}
		$data->restore();

		message($data,'Data berhasil dikembalikan!','Data gagal dikembalikan!');
	}

	public function deleteAllManajemenPengguna()
	{
		$data = User::onlyTrashed();
		foreach($data->get() as $key=>$val)
		{
			$this->logDeletedActivity($val,'Delete permanen data id='.$val->id.' di menu Manajemen Pengguna','Recycle Bin','users');
		}
		$data->forceDelete();

		message($data,'Data berhasil dihapus!','Data gagal dihapus!');
	}

	public function loadTransaksi()
	{
		$GLOBALS['nomor']=\Request::input('start',1)+1;
		$created_at=\Request::input('created_at',null);
		$updated_at=\Request::input('updated_at',null);
		$deleted_at=\Request::input('deleted_at',null);
		$dataList = RawDatum::select('*')
		->where(function($q) use($created_at,$updated_at,$deleted_at){
			if(!empty($created_at))
			{
				$q->whereDate('created_at',date('Y-m-d',strtotime($created_at)));
			}
			if(!empty($updated_at))
			{
				$q->whereDate('updated_at',date('Y-m-d',strtotime($updated_at)));
			}
			if(!empty($deleted_at))
			{
				$q->whereDate('deleted_at',date('Y-m-d',strtotime($deleted_at)));
			}
		})
		->onlyTrashed()
		->get();

		if (request()->get('status') == 'trash') {
			$dataList->onlyTrashed();
		}
		return DataTables::of($dataList)
		->addColumn('nomor',function($kategori){
			return $GLOBALS['nomor']++;
		})
		->addColumn('action', function ($data) {
			$restore=url("recycle-bin/".$data->id)."/restore-transaksi";
			$delete=url("recycle-bin/".$data->id)."/delete-transaksi";
			$content = '';
			$content .= "<a onclick='restore(\"$restore\")' style='color:white' class='btn btn-primary btn-sm' data-toggle='tooltip' data-original-title='Restore'><i class='fa fa-history' aria-hidden='true'></i> Restore</a>";
			$content .= " <a onclick='hapus(\"$delete\")' style='color:white' class='btn btn-danger btn-sm' data-toggle='tooltip' data-original-title='Hapus'><i class='fa fa-trash' aria-hidden='true'></i> Delete</a>";

			return $content;
		})
		->make(true);
	}

	public function restoreTransaksi(Request $request, $id)
	{
		$data = RawDatum::onlyTrashed()->where('id',$id);
		$this->logRestoredActivity($data->first(),'Restore data id='.$id.' di menu Rekap Transaksi','Recycle Bin','raw_datum');
		$data->restore();

		message($data,'Data berhasil dikembalikan!','Data gagal dikembalikan!');
	}

	public function deleteTransaksi(Request $request, $id)
	{
		$data = RawDatum::onlyTrashed()->where('id',$id);
		$this->logDeletedActivity($data->first(),'Delete permanen data id='.$id.' di menu Rekap Transaksi','Recycle Bin','raw_datum');
		$data->forceDelete();

		message($data,'Data berhasil dihapus!','Data gagal dihapus!');
	}

	public function restoreAllTransaksi()
	{
		$data = RawDatum::onlyTrashed();
		foreach($data->get() as $key=>$val)
		{
			$this->logRestoredActivity($val,'Restore data id='.$val->id.' di menu Rekap Transaksi','Recycle Bin','raw_datum');
		}
		$data->restore();

		message($data,'Data berhasil dikembalikan!','Data gagal dikembalikan!');
	}

	public function deleteAllTransaksi()
	{
		$data = RawDatum::onlyTrashed();
		foreach($data->get() as $key=>$val)
		{
			$this->logDeletedActivity($val,'Delete permanen data id='.$val->id.' di menu Rekap Transaksi','Recycle Bin','raw_datum');
		}
		$data->forceDelete();

		message($data,'Data berhasil dihapus!','Data gagal dihapus!');
	}
}
