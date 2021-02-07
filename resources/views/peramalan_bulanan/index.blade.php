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
		<form action="{{url('peramalan-bulanan/cari')}}" id="form" class="js-validation" method="POST" enctype="multipart/form-data">
			{{ csrf_field() }}
			<div class="block-content block-content-full">
				<h4 class="font-w400">Filter Pencarian</h4>
				<div class="form-group row">
					<div class="col-sm-2" ><b>Range Waktu</b></div>
					<div class="col-md-2" style="text-align:left;">
						<input type="text" class="datepicker form-control" id="month_year_start" name="month_year_start" value="{{ date('m-Y',strtotime('-1 month')) }}" placeholder="Start" required>
					</div>
					<div class="col-md-2" style="text-align:left;">
					<input type="text" class="datepicker form-control" id="month_year_end" name="month_year_end" value="{{ date('m-Y') }}" placeholder="End" required>
					</div>
				</div>

				<div class="form-group row">
					<div class="col-sm-2" ><b>Produk yang diramalkan</b></div>
					<div class="col-md-4" style="text-align:left;">
						<select class="form-control" name="produk" id="produk">
							<option value="abu">Abu</option>
							<option value="gendol">Pasir Gendol</option>
							<option value="pasir">Pasir Biasa</option>
							<option value="split1_2">Split 1/2</option>
							<option value="split2_3">Split 2/3</option>
							<option value="lpa">LPA</option>
						</select>
					</div>
				</div>

				<div class="form-group row">
					<div class="col-sm-2" ><b>Koefisien Alpha Beta</b></div>
					<div class="col-md-4" style="text-align:left;">
						<select class="form-control" name="koefisien_alpha_beta" id="koefisien_alpha_beta">
							<option value="random">Random (0,01 sd 0,99)</option>
							<option value="rumus">Periode Uji (2 / (n+1))</option>
						</select>
					</div>
				</div>

				<div class="form-group row">
					<div class="col-sm-2 hide_form" style="display: none"><b>Ketetapan Nilai Peramalan</b></div>
					<div class="col-md-4 hide_form" style="text-align:left;display: none">
						<select class="form-control" name="ketetapan_nilai_peramalan" id="ketetapan_nilai_peramalan">
							<option value="mape">MAPE</option>
							<option value="mad">MAD</option>
						</select>
					</div>
				</div>

				<div class="row" style="margin-bottom: 0.2em">
					<div class="col-md-6 text-right">
						<button type="submit" id="simpan" class="btn btn-primary">Cari</button>
					</div>
				</div>
				<br/>
				<!-- DataTables init on table by adding .js-dataTable-full-pagination class, functionality is initialized in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->
				<div class="row">
					<div class="col-lg-12" id="table_width">

					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="modal-block-popin" aria-hidden="true">
</div>
@endsection

@push('js')
<script>
	$(function(){
		$(".datepicker").datepicker( {
			format: "mm-yyyy",
			startView: "months", 
			minViewMode: "months",
			orientation : "bottom auto",
			autoclose: true,
		});

		ketetapan();
		
		$('#koefisien_alpha_beta').on('change',function(){
			ketetapan();
		});

		function ketetapan()
		{
			if($('#koefisien_alpha_beta').val() == 'random')
			{
				$('.hide_form').fadeIn();
			}
			else
			{
				$('.hide_form').fadeOut();
			}
		}
	})


</script>
@endpush