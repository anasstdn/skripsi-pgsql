@extends('layouts.app')
@section('content')
<style>
	.hidden {
		width: 0;
		display: none;
	}
</style>
@php
$array = array();
$array['produk'] = $produk;
$array['tanggal_awal'] = $tanggal_awal;
$array['tanggal_akhir'] = $tanggal_akhir;
$array['koefisien_alpha_beta'] = $koefisien_alpha_beta;
if($koefisien_alpha_beta !== 'rumus')
{
	$array['ketetapan_nilai_peramalan'] = $ketetapan_nilai_peramalan;
} 
@endphp

<div class="bg-body-light">
	<div class="content content-full">
		<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
			<h1 class="flex-sm-fill h3 my-2">
				Peramalan Penjualan
			</h1>
		</div>
	</div>
</div>
<div class="content">
	<div class="block">
		<div class="block-header">
		</div>
			<div class="block-content block-content-full">
				<!-- DataTables init on table by adding .js-dataTable-full-pagination class, functionality is initialized in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->
				<h4 class="font-w400">Peramalan Produk {{column_name($produk)}}</h4>
				<p>Bulan Tahun Awal Pencarian : {{ $tanggal_awal }}</p>
				<p>Bulan Tahun Akhir Pencarian : {{ $tanggal_akhir }}</p>
				<p>Koefisien Alpha / Beta : 
					@if(isset($koefisien_alpha_beta) && !empty($koefisien_alpha_beta))
					@if($koefisien_alpha_beta == 'random')
					<span class="badge badge-primary">Random (0,01 sd 0,99)</span>
					@else
					<span class="badge badge-info">Periode Uji (2 / (n+1))</span>
					@endif
					@endif
				</p>
				@if(isset($koefisien_alpha_beta) && !empty($koefisien_alpha_beta))
				@if($koefisien_alpha_beta == 'random')
				<p>Ketetapan Nilai Peramalan : 
					@if($ketetapan_nilai_peramalan == 'mape')
					<span class="badge badge-primary">MAPE</span>
					@else
					<span class="badge badge-success">MAD</span>
					@endif
				</p>
				@endif
				@endif
				<div class="row">
					<div class="col-lg-12">
						<table class="table table-bordered">
							<thead class="thead-dark">
								<tr>
									<th>Bulan Tahun</th>
									<th>Aktual</th>
									<th>ARRSES (beta = {{ $beta_arrses }})</th>
									<th>DES (alpha = {{ $alpha_des }})</th>
								</tr>
							</thead>
							<tbody>
								@if(isset($aktual) && !empty($aktual))
								@foreach($aktual as $key => $val)
								@php
								$bgcolor = '';
								if(($key + 1) == count($aktual))
								{
									$bgcolor = 'success';
								}
								@endphp
								<tr bgcolor="{{ $bgcolor }}">
									<td>{{$periode[$key]}}</td>
									<td>{{$val}}</td>
									<td>{{$peramalan_arrses[$key]}}</td>
									<td>{{$peramalan_des[$key]}}</td>
								</tr>
								@endforeach
								@endif
							</tbody>
							<tfoot style="font-weight: bold">
								<tr>
									<td colspan="2">Jumlah MAPE</td>
									<td>{{$pe_arrses}}</td>
									<td>{{$pe_des}}</td>
								</tr>
								<tr>
									<td colspan="2">Rata-rata MAPE</td>
									<td>{{number_format(($pe_arrses / $length_arrses),2)}} %</td>
									<td>{{number_format(($pe_des / $length_des),2)}} %</td>
								</tr>
								<tr>
									<td colspan="2">Kriteria Nilai MAPE</td>
									<td>
										@if(number_format(($pe_arrses / $length_arrses),2) < 10)
										<span class="badge badge-primary" style="background-color: #006400">SANGAT BAIK</span>
										@elseif(number_format(($pe_arrses / $length_arrses),2) >= 10 && number_format(($pe_arrses / $length_arrses),2) <= 20)
										<span class="badge badge-primary" style="background-color: #00ff00">BAIK</span>
										@elseif(number_format(($pe_arrses / $length_arrses),2) > 20 && number_format(($pe_arrses / $length_arrses),2) <= 50)
										<span class="badge badge-primary" style="background-color: #ffd700;color:black">CUKUP</span>
										@elseif(number_format(($pe_arrses / $length_arrses),2) > 50)
										<span class="badge badge-primary" style="background-color: #8b0000">BURUK</span>
										@endif
									</td>
									<td>
										@if(number_format(($pe_des / $length_des),2) < 10)
										<span class="badge badge-primary" style="background-color: #006400">SANGAT BAIK</span>
										@elseif(number_format(($pe_des / $length_des),2) >= 10 && number_format(($pe_des / $length_des),2) <= 20)
										<span class="badge badge-primary" style="background-color: #00ff00">BAIK</span>
										@elseif(number_format(($pe_des / $length_des),2) > 20 && number_format(($pe_des / $length_des),2) <= 50)
										<span class="badge badge-primary" style="background-color: #ffd700;color:black">CUKUP</span>
										@elseif(number_format(($pe_des / $length_des),2) > 50)
										<span class="badge badge-primary" style="background-color: #8b0000">BURUK</span>
										@endif
									</td>
								</tr>
								<tr>
									<td colspan="2">Jumlah MAD</td>
									<td>{{$mad_arrses}}</td>
									<td>{{$mad_des}}</td>
								</tr>
								<tr>
									<td colspan="2">Rata - rata MAD</td>
									<td>{{number_format(($mad_arrses / $length_arrses),3)}}</td>
									<td>{{number_format(($mad_des / $length_arrses),3)}}</td>
								</tr>
								<tr>
									<td colspan="2">Detail</td>
									<td>
										{{-- <a href="{{url('peramalan/detail-arrses/'.$produk.'/'.$tanggal_awal.'/'.$tanggal_akhir)}}" class="btn btn-info">Detail ARRSES</a> --}}
										<a href="{{url('peramalan-bulanan/detail-arrses/'.serialize($array))}}" class="btn btn-info">Detail ARRSES</a>
									</td>
									<td>
										{{-- <a href="{{url('peramalan/detail-des/'.$produk.'/'.$tanggal_awal.'/'.$tanggal_akhir)}}" class="btn btn-success">Detail DES</a> --}}
										<a href="{{url('peramalan-bulanan/detail-des/'.serialize($array))}}" class="btn btn-success">Detail DES</a>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
				<br/>
				<h4 class="font-w400">Grafik Peramalan Produk {{column_name($produk)}}</h4>
				<div class="row">
					<div class="col-lg-12">
						<div id="grafik_penjualan"></div>
					</div>
				</div>
				<div class="row" style="margin-bottom: 0.2em">
					<div class="col-md-12 text-right">
						<a href="{{url('/peramalan')}}" class="btn btn-success">Kembali ke Cari</a>
					</div>
				</div>
			</div>
	</div>
</div>
<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="modal-block-popin" aria-hidden="true">
</div>
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
                curve: 'smooth',
            },
            series: [
            {
                name: 'Aktual',
                data: <?=json_encode($aktual)?>,
            },
            {
                name: 'ARRSES',
                data: <?=json_encode($peramalan_arrses)?>,
            },
             {
                name: 'DES',
                data: <?=json_encode($peramalan_des)?>,
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
                categories: <?=json_encode($periode)?>,
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
    });
</script>
@endpush