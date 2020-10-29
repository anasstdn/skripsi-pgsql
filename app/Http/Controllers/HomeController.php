<?php

namespace App\Http\Controllers;

use App\Models\RawDatum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use DatePeriod;
use DateTime;
use DateInterval;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $total_transaksi            =   RawDatum::select(\DB::raw('count(id) as total'))->first();

        $total_transaksi_tahun_ini  =   RawDatum::select(\DB::raw('count(id) as total'))
        ->whereYear('tgl_transaksi',date('Y'))
        ->first();

        $total_transaksi_tahun_lalu =   RawDatum::select(\DB::raw('count(id) as total'))
        ->whereYear('tgl_transaksi',date('Y')-1)
        ->first();

        $total_transaksi_bulan_ini  =   RawDatum::select(\DB::raw('count(id) as total'))
        ->whereMonth('tgl_transaksi',date('m'))
        ->whereYear('tgl_transaksi',date('Y'))
        ->first();

        Carbon::setWeekStartsAt(Carbon::MONDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);

        $total_transaksi_minggu_ini =   RawDatum::select(\DB::raw('count(id) as total'))
        ->whereBetween('tgl_transaksi', [Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')])
        ->first();

        $total_transaksi_hari_ini   =   RawDatum::select(\DB::raw('count(id) as total'))
        ->whereDate('tgl_transaksi',date('Y-m-d'))
        ->first();

        $date_from                  =   date('Y-01-01');
        $date_to                    =   date('Y-12-31');

        $penjualan_tahun_ini        =   $this->penjualan_setahun(date('Y'));
        $penjualan_tahun_lalu       =   $this->penjualan_setahun(date('Y')-1);
        $penjualan_all_time         =   $this->penjualan_all_time();

        $bulan=$this->month_between_two_dates($date_from,$date_to);

        return view('home',compact('total_transaksi','total_transaksi_bulan_ini','total_transaksi_minggu_ini','total_transaksi_hari_ini','bulan','penjualan_tahun_ini','penjualan_tahun_lalu','total_transaksi_tahun_ini','total_transaksi_tahun_lalu','penjualan_all_time'));
    }

    public static function month_between_two_dates($start_date, $end_date)
    {
        $p = new DatePeriod(
            new DateTime($start_date), 
            new DateInterval('P1M'), 
            new DateTime($end_date)
        );
        foreach ($p as $w) {
            $minggu[]=$w->format('M');
        }
        return $minggu;
    }

    public function penjualan_setahun($tahun)
    {
        $total_transaksi=array();

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
            ->whereYear('tgl_transaksi',$tahun)
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
            ->whereYear('tgl_transaksi',$tahun)
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

    public function penjualan_all_time()
    {
        $data = array();
        $bulan_tahun = array();
        $total = array();

        if (env('DB_CONNECTION') == 'pgsql') {
            $data_penjualan=RawDatum::select(DB::raw("to_char(tgl_transaksi, 'YYYY/MM') AS bulan_tahun, count(id) AS total"))
            ->groupBy(\DB::raw("bulan_tahun"))
            ->orderBy('bulan_tahun','ASC')
            ->get();
        }
        else
        {
            $data_penjualan=RawDatum::select(DB::raw("date_format(tgl_transaksi, '%Y/%m') AS bulan_tahun, count(id) AS total"))
            ->groupBy(\DB::raw("bulan_tahun"))
            ->orderBy('bulan_tahun','ASC')
            ->get();
        }

        foreach($data_penjualan as $key => $val)
        {
            array_push($bulan_tahun,$val->bulan_tahun);
            array_push($total,$val->total);
        }

        $data['bulan_tahun'] = $bulan_tahun;
        $data['total'] = $total;

        return $data;
    }
}
