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
				Kelurahan
			</h1>
		</div>
	</div>
</div>
<div class="content">
	<div class="block">
		<div class="block-header">
			<div class="col-md-1">
				<a class="btn btn-primary btn-sm" id="tambah" href="#" >Tambah</a>
			</div>
		</div>
		<div class="block-content block-content-full">
			<!-- DataTables init on table by adding .js-dataTable-full-pagination class, functionality is initialized in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->
			<div class="row">
				<div class="col-lg-12" id="table_width">
					<div class="table-responsive">
						<table class="table table-bordered table-striped table-vcenter" id="tabel">
							<thead>
								<tr>
									<th class="text-center" style="width: 80px;">No</th>
									<th>Kode Kelurahan</th>
                                    <th>Nama Kelurahan</th>
									<th>Nama Kecamatan</th>
									<th>Nama Kabupaten</th>
                                    <th>Nama Provinsi</th>
                                    <th>Kodepos</th>
									<th style="width: 15%;">Aksi</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
				</div>
				<div class="col-md-6 hidden" id='createNewItemDiv'>
					<form action="" method="POST" class="js-validation" id="form">
                        {{csrf_field()}}
						<div class="block">
							<div class="block-header block-header-default">
								<h3 class="block-title" id="judul"></h3>
								<div class="block-options">
									<a href="#" class="close cancelAddItem">&times;</a><br>
								</div>
							</div>
                            <input type="hidden" id="mode" name="mode" value="">
                            <input type="hidden" id="id" name="id" value="">
							<div class="block-content">
								<div class="row justify-content-center py-sm-3 py-md-5">
									<div class="col-sm-10 col-md-8">
										<div class="form-group">
											<label for="block-form1-name">Kode Kelurahan</label>
											<input type="text" class="form-control form-control-alt form-control-sm" id="kode_kelurahan" name="kode_kelurahan">
										</div>
										<div class="form-group">
											<label for="block-form1-username">Nama Kelurahan</label>
											<input type="text" class="form-control form-control-alt form-control-sm" id="nama_kelurahan" name="nama_kelurahan">
										</div>
                                        <div class="form-group">
                                            <label for="block-form1-username">Kodepos</label>
                                            <input type="text" class="form-control form-control-alt form-control-sm" id="kodepos" name="kodepos">
                                        </div>
										<div class="form-group">
											<label for="block-form1-username">Nama Kecamatan</label>
                                            <select class="js-select2 form-control" id="id_kecamatan" name="id_kecamatan" style="width: 100%;" data-placeholder="">
                                                <option value="">-Silahkan Pilih-</option>
                                                @if(isset($kecamatan) && !$kecamatan->isEmpty())
                                                @foreach($kecamatan as $r)
                                                <option value="{{$r->id}}">{{$r->nama_kecamatan}},&nbsp{{$r->kabupaten->nama_kabupaten}},&nbsp{{$r->kabupaten->provinsi->nama_provinsi}}</option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
										<br/>
										<div class="form-group">
											<button type="submit" class="btn btn-sm btn-primary" id="simpan">
												Submit
											</button>
											&nbsp&nbsp
											<button type="reset" class="btn btn-sm btn-secondary cancelAddItem">
												Reset
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('js')
<script type="text/javascript">
	$(function() {

		 $('.js-select2:not(.js-select2-enabled)').each((index, element) => {
            let el = jQuery(element);
            el.addClass('js-select2-enabled').select2({
                placeholder: el.data('placeholder') || false,
                width: '100%'
            });
        });

		$("#tambah").click(function(){
			$("#table_width").attr('class', "col-lg-6");
			$("#createNewItemDiv").toggleClass('hidden');
			$("#judul").html('Tambah Kelurahan');
            $('#mode').val('add');
            $('#form').prop('action', '{{url('kelurahan/store')}}');
            $('#id_kecamatan').val('').trigger('change');
		});

		$(".cancelAddItem").click(function(){
			document.getElementById("form").reset();
			$("#table_width").attr('class', "col-lg-12");
			$("#createNewItemDiv").addClass('hidden');
			$("#createNewItemDiv").fadeOut();
            $('#mode').val('');
            $('#id_kecamatan').val('').trigger('change');
		});

		$('#tabel').dataTable({
			pagingType: "full_numbers",
			pageLength: 10,
			lengthMenu: [[5, 10, 15, 20], [5, 10, 15, 20]],
			autoWidth: true,
			stateSave: true,
			processing : true,
			serverSide : true,
			ajax : {
				url:"{{ url('kelurahan/get-data') }}",
				data: function (d) {

				}
			},
			columns: [
            {data: 'nomor', name: 'nomor'},
            {data: 'kode_kelurahan', name: 'kode_kelurahan'},
            {data: 'nama_kelurahan', name: 'nama_kelurahan'},
            {data: 'nama_kecamatan', name: 'nama_kecamatan'},
            {data: 'nama_kabupaten', name: 'nama_kabupaten'},
            {data: 'nama_provinsi', name: 'nama_provinsi'},
            {data: 'kodepos', name: 'kodepos'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        language: {
          lengthMenu : '{{ "Menampilkan _MENU_ data" }}',
          zeroRecords : '{{ "Data tidak ditemukan" }}' ,
          info : '{{ "_PAGE_ dari _PAGES_ halaman" }}',
          infoEmpty : '{{ "Data tidak ditemukan" }}',
          infoFiltered : '{{ "(Penyaringan dari _MAX_ data)" }}',
          loadingRecords : '{{ "Memuat data dari server" }}' ,
          processing :    '{{ "Memuat data data" }}',
          sSearchPlaceholder: "Pencarian..",
          lengthMenu: "_MENU_",
          search: "_INPUT_",
          paginate : {
              first :     '{{ "<" }}' ,
              last :      '{{ ">" }}' ,
              next :      '{{ ">>" }}',
              previous :  '{{ "<<" }}'
          }
        },
        aoColumnDefs: [{
          bSortable: false,
          aTargets: [-1]
        }],
        iDisplayLength: 5,

      });

		One.helpers('validation');

		$.validator.addMethod("noSpace", function(value, element) { 
			return value.indexOf(" ") < 0 && value != ""; 
		}, "Username tidak boleh diisi spasi");

		$('.js-validation').validate({
            ignore: [],
            button: {
                selector: "#simpan",
                disabled: "disabled"
            },
            debug: false,
            errorClass: 'invalid-feedback',
            rules: {
                'kode_kelurahan': {
                    required: true,
                    minlength: 1
                },
                'nama_kelurahan': {
                    required: true,
                    minlength: 1
                },
                'kodepos': {
                    required: true,
                    minlength: 5,
                    minlength: 5,
                    number:true
                },
                'id_kecamatan': {
                    required: true,
                },
            },
            messages: {
                'kode_kelurahan': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal diisi 1'
                },
                'nama_kelurahan': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal diisi 1'
                },
                'kodepos': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal diisi 5',
                    maxlength: 'Karakter minimal diisi 5',
                    number:'Silahkan isi format angka'
                },
                'id_kecamatan': {
                    required: 'Silahkan isi form',
                },
            }
        });

        $('.js-select2').on('change', e => {
            jQuery(e.currentTarget).valid();
        });

      

	})
</script>
<script type="text/javascript">
   function ubah_data(id){
    $.ajax({
        url : "{{url('kelurahan/edit/')}}/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            $("#table_width").attr('class', "col-lg-6");
            $("#createNewItemDiv").toggleClass('hidden');
            $("#judul").html('Edit Kelurahan');
            $('#mode').val('edit');
            $('#form').prop('action', '{{url('kelurahan/update')}}');
            $('#kode_kelurahan').val(data.data.kode_kelurahan);
            $('#nama_kelurahan').val(data.data.nama_kelurahan);
            $('#kodepos').val(data.data.kodepos);
            $('#id_kecamatan').val(data.data.id_kecamatan).trigger('change');
            $('#id').val(id);
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}
</script>
@endpush