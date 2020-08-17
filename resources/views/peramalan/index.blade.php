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
		<form action="{{url('peramalan/cari')}}" id="form" class="js-validation" method="POST" enctype="multipart/form-data">
			{{ csrf_field() }}
			<div class="block-content block-content-full">
				<h4 class="font-w400">Filter Pencarian</h4>
				<div class="form-group row">
					<div class="col-sm-2" ><b>Tanggal</b></div>
					<div class="col-md-2" style="text-align:left;">
						<input type="text" class="js-flatpickr form-control form-control-lg bg-white" id="tanggal_awal" name="tanggal_awal" placeholder="Tanggal Awal" data-date-format="d-m-Y" value="">
					</div>
					<div class="col-md-2" style="text-align:left;">
						<input type="text" class="js-flatpickr form-control form-control-lg bg-white" id="tanggal_akhir" name="tanggal_akhir" placeholder="Tanggal Akhir" data-date-format="d-m-Y" value="">
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
	$(function() {
		One.helpers('validation');
		$.validator.addMethod("noSpace", function(value, element) { 
			return value.indexOf(" ") < 0 && value != ""; 
		}, "Username tidak boleh diisi spasi");

		$.validator.addMethod("validDate", function(value, element) {
			return this.optional(element) || moment(value,"DD-MM-YYYY").isValid();
		}, "Entry true date format, exp: DD-MM-YYYY");

		$('.js-validation').validate({
			ignore: [],
			button: {
				selector: "#simpan",
				disabled: "disabled"
			},
			debug: false,
			errorClass: 'invalid-feedback',
			errorElement: 'div',
			errorPlacement: (error, e) => {
				jQuery(e).parents('.form-group > div').append(error);
			},
			highlight: e => {
				jQuery(e).closest('.form-group').removeClass('is-invalid').addClass('is-invalid');
			},
			success: e => {
				jQuery(e).closest('.form-group').removeClass('is-invalid');
				jQuery(e).remove();
			},
			rules: {
				'tanggal_awal': {
					required: true,
					validDate:true,
				},
				'tanggal_akhir': {
					required: true,
					validDate:true,
				},
			},
			messages: {
				'tanggal_awal': {
					required: 'Silahkan isi form',
				},
				'tanggal_akhir': {
					required: 'Silahkan isi form',
				},
			}
		});

		$('.js-select2').on('change', e => {
			jQuery(e.currentTarget).valid();
		});

		$(".js-flatpickr").on("change", function (e) {  
			$(this).valid(); 
		});

		$('input').on('focus focusout keyup click change', function () {
			var tgl_awal=$('#tanggal_awal').val();
			var tgl_akhir=$('#tanggal_akhir').val();
			if(new Date(reformatDateString(tgl_awal)).getTime() > new Date(reformatDateString(tgl_akhir)).getTime())
			{
				bootstrap_toast('Tanggal Awal tidak boleh > Tanggal Akhir','gagal');
				$('#tanggal_akhir').val(tgl_awal);
				$(this).valid();
				return false;
			}
			else
			{
				$(this).valid();
				$('#tanggal_awal').valid();
				$('#tanggal_akhir').valid();
			}
		});

	});

	function reformatDateString(s) {
		var b = s.split(/\D/);
		return b.reverse().join('-');
	}
</script>
@endpush