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
use Illuminate\Support\Facades\Schema;

class LaporanController extends Controller
{
    //
    use ActivityTraits;

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('permission:read-laporan');
	}

	public function index()
	{
		$dataList=Schema::getColumnListing('raw_data');
		$this->menuAccess(\Auth::user(),'Laporan');
		return view('laporan.index');
	}

	public function harian()
	{
		$GLOBALS['nomor']=\Request::input('start',1)+1;
		$tanggal=\Request::input('tanggal',null);

		$dataList = RawDatum::select('*')
		->where(function($q) use($tanggal){
			if(!empty($tanggal))
			{
				$q->whereDate('tgl_transaksi','=',date('Y-m-d',strtotime($tanggal)));
			}
		})
		->orderby('tgl_transaksi','ASC')
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
		->make(true);
	}

	public function mingguan()
	{
		$GLOBALS['nomor']=\Request::input('start',1)+1;
		$tanggal=\Request::input('tanggal',null);
		$tahun=date('Y',strtotime(\Request::input('tanggal')));
		$minggu=strftime('%V', strtotime($tanggal));

		$dataList = RawDatum::select(\DB::raw('sum(pasir) as pasir,sum(abu) as abu,sum(gendol) as gendol,sum(split2_3) as split2_3,sum(split1_2) as split1_2,sum(lpa) as lpa,tgl_transaksi'))
		->where(function($q) use($minggu){
			if (env('DB_CONNECTION') == 'pgsql') {
				$q->where(\DB::raw('extract("week" from tgl_transaksi)'),'=',$minggu);
			}
			else
			{
				$q->where(\DB::raw('EXTRACT(WEEK FROM tgl_transaksi)'),'=',$minggu);
			}
		})
		->whereYear('tgl_transaksi',$tahun)
		->whereMonth('tgl_transaksi',date('m',strtotime($tanggal)))
		->groupBy('tgl_transaksi')
		->get();

		if (request()->get('status') == 'trash') {
			$dataList->onlyTrashed();
		}
		return DataTables::of($dataList)
		->addColumn('nomor',function($kategori){
			return $GLOBALS['nomor']++;
		})
		->addColumn('minggu',function($data){
			if(isset($data->tgl_transaksi)){
				return date( 'W', strtotime( "".$data->tgl_transaksi." + 1 day" ) );
			}else{
				return null;
			}
		})
		->addColumn('tgl_transaksi',function($data){
			if(isset($data->tgl_transaksi)){
				return date_indo(date('Y-m-d',strtotime($data->tgl_transaksi)));
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
		->make(true);
	}

	public function bulanan()
	{
		$GLOBALS['nomor']=\Request::input('start',1)+1;
		$tahun=\Request::input('tahun');
		$bulan=\Request::input('bulan');

		$dataList=RawDatum::select(DB::raw('tgl_transaksi,COALESCE(sum(pasir),0) as pasir,COALESCE(sum(gendol),0) as gendol,COALESCE(sum(abu),0) as abu, COALESCE(sum(split2_3),0) as split2_3, COALESCE(sum(split1_2),0) as split1_2, COALESCE(sum(lpa),0) as lpa'))
		->whereMonth('tgl_transaksi',$bulan)
		->whereYear('tgl_transaksi',$tahun)
		->groupBy(DB::raw('tgl_transaksi'))
		->orderBy('tgl_transaksi','asc')
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
		->make(true);
	}

	public function tahunan()
	{
		$GLOBALS['nomor']=\Request::input('start',1)+1;
		$tahun=\Request::input('tahun');
		$dataList=Schema::getColumnListing('raw_data');

		$arr_to_rem=array('id','tgl_transaksi','no_nota','created_at','updated_at','campur','deleted_at');
		$dataList=array_diff($dataList,$arr_to_rem);

		if (request()->get('status') == 'trash') {
			$dataList->onlyTrashed();
		}
		return DataTables::of($dataList)
		->addColumn('nomor',function($kategori){
			return $GLOBALS['nomor']++;
		})
		->addColumn('produk',function($data){
        // dd($data);
			if($data!=='id' && $data!=='tgl_transaksi' && $data !=='no_nota' && $data!=='created_at' && $data!=='updated_at')
			{
				if(isset($data)){
					return column_name($data);
				}
			}

		})

		->addColumn('jan',function($data) use($tahun){
			if(isset($data)){
				$jum=RawDatum::select(\DB::raw('sum('.$data.') as total'))
				->whereMonth('tgl_transaksi',1)
				->whereYear('tgl_transaksi',$tahun)
				->first();
        // dd($jum);
				$total=isset($jum) && !empty($jum->total)?$jum->total:0;
				return number_format($total,1,',','.');
			}else{
				return null;
			}
		})
		->addColumn('feb',function($data) use($tahun){
			if(isset($data)){
				$jum=RawDatum::select(\DB::raw('sum('.$data.') as total'))
				->whereMonth('tgl_transaksi',2)
				->whereYear('tgl_transaksi',$tahun)
				->first();
        // dd($jum);
				$total=isset($jum) && !empty($jum->total)?$jum->total:0;
				return number_format($total,1,',','.');
			}else{
				return null;
			}
		})
		->addColumn('mar',function($data) use($tahun){
			if(isset($data)){
				$jum=RawDatum::select(\DB::raw('sum('.$data.') as total'))
				->whereMonth('tgl_transaksi',3)
				->whereYear('tgl_transaksi',$tahun)
				->first();
        // dd($jum);
				$total=isset($jum) && !empty($jum->total)?$jum->total:0;
				return number_format($total,1,',','.');
			}else{
				return null;
			}
		})
		->addColumn('apr',function($data) use($tahun){
			if(isset($data)){
				$jum=RawDatum::select(\DB::raw('sum('.$data.') as total'))
				->whereMonth('tgl_transaksi',4)
				->whereYear('tgl_transaksi',$tahun)
				->first();
        // dd($jum);
				$total=isset($jum) && !empty($jum->total)?$jum->total:0;
				return number_format($total,1,',','.');
			}else{
				return null;
			}
		})
		->addColumn('mei',function($data) use($tahun){
			if(isset($data)){
				$jum=RawDatum::select(\DB::raw('sum('.$data.') as total'))
				->whereMonth('tgl_transaksi',5)
				->whereYear('tgl_transaksi',$tahun)
				->first();
        // dd($jum);
				$total=isset($jum) && !empty($jum->total)?$jum->total:0;
				return number_format($total,1,',','.');
			}else{
				return null;
			}
		})
		->addColumn('jun',function($data) use($tahun){
			if(isset($data)){
				$jum=RawDatum::select(\DB::raw('sum('.$data.') as total'))
				->whereMonth('tgl_transaksi',6)
				->whereYear('tgl_transaksi',$tahun)
				->first();
        // dd($jum);
				$total=isset($jum) && !empty($jum->total)?$jum->total:0;
				return number_format($total,1,',','.');
			}else{
				return null;
			}
		})
		->addColumn('jul',function($data) use($tahun){
			if(isset($data)){
				$jum=RawDatum::select(\DB::raw('sum('.$data.') as total'))
				->whereMonth('tgl_transaksi',7)
				->whereYear('tgl_transaksi',$tahun)
				->first();
        // dd($jum);
				$total=isset($jum) && !empty($jum->total)?$jum->total:0;
				return number_format($total,1,',','.');
			}else{
				return null;
			}
		})
		->addColumn('aug',function($data) use($tahun){
			if(isset($data)){
				$jum=RawDatum::select(\DB::raw('sum('.$data.') as total'))
				->whereMonth('tgl_transaksi',8)
				->whereYear('tgl_transaksi',$tahun)
				->first();
        // dd($jum);
				$total=isset($jum) && !empty($jum->total)?$jum->total:0;
				return number_format($total,1,',','.');
			}else{
				return null;
			}
		})
		->addColumn('sep',function($data) use($tahun){
			if(isset($data)){
				$jum=RawDatum::select(\DB::raw('sum('.$data.') as total'))
				->whereMonth('tgl_transaksi',9)
				->whereYear('tgl_transaksi',$tahun)
				->first();
        // dd($jum);
				$total=isset($jum) && !empty($jum->total)?$jum->total:0;
				return number_format($total,1,',','.');
			}else{
				return null;
			}
		})
		->addColumn('okt',function($data) use($tahun){ 
			if(isset($data)){
				$jum=RawDatum::select(\DB::raw('sum('.$data.') as total'))
				->whereMonth('tgl_transaksi',10)
				->whereYear('tgl_transaksi',$tahun)
				->first();
        // dd($jum);
				$total=isset($jum) && !empty($jum->total)?$jum->total:0;
				return number_format($total,1,',','.');
			}else{
				return null;
			}
		})
		->addColumn('nov',function($data) use($tahun){
			if(isset($data)){
				$jum=RawDatum::select(\DB::raw('sum('.$data.') as total'))
				->whereMonth('tgl_transaksi',11)
				->whereYear('tgl_transaksi',$tahun)
				->first();
        // dd($jum);
				$total=isset($jum) && !empty($jum->total)?$jum->total:0;
				return number_format($total,1,',','.');
			}else{
				return null;
			}
		})
		->addColumn('des',function($data) use($tahun){
			if(isset($data)){
				$jum=RawDatum::select(\DB::raw('sum('.$data.') as total'))
				->whereMonth('tgl_transaksi',12)
				->whereYear('tgl_transaksi',$tahun)
				->first();
        // dd($jum);
				$total=isset($jum) && !empty($jum->total)?$jum->total:0;
				return number_format($total,1,',','.');
			}else{
				return null;
			}
		})
		->make(true);
	}
}
