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


		$total_pasir=$this->totalPasir($all_data['tahun']);

		$total_abu=$this->totalAbu($all_data['tahun']);

		$total_gendol=$this->totalGendol($all_data['tahun']);

		$total_split_1=$this->totalSplit1($all_data['tahun']);

		$total_split_2=$this->totalSplit2($all_data['tahun']);

		$total_lpa=$this->totalLpa($all_data['tahun']);

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
