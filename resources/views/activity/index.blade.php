@extends('layouts.app')
@section('content')

<div class="bg-body-light">
	<div class="content content-full">
		<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
			<h1 class="flex-sm-fill h3 my-2">
				Riwayat Aktivitas Pengguna
			</h1>
		</div>
	</div>
</div>
<div class="content">
	<div class="block">
		<div class="block-header">
			<div class="col-md-1">
	
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
									<th>No</th>
                                    <th>Nama Log</th>
                                    <th>Waktu</th>
                                    <th>Deskripsi</th>
                                    <th>Pengguna</th>
                                    <th>Properties</th>
								</tr>
							</thead>
							<tbody>

							</tbody>
						</table>
					</div>
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
			$("#judul").html('Tambah Pengguna');
            $('#mode').val('add');
            $('#form').prop('action', '{{url('user/store')}}');
            $('#roles').val('').trigger('change');
		});

		$(".cancelAddItem").click(function(){
			document.getElementById("form").reset();
			$("#table_width").attr('class', "col-lg-12");
			$("#createNewItemDiv").addClass('hidden');
			$("#createNewItemDiv").fadeOut();
            $('#mode').val('');
            $('#roles').val('').trigger('change');
		});

		$('#tabel').dataTable({
			pagingType: "full_numbers",
			pageLength: 10,
			lengthMenu: [[5, 10, 15, 20], [5, 10, 15, 20]],
			autoWidth: false,
			stateSave: true,
			processing : true,
			serverSide : true,
			ajax : {
				url:"{{ url('activity-log/get-data') }}",
				data: function (d) {

				}
			},
			columns: [
            {data: 'nomor', name: 'nomor'},
            {data: 'log_name', name: 'log_name'},
            {data: 'created_at', name: 'created_at'},
            {data: 'description', name: 'description'},
            {data: 'causer_id', name: 'causer_id'},
            {data: 'action', name: 'action'},
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
                'name': {
                    required: true,
                    minlength: 1
                },
                'username': {
                    required: true,
                    noSpace: true,     
                    remote: {
                        url: "user/check-username",
                        type: "post",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            username: function()
                            {
                              return $('#form :input[name="username"]').val();
                          },
                          mode: function()
                          {
                              return $('#form :input[name="mode"]').val();
                          },
                          id: function()
                          {
                              return $('#form :input[name="id"]').val();
                          }
                        },
                        beforeSend: function () {
                            $('.ajax-loader').fadeIn();
                            $("#status").html("Silahkan tunggu sesaat...");
                            $('#loader').css('width','100%');
                        },
                        dataFilter: function (data) {
                            var json = JSON.parse(data);
                            $('.ajax-loader').fadeOut();
                            if (json.msg == "true") {
                                bootstrap_toast('Username sudah digunakan','gagal');
                                return "\"" + "Username sudah digunakan" + "\"";
                            } else {
                                bootstrap_toast('Username tersedia','sukses');
                                return 'true';
                            }
                        }
                    }
                }, 
               'email': {
                    required: true,     
                    email:true,
                    remote: {
                        url: "user/check-email",
                        type: "post",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            email_username: function()
                          {
                              return $('#form :input[name="email"]').val();
                          },
                          mode: function()
                          {
                              return $('#form :input[name="mode"]').val();
                          },
                          id: function()
                          {
                              return $('#form :input[name="id"]').val();
                          }
                        },
                        beforeSend: function () {
                            $('.ajax-loader').fadeIn();
                            $("#status").html("Silahkan tunggu sesaat...");
                            $('#loader').css('width','100%');
                        },
                        dataFilter: function (data) {
                            var json = JSON.parse(data);
                            $('.ajax-loader').fadeOut();
                            if (json.msg == "true") {
                                bootstrap_toast('Email sudah digunakan','gagal');
                                return "\"" + "Email sudah digunakan" + "\"";
                            } else {
                                bootstrap_toast('Email tersedia','sukses');
                                return 'true';
                            }
                        }
                    }
                },
                'password': {
                    required: true,
                    minlength: 8,
                },
                'roles': {
                    required: true,
                },
            },
            messages: {
                'name': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal diisi 1'
                },
                'username': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal diisi 1'
                },
                'email': {
                    required: 'Silahkan isi form',
                    remote: $.validator.format("{0} is already taken."),
                    email:"Format yang diisi harus email",
                },
                'password': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal 8',
                },
                'roles': {
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
        url : "{{url('user/edit/')}}/" + id,
        type: "GET",
        dataType: "JSON",
        success: function(data)
        {
            $("#table_width").attr('class', "col-lg-6");
            $("#createNewItemDiv").toggleClass('hidden');
            $("#judul").html('Edit Pengguna');
            $('#mode').val('edit');
            $('#form').prop('action', '{{url('user/update')}}');
            $('#name').val(data.data.name);
            $('#username').val(data.data.username);
            $('#email').val(data.data.email);
            $('#roles').val(data.data.role_id).trigger('change');
            $('#id').val(id);
            $('#password').rules('remove', 'required');
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
            alert('Error get data from ajax');
        }
    });
}
</script>
@endpush