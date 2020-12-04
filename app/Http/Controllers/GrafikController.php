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
use DatePeriod;
use DateTime;
use DateInterval;

class GrafikController extends Controller
{
    //
	use ActivityTraits;

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('permission:read-grafik');
	}

	public function index()
	{
		$this->menuAccess(\Auth::user(),'Grafik');
		return view('grafik.index');
	}

	public function getChart(Request $request)
	{
		  // dd('aaaaa');
		$total_transaksi=array();

		$graph_pie=array();

		$all_data=$request->all();

		$date_from=date(''.$all_data['tahun'].'-01-01');
		$date_to=date(''.$all_data['tahun'].'-12-31');

		if (env('DB_CONNECTION') == 'pgsql') {
			$data_penjualan=RawDatum::select(DB::raw('
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 1 THEN 1
				ELSE 0
				END
				) AS "Jan",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 2 THEN 1
				ELSE 0
				END
				) AS "Feb",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 3 THEN 1
				ELSE 0
				END
				) AS "Mar",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 4 THEN 1
				ELSE 0
				END
				) AS "Apr",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 5 THEN 1
				ELSE 0
				END
				) AS "May",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 6 THEN 1
				ELSE 0
				END
				) AS "Jun",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 7 THEN 1
				ELSE 0
				END
				) AS "Jul",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 8 THEN 1
				ELSE 0
				END
				) AS "Aug",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 9 THEN 1
				ELSE 0
				END
				) AS "Sep",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 10 THEN 1
				ELSE 0
				END
				) AS "Oct",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 11 THEN 1
				ELSE 0
				END
				) AS "Nov",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 12 THEN 1
				ELSE 0
				END
				) AS "Dec"
				'))
			->whereYear('tgl_transaksi',date(''.$all_data['tahun'].''))
			->first();
		}
		else
		{
			$data_penjualan=RawDatum::select(DB::raw('
                SUM(if(MONTH(tgl_transaksi) = 1, 1,0)) AS "Jan",
                SUM(if(MONTH(tgl_transaksi) = 2, 1,0)) AS "Feb",
                SUM(if(MONTH(tgl_transaksi) = 3, 1,0)) AS "Mar",
                SUM(if(MONTH(tgl_transaksi) = 4, 1,0)) AS "Apr",
                SUM(if(MONTH(tgl_transaksi) = 5, 1,0)) AS "May",
                SUM(if(MONTH(tgl_transaksi) = 6, 1,0)) AS "Jun",
                SUM(if(MONTH(tgl_transaksi) = 7, 1,0)) AS "Jul",
                SUM(if(MONTH(tgl_transaksi) = 8, 1,0)) AS "Aug",
                SUM(if(MONTH(tgl_transaksi) = 9, 1,0)) AS "Sep",
                SUM(if(MONTH(tgl_transaksi) = 10, 1,0)) AS "Oct",
                SUM(if(MONTH(tgl_transaksi) = 11, 1,0)) AS "Nov",
                SUM(if(MONTH(tgl_transaksi) = 12, 1,0)) AS "Dec"
                '))
            ->whereYear('tgl_transaksi',date(''.$all_data['tahun'].''))
            ->first();
		}

		// array_push($total_transaksi,$data_penjualan->Jan);
		// array_push($total_transaksi,$data_penjualan->Feb);
		// array_push($total_transaksi,$data_penjualan->Mar);
		// array_push($total_transaksi,$data_penjualan->Apr);
		// array_push($total_transaksi,$data_penjualan->May);
		// array_push($total_transaksi,$data_penjualan->Jun);
		// array_push($total_transaksi,$data_penjualan->Jul);
		// array_push($total_transaksi,$data_penjualan->Aug);
		// array_push($total_transaksi,$data_penjualan->Sep);
		// array_push($total_transaksi,$data_penjualan->Oct);
		// array_push($total_transaksi,$data_penjualan->Nov);
		// array_push($total_transaksi,$data_penjualan->Dec);

		$data_penjualan 	=	RawDatum::select(DB::raw('DATE_FORMAT(tgl_transaksi, "%v/%x") as minggu, COUNT(*) as total'))
		->whereYear('tgl_transaksi','=',$all_data['tahun'])
		->groupBy(DB::raw('DATE_FORMAT(tgl_transaksi, "%v/%x")'))
		->orderby('tgl_transaksi','ASC')
		->get();

		$minggu = array();

		if(isset($data_penjualan) && !$data_penjualan->isEmpty())
		{
			foreach($data_penjualan as $key => $val)
			{
				array_push($minggu,$val->minggu);
				array_push($total_transaksi,$val->total);
			}
		}

		$penjualan_barang 	=	RawDatum::select(DB::raw('DATE_FORMAT(tgl_transaksi, "%v/%x") as minggu,IF(sum(pasir) IS NULL,0,sum(pasir)) as pasir,IF(sum(gendol) IS NULL, 0, sum(gendol)) as gendol,IF(sum(abu) IS NULL,0,sum(abu)) as abu, IF(sum(split2_3) IS NULL,0,sum(split2_3)) as split2_3, IF(sum(split1_2) IS NULL, 0, sum(split2_3)) as split1_2, IF(sum(lpa) IS NULL,0,sum(lpa)) as lpa'))
		->whereYear('tgl_transaksi','=',$all_data['tahun'])
		->groupBy(DB::raw('DATE_FORMAT(tgl_transaksi, "%v/%x")'))
		->orderby('tgl_transaksi','ASC')
		->get();

		$total_pasir = array();
		$total_abu = array();
		$total_gendol = array();
		$total_split_1 = array();
		$total_split_2 = array();
		$total_lpa = array();

		if(isset($penjualan_barang) && !$penjualan_barang->isEmpty())
		{
			foreach($penjualan_barang as $key => $val)
			{
				array_push($total_pasir,$val->pasir);
				array_push($total_abu,$val->abu);
				array_push($total_gendol,$val->gendol);
				array_push($total_split_1,$val->split1_2);
				array_push($total_split_2,$val->split2_3);
				array_push($total_lpa,$val->lpa);
			}
		}


		// $total_pasir=$this->totalPasir($all_data['tahun']);

		// $total_abu=$this->totalAbu($all_data['tahun']);

		// $total_gendol=$this->totalGendol($all_data['tahun']);

		// $total_split_1=$this->totalSplit1($all_data['tahun']);

		// $total_split_2=$this->totalSplit2($all_data['tahun']);

		// $total_lpa=$this->totalLpa($all_data['tahun']);

		$bulan=$this->month_between_two_dates($date_from,$date_to);

		$total_pasir_pie=RawDatum::select(DB::raw('count(id) as pasir'))
		->whereNotNull('pasir')
		->where('campur','N')
		->whereYear('tgl_transaksi',date(''.$all_data['tahun'].''))
		->first();
		array_push($graph_pie, $total_pasir_pie->pasir);

		$total_abu_pie=RawDatum::select(DB::raw('count(id) as abu'))
		->whereNotNull('abu')
		->where('campur','N')
		->whereYear('tgl_transaksi',date(''.$all_data['tahun'].''))->first();
		array_push($graph_pie, $total_abu_pie->abu);

		$total_gendol_pie=RawDatum::select(DB::raw('count(id) as gendol'))
		->whereNotNull('gendol')
		->where('campur','N')
		->whereYear('tgl_transaksi',date(''.$all_data['tahun'].''))->first();
		array_push($graph_pie, $total_gendol_pie->gendol);

		$total_split_1_pie=RawDatum::select(DB::raw('count(id) as split1_2'))
		->whereNotNull('split1_2')
		->where('campur','N')
		->whereYear('tgl_transaksi',date(''.$all_data['tahun'].''))->first();
		array_push($graph_pie, $total_split_1_pie->split1_2);

		$total_split_2_pie=RawDatum::select(DB::raw('count(id) as split2_3'))
		->whereNotNull('split2_3')
		->where('campur','N')
		->whereYear('tgl_transaksi',date(''.$all_data['tahun'].''))->first();
		array_push($graph_pie, $total_split_2_pie->split2_3);

		$total_lpa_pie=RawDatum::select(DB::raw('count(id) as lpa'))
		->whereNotNull('lpa')
		->where('campur','N')
		->whereYear('tgl_transaksi',date(''.$all_data['tahun'].''))->first();
		array_push($graph_pie, $total_lpa_pie->lpa);

		$total_campur_pie=RawDatum::select(DB::raw('count(id) as campur'))
		->where('campur','Y')
		->whereYear('tgl_transaksi',date(''.$all_data['tahun'].''))->first();
		array_push($graph_pie, $total_campur_pie->campur);

		$label_pie=['Pasir','Abu','Pasir Gendol','Split 1/2','Split 2/3','LPA','Campur'];

		$data=array(
			'bulan'=>$bulan,
			'minggu' => $minggu,
			'total_transaksi'=>$total_transaksi,
			'total_pasir'=>$total_pasir,
			'total_abu'=>$total_abu,
			'total_gendol'=>$total_gendol,
			'total_split_1'=>$total_split_1,
			'total_split_2'=>$total_split_2,
			'total_lpa'=>$total_lpa,
			'graph_pie'=>$graph_pie,
			'label_pie'=>$label_pie,
		);

		return \Response::json($data);  
	}

	public function totalPasir($tahun)
	{
		$total_transaksi=array();
		if (env('DB_CONNECTION') == 'pgsql') {
			$data_penjualan=RawDatum::select(DB::raw('
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 1 THEN pasir
				ELSE 0
				END
				) AS "Jan",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 2 THEN pasir
				ELSE 0
				END
				) AS "Feb",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 3 THEN pasir
				ELSE 0
				END
				) AS "Mar",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 4 THEN pasir
				ELSE 0
				END
				) AS "Apr",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 5 THEN pasir
				ELSE 0
				END
				) AS "May",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 6 THEN pasir
				ELSE 0
				END
				) AS "Jun",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 7 THEN pasir
				ELSE 0
				END
				) AS "Jul",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 8 THEN pasir
				ELSE 0
				END
				) AS "Aug",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 9 THEN pasir
				ELSE 0
				END
				) AS "Sep",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 10 THEN pasir
				ELSE 0
				END
				) AS "Oct",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 11 THEN pasir
				ELSE 0
				END
				) AS "Nov",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 12 THEN pasir
				ELSE 0
				END
				) AS "Dec"
				'))
			->whereYear('tgl_transaksi',date(''.$tahun.''))
      // ->groupby('tgl_transaksi')
			->first();
		}
		else
		{
			$data_penjualan=RawDatum::select(DB::raw('
                SUM(if(MONTH(tgl_transaksi) = 1, pasir,0)) AS "Jan",
                SUM(if(MONTH(tgl_transaksi) = 2, pasir,0)) AS "Feb",
                SUM(if(MONTH(tgl_transaksi) = 3, pasir,0)) AS "Mar",
                SUM(if(MONTH(tgl_transaksi) = 4, pasir,0)) AS "Apr",
                SUM(if(MONTH(tgl_transaksi) = 5, pasir,0)) AS "May",
                SUM(if(MONTH(tgl_transaksi) = 6, pasir,0)) AS "Jun",
                SUM(if(MONTH(tgl_transaksi) = 7, pasir,0)) AS "Jul",
                SUM(if(MONTH(tgl_transaksi) = 8, pasir,0)) AS "Aug",
                SUM(if(MONTH(tgl_transaksi) = 9, pasir,0)) AS "Sep",
                SUM(if(MONTH(tgl_transaksi) = 10, pasir,0)) AS "Oct",
                SUM(if(MONTH(tgl_transaksi) = 11, pasir,0)) AS "Nov",
                SUM(if(MONTH(tgl_transaksi) = 12, pasir,0)) AS "Dec"
                '))
            ->whereYear('tgl_transaksi',date(''.$tahun.''))
            ->first();
		}

		array_push($total_transaksi,$data_penjualan->Jan);
		array_push($total_transaksi,$data_penjualan->Feb);
		array_push($total_transaksi,$data_penjualan->Mar);
		array_push($total_transaksi,$data_penjualan->Apr);
		array_push($total_transaksi,$data_penjualan->May);
		array_push($total_transaksi,$data_penjualan->Jun);
		array_push($total_transaksi,$data_penjualan->Jul);
		array_push($total_transaksi,$data_penjualan->Aug);
		array_push($total_transaksi,$data_penjualan->Sep);
		array_push($total_transaksi,$data_penjualan->Oct);
		array_push($total_transaksi,$data_penjualan->Nov);
		array_push($total_transaksi,$data_penjualan->Dec);

		return $total_transaksi;
	}

	public function totalAbu($tahun)
	{
		$total_transaksi=array();
		if (env('DB_CONNECTION') == 'pgsql') {
			$data_penjualan=RawDatum::select(DB::raw('
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 1 THEN abu
				ELSE 0
				END
				) AS "Jan",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 2 THEN abu
				ELSE 0
				END
				) AS "Feb",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 3 THEN abu
				ELSE 0
				END
				) AS "Mar",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 4 THEN abu
				ELSE 0
				END
				) AS "Apr",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 5 THEN abu
				ELSE 0
				END
				) AS "May",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 6 THEN abu
				ELSE 0
				END
				) AS "Jun",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 7 THEN abu
				ELSE 0
				END
				) AS "Jul",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 8 THEN abu
				ELSE 0
				END
				) AS "Aug",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 9 THEN abu
				ELSE 0
				END
				) AS "Sep",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 10 THEN abu
				ELSE 0
				END
				) AS "Oct",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 11 THEN abu
				ELSE 0
				END
				) AS "Nov",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 12 THEN abu
				ELSE 0
				END
				) AS "Dec"
				'))
			->whereYear('tgl_transaksi',date(''.$tahun.''))
      // ->groupby('tgl_transaksi')
			->first();
		}
		else
		{
			$data_penjualan=RawDatum::select(DB::raw('
                SUM(if(MONTH(tgl_transaksi) = 1, abu,0)) AS "Jan",
                SUM(if(MONTH(tgl_transaksi) = 2, abu,0)) AS "Feb",
                SUM(if(MONTH(tgl_transaksi) = 3, abu,0)) AS "Mar",
                SUM(if(MONTH(tgl_transaksi) = 4, abu,0)) AS "Apr",
                SUM(if(MONTH(tgl_transaksi) = 5, abu,0)) AS "May",
                SUM(if(MONTH(tgl_transaksi) = 6, abu,0)) AS "Jun",
                SUM(if(MONTH(tgl_transaksi) = 7, abu,0)) AS "Jul",
                SUM(if(MONTH(tgl_transaksi) = 8, abu,0)) AS "Aug",
                SUM(if(MONTH(tgl_transaksi) = 9, abu,0)) AS "Sep",
                SUM(if(MONTH(tgl_transaksi) = 10, abu,0)) AS "Oct",
                SUM(if(MONTH(tgl_transaksi) = 11, abu,0)) AS "Nov",
                SUM(if(MONTH(tgl_transaksi) = 12, abu,0)) AS "Dec"
                '))
            ->whereYear('tgl_transaksi',date(''.$tahun.''))
            ->first();
		}

		array_push($total_transaksi,$data_penjualan->Jan);
		array_push($total_transaksi,$data_penjualan->Feb);
		array_push($total_transaksi,$data_penjualan->Mar);
		array_push($total_transaksi,$data_penjualan->Apr);
		array_push($total_transaksi,$data_penjualan->May);
		array_push($total_transaksi,$data_penjualan->Jun);
		array_push($total_transaksi,$data_penjualan->Jul);
		array_push($total_transaksi,$data_penjualan->Aug);
		array_push($total_transaksi,$data_penjualan->Sep);
		array_push($total_transaksi,$data_penjualan->Oct);
		array_push($total_transaksi,$data_penjualan->Nov);
		array_push($total_transaksi,$data_penjualan->Dec);

		return $total_transaksi;
	}

	public function totalGendol($tahun)
	{
		$total_transaksi=array();
		if (env('DB_CONNECTION') == 'pgsql') {
			$data_penjualan=RawDatum::select(DB::raw('
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 1 THEN gendol
				ELSE 0
				END
				) AS "Jan",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 2 THEN gendol
				ELSE 0
				END
				) AS "Feb",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 3 THEN gendol
				ELSE 0
				END
				) AS "Mar",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 4 THEN gendol
				ELSE 0
				END
				) AS "Apr",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 5 THEN gendol
				ELSE 0
				END
				) AS "May",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 6 THEN gendol
				ELSE 0
				END
				) AS "Jun",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 7 THEN gendol
				ELSE 0
				END
				) AS "Jul",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 8 THEN gendol
				ELSE 0
				END
				) AS "Aug",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 9 THEN gendol
				ELSE 0
				END
				) AS "Sep",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 10 THEN gendol
				ELSE 0
				END
				) AS "Oct",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 11 THEN gendol
				ELSE 0
				END
				) AS "Nov",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 12 THEN gendol
				ELSE 0
				END
				) AS "Dec"
				'))
			->whereYear('tgl_transaksi',date(''.$tahun.''))
      // ->groupby('tgl_transaksi')
			->first();
		}
		else
		{
			$data_penjualan=RawDatum::select(DB::raw('
                SUM(if(MONTH(tgl_transaksi) = 1, gendol,0)) AS "Jan",
                SUM(if(MONTH(tgl_transaksi) = 2, gendol,0)) AS "Feb",
                SUM(if(MONTH(tgl_transaksi) = 3, gendol,0)) AS "Mar",
                SUM(if(MONTH(tgl_transaksi) = 4, gendol,0)) AS "Apr",
                SUM(if(MONTH(tgl_transaksi) = 5, gendol,0)) AS "May",
                SUM(if(MONTH(tgl_transaksi) = 6, gendol,0)) AS "Jun",
                SUM(if(MONTH(tgl_transaksi) = 7, gendol,0)) AS "Jul",
                SUM(if(MONTH(tgl_transaksi) = 8, gendol,0)) AS "Aug",
                SUM(if(MONTH(tgl_transaksi) = 9, gendol,0)) AS "Sep",
                SUM(if(MONTH(tgl_transaksi) = 10, gendol,0)) AS "Oct",
                SUM(if(MONTH(tgl_transaksi) = 11, gendol,0)) AS "Nov",
                SUM(if(MONTH(tgl_transaksi) = 12, gendol,0)) AS "Dec"
                '))
            ->whereYear('tgl_transaksi',date(''.$tahun.''))
            ->first();
		}

		array_push($total_transaksi,$data_penjualan->Jan);
		array_push($total_transaksi,$data_penjualan->Feb);
		array_push($total_transaksi,$data_penjualan->Mar);
		array_push($total_transaksi,$data_penjualan->Apr);
		array_push($total_transaksi,$data_penjualan->May);
		array_push($total_transaksi,$data_penjualan->Jun);
		array_push($total_transaksi,$data_penjualan->Jul);
		array_push($total_transaksi,$data_penjualan->Aug);
		array_push($total_transaksi,$data_penjualan->Sep);
		array_push($total_transaksi,$data_penjualan->Oct);
		array_push($total_transaksi,$data_penjualan->Nov);
		array_push($total_transaksi,$data_penjualan->Dec);

		return $total_transaksi;
	}

	public function totalSplit1($tahun)
	{
		$total_transaksi=array();
		if (env('DB_CONNECTION') == 'pgsql') {
			$data_penjualan=RawDatum::select(DB::raw('
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 1 THEN split1_2
				ELSE 0
				END
				) AS "Jan",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 2 THEN split1_2
				ELSE 0
				END
				) AS "Feb",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 3 THEN split1_2
				ELSE 0
				END
				) AS "Mar",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 4 THEN split1_2
				ELSE 0
				END
				) AS "Apr",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 5 THEN split1_2
				ELSE 0
				END
				) AS "May",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 6 THEN split1_2
				ELSE 0
				END
				) AS "Jun",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 7 THEN split1_2
				ELSE 0
				END
				) AS "Jul",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 8 THEN split1_2
				ELSE 0
				END
				) AS "Aug",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 9 THEN split1_2
				ELSE 0
				END
				) AS "Sep",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 10 THEN split1_2
				ELSE 0
				END
				) AS "Oct",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 11 THEN split1_2
				ELSE 0
				END
				) AS "Nov",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 12 THEN split1_2
				ELSE 0
				END
				) AS "Dec"
				'))
			->whereYear('tgl_transaksi',date(''.$tahun.''))
      // ->groupby('tgl_transaksi')
			->first();
		}
		else
		{
			$data_penjualan=RawDatum::select(DB::raw('
                SUM(if(MONTH(tgl_transaksi) = 1, split1_2,0)) AS "Jan",
                SUM(if(MONTH(tgl_transaksi) = 2, split1_2,0)) AS "Feb",
                SUM(if(MONTH(tgl_transaksi) = 3, split1_2,0)) AS "Mar",
                SUM(if(MONTH(tgl_transaksi) = 4, split1_2,0)) AS "Apr",
                SUM(if(MONTH(tgl_transaksi) = 5, split1_2,0)) AS "May",
                SUM(if(MONTH(tgl_transaksi) = 6, split1_2,0)) AS "Jun",
                SUM(if(MONTH(tgl_transaksi) = 7, split1_2,0)) AS "Jul",
                SUM(if(MONTH(tgl_transaksi) = 8, split1_2,0)) AS "Aug",
                SUM(if(MONTH(tgl_transaksi) = 9, split1_2,0)) AS "Sep",
                SUM(if(MONTH(tgl_transaksi) = 10, split1_2,0)) AS "Oct",
                SUM(if(MONTH(tgl_transaksi) = 11, split1_2,0)) AS "Nov",
                SUM(if(MONTH(tgl_transaksi) = 12, split1_2,0)) AS "Dec"
                '))
            ->whereYear('tgl_transaksi',date(''.$tahun.''))
            ->first();
		}

		array_push($total_transaksi,$data_penjualan->Jan);
		array_push($total_transaksi,$data_penjualan->Feb);
		array_push($total_transaksi,$data_penjualan->Mar);
		array_push($total_transaksi,$data_penjualan->Apr);
		array_push($total_transaksi,$data_penjualan->May);
		array_push($total_transaksi,$data_penjualan->Jun);
		array_push($total_transaksi,$data_penjualan->Jul);
		array_push($total_transaksi,$data_penjualan->Aug);
		array_push($total_transaksi,$data_penjualan->Sep);
		array_push($total_transaksi,$data_penjualan->Oct);
		array_push($total_transaksi,$data_penjualan->Nov);
		array_push($total_transaksi,$data_penjualan->Dec);

		return $total_transaksi;
	}

	public function totalSplit2($tahun)
	{
		$total_transaksi=array();
		if (env('DB_CONNECTION') == 'pgsql') {
			$data_penjualan=RawDatum::select(DB::raw('
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 1 THEN split2_3
				ELSE 0
				END
				) AS "Jan",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 2 THEN split2_3
				ELSE 0
				END
				) AS "Feb",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 3 THEN split2_3
				ELSE 0
				END
				) AS "Mar",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 4 THEN split2_3
				ELSE 0
				END
				) AS "Apr",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 5 THEN split2_3
				ELSE 0
				END
				) AS "May",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 6 THEN split2_3
				ELSE 0
				END
				) AS "Jun",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 7 THEN split2_3
				ELSE 0
				END
				) AS "Jul",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 8 THEN split2_3
				ELSE 0
				END
				) AS "Aug",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 9 THEN split2_3
				ELSE 0
				END
				) AS "Sep",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 10 THEN split2_3
				ELSE 0
				END
				) AS "Oct",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 11 THEN split2_3
				ELSE 0
				END
				) AS "Nov",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 12 THEN split2_3
				ELSE 0
				END
				) AS "Dec"
				'))
			->whereYear('tgl_transaksi',date(''.$tahun.''))
      // ->groupby('tgl_transaksi')
			->first();
		}
		else
		{
			$data_penjualan=RawDatum::select(DB::raw('
                SUM(if(MONTH(tgl_transaksi) = 1, split2_3,0)) AS "Jan",
                SUM(if(MONTH(tgl_transaksi) = 2, split2_3,0)) AS "Feb",
                SUM(if(MONTH(tgl_transaksi) = 3, split2_3,0)) AS "Mar",
                SUM(if(MONTH(tgl_transaksi) = 4, split2_3,0)) AS "Apr",
                SUM(if(MONTH(tgl_transaksi) = 5, split2_3,0)) AS "May",
                SUM(if(MONTH(tgl_transaksi) = 6, split2_3,0)) AS "Jun",
                SUM(if(MONTH(tgl_transaksi) = 7, split2_3,0)) AS "Jul",
                SUM(if(MONTH(tgl_transaksi) = 8, split2_3,0)) AS "Aug",
                SUM(if(MONTH(tgl_transaksi) = 9, split2_3,0)) AS "Sep",
                SUM(if(MONTH(tgl_transaksi) = 10, split2_3,0)) AS "Oct",
                SUM(if(MONTH(tgl_transaksi) = 11, split2_3,0)) AS "Nov",
                SUM(if(MONTH(tgl_transaksi) = 12, split2_3,0)) AS "Dec"
                '))
            ->whereYear('tgl_transaksi',date(''.$tahun.''))
            ->first();
		}

		array_push($total_transaksi,$data_penjualan->Jan);
		array_push($total_transaksi,$data_penjualan->Feb);
		array_push($total_transaksi,$data_penjualan->Mar);
		array_push($total_transaksi,$data_penjualan->Apr);
		array_push($total_transaksi,$data_penjualan->May);
		array_push($total_transaksi,$data_penjualan->Jun);
		array_push($total_transaksi,$data_penjualan->Jul);
		array_push($total_transaksi,$data_penjualan->Aug);
		array_push($total_transaksi,$data_penjualan->Sep);
		array_push($total_transaksi,$data_penjualan->Oct);
		array_push($total_transaksi,$data_penjualan->Nov);
		array_push($total_transaksi,$data_penjualan->Dec);

		return $total_transaksi;
	}

	public function totalLpa($tahun)
	{
		$total_transaksi=array();
		if (env('DB_CONNECTION') == 'pgsql') {
			$data_penjualan=RawDatum::select(DB::raw('
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 1 THEN lpa
				ELSE 0
				END
				) AS "Jan",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 2 THEN lpa
				ELSE 0
				END
				) AS "Feb",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 3 THEN lpa
				ELSE 0
				END
				) AS "Mar",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 4 THEN lpa
				ELSE 0
				END
				) AS "Apr",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 5 THEN lpa
				ELSE 0
				END
				) AS "May",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 6 THEN lpa
				ELSE 0
				END
				) AS "Jun",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 7 THEN lpa
				ELSE 0
				END
				) AS "Jul",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 8 THEN lpa
				ELSE 0
				END
				) AS "Aug",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 9 THEN lpa
				ELSE 0
				END
				) AS "Sep",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 10 THEN lpa
				ELSE 0
				END
				) AS "Oct",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 11 THEN lpa
				ELSE 0
				END
				) AS "Nov",
				SUM (CASE
				WHEN EXTRACT(MONTH FROM tgl_transaksi) = 12 THEN lpa
				ELSE 0
				END
				) AS "Dec"
				'))
			->whereYear('tgl_transaksi',date(''.$tahun.''))
      // ->groupby('tgl_transaksi')
			->first();
		}
		else
		{
			$data_penjualan=RawDatum::select(DB::raw('
                SUM(if(MONTH(tgl_transaksi) = 1, lpa,0)) AS "Jan",
                SUM(if(MONTH(tgl_transaksi) = 2, lpa,0)) AS "Feb",
                SUM(if(MONTH(tgl_transaksi) = 3, lpa,0)) AS "Mar",
                SUM(if(MONTH(tgl_transaksi) = 4, lpa,0)) AS "Apr",
                SUM(if(MONTH(tgl_transaksi) = 5, lpa,0)) AS "May",
                SUM(if(MONTH(tgl_transaksi) = 6, lpa,0)) AS "Jun",
                SUM(if(MONTH(tgl_transaksi) = 7, lpa,0)) AS "Jul",
                SUM(if(MONTH(tgl_transaksi) = 8, lpa,0)) AS "Aug",
                SUM(if(MONTH(tgl_transaksi) = 9, lpa,0)) AS "Sep",
                SUM(if(MONTH(tgl_transaksi) = 10, lpa,0)) AS "Oct",
                SUM(if(MONTH(tgl_transaksi) = 11, lpa,0)) AS "Nov",
                SUM(if(MONTH(tgl_transaksi) = 12, lpa,0)) AS "Dec"
                '))
            ->whereYear('tgl_transaksi',date(''.$tahun.''))
            ->first();
		}

		array_push($total_transaksi,$data_penjualan->Jan);
		array_push($total_transaksi,$data_penjualan->Feb);
		array_push($total_transaksi,$data_penjualan->Mar);
		array_push($total_transaksi,$data_penjualan->Apr);
		array_push($total_transaksi,$data_penjualan->May);
		array_push($total_transaksi,$data_penjualan->Jun);
		array_push($total_transaksi,$data_penjualan->Jul);
		array_push($total_transaksi,$data_penjualan->Aug);
		array_push($total_transaksi,$data_penjualan->Sep);
		array_push($total_transaksi,$data_penjualan->Oct);
		array_push($total_transaksi,$data_penjualan->Nov);
		array_push($total_transaksi,$data_penjualan->Dec);

		return $total_transaksi;
	}

	public static function month_between_two_dates($start_date, $end_date)
	{
		$p = new DatePeriod(
			new DateTime($start_date), 
			new DateInterval('P1M'), 
			new DateTime($end_date)
		);
		foreach ($p as $w) {
			$minggu[]=$w->format('m/Y');
		}
		return $minggu;
	}
}
