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
use Carbon\Carbon;

class PeramalanController extends Controller
{
    //
    use ActivityTraits;

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware('permission:read-peramalan');
	}

	public function index()
	{
		$this->menuAccess(\Auth::user(),'Peramalan');
		return view('peramalan.index');
	}

	public function cari(Request $request)
	{
		$all_data 			=	$request->all();

		$tanggal_awal		=	$all_data['tanggal_awal'];
		$tanggal_akhir		=	$all_data['tanggal_akhir'];
		$produk 			=	$all_data['produk'];

		return redirect(url('peramalan/search/'.$produk.'/'.$tanggal_awal.'/'.$tanggal_akhir));
	}

	public function search($produk,$tanggal_awal,$tanggal_akhir)
	{
		$periode 			=	array();
		$aktual		 		=	array();
		$peramalan_arrses 	=	array();
		$peramalan_des 		=	array();

		$mad_arrses			=	0;
		$pe_arrses 			=	0;

		$mad_des			=	0;
		$pe_des 			=	0;


		$arrses 			=	$this->forecastingArrses($tanggal_awal,$tanggal_akhir,$produk);
		$des 				= 	$this->forecastingDes($tanggal_awal,$tanggal_akhir,$produk);

		if(isset($arrses) && isset($des))
		{
			$length_arrses	=	count($arrses) - 1;
			$length_des		=	count($des)	- 1;

			foreach($arrses as $key	=> $val)
			{
				array_push($periode,$val['periode']);
				array_push($aktual,$val['aktual']);
				array_push($peramalan_arrses,$val['peramalan']);

				$mad_arrses	+=	$val['MAD'];
				$pe_arrses 	+=	$val['percentage_error'];
			}

			foreach ($des as $key => $val) {
				array_push($peramalan_des,$val['peramalan']);

				$mad_des	+=	$val['MAD'];
				$pe_des 	+=	$val['PE'];
			}

			return view('peramalan.hasil',compact('arrses','des','periode','aktual','peramalan_arrses','peramalan_des','length_arrses','length_des','mad_arrses','pe_arrses','mad_des','pe_des','tanggal_awal','tanggal_akhir','produk'));
		}
		else
		{
			message(false,'','Tidak ditemukan transaksi penjualan antara periode '.$tanggal_awal.' sampai '.$tanggal_akhir);

			return redirect('/peramalan');
		}
	}

	public function forecastingArrses($tanggal_awal,$tanggal_akhir,$nama_produk)
	{
        // dd($request->all());
		$date_from 	=	date('Y-m-d',strtotime($tanggal_awal));
		$date_to 	=	date('Y-m-d',strtotime($tanggal_akhir));  

		$data_penjualan 	=	RawDatum::select(DB::raw('extract("week" from tgl_transaksi) as minggu,sum(pasir) as pasir,sum(gendol) as gendol,sum(abu) as abu, sum(split2_3) as split2_3, sum(split1_2) as split1_2, sum(lpa) as lpa'))
		->where('tgl_transaksi','>=',$date_from)
		->where('tgl_transaksi','<=',$date_to)
    	// ->groupby('tgl_transaksi')
		->groupBy(DB::raw('extract("week" from tgl_transaksi),extract("year" from tgl_transaksi)'))
		->get();

		if(isset($data_penjualan) && !$data_penjualan->isEmpty()){
			$minggu=$this->week_between_two_dates($date_from,$date_to);
        // dd($minggu);

    	// $periode=$this->getPeriode($date_from,$date_to);
			$total=$this->getTotal($minggu,$data_penjualan,$produk 	=	$nama_produk);
        // dd($total);
			$result=$this->arrses($data_penjualan,$minggu,$total,$date_to);
		}
		else
		{
			$result=array();
		}
		return $result;
	}

	private function arrses($data_penjualan,$periode,$total,$date_to)
	{
		$periode=$periode;
		$X=$total;
		$F = array();
		$e = array();
		$E = array();
		$AE = array();
		$alpha = array();
		// $beta = [0.01,0.02,0.03,0.04,0.05,0.06,0.07,0.08,0.09,0.10,0.11,0.12,0.13,0.14,0.15,0.16,0.17,0.18,0.19,0.20, 0.21,0.22,0.23,0.24,0.25,0.26,0.27,0.28,0.29,0.30,0.31,0.32,0.33,0.34,0.35,0.36,0.37,0.38,0.39,0.40,0.41,0.42,0.43,0.44,0.45,0.46,0.47,0.48,0.49,0.50,0.51,0.52,0.53,0.54,0.55,0.56,0.57,0.58,0.59,0.60,0.61,0.62,0.63,0.64,0.65,0.66,0.67,0.68,0.69,0.70,0.71,0.72,0.73,0.74,0.75,0.76,0.77,0.78,0.79,0.80,0.81,0.82,0.83,0.84,0.85,0.86,0.87,0.88,0.89,0.90,0.91,0.92,0.93,0.94,0.95,0.96,0.97,0.98,0.99];

        $beta=[0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9];
		$PE = array();
		$MAPE = array();
		$MAD=array();

		for($i = 0; $i < count($beta); $i++) 
		{
			$F[$i][0] = $e[$i][0] = $E[$i][0] = $AE[$i][0] = $alpha[$i][0] = $PE[$i][0] =$MAD[$i][0]= 0;
			$F[$i][1] = $X[0];
			$alpha[$i][1] = $beta[$i];

			for($j = 1; $j < count($periode); $j++){
                // perhitungan peramalan untuk periode berikutnya
				$F[$i][$j + 1] = ($alpha[$i][$j] * $X[$j]) + ((1 - $alpha[$i][$j]) * $F[$i][$j]);

                // menghitung selisih antara nilai aktual dengan hasil peramalan
				$e[$i][$j] = $X[$j] - $F[$i][$j]; 

                // menghitung nilai kesalahan peramalan yang dihaluskan
				$E[$i][$j] = ($beta[$i] * $e[$i][$j]) + ((1 - $beta[$i]) * $E[$i][$j - 1]);

                // menghitung nilai kesalahan absolut peramalan yang dihaluskan
				$AE[$i][$j] = ($beta[$i] * abs($e[$i][$j])) + ((1 - $beta[$i]) * $AE[$i][$j - 1]);

                // menghitung nilai alpha untuk periode berikutnya
				$alpha[$i][$j + 1] = $E[$i][$j] == 0 ? $beta[$i] : abs($E[$i][$j] / $AE[$i][$j]);

                // menghitung nilai kesalahan persentase peramalan
				$PE[$i][$j] = $X[$j] == 0 ? 0 : abs((($X[$j] - $F[$i][$j]) / $X[$j]) * 100);
				
				$MAD[$i][$j] = $X[$j] == 0 ? 0 : abs(($X[$j] - $F[$i][$j]));
			}

            // menghitung rata-rata kesalahan peramalan
            // $MAPE[$i] = array_sum($PE[$i])/(count($periode) - 1);
			$MAPE[$i] = array_sum($PE[$i])/(count($periode));
		}
        // dd($MAD);
		$bestBetaIndex = array_search(min($MAPE), $MAPE);

		$hasil = array();
		for ($i = 0; $i <= count($periode); $i++) {
			if ($i < count($periode)) {
				$hasil[$i] = [
					'periode'                   => $periode[$i],
					'aktual'                    => $X[$i],
					'peramalan'                 => $F[$bestBetaIndex][$i],
					'galat'                     => $e[$bestBetaIndex][$i],
					'galat_pemulusan'           => $E[$bestBetaIndex][$i],
					'galat_pemulusan_absolut'   => $AE[$bestBetaIndex][$i],
					'alpha'                     => $alpha[$bestBetaIndex][$i],
					'percentage_error'          => $PE[$bestBetaIndex][$i],
					'MAD'                       => $MAD[$bestBetaIndex][$i],

				];
			} else {
        		// $nextPeriode = date('W', strtotime(date($date_to)));
				$nextPeriode = Carbon::parse($date_to)->addWeeks(1)->format('W/Y');
				$hasil[$i] = [
					'periode'                   => $nextPeriode,
					'aktual'                    => 0,
					'peramalan'                 => $F[$bestBetaIndex][$i],
					'galat'                     => 0,
					'galat_pemulusan'           => 0,
					'galat_pemulusan_absolut'   => 0,
					'alpha'                     => $alpha[$bestBetaIndex][$i],
					'percentage_error'          => 0,
					'MAD'                       => 0,
				];
			}
		}
		return $hasil;
	}

	public function forecastingDes($tanggal_awal,$tanggal_akhir,$nama_produk)
	{

		$date_from 		=	date('Y-m-d',strtotime($tanggal_awal));
		$date_to 		=	date('Y-m-d',strtotime($tanggal_akhir));  
		
		$data_penjualan =	RawDatum::select(DB::raw('extract("week" from tgl_transaksi) as minggu,sum(pasir) as pasir,sum(gendol) as gendol,sum(abu) as abu, sum(split2_3) as split2_3, sum(split1_2) as split1_2, sum(lpa) as lpa'))
		->where('tgl_transaksi','>=',$date_from)
		->where('tgl_transaksi','<=',$date_to)
        // ->groupby('tgl_transaksi')
		->groupBy(DB::raw('extract("week" from tgl_transaksi),extract("year" from tgl_transaksi)'))
		->get();
        // dd($data_penjualan);

		if(isset($data_penjualan) && !$data_penjualan->isEmpty()){
			$minggu=$this->week_between_two_dates($date_from,$date_to);
        // dd($minggu);

        // $periode=$this->getPeriode($date_from,$date_to);
			$total=$this->getTotal($minggu,$data_penjualan,$produk 	=	$nama_produk);
        // dd($total);
			$result=$this->des1($data_penjualan,$minggu,$total,$date_to);
		}
		else
		{
			$result=array();
		}
        // dd($result);
		return $result;
	}

	private function des1($data_penjualan,$periode,$total,$date_to)
	{
		$periode=$periode;
		$X=$total;
		$F = array();
		$s1 = array();
		$s2 = array();
		$at = array();
		$bt = array();
		$alpha=[0.01,0.02,0.03,0.04,0.05,0.06,0.07,0.08,0.09,0.10,0.11,0.12,0.13,0.14,0.15,0.16,0.17,0.18,0.19,0.20, 0.21,0.22,0.23,0.24,0.25,0.26,0.27,0.28,0.29,0.30,0.31,0.32,0.33,0.34,0.35,0.36,0.37,0.38,0.39,0.40,0.41,0.42,0.43,0.44,0.45,0.46,0.47,0.48,0.49,0.50,0.51,0.52,0.53,0.54,0.55,0.56,0.57,0.58,0.59,0.60,0.61,0.62,0.63,0.64,0.65,0.66,0.67,0.68,0.69,0.70,0.71,0.72,0.73,0.74,0.75,0.76,0.77,0.78,0.79,0.80,0.81,0.82,0.83,0.84,0.85,0.86,0.87,0.88,0.89,0.90,0.91,0.92,0.93,0.94,0.95,0.96,0.97,0.98,0.99];
        // $alpha=[0.01];
		$PE = array();
		$MAPE = array();
		$MAD=array();

		for($i=0;$i<count($alpha);$i++)
		{
			$F[$i][0]=$bt[$i][0]=$MAD[$i][0]=$PE[$i][0]=0;
			$s1[$i][0]=$s2[$i][0]=$X[0];
			$at[$i][0]=(2*$s1[$i][0])-$s2[$i][0];

			for($j=0;$j<count($periode);$j++)
			{
                // $s1[$i][$j+1]=($alpha[$i] * $X[$j+1]) + ((1-$alpha[$i]) * $s1[$i][$j]);
				if($j!==count($periode)-1)
				{                   
					$s1[$i][$j+1]=($alpha[$i] * $X[$j+1]) + ((1-$alpha[$i]) * $s1[$i][$j]);
					$s2[$i][$j+1]=($alpha[$i] * $s1[$i][$j+1]) + ((1-$alpha[$i]) * $s2[$i][$j]);

					$at[$i][$j+1]=(2*$s1[$i][$j+1])-$s2[$i][$j+1];
					$bt[$i][$j+1]=($alpha[$i]/(1-$alpha[$i]))*($s1[$i][$j+1]-$s2[$i][$j+1]);
					$F[$i][$j+1]=$at[$i][$j+1]+$bt[$i][$j+1];

					$MAD[$i][$j+1]=$X[$j+1]==0?0:abs($X[$j+1]-$F[$i][$j+1]);
					$PE[$i][$j+1]=$X[$j+1] == 0 ? 0 : abs((($X[$j+1] - $F[$i][$j+1]) / $X[$j+1]) * 100);
				}
				else
				{
					$s1[$i][$j+1]=0;
					$s2[$i][$j+1]=0;
					$at[$i][$j+1]=0;
					$bt[$i][$j+1]=0;
					$F[$i][$j+1]=$at[$i][$j]+($bt[$i][$j] * 1);
					$MAD[$i][$j+1]=0;
					$PE[$i][$j+1]=0;
				}
			}
			$MAPE[$i] = array_sum($PE[$i])/(count($periode)+1);
		}

		$bestAlphaIndex = array_search(min($MAPE), $MAPE);

		$hasil = array();
		for ($i = 0; $i <= count($periode); $i++) {
			if($i<count($periode))
			{
				$hasil[$i] = [
					'periode'                   => $periode[$i],
					'aktual'                    => $X[$i],
					'peramalan'                 => $F[$bestAlphaIndex][$i],
					'alpha'                     => $alpha[$bestAlphaIndex],
					's1'                        => $s1[$bestAlphaIndex][$i],
					's2'                        => $s2[$bestAlphaIndex][$i],
					'at'                        => $at[$bestAlphaIndex][$i],
					'bt'                        => $bt[$bestAlphaIndex][$i],
					'MAD'                       => $MAD[$bestAlphaIndex][$i],
					'PE'                        => $PE[$bestAlphaIndex][$i],
				];
			}
			else
			{
				$nextPeriode = Carbon::parse($date_to)->addWeeks(1)->format('W/Y');
				$hasil[$i] = [
					'periode'                   => $nextPeriode,
					'aktual'                    => 0,
					'peramalan'                 => $F[$bestAlphaIndex][$i],
					'alpha'                     => $alpha[$bestAlphaIndex],
					's1'                        => $s1[$bestAlphaIndex][$i],
					's2'                        => $s2[$bestAlphaIndex][$i],
					'at'                        => $at[$bestAlphaIndex][$i],
					'bt'                        => $bt[$bestAlphaIndex][$i],
					'MAD'                       => $MAD[$bestAlphaIndex][$i],
					'PE'                        => $PE[$bestAlphaIndex][$i],
				];
			}
		}
		return $hasil;
	}

	private function des($data_penjualan,$periode,$total,$date_to)
	{
        // dd($total);
		$no = 0;
		$data = array();
		$jumlah = 0;
		$perediksiData = array();
		$prediksi=array();
		$PE=array();
		$MAD=array();
		$raw=array();

		$m = 0;
		$n = 0;

		foreach($periode as $i => $minggu)
		{
			$data=array(
				'minggu'=>$i+1,
				'total'=>$total[$i],
			);
			array_push($raw,$data);
		}

		$a = 0.5;
		$xt = $raw[0]['total'];
		$s1lalu = 0;
		$s2lalu = 0;
		$priode = 0;

		$alpha=[0.01,0.02,0.03,0.04,0.05,0.06,0.07,0.08,0.09,0.10,0.11,0.12,0.13,0.14,0.15,0.16,0.17,0.18,0.19,0.20, 0.21,0.22,0.23,0.24,0.25,0.26,0.27,0.28,0.29,0.30,0.31,0.32,0.33,0.34,0.35,0.36,0.37,0.38,0.39,0.40,0.41,0.42,0.43,0.44,0.45,0.46,0.47,0.48,0.49,0.50,0.51,0.52,0.53,0.54,0.55,0.56,0.57,0.58,0.59,0.60,0.61,0.62,0.63,0.64,0.65,0.66,0.67,0.68,0.69,0.70,0.71,0.72,0.73,0.74,0.75,0.76,0.77,0.78,0.79,0.80,0.81,0.82,0.83,0.84,0.85,0.86,0.87,0.88,0.89,0.90,0.91,0.92,0.93,0.94,0.95,0.96,0.97,0.98,0.99];

		
        // foreach($raw as $i => $val)
		for ($alp=0;$alp<count($alpha);$alp++)
		{
			for($i=0;$i<=count($raw);$i++)
			{
				if($i==0)
				{
					$s1=$raw[$i]['total'];
					$s2=$raw[$i]['total'];
				}
				else
				{
					if($i<count($raw))
					{
						$s1 = ($alpha[$alp] * $raw[$i]['total']) + ((1-$alpha[$alp]) * $s1lalu);
						$s2 = ($alpha[$alp] * $s1) + ((1-$alpha[$alp]) * $s2lalu);
					}      
				}

				$nilaiA = (2 * $s1) - $s2;
				$nilaiB = ($alpha[$alp] / (1-$alpha[$alp])) * ($s1-$s2);

				$prediksi[$alp][$i+1] = $nilaiA + $nilaiB;


				if($i==0)
				{
					$PE[$alp][$i] =0;
					$MAD[$alp][$i] = 0;
				}
				else if($i!==0 && $i<count($raw))
				{
					$PE[$alp][$i] = $raw[$i]['total'] == 0 ? 0 : abs((($raw[$i]['total'] - $prediksi[$alp][$i]) / $raw[$i]['total']) * 100);

					$MAD[$alp][$i] = $raw[$i]['total'] == 0 ? 0 : abs(($raw[$i]['total'] - $prediksi[$alp][$i]));
				}
				else
				{
               // $nextPeriode = date('W', strtotime(date($date_to)));
					$date = new DateTime($date_to);
               // $nextPeriode = Carbon::parse($date_to)->addWeeks(1)->format('W');
					$PE[$alp][$i] =0;
					$MAD[$alp][$i] = 0;
				}
         // array_push($perediksiData,$data);
				if (!empty($total[$i])) {
					$xt = $total[$i];
					$s1lalu = $s1;
					$s2lalu = $s2;
				}
			}

    // $MAPE[$alp] = array_sum($PE[$alp])/(count($raw) - 1);
			$MAPE[$alp] = array_sum($PE[$alp])/(count($raw));
		}
    // dd($MAPE);

		$bestAlphaIndex = array_search(min($MAPE), $MAPE);

		for($i=0;$i<=count($raw);$i++)
		{
			if($i==0)
			{
				$perediksiData[$i]=array(
					'minggu'=>$periode[$i],
					'aktual'=>$raw[$i]['total'],
					'prediksi'=>0,
					's1'=>$s1,
					's2'=>$s2,
					's1lalu'=>$s1lalu,
					's2lalu'=>$s2lalu,
					'nilaiA'=>$nilaiA,
					'nilaiB'=>0,
					'error'=>$PE[$bestAlphaIndex][$i],
					'alpha'=>$alpha[$bestAlphaIndex],
					'MAD'=>$MAD[$bestAlphaIndex][$i],
				);
			}
			else if($i!==0 && $i<count($raw))
			{
				$perediksiData[$i]=array(
					'minggu'=>$periode[$i],
					'aktual'=>$raw[$i]['total'],
					'prediksi'=>$prediksi[$bestAlphaIndex][$i],
					's1'=>$s1,
					's2'=>$s2,
					's1lalu'=>$s1lalu,
					's2lalu'=>$s2lalu,
					'nilaiA'=>$nilaiA,
					'nilaiB'=>$nilaiB,
					'error'=>$PE[$bestAlphaIndex][$i],
					'alpha'=>$alpha[$bestAlphaIndex],
					'MAD'=>$MAD[$bestAlphaIndex][$i],
				);
			}
			else
			{
               // $nextPeriode = date('W', strtotime(date($date_to)));
				$date = new DateTime($date_to);
               // $nextPeriode = Carbon::parse($date_to)->addWeeks(1)->format('W');
				$perediksiData[$i]=array(
					'minggu'=>$date->modify('+7 day')->format('W'),
					'aktual'=>0,
					'prediksi'=>$prediksi[$bestAlphaIndex][$i],
					's1'=>0,
					's2'=>0,
					's1lalu'=>0,
					's2lalu'=>0,
					'nilaiA'=>0,
					'nilaiB'=>0,
					'error'=>$PE[$bestAlphaIndex][$i],
					'alpha'=>$alpha[$bestAlphaIndex],
					'MAD'=>$MAD[$bestAlphaIndex][$i],
				); 
			}
		}
		
		return $perediksiData;
	}



	public static function week_between_two_dates($start_date, $end_date)
	{
		$p = new DatePeriod(
			new DateTime($start_date), 
			new DateInterval('P7D'), 
			new DateTime($end_date)
		);
		foreach ($p as $w) {
			$minggu[]=$w->format('W/Y');
		}
		return $minggu;
	}

	public static function getTotal($periode,$data,$produk)
	{
		$array = array();
		for($i=0; $i<count($periode); $i++) {
			for($j=0; $j<count($data); $j++) {
            	// dd($data[$j]['minggu']+1);
				if(explode('/',$periode[$i])[0] == ($data[$j]['minggu'])){
					switch($produk)
					{
						case 'abu':
						$array[$i] = floatval($data[$j]['abu']);
						break;
						case 'gendol':
						$array[$i] = floatval($data[$j]['gendol']);
						break;
						case 'pasir':
						$array[$i] = floatval($data[$j]['pasir']);
						break;
						case 'split2_3':
						$array[$i] = floatval($data[$j]['split2_3']);
						break;
						case 'split1_2':
						$array[$i] = floatval($data[$j]['split1_2']);
						break;
						case 'lpa':
						$array[$i] = floatval($data[$j]['lpa']);
						break;
					}
					
					break;
				}else{
					$array[$i] = 0;
				}
			}
		}
		return $array;
	}

	public static function getPeriode($date_from,$date_to)
	{
		$array= array();
		$date_from = $date_from;
		$i = 0;

		for($a=date('Y-m-d', strtotime($date_from));$a<=date('Y-m-d', strtotime($date_to));$a++)
		{
			$array[$i] = $date_from;
			$date_from = date('Y-m-d', strtotime("+1 day", strtotime(date($date_from))));
			$i++;
		}

        // while(date('Y-m-d', strtotime($date_from)) <= date('Y-m-d', strtotime($date_to))) {
        //     $array[$i] = $date_from;
        //     $month = date('Y-m-d', strtotime("+1 day", strtotime(date($date_from))));
        //     $i++;
        // }

		return $array;
	}

	public function detailArrses(Request $request,$produk,$date_from,$date_to)
	{
		$date_from=date('Y-m-d',strtotime($date_from));
		$date_to=date('Y-m-d',strtotime($date_to));

		$data_penjualan=RawDatum::select(DB::raw('extract("week" from tgl_transaksi) as minggu,sum(pasir) as pasir,sum(gendol) as gendol,sum(abu) as abu, sum(split2_3) as split2_3, sum(split1_2) as split1_2, sum(lpa) as lpa'))
		->where('tgl_transaksi','>=',$date_from)
		->where('tgl_transaksi','<=',$date_to)
    	// ->groupby('tgl_transaksi')
		->groupBy(DB::raw('extract("week" from tgl_transaksi),extract("year" from tgl_transaksi)'))
		->get();

		$minggu=$this->week_between_two_dates($date_from,$date_to);
        // dd($minggu);

        // $periode=$this->getPeriode($date_from,$date_to);
		$total=$this->getTotal($minggu,$data_penjualan,$produk);

		$periode=$minggu;
		$X=$total;
		$F = array();
		$e = array();
		$E = array();
		$AE = array();
		$alpha = array();
		// $beta = [0.01,0.02,0.03,0.04,0.05,0.06,0.07,0.08,0.09,0.10,0.11,0.12,0.13,0.14,0.15,0.16,0.17,0.18,0.19,0.20, 0.21,0.22,0.23,0.24,0.25,0.26,0.27,0.28,0.29,0.30,0.31,0.32,0.33,0.34,0.35,0.36,0.37,0.38,0.39,0.40,0.41,0.42,0.43,0.44,0.45,0.46,0.47,0.48,0.49,0.50,0.51,0.52,0.53,0.54,0.55,0.56,0.57,0.58,0.59,0.60,0.61,0.62,0.63,0.64,0.65,0.66,0.67,0.68,0.69,0.70,0.71,0.72,0.73,0.74,0.75,0.76,0.77,0.78,0.79,0.80,0.81,0.82,0.83,0.84,0.85,0.86,0.87,0.88,0.89,0.90,0.91,0.92,0.93,0.94,0.95,0.96,0.97,0.98,0.99];

        $beta=[0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9];
		$PE = array();
		$MAPE = array();
		$MAD=array();

		for($i = 0; $i < count($beta); $i++) 
		{
			$F[$i][0] = $e[$i][0] = $E[$i][0] = $AE[$i][0] = $alpha[$i][0] = $PE[$i][0] =$MAD[$i][0]= 0;
			$F[$i][1] = $X[0];
			$alpha[$i][1] = $beta[$i];

			for($j = 1; $j < count($periode); $j++){
                // perhitungan peramalan untuk periode berikutnya
				$F[$i][$j + 1] = ($alpha[$i][$j] * $X[$j]) + ((1 - $alpha[$i][$j]) * $F[$i][$j]);

                // menghitung selisih antara nilai aktual dengan hasil peramalan
				$e[$i][$j] = $X[$j] - $F[$i][$j]; 

                // menghitung nilai kesalahan peramalan yang dihaluskan
				$E[$i][$j] = ($beta[$i] * $e[$i][$j]) + ((1 - $beta[$i]) * $E[$i][$j - 1]);

                // menghitung nilai kesalahan absolut peramalan yang dihaluskan
				$AE[$i][$j] = ($beta[$i] * abs($e[$i][$j])) + ((1 - $beta[$i]) * $AE[$i][$j - 1]);

                // menghitung nilai alpha untuk periode berikutnya
				$alpha[$i][$j + 1] = $E[$i][$j] == 0 ? $beta[$i] : abs($E[$i][$j] / $AE[$i][$j]);

                // menghitung nilai kesalahan persentase peramalan
				$PE[$i][$j] = $X[$j] == 0 ? 0 : abs((($X[$j] - $F[$i][$j]) / $X[$j]) * 100);
				
				$MAD[$i][$j] = $X[$j] == 0 ? 0 : abs(($X[$j] - $F[$i][$j]));
			}

            // menghitung rata-rata kesalahan peramalan
            // $MAPE[$i] = array_sum($PE[$i])/(count($periode) - 1);
			$MAPE[$i] = array_sum($PE[$i])/(count($periode));
			$MADTotal[$i]=array_sum($MAD[$i])/(count($periode));

		}
		$bestBetaIndex = array_search(min($MAPE), $MAPE);
        // dd($bestBetaIndex);

		return view('peramalan.mape-arrses',compact('MAPE','bestBetaIndex','beta','MADTotal','produk','date_from','date_to'));
	}

	public function detailArrses1(Request $request, $produk, $date_from, $date_to)
	{
		$tanggal_awal		=	$date_from;
		$tanggal_akhir		=	$date_to;
		$produk 			=	$produk;

		$periode 			=	array();
		$aktual		 		=	array();
		$peramalan_arrses 	=	array();
		$peramalan_des 		=	array();

		$mad_arrses			=	0;
		$pe_arrses 			=	0;

		$mad_des			=	0;
		$pe_des 			=	0;


		$arrses 			=	$this->forecastingArrses($tanggal_awal,$tanggal_akhir,$produk);

		if(isset($arrses))
		{
			$length_arrses	=	count($arrses) - 1;

			foreach($arrses as $key	=> $val)
			{
				array_push($periode,$val['periode']);
				array_push($aktual,$val['aktual']);
				array_push($peramalan_arrses,$val['peramalan']);

				$mad_arrses	+=	$val['MAD'];
				$pe_arrses 	+=	$val['percentage_error'];
			}

			return view('peramalan.detail-arrses',compact('arrses','periode','aktual','peramalan_arrses','length_arrses','mad_arrses','pe_arrses','tanggal_awal','tanggal_akhir','produk'));
		}
		else
		{
			message(false,'','Tidak ditemukan transaksi penjualan antara periode '.$tanggal_awal.' sampai '.$tanggal_akhir);

			return redirect('/peramalan');
		}
	}

	public function detailDes1(Request $request, $produk, $date_from, $date_to)
	{
		$tanggal_awal		=	$date_from;
		$tanggal_akhir		=	$date_to;
		$produk 			=	$produk;

		$periode 			=	array();
		$aktual		 		=	array();
		$peramalan_arrses 	=	array();
		$peramalan_des 		=	array();

		$mad_arrses			=	0;
		$pe_arrses 			=	0;

		$mad_des			=	0;
		$pe_des 			=	0;


		$des 				=	$this->forecastingDes($tanggal_awal,$tanggal_akhir,$produk);

		if(isset($des))
		{
			$length_des		=	count($des) - 1;

			foreach($des as $key	=> $val)
			{
				array_push($periode,$val['periode']);
				array_push($aktual,$val['aktual']);
				array_push($peramalan_des,$val['peramalan']);

				$mad_des	+=	$val['MAD'];
				$pe_des 	+=	$val['PE'];
			}

			return view('peramalan.detail-des',compact('des','periode','aktual','peramalan_des','length_des','mad_des','pe_des','tanggal_awal','tanggal_akhir','produk'));
		}
		else
		{
			message(false,'','Tidak ditemukan transaksi penjualan antara periode '.$tanggal_awal.' sampai '.$tanggal_akhir);

			return redirect('/peramalan');
		}
	}

	public function detailDes(Request $request, $produk, $date_from, $date_to)
	{
		$date_from=date('Y-m-d',strtotime($date_from));
		$date_to=date('Y-m-d',strtotime($date_to));

		$data_penjualan=RawDatum::select(DB::raw('extract("week" from tgl_transaksi) as minggu,sum(pasir) as pasir,sum(gendol) as gendol,sum(abu) as abu, sum(split2_3) as split2_3, sum(split1_2) as split1_2, sum(lpa) as lpa'))
		->where('tgl_transaksi','>=',$date_from)
		->where('tgl_transaksi','<=',$date_to)
    	// ->groupby('tgl_transaksi')
		->groupBy(DB::raw('extract("week" from tgl_transaksi),extract("year" from tgl_transaksi)'))
		->get();

		$minggu=$this->week_between_two_dates($date_from,$date_to);
        // dd($minggu);

        // $periode=$this->getPeriode($date_from,$date_to);
		$total=$this->getTotal($minggu,$data_penjualan,$produk);

		$periode=$minggu;
		$X=$total;
		$F = array();
		$s1 = array();
		$s2 = array();
		$at = array();
		$bt = array();
		$alpha=[0.01,0.02,0.03,0.04,0.05,0.06,0.07,0.08,0.09,0.10,0.11,0.12,0.13,0.14,0.15,0.16,0.17,0.18,0.19,0.20, 0.21,0.22,0.23,0.24,0.25,0.26,0.27,0.28,0.29,0.30,0.31,0.32,0.33,0.34,0.35,0.36,0.37,0.38,0.39,0.40,0.41,0.42,0.43,0.44,0.45,0.46,0.47,0.48,0.49,0.50,0.51,0.52,0.53,0.54,0.55,0.56,0.57,0.58,0.59,0.60,0.61,0.62,0.63,0.64,0.65,0.66,0.67,0.68,0.69,0.70,0.71,0.72,0.73,0.74,0.75,0.76,0.77,0.78,0.79,0.80,0.81,0.82,0.83,0.84,0.85,0.86,0.87,0.88,0.89,0.90,0.91,0.92,0.93,0.94,0.95,0.96,0.97,0.98,0.99];
        // $alpha=[0.01];
		$PE = array();
		$MAPE = array();
		$MAD=array();

		for($i=0;$i<count($alpha);$i++)
		{
			$F[$i][0]=$bt[$i][0]=$MAD[$i][0]=$PE[$i][0]=0;
			$s1[$i][0]=$s2[$i][0]=$X[0];
			$at[$i][0]=(2*$s1[$i][0])-$s2[$i][0];

			for($j=0;$j<count($periode);$j++)
			{
                // $s1[$i][$j+1]=($alpha[$i] * $X[$j+1]) + ((1-$alpha[$i]) * $s1[$i][$j]);
				if($j!==count($periode)-1)
				{                   
					$s1[$i][$j+1]=($alpha[$i] * $X[$j+1]) + ((1-$alpha[$i]) * $s1[$i][$j]);
					$s2[$i][$j+1]=($alpha[$i] * $s1[$i][$j+1]) + ((1-$alpha[$i]) * $s2[$i][$j]);

					$at[$i][$j+1]=(2*$s1[$i][$j+1])-$s2[$i][$j+1];
					$bt[$i][$j+1]=($alpha[$i]/(1-$alpha[$i]))*($s1[$i][$j+1]-$s2[$i][$j+1]);
					$F[$i][$j+1]=$at[$i][$j+1]+$bt[$i][$j+1];

					$MAD[$i][$j+1]=$X[$j+1]==0?0:abs($X[$j+1]-$F[$i][$j+1]);
					$PE[$i][$j+1]=$X[$j+1] == 0 ? 0 : abs((($X[$j+1] - $F[$i][$j+1]) / $X[$j+1]) * 100);
				}
				else
				{
					$s1[$i][$j+1]=0;
					$s2[$i][$j+1]=0;
					$at[$i][$j+1]=0;
					$bt[$i][$j+1]=0;
					$F[$i][$j+1]=$at[$i][$j]+($bt[$i][$j] * 1);
					$MAD[$i][$j+1]=0;
					$PE[$i][$j+1]=0;
				}
			}
		
			$MAPE[$i] = array_sum($PE[$i])/(count($periode));
			$MADTotal[$i]=array_sum($MAD[$i])/(count($periode));
		}

		$bestAlphaIndex = array_search(min($MAPE), $MAPE);
        // dd($bestAlphaIndex);
		return view('peramalan.mape-des',compact('MAPE','bestAlphaIndex','alpha','MADTotal','produk','date_from','date_to'));
	}
}
