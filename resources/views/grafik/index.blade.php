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
								<canvas class="js-chartjs-lines"></canvas>
								{{-- <div id="grafik"></div> --}}
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
								{{-- <div id="grafik_penjualan"></div> --}}
								<canvas class="js-chartjs-lines1"></canvas>
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

		Chart.defaults.global.defaultFontColor              = '#999';
        Chart.defaults.global.defaultFontStyle              = '600';
        Chart.defaults.scale.gridLines.color                = "rgba(0,0,0,.05)";
        Chart.defaults.scale.gridLines.zeroLineColor        = "rgba(0,0,0,.1)";
        Chart.defaults.scale.ticks.beginAtZero              = false;
        Chart.defaults.global.elements.line.borderWidth     = 2;
        Chart.defaults.global.elements.point.radius         = 4;
        Chart.defaults.global.elements.point.hoverRadius    = 6;
        Chart.defaults.global.tooltips.cornerRadius         = 3;
        Chart.defaults.global.legend.labels.boxWidth        = 15;

        let chartLinesCon  = $('.js-chartjs-lines');
        let chartLinesCon1  = $('.js-chartjs-lines1');

        let chartLines;

        let chartLines1;

        let chartTransaksi;

        let chartTotal;

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
			chartTransaksi = {
				labels: label,
				datasets: [
				{
					label: 'Total Transaksi Tahun '+$('#tahun').val(),
					fill: true,
					backgroundColor: 'rgba(171, 227, 125, .3)',
                    borderColor: 'rgba(171, 227, 125, 1)',
                    pointBackgroundColor: 'rgba(171, 227, 125, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(171, 227, 125, 1)',
					data: total,
					lineTension: 0,
				},
				]
			};

			if (chartLinesCon.length) 
			{
				chartLines = new Chart(chartLinesCon, { type: 'line', 
					data: chartTransaksi,
					options: {
						scales: {
							xAxes: [{
								display: true,
								scaleLabel: {
									display: true,
									labelString: 'Minggu / Tahun',
									fontStyle: 'bold'
								},
								ticks: {
									autoSkip: false,
									maxTicksLimit: 10,
									stepSize: 3
								},
								gridLines: {
									display: false
								}
							}],
							yAxes: [{
								gridLines: {
									display: true
								}
							}]
						}
					}
				});
			}
		}

		function chart_total(label,pasir,abu,gendol,split1,split2,lpa)
		{

			chartTotal = {
				labels: label,
				datasets: [
				{
					label: 'Pasir',
					fill: false,
					backgroundColor: 'rgba(171, 227, 125, .3)',
                    borderColor: 'rgba(171, 227, 125, 1)',
                    pointBackgroundColor: 'rgba(171, 227, 125, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(171, 227, 125, 1)',
					data: pasir,
					lineTension: 0,
				},
				{
					label: 'Abu',
					fill: false,
					backgroundColor: 'rgba(220,220,220,.3)',
                    borderColor: 'rgba(220,220,220,1)',
                    pointBackgroundColor: 'rgba(220,220,220,1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(220,220,220,1)',
					data: abu,
					lineTension: 0,
				},
				{
					label: 'Gendol',
					fill: false,
					backgroundColor: 'rgba(0,128,128,.3)',
                    borderColor: 'rgba(0,128,128,1)',
                    pointBackgroundColor: 'rgba(0,128,128,1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(0,128,128,1)',
					data: gendol,
					lineTension: 0,
				},
				{
					label: 'Split 1/2',
					fill: false,
					backgroundColor: 'rgba(32,178,170,.3)',
                    borderColor: 'rgba(32,178,170,1)',
                    pointBackgroundColor: 'rgba(32,178,170,1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(32,178,170,1)',
					data: split1,
					lineTension: 0,
				},
				{
					label: 'Split 2/3',
					fill: false,
					backgroundColor: 'rgba(139,69,19,.3)',
                    borderColor: 'rgba(139,69,19,1)',
                    pointBackgroundColor: 'rgba(139,69,19,1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(139,69,19,1)',
					data: split2,
					lineTension: 0,
				},
				{
					label: 'LPA',
					fill: false,
					backgroundColor: 'rgba(176,196,222,.3)',
                    borderColor: 'rgba(176,196,222,1)',
                    pointBackgroundColor: 'rgba(176,196,222,1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(176,196,222,1)',
					data: lpa,
					lineTension: 0,
				},
				]
			};

			if (chartLinesCon1.length) 
			{
				chartLines1 = new Chart(chartLinesCon1, { type: 'line', 
					data: chartTotal,
					options: {
						scales: {
							xAxes: [{
								display: true,
								scaleLabel: {
									display: true,
									labelString: 'Minggu / Tahun',
									fontStyle: 'bold'
								},
								ticks: {
									autoSkip: false,
									maxTicksLimit: 10,
									stepSize: 3
								},
								gridLines: {
									display: false
								}
							}],
							yAxes: [{
								gridLines: {
									display: true
								}
							}]
						}
					}
				});
			}

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