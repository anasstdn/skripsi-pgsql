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
				Manajemen Pengguna
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

            <div class="form-group row">
                <div class="col-md-1">
                    <a class="btn btn-primary btn-sm" id="tambah" href="#" >Tambah</a>
                </div>
            </div>
			<!-- DataTables init on table by adding .js-dataTable-full-pagination class, functionality is initialized in js/pages/be_tables_datatables.min.js which was auto compiled from _es6/pages/be_tables_datatables.js -->
			<div class="row">
				<div class="col-lg-12" id="table_width">
					<div class="table-responsive">
						<table class="table table-bordered table-striped table-vcenter" id="tabel">
							<thead>
								<tr>
									<th class="text-center" style="width: 80px;">No</th>
									<th>Nama</th>
									<th>Username</th>
									<th class="d-none d-sm-table-cell" style="width: 30%;">Email</th>
									<th class="d-none d-sm-table-cell" style="width: 15%;">Role</th>
									<th class="d-none d-sm-table-cell" style="width: 15%;">Status Aktif</th>
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
											<label for="block-form1-name">Nama Depan</label>
											<input type="text" class="form-control form-control-alt form-control-sm" id="nama_depan" name="nama_depan">
										</div>
                                        <div class="form-group">
                                            <label for="block-form1-name">Nama Belakang</label>
                                            <input type="text" class="form-control form-control-alt form-control-sm" id="nama_belakang" name="nama_belakang">
                                        </div>
										<div class="form-group">
											<label for="block-form1-username">Username</label>
											<input type="text" class="form-control form-control-alt form-control-sm" id="username" name="username">
										</div>
										<div class="form-group">
											<label for="block-form1-username">Email</label>
											<input type="email" class="form-control form-control-alt form-control-sm" id="email" name="email">
										</div>
										<div class="form-group">
											<label for="block-form1-password">Password</label>
											<input type="password" class="form-control form-control-alt form-control-sm" id="password" name="password" placeholder="">
										</div>
										<div class="form-group">
											<label for="block-form1-username">Roles</label>
                                            <select class="js-select2 form-control" id="roles" name="roles" style="width: 100%;" data-placeholder="">
                                                <option value="">-Silahkan Pilih-</option>
                                                @foreach($role as $r)
                                                <option value="{{$r->id}}" {{isset($user->roleUser->role_id) && $user->roleUser->role_id==$r->id?'selected':''}}>{{$r->display_name}}</option>
                                                @endforeach
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
				url:"{{ url('user/get-data') }}",
				data: function (d) {

				}
			},
			columns: [
            {data: 'nomor', name: 'nomor'},
            {data: 'name', name: 'name'},
            {data: 'username', name: 'username'},
            {data: 'email', name: 'email'},
            { data: 'role', name: 'role',searchable:false,orderable:false , "render":function(data,type,row){
            	if(data.id==1)
            	{
            		return '<span class="badge badge-primary">'+data.role+'</span>';
            	}
            	else
            	{
            		return '<span class="badge badge-success">'+data.role+'</span>';
            	}
            }},
            { data: 'status', name: 'status',searchable:false,orderable:false , "render":function(data,type,row){
            	if(data.status_aktif==1)
            	{
            		return '<a class="btn btn-success btn-sm" href="#" style="color:white;font-family:Arial" title="Nonaktifkan User" onclick="nonaktifkan(\'' + data.url + '\',\'' + data.status_aktif + '\')">Aktif</a>';
            	}
            	else
            	{
            		return '<a class="btn btn-danger btn-sm" href="#" style="color:white;font-family:Arial" title="Aktifkan User" onclick="nonaktifkan(\'' + data.url + '\',\'' + data.status_aktif + '\')">Nonaktif</a>';
            	}
            }},
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
                'nama_depan': {
                    required: true,
                    minlength: 1
                },
                'nama_belakang': {
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
                'nama_depan': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal diisi 1'
                },
                'nama_belakang': {
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
            $('#nama_depan').val(data.data.nama_depan);
            $('#nama_belakang').val(data.data.nama_belakang);
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