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
			<h4 class="font-w400">Detail ARRSES</h4>
			<div class="row col-lg-6" style="margin-bottom:1em;font-family: sans-serif;font-weight: bold">
				<div class="col-lg-7">
					<span>Total Array Uji Alpha</span>
				</div>
				<div class="col-lg-1">
					:
				</div>
				<div class="col-lg-4" style="text-align:left">
					<span>{{count($alpha)}}</span>
				</div>
			</div>
			<div class="row col-lg-6" style="margin-bottom:1em;font-family: sans-serif;font-weight: bold">
				<div class="col-lg-7">
					<span>Nilai Beta / MAPE Terkecil</span>
				</div>
				<div class="col-lg-1">
					:
				</div>
				<div class="col-lg-4" style="text-align:left">
					<span>{{$alpha[$bestAlphaIndex]}} &nbsp / &nbsp{{round($MAPE[$bestAlphaIndex],4)}} %</span>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead class="thead-dark">
								<tr>
									<th>No</th>
									<th>Nilai Alpha</th>
									<th>MAD (Mean Absolute Deviation)</th>
									<th>MAPE (Mean Absolute Percentage Error)</th>
								</tr>
							</thead>
							<tbody>
								@if(isset($alpha) && !empty($alpha))
								@foreach($alpha as $key=>$val)
								@if($key==$bestAlphaIndex)
								<tr style="background-color: yellow">
									<td>{{$key+1}}</td>
									<td>{{$val}}</td>
									<td>{{$MADTotal[$key]}}</td>
									<td>{{$MAPE[$key]}} %</td>
								</tr>
								@else
								<tr>
									<td>{{$key+1}}</td>
									<td>{{$val}}</td>
									<td>{{$MADTotal[$key]}}</td>
									<td>{{$MAPE[$key]}} %</td>
								</tr>
								@endif
								
								@endforeach
								@endif
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<br/>
			<div class="row" style="margin-bottom: 0.2em">
					<div class="col-md-12 text-right">
						<a href="{{url('/peramalan')}}" class="btn btn-success">Kembali ke Cari</a>&nbsp
						<a href="{{url('peramalan/detail-des/'.$produk.'/'.$date_from.'/'.$date_to)}}" class="btn btn-outline-success">Sebelumnya</a>
					</div>
				</div>
		</div>
	</div>
</div>
<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="modal-block-popin" aria-hidden="true">
</div>
@endsection