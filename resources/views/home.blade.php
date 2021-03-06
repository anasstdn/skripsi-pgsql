@extends('layouts.app')

@section('content')
<style>
    .apexcharts-xaxis-label:nth-last-child(2) {
  transform: translateX(-20px)
}
</style>

<div class="bg-image overflow-hidden" style="background-image: url('{{asset('oneui/')}}/src/assets/media/photos/photo3@2x.jpg');">
    <div class="bg-primary-dark-op">
        <div class="content content-narrow content-full">
            <div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center mt-5 mb-2 text-center text-sm-left">
                <div class="flex-sm-fill">
                    <h1 class="font-w600 text-white mb-0 invisible" data-toggle="appear">Beranda</h1>
                    <h2 class="h4 font-w400 text-white-75 mb-0 invisible" data-toggle="appear" data-timeout="250">Selamat Datang {{\Auth::user()->name}}</h2>
                </div>
                <div class="flex-sm-00-auto mt-3 mt-sm-0 ml-sm-3">
                    <span class="d-inline-block invisible" data-toggle="appear" data-timeout="350">
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END Hero -->

<div class="content content-narrow">
  <div class="row">
    <div class="col-6 col-md-3 col-lg-6 col-xl-3">
        <a class="block block-rounded block-link-pop border-left border-primary border-4x" href="javascript:void(0)">
            <div class="block-content block-content-full">
                <div class="font-size-sm font-w600 text-uppercase text-muted">Total Transaksi</div>
                <div class="font-size-h2 font-w400 text-dark">{{$total_transaksi->total}}</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3 col-lg-6 col-xl-3">
        <a class="block block-rounded block-link-pop border-left border-primary border-4x" href="javascript:void(0)">
            <div class="block-content block-content-full">
                <div class="font-size-sm font-w600 text-uppercase text-muted">Transaksi Hari Ini</div>
                <div class="font-size-h2 font-w400 text-dark">{{$total_transaksi_hari_ini->total}}</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3 col-lg-6 col-xl-3">
        <a class="block block-rounded block-link-pop border-left border-primary border-4x" href="javascript:void(0)">
            <div class="block-content block-content-full">
                <div class="font-size-sm font-w600 text-uppercase text-muted">Transaksi Minggu Ini</div>
                <div class="font-size-h2 font-w400 text-dark">{{$total_transaksi_minggu_ini->total}}</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3 col-lg-6 col-xl-3">
        <a class="block block-rounded block-link-pop border-left border-primary border-4x" href="javascript:void(0)">
            <div class="block-content block-content-full">
                <div class="font-size-sm font-w600 text-uppercase text-muted">Transaksi Bulan Ini</div>
                <div class="font-size-h2 font-w400 text-dark">{{$total_transaksi_bulan_ini->total}}</div>
            </div>
        </a>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="block block-rounded block-mode-loading-oneui">
           <div class="block-header">
            <h3 class="block-title">Grafik Transaksi</h3>
        </div>
        <div class="block-content p-0 bg-body-light text-center">
            <div id="grafik_penjualan"></div>
        </div>
        <div class="block-content">
            <div class="row items-push text-center py-3">
                <div class="col-6 col-xl-4">
                    <i class="fa fa-wallet fa-2x text-muted"></i>
                    <div class="text-muted mt-3">{{$total_transaksi_tahun_lalu->total}} Transaksi di tahun {{date('Y')-1}}</div>
                </div>
                <div class="col-6 col-xl-4">
                    <i class="fa fa-wallet fa-2x text-muted"></i>
                    <div class="text-muted mt-3">{{$total_transaksi_tahun_ini->total}} Transaksi di tahun {{date('Y')}}</div>
                </div>
                @php
                $tahun_ini  =   $total_transaksi_tahun_ini->total;
                $tahun_lalu =   $total_transaksi_tahun_lalu->total;

                $total      =   $tahun_ini - $tahun_lalu;

                if($total < 0)
                {
                    $arrow      =   'down';
                    $percentage =   (abs($total) / $tahun_lalu) * 100;
                    $sign       =   '-';
                }
                else
                {
                    $arrow      =   'up';
                    $percentage =   $tahun_lalu > 0?($total / $tahun_lalu) * 100:0;
                    $sign       =   '+';
                }

                @endphp
                <div class="col-6 col-xl-4">
                    <i class="fa fa-angle-double-{{$arrow}} fa-2x text-muted"></i>
                    <div class="text-muted mt-3">{{$sign}}{{number_format($percentage,2)}}% dari tahun sebelumnya</div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="block block-rounded block-mode-loading-oneui">
         <div class="block-header">
            <h3 class="block-title">Grafik Transaksi All Time</h3>
        </div>
        <div class="block-content p-0 bg-body-light text-center">
            <div id="grafik_penjualan1"></div>
        </div>
    </div>
</div>
</div>
</div>
<!-- END Page Content -->
@endsection

@push('js')
<script>
    $(document).ready(function(){
        var options = {
            chart: {
                height: 500,
                type: 'area',
                stacked: false,
                zoom: {
                    enabled: false,
                },
                foreColor: '#4e4e4e',
                toolbar: {
                    show: false,
                },
                shadow: {
                    enabled: false,
                    color: '#000',
                    top: 3,
                    left: 2,
                    blur: 3,
                    opacity: 1,
                },
            },
            stroke: {
                width: 4,   
                curve: 'straight',
            },
            series: [
            {
                name: 'Tahun Ini',
                data: <?=json_encode($penjualan_tahun_ini)?>,
            },
            {
                name: 'Tahun Lalu',
                data: <?=json_encode($penjualan_tahun_lalu)?>,
            },
            ],

            tooltip: {
                enabled: true,
                theme: 'dark',
            },
            markers:{
                size:3,
            },

            xaxis: {
                labels: {
                    format: 'dd/MM',
                },
                categories: <?=json_encode($bulan)?>,
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    inverseColors: false,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [20, 100, 100, 100]
                },
            },
            grid:{
                show: true,
                borderColor: 'rgba(66, 59, 116, 0.15)',
            },
            yaxis: {

            }
        };

        var chart1 = new ApexCharts(
            document.querySelector("#grafik_penjualan"),
            options
            );

        chart1.render();

        var options1 = {
            chart: {
                height: 500,
                type: 'area',
                stacked: false,
                zoom: {
                    enabled: false,
                },
                foreColor: '#4e4e4e',
                toolbar: {
                    show: false,
                },
                shadow: {
                    enabled: false,
                    color: '#000',
                    top: 3,
                    left: 2,
                    blur: 3,
                    opacity: 1,
                },
            },
            stroke: {
                width: 4,   
                curve: 'straight',
            },
            series: [
            {
                name: 'All Time',
                data: <?=json_encode($penjualan_all_time['total'])?>,
            },
            ],

            tooltip: {
                enabled: true,
                theme: 'dark',
            },
            markers:{
                size:3,
            },

            xaxis: {
                labels: {
                    format: 'YYYY/MM',
                },
                categories: <?=json_encode($penjualan_all_time['bulan_tahun'])?>,
                tickPlacement: 'on'
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    inverseColors: false,
                    opacityFrom: 0.45,
                    opacityTo: 0.05,
                    stops: [20, 100, 100, 100]
                },
            },
            grid:{
                show: true,
                borderColor: 'rgba(66, 59, 116, 0.15)',
            },
            yaxis: {

            }
        };

        var chart2 = new ApexCharts(
            document.querySelector("#grafik_penjualan1"),
            options1
            );

        chart2.render();
    });
</script>
@endpush
