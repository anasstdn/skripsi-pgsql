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
				Kecamatan
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
									<th>Kode Kecamatan</th>
									<th>Nama Kecamatan</th>
									<th>Nama Kabupaten</th>
                                    <th>Nama Provinsi</th>
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
											<label for="block-form1-name">Kode Kecamatan</label>
											<input type="text" class="form-control form-control-alt form-control-sm" id="kode_kecamatan" name="kode_kecamatan">
										</div>
										<div class="form-group">
											<label for="block-form1-username">Nama Kecamatan</label>
											<input type="text" class="form-control form-control-alt form-control-sm" id="nama_kecamatan" name="nama_kecamatan">
										</div>
										<div class="form-group">
											<label for="block-form1-username">Nama Kabupaten</label>
                                            <select class="js-select2 form-control" id="id_kabupaten" name="id_kabupaten" style="width: 100%;" data-placeholder="">
                                                <option value="">-Silahkan Pilih-</option>
                                                @if(isset($kabupaten) && !$kabupaten->isEmpty())
                                                @foreach($kabupaten as $r)
                                                <option value="{{$r->id}}">{{$r->nama_kabupaten}},&nbsp{{$r->provinsi->nama_provinsi}}</option>
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
			$("#judul").html('Tambah Kecamatan');
            $('#mode').val('add');
            $('#form').prop('action', '{{url('kecamatan/store')}}');
            $('#id_kabupaten').val('').trigger('change');
		});

		$(".cancelAddItem").click(function(){
			document.getElementById("form").reset();
			$("#table_width").attr('class', "col-lg-12");
			$("#createNewItemDiv").addClass('hidden');
			$("#createNewItemDiv").fadeOut();
            $('#mode').val('');
            $('#id_kabupaten').val('').trigger('change');
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
				url:"{{ url('kecamatan/get-data') }}",
				data: function (d) {

				}
			},
			columns: [
            {data: 'nomor', name: 'nomor'},
            {data: 'kode_kecamatan', name: 'kode_kecamatan'},
            {data: 'nama_kecamatan', name: 'nama_kecamatan'},
            {data: 'nama_kabupaten', name: 'nama_kabupaten'},
            {data: 'nama_provinsi', name: 'nama_provinsi'},
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
                'kode_kecamatan': {
                    required: true,
                    minlength: 1
                },
                'nama_kecamatan': {
                    required: true,
                    minlength: 1
                },
                'id_kabupaten': {
                    required: true,
                },
            },
            messages: {
                'kode_kecamatan': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal diisi 1'
                },
                'nama_kecamatan': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal diisi 1'
                },
                'id_kabupaten': {
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
        url : "{{url('kecamatan/edit/')}}/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            $("#table_width").attr('class', "col-lg-6");
            $("#createNewItemDiv").toggleClass('hidden');
            $("#judul").html('Edit Kecamatan');
            $('#mode').val('edit');
            $('#form').prop('action', '{{url('kecamatan/update')}}');
            $('#kode_kecamatan').val(data.data.kode_kecamatan);
            $('#nama_kecamatan').val(data.data.nama_kecamatan);
            $('#id_kabupaten').val(data.data.id_kabupaten).trigger('change');
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