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

class PeramalanBulananController extends Controller
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
		$this->menuAccess(\Auth::user(),'Peramalan Bulanan');
		return view('peramalan_bulanan.index');
	}

	public function cari(Request $request)
	{
		$all_data = $request->all();
		$tanggal_awal		=	$all_data['month_year_start'];
		$tanggal_akhir		=	$all_data['month_year_end'];
		$produk 			=	$all_data['produk'];

		$data['produk'] = $produk;
		$data['tanggal_awal'] = $tanggal_awal;
		$data['tanggal_akhir'] = $tanggal_akhir;
		$data['koefisien_alpha_beta'] = $all_data['koefisien_alpha_beta'];
		if($all_data['koefisien_alpha_beta'] !== 'rumus')
		{
			$data['ketetapan_nilai_peramalan'] = $all_data['ketetapan_nilai_peramalan'];
		} 

		return redirect(url('peramalan-bulanan/search/'.serialize($data)));
	}

	public function search($array)
	{
		$array = unserialize($array);

		$periode 			=	array();
		$aktual		 		=	array();
		$peramalan_arrses 	=	array();
		$peramalan_des 		=	array();

		$mad_arrses			=	0;
		$pe_arrses 			=	0;

		$mad_des			=	0;
		$pe_des 			=	0;


		$arrses 			=	$this->forecastingArrses($array);
		$des                =   $this->forecastingDes($array);

		 if((isset($arrses) && !empty($arrses)) && (isset($des) && !empty($des)))
        {
            $length_arrses  =   count($arrses) - 1;
            $length_des     =   count($des) - 1;

            foreach($arrses as $key => $val)
            {
                array_push($periode,$val['periode']);
                array_push($aktual,$val['aktual']);
                array_push($peramalan_arrses,$val['peramalan']);

                $mad_arrses +=  $val['MAD'];
                $pe_arrses  +=  $val['percentage_error'];
            }

            foreach ($des as $key => $val) {
                array_push($peramalan_des,$val['peramalan']);

                $mad_des    +=  $val['MAD'];
                $pe_des     +=  $val['PE'];
            }

            $data = array(
                'arrses' => $arrses,
                'des' => $des,
                'periode' => $periode,
                'aktual' => $aktual,
                'peramalan_arrses' => $peramalan_arrses,
                'peramalan_des' => $peramalan_des,
                'length_arrses' => $length_arrses,
                'length_des' => $length_des,
                'mad_arrses' => $mad_arrses,
                'pe_arrses' => $pe_arrses,
                'mad_des' => $mad_des,
                'pe_des' => $pe_des,
                'tanggal_awal' => date('m-Y',strtotime('01'.'-'.$array['tanggal_awal'])),
                'tanggal_akhir' => date('m-Y',strtotime('01'.'-'.$array['tanggal_akhir'])),
                'produk' => $array['produk'],
                'status' => true,
                'beta_arrses' => $arrses[0]['beta'],
                'alpha_des' => $des[0]['alpha'],
                'koefisien_alpha_beta' => $array['koefisien_alpha_beta'],
                'ketetapan_nilai_peramalan' => isset($array['ketetapan_nilai_peramalan'])?$array['ketetapan_nilai_peramalan']:null,
            );

        	return view('peramalan_bulanan.hasil',$data);
        }
        else
        {
            message(false,'','Data tidak ditemukan');
            return back();
        }
	}

	public function forecastingArrses($array)
    {
        $date_from  =   $array['tanggal_awal'];
        $date_to    =   $array['tanggal_akhir'];

        $month_start = explode('-',$date_from)[0];
        $year_start = explode('-',$date_from)[1];

        $month_end = explode('-',$date_to)[0];
        $year_end = explode('-',$date_to)[1]; 

        $tanggal_awal = date('Y-m-d',strtotime('01-'.$date_from));
        $tanggal_akhir = date('Y-m-d',strtotime(total_days($month_end,$year_end).'-'.$date_to));

        $data_penjualan     =   RawDatum::select(DB::raw('DATE_FORMAT(tgl_transaksi, "%Y-%m") as bulan,IF(sum(pasir) IS NULL,0,sum(pasir)) as pasir,IF(sum(gendol) IS NULL, 0, sum(gendol)) as gendol,IF(sum(abu) IS NULL,0,sum(abu)) as abu, IF(sum(split2_3) IS NULL,0,sum(split2_3)) as split2_3, IF(sum(split1_2) IS NULL, 0, sum(split1_2)) as split1_2, IF(sum(lpa) IS NULL,0,sum(lpa)) as lpa'))
        ->where('tgl_transaksi','>=',$tanggal_awal)
        ->where('tgl_transaksi','<=',$tanggal_akhir)
        // ->groupby('tgl_transaksi')
        ->groupBy(DB::raw('DATE_FORMAT(tgl_transaksi, "%Y-%m")'))
        ->orderby('tgl_transaksi','ASC')
        ->get();

        if(isset($data_penjualan) && !$data_penjualan->isEmpty()){
            // $minggu=$this->week_between_two_dates($date_from,$date_to);
            $minggu = array();
            $total = array();
            $subtotal = array();
            
            foreach($data_penjualan as $key => $val)
            {
                switch($array['produk'])
                {
                    case 'abu':
                    $total[] = array(
                    	'bulan' => $val->bulan,
                    	'total_transaksi' => $val->abu
                    );
                    break;
                    case 'gendol':
                    $total[] = array(
                    	'bulan' => $val->bulan,
                    	'total_transaksi' => $val->gendol
                    );
                    break;
                    case 'pasir':
                    $total[] = array(
                    	'bulan' => $val->bulan,
                    	'total_transaksi' => $val->pasir
                    );
                    break;
                    case 'split2_3':
                    $total[] = array(
                    	'bulan' => $val->bulan,
                    	'total_transaksi' => $val->split2_3
                    );
                    break;
                    case 'split1_2':
                    $total[] = array(
                    	'bulan' => $val->bulan,
                    	'total_transaksi' => $val->split1_2
                    );
                    break;
                    case 'lpa':
                    $total[] = array(
                    	'bulan' => $val->bulan,
                    	'total_transaksi' => $val->lpa
                    );
                    break;
                }
            }

            $total = totalData($total,$year_start.'-'.$month_start,$year_end.'-'.$month_end);

            $arr = array(
                'data_penjualan' => $data_penjualan,
                'bulan' => getMonths($year_start.'-'.$month_start,$year_end.'-'.$month_end),
                'total' => $total,
                'date_to' => $date_to,
            );

            $result=$this->arrses(array_merge($arr,$array));
        }
        else
        {
            $result=array();
        }
        return $result;
    }

    private function arrses($array)
    {
        $periode=$array['bulan'];
        $X=$array['total'];
        $F = array();
        $e = array();
        $E = array();
        $AE = array();
        $alpha = array();

        if($array['koefisien_alpha_beta'] == 'random')
        {
            $beta = [0.01,0.02,0.03,0.04,0.05,0.06,0.07,0.08,0.09,0.10,0.11,0.12,0.13,0.14,0.15,0.16,0.17,0.18,0.19,0.20, 0.21,0.22,0.23,0.24,0.25,0.26,0.27,0.28,0.29,0.30,0.31,0.32,0.33,0.34,0.35,0.36,0.37,0.38,0.39,0.40,0.41,0.42,0.43,0.44,0.45,0.46,0.47,0.48,0.49,0.50,0.51,0.52,0.53,0.54,0.55,0.56,0.57,0.58,0.59,0.60,0.61,0.62,0.63,0.64,0.65,0.66,0.67,0.68,0.69,0.70,0.71,0.72,0.73,0.74,0.75,0.76,0.77,0.78,0.79,0.80,0.81,0.82,0.83,0.84,0.85,0.86,0.87,0.88,0.89,0.90,0.91,0.92,0.93,0.94,0.95,0.96,0.97,0.98,0.99];

            $count = count($beta);
        }
        else
        {
            $beta[0] = 2 / (count($periode) + 1);
            $count = 1;
        }
        

        // $beta=[0.1,0.2,0.3,0.4,0.5,0.6,0.7,0.8,0.9];
        $PE = array();
        $MAPE = array();
        $MAD=array();

        for($i = 0; $i < $count; $i++) 
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
            $SUMMAD[$i] = array_sum($MAD[$i])/(count($periode));
        }
        // dd($MAD);
        if($array['koefisien_alpha_beta'] == 'random')
        {
            if($array['ketetapan_nilai_peramalan'] == 'mape')
            {
                $bestBetaIndex = array_search(min($MAPE), $MAPE); 
            }
            else
            {
                $bestBetaIndex = array_search(min($SUMMAD), $SUMMAD); 
            }
        }
        else
        {
           $bestBetaIndex = array_search(min($MAPE), $MAPE); 
        }

        $hasil = array();
        for ($i = 0; $i <= count($periode); $i++) {
            if ($i < count($periode)) {
                $hasil[$i] = [
                    'periode'                   => date('m-Y',strtotime($periode[$i].'-'.'01')),
                    'aktual'                    => $X[$i],
                    'peramalan'                 => $F[$bestBetaIndex][$i],
                    'galat'                     => $e[$bestBetaIndex][$i],
                    'galat_pemulusan'           => $E[$bestBetaIndex][$i],
                    'galat_pemulusan_absolut'   => $AE[$bestBetaIndex][$i],
                    'alpha'                     => $alpha[$bestBetaIndex][$i],
                    'percentage_error'          => $PE[$bestBetaIndex][$i],
                    'MAD'                       => $MAD[$bestBetaIndex][$i],
                    'beta'                      => $beta[$bestBetaIndex],

                ];
            } else {
                // $nextPeriode = date('W', strtotime(date($date_to)));
                $nextPeriode = Carbon::parse('01'.'-'.$array['date_to'])->addMonths(1)->format('m-Y');
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
                    'beta'                      => $beta[$bestBetaIndex],
                ];
            }
        }

        return $hasil;
    }

    public function forecastingDes($array)
    {
        $date_from  =   $array['tanggal_awal'];
        $date_to    =   $array['tanggal_akhir'];

        $month_start = explode('-',$date_from)[0];
        $year_start = explode('-',$date_from)[1];

        $month_end = explode('-',$date_to)[0];
        $year_end = explode('-',$date_to)[1];  

        $tanggal_awal = date('Y-m-d',strtotime('01-'.$date_from));
        $tanggal_akhir = date('Y-m-d',strtotime(total_days($month_end,$year_end).'-'.$date_to)); 

        $data_penjualan     =   RawDatum::select(DB::raw('DATE_FORMAT(tgl_transaksi, "%Y-%m") as bulan,IF(sum(pasir) IS NULL,0,sum(pasir)) as pasir,IF(sum(gendol) IS NULL, 0, sum(gendol)) as gendol,IF(sum(abu) IS NULL,0,sum(abu)) as abu, IF(sum(split2_3) IS NULL,0,sum(split2_3)) as split2_3, IF(sum(split1_2) IS NULL, 0, sum(split1_2)) as split1_2, IF(sum(lpa) IS NULL,0,sum(lpa)) as lpa'))
        ->where('tgl_transaksi','>=',$tanggal_awal)
        ->where('tgl_transaksi','<=',$tanggal_akhir)
        // ->groupby('tgl_transaksi')
            ->groupBy(DB::raw('DATE_FORMAT(tgl_transaksi, "%Y-%m")'))
            ->orderby('tgl_transaksi','ASC')
            ->get();

            if(isset($data_penjualan) && !$data_penjualan->isEmpty()){
            // $minggu=$this->week_between_two_dates($date_from,$date_to);
        // dd($minggu);
            $minggu = array();
            $total = array();
            $subtotal = 0;
            
            foreach($data_penjualan as $key => $val)
            {
                switch($array['produk'])
                {
                    case 'abu':
                    $total[] = array(
                    	'bulan' => $val->bulan,
                    	'total_transaksi' => $val->abu
                    );
                    break;
                    case 'gendol':
                    $total[] = array(
                    	'bulan' => $val->bulan,
                    	'total_transaksi' => $val->gendol
                    );
                    break;
                    case 'pasir':
                    $total[] = array(
                    	'bulan' => $val->bulan,
                    	'total_transaksi' => $val->pasir
                    );
                    break;
                    case 'split2_3':
                    $total[] = array(
                    	'bulan' => $val->bulan,
                    	'total_transaksi' => $val->split2_3
                    );
                    break;
                    case 'split1_2':
                    $total[] = array(
                    	'bulan' => $val->bulan,
                    	'total_transaksi' => $val->split1_2
                    );
                    break;
                    case 'lpa':
                    $total[] = array(
                    	'bulan' => $val->bulan,
                    	'total_transaksi' => $val->lpa
                    );
                    break;
                }
            }

            $total = totalData($total,$year_start.'-'.$month_start,$year_end.'-'.$month_end);

            $arr = array(
                'data_penjualan' => $data_penjualan,
                'bulan' => getMonths($year_start.'-'.$month_start,$year_end.'-'.$month_end),
                'total' => $total,
                'date_to' => $date_to,
            );

        // $periode=$this->getPeriode($date_from,$date_to);
            // $total=$this->getTotal($minggu,$data_penjualan,$produk   =   $nama_produk);
        // dd($total);
            $result=$this->des1(array_merge($arr,$array));
        }
        else
        {
            $result=array();
        }
        // dd($result);
        return $result;
    }

    private function des1($array)
    {
        $periode=$array['bulan'];
        $X=$array['total'];
        $F = array();
        $s1 = array();
        $s2 = array();
        $at = array();
        $bt = array();

        if($array['koefisien_alpha_beta'] == 'random')
        {
            $alpha=[0.01,0.02,0.03,0.04,0.05,0.06,0.07,0.08,0.09,0.10,0.11,0.12,0.13,0.14,0.15,0.16,0.17,0.18,0.19,0.20, 0.21,0.22,0.23,0.24,0.25,0.26,0.27,0.28,0.29,0.30,0.31,0.32,0.33,0.34,0.35,0.36,0.37,0.38,0.39,0.40,0.41,0.42,0.43,0.44,0.45,0.46,0.47,0.48,0.49,0.50,0.51,0.52,0.53,0.54,0.55,0.56,0.57,0.58,0.59,0.60,0.61,0.62,0.63,0.64,0.65,0.66,0.67,0.68,0.69,0.70,0.71,0.72,0.73,0.74,0.75,0.76,0.77,0.78,0.79,0.80,0.81,0.82,0.83,0.84,0.85,0.86,0.87,0.88,0.89,0.90,0.91,0.92,0.93,0.94,0.95,0.96,0.97,0.98,0.99];

            $count = count($alpha);
        }
        else
        {
            $alpha[0] = 2 / (count($periode) + 1);
            $count = 1;
        }

        // $alpha=[0.01];
        $PE = array();
        $MAPE = array();
        $MAD=array();

        for($i=0;$i<$count;$i++)
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
                    $F[$i][$j+1]=$at[$i][$j]+$bt[$i][$j];

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
            $SUMMAD[$i] = array_sum($MAD[$i])/(count($periode));
        }


        if($array['koefisien_alpha_beta'] == 'random')
        {
            if($array['ketetapan_nilai_peramalan'] == 'mape')
            {
                $bestAlphaIndex = array_search(min($MAPE), $MAPE); 
            }
            else
            {
                $bestAlphaIndex = array_search(min($SUMMAD), $SUMMAD); 
            }
        }
        else
        {
           $bestAlphaIndex = array_search(min($MAPE), $MAPE); 
        }


        $hasil = array();
        for ($i = 0; $i <= count($periode); $i++) {
            if($i<count($periode))
            {
                $hasil[$i] = [
                    'periode'                   => date('m-Y',strtotime($periode[$i].'-'.'01')),
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
                $nextPeriode = Carbon::parse('01'.'-'.$array['date_to'])->addMonths(1)->format('m-Y');
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

    public function detailArrses1(Request $request, $array)
	{
		$input = unserialize($array);
		
		$tanggal_awal		=	$input['tanggal_awal'];
		$tanggal_akhir		=	$input['tanggal_akhir'];
		$produk 			=	$input['produk'];
		$koefisien_alpha_beta = $input['koefisien_alpha_beta'];
        $ketetapan_nilai_peramalan = isset($input['ketetapan_nilai_peramalan'])?$input['ketetapan_nilai_peramalan']:null;

		$periode 			=	array();
		$aktual		 		=	array();
		$peramalan_arrses 	=	array();
		$peramalan_des 		=	array();

		$mad_arrses			=	0;
		$pe_arrses 			=	0;

		$mad_des			=	0;
		$pe_des 			=	0;


		$arrses 			=	$this->forecastingArrses($input);

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

			$beta_arrses = $arrses[0]['beta'];

			return view('peramalan_bulanan.detail-arrses',compact('arrses','periode','aktual','peramalan_arrses','length_arrses','mad_arrses','pe_arrses','tanggal_awal','tanggal_akhir','produk','beta_arrses','koefisien_alpha_beta','ketetapan_nilai_peramalan'));
		}
		else
		{
			message(false,'','Tidak ditemukan transaksi penjualan antara periode '.$tanggal_awal.' sampai '.$tanggal_akhir);

			return redirect('/peramalan');
		}
	}

		public function detailDes1(Request $request, $array)
	{
		$input = unserialize($array);
		
		$tanggal_awal		=	$input['tanggal_awal'];
		$tanggal_akhir		=	$input['tanggal_akhir'];
		$produk 			=	$input['produk'];
		$koefisien_alpha_beta = $input['koefisien_alpha_beta'];
        $ketetapan_nilai_peramalan = isset($input['ketetapan_nilai_peramalan'])?$input['ketetapan_nilai_peramalan']:null;

		$periode 			=	array();
		$aktual		 		=	array();
		$peramalan_arrses 	=	array();
		$peramalan_des 		=	array();

		$mad_arrses			=	0;
		$pe_arrses 			=	0;

		$mad_des			=	0;
		$pe_des 			=	0;


		$des 				=	$this->forecastingDes($input);

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

			$alpha_des = $des[0]['alpha'];

			return view('peramalan_bulanan.detail-des',compact('des','periode','aktual','peramalan_des','length_des','mad_des','pe_des','tanggal_awal','tanggal_akhir','produk','koefisien_alpha_beta','ketetapan_nilai_peramalan','alpha_des'));
		}
		else
		{
			message(false,'','Tidak ditemukan transaksi penjualan antara periode '.$tanggal_awal.' sampai '.$tanggal_akhir);

			return redirect('/peramalan');
		}
	}
}
