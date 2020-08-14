@extends('layouts.app')
@section('content')
<style>
	.hidden {
		width: 0;
		display: none;
	}
</style>

<div class="bg-body-light">
	<div class="content content-full">
		<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
			<h1 class="flex-sm-fill h3 my-2">
				Grafik Transaksi
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
			<h4 class="font-w400">Filter Pencarian</h4>
			<div class="form-group row">
				<div class="col-sm-2" ><b>Tahun</b></div>
				<div class="col-md-4" style="text-align:left;">
					<select class="form-control" id="tahun">
						<?php $start=2017?>
						<?php for($start;$start<=date('Y');$start++)
						{?>
							@if($start==date('Y'))
							<option value="{{$start}}" selected="">{{$start}}</option>
							@else
							<option value="{{$start}}">{{$start}}</option>
							@endif
							<?php }?>                
						</select>
					</div>
				</div>
				<br/>
				<div class="row">
					<div class="col-lg-12 col-xl-12">
						<div class="card">
							<div class="card-header text-uppercase">GRAFIK TRANSAKSI PRODUK TAHUNAN</div>
							<div class="card-body">
								<div id="grafik"></div>
							</div>
						</div>
					</div>
				</div><!--End Row-->
				<hr/>
				<div class="row">
					<div class="col-lg-12 col-xl-12">
						<div class="card">
							<div class="card-header text-uppercase">GRAFIK PIE TRANSAKSI PRODUK</div>
							<div class="card-body">
								<div id="chart"></div>
							</div>
						</div>
					</div>
				</div>
				<hr/>
				<div class="row">
					<div class="col-lg-12 col-xl-12">
						<div class="card">
							<div class="card-header text-uppercase">GRAFIK TOTAL PENJUALAN PRODUK TAHUNAN (SATUAN METER KUBIK)</div>
							<div class="card-body">
								<div id="grafik_penjualan"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endsection

	@push('js')
	<script type="text/javascript">
		$(document).ready(function(){
			reload();
			$('#tahun').on('change',function(){
				reload();
			})
		});

		function reload()
		{
			$('.ajax-loader').fadeIn();
			$("#status").html("Loading...Please Wait!");
			$('#grafik').empty();
			$('#grafik_penjualan').empty();
			$('#chart').empty();
			$.ajax({
				url: '{{url('grafik/get-chart')}}',
				type: 'GET',
				data: {tahun:$('#tahun').val()},
				success:function(data){      
					chart_penjualan(data.bulan,data.total_transaksi);
					chart_total(data.bulan,data.total_pasir,data.total_abu,data.total_gendol,data.total_split_1,data.total_split_2,data.total_lpa);
					chart_pie(data.label_pie,data.graph_pie);
					$('.ajax-loader').fadeOut();
				},
				error:function (xhr, status, error){
					alert(xhr.responseText);
				},
			});
		}

		function chart_penjualan(label,total)
		{
			var options = {
				chart: {
					height: 350,
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
						opacity: 1
					},
				},
				stroke: {
					width: 4,   
					curve: 'straight',
				},
				series: [
				{
					name: 'Total Transaksi',
					data: total,
				},
				],

				tooltip: {
					enabled: true,
					theme: 'dark',
				},
				markers:{
					size:3
				},

				xaxis: {
					labels: {
						format: 'dd/MM',
					},
					categories: label,
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
				colors: ['#2E93fA', '#66DA26', '#546E7A', '#E91E63', '#FF9800'],
				grid:{
					show: true,
					borderColor: 'rgba(66, 59, 116, 0.15)',
				},
			};

			var chart = new ApexCharts(
				document.querySelector("#grafik"),
				options
				);

			chart.render();
		}

		function chart_total(label,pasir,abu,gendol,split1,split2,lpa)
		{
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
					name: 'Pasir',
					data: pasir,
				},
				{
					name: 'Abu',
					data: abu,
				},
				{
					name: 'Gendol',
					data: gendol,
				},
				{
					name: 'Split 1/2',
					data: split1,
				},
				{
					name: 'Split 2/3',
					data: split2,
				},
				{
					name: 'LPA',
					data: lpa,
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
					categories: label,
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
				colors: ['#2E93fA', '#66DA26', '#546E7A', '#E91E63', '#FF9800'],
				grid:{
					show: true,
					borderColor: 'rgba(66, 59, 116, 0.15)',
				},
				yaxis: {
					max: 3000,                
				}
			};

			var chart1 = new ApexCharts(
				document.querySelector("#grafik_penjualan"),
				options
				);

			chart1.render();
		}

		function chart_pie(label,data)
		{
			var options = {
				series: data,
				chart: {
					width: '50%',
					type: 'pie',
				},
				labels: label,
				theme: {
					monochrome: {
						enabled: false,
					}
				},
				plotOptions: {
					pie: {
						dataLabels: {
							offset: -5,
						}
					}
				},

				dataLabels: {
					formatter(val, opts) {
						const name = opts.w.globals.labels[opts.seriesIndex];
						return [name, val.toFixed(1) + '%'];
					}
				},
				legend: {
					show: true,
				}
			};

			var chart2 = new ApexCharts(document.querySelector("#chart"), options);
			chart2.render();

		}

	</script>
	@endpush