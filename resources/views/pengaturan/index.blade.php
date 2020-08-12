@extends('layouts.app')
@section('content')
<div class="bg-body-light">
	<div class="content content-full">
		<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
			<h1 class="flex-sm-fill h3 my-2">
				Pengaturan
			</h1>
		</div>
	</div>
</div>
<div class="content">
	<form action="{{url('pengaturan/store')}}" id="form" class="js-validation" method="POST" enctype="multipart/form-data">
		{{ csrf_field() }}
		<div class="block">
			<div class="col-lg-12">
				<input type="text" style="display: none" id="address" value="">
				<input type="text" class="form-control" style="display: none" value="" name="mode" id="mode"> 
				<div class="block">
					<ul class="nav nav-tabs nav-tabs-block" data-toggle="tabs" role="tablist">
						@if(in_array(Auth::user()->roles[0]->id,getConfigValues('ROLE_ADMIN')))
						<li class="nav-item">
							<a class="nav-link active" href="#config">ID Konfigurasi</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="#perusahaan">Perusahaan</a>
						</li>
						@endif
						<li class="nav-item">
							<a class="nav-link" href="#user_settings">Pengguna</a>
						</li>
						
					</ul>
					<div class="block-content tab-content overflow-hidden">
						@if(in_array(Auth::user()->roles[0]->id,getConfigValues('ROLE_ADMIN')))
						<div class="tab-pane fade fade-left show active" id="config" role="tabpanel">
							@include('pengaturan.config')
						</div>
						<div class="tab-pane fade fade-left" id="perusahaan" role="tabpanel">
							@include('pengaturan.perusahaan')
						</div>
						@endif
						<div class="tab-pane fade fade-left" id="user_settings" role="tabpanel">
							@include('pengaturan.user-settings')
						</div>
						<div class="form-group row">
							<div class="col-12 text-right">
								<button class="btn btn-primary" id="simpan" style="display: none">Simpan</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

@php
if(in_array(Auth::user()->roles[0]->id,getConfigValues('ROLE_ADMIN')))
{
	$start='#config';
}
elseif(in_array(Auth::user()->roles[0]->id,getConfigValues('ROLE_ADMIN')))
{
	$start='#perusahaan';
}
else
{
	$start='#user_settings';
}
@endphp
 <div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="modal-block-popin" aria-hidden="true">
  </div>
@endsection

@push('js')
<script type="text/javascript">
	var nama='{{$start}}';
	$(document).ready(function(){
		$('#address').val(nama);
		$('[href="'+nama+'"]').click();

		if(nama=='#perusahaan')
		{
			$('#mode').val('perusahaan');
			$('#simpan').fadeIn();
			perusahaan();
		}

		if(nama=='#user_settings')
		{
			$('#mode').val('user_settings');
			$('#simpan').fadeIn();
			user_settings();
		}

		 $('.nav-tabs-block li a.nav-link').click(function(){
            var a=$(this).attr('href');
            $('#address').val(a);
            if(a=='#config')
            {
            	$('#simpan').fadeOut();
            }
            else if(a=='#perusahaan')
            {
            	$('#simpan').fadeIn();
            	$('#mode').val('perusahaan');
            	perusahaan();
            }
            else if(a=='#user_settings')
            {
            	$('#simpan').fadeIn();
            	$('#mode').val('user_settings');
            	user_settings();
            }
         })
	});

	function perusahaan()
	{
		One.helpers('validation');

		$.validator.addMethod("noSpace", function(value, element) { 
			return value.indexOf(" ") < 0 && value != ""; 
		}, "Username tidak boleh diisi spasi");

		$.validator.addMethod("validDate", function(value, element) {
			return this.optional(element) || moment(value,"DD-MM-YYYY").isValid();
		}, "Silahkan masukkan format tanggal, exp: DD-MM-YYYY");

		$.validator.addMethod("phoneUS", function(phone_number, element) {
			phone_number = phone_number.replace(/\s+/g, "");
			return this.optional(element) || phone_number.length > 9 && 
			phone_number.match(/\+?([ -]?\d+)+|\(\d+\)([ -]\d+)/);
		}, "Silahkan tulis format telepon dengan benar");


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
				'nama_ps': {
					required: true,
					minlength: 1,
					maxlength: 100
				},
				'alamat_ps': {
					required: true,
					minlength: 1
				},
				'email_ps': {
					required: true,
					minlength: 1,
					email:true,
				},
				'telp_ps': {
					required: true,
					minlength: 8,
					maxlength:14,
					phoneUS:true,
				},
				'fax_ps': {
					required: false,
					minlength: 8,
					maxlength:14,
					phoneUS:true,
				},
				'website_ps': {
					required: false,
					minlength: 1,
					url:true,
				},
				 'tgl_berdiri_ps': {
                    required: true,
                    validDate:true,
                },
			},
			messages: {
				'nama_ps': {
					required: 'Silahkan isi form',
					minlength: 'Karakter minimal diisi 1',
					maxlength: 'Karakter maksimal diisi 100'
				},
				'alamat_ps': {
					required: 'Silahkan isi form',
					minlength: 'Karakter minimal diisi 1'
				},
				'email_ps': {
                    required: 'Silahkan isi form',
                    remote: $.validator.format("{0} is already taken."),
                    email:"Format yang diisi harus email",
                },
                'telp_ps': {
					required: 'Silahkan isi form',
					minlength: 'Karakter minimal diisi 8',
					maxlength: 'Karakter maksimal diisi 14'
				},
				'fax_ps': {
					required: 'Silahkan isi form',
					minlength: 'Karakter minimal diisi 8',
					maxlength: 'Karakter maksimal diisi 14'
				},
				'website_ps': {
					required: 'Silahkan isi form',
					minlength: 'Karakter minimal diisi 1',
					url:'Masukkan format url dengan benar'
				},
				'tgl_berdiri_ps': {
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

	}

	function user_settings()
	{
		One.helpers('validation');

		$.validator.addMethod("noSpace", function(value, element) { 
			return value.indexOf(" ") < 0 && value != ""; 
		}, "Username tidak boleh diisi spasi");

		$.validator.addMethod("validDate", function(value, element) {
			return this.optional(element) || moment(value,"DD-MM-YYYY").isValid();
		}, "Silahkan masukkan format tanggal, exp: DD-MM-YYYY");

		$.validator.addMethod("phoneUS", function(phone_number, element) {
			phone_number = phone_number.replace(/\s+/g, "");
			return this.optional(element) || phone_number.length > 9 && 
			phone_number.match(/\+?([ -]?\d+)+|\(\d+\)([ -]\d+)/);
		}, "Silahkan tulis format telepon dengan benar");


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
				'username': {
                	required: true,
                	noSpace: true,     
                	remote: {
                		url: "pengaturan/check-username",
                		type: "post",
                		data: {
                			"_token": "{{ csrf_token() }}",
                			username: function()
                          {
                              return $('#form :input[name="username"]').val();
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
                				bootstrap_toast('Username sudah digunakan user lain!','gagal');
                				return "\"" + "Username sudah digunakan" + "\"";
                			} else {
                				bootstrap_toast('Username tersedia','sukses');
                				return 'true';
                			}
                		}
                	}
                }, 
				'email_username': {
                	required: true,     
                	email:true,
                	remote: {
                		url: "pengaturan/check-email",
                		type: "post",
                		data: {
                			"_token": "{{ csrf_token() }}",
                			email_username: function()
                          {
                              return $('#form :input[name="email_username"]').val();
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
                				bootstrap_toast('Email sudah digunakan user lain!','gagal');
                				return "\"" + "Email sudah digunakan" + "\"";
                			} else {
                				bootstrap_toast('Email tersedia','sukses');
                				return 'true';
                			}
                		}
                	}
                },
                'old_password': {
                	required:false,
                    remote: {
                		url: "pengaturan/check-password",
                		type: "post",
                		data: {
                			"_token": "{{ csrf_token() }}",
                			password: function()
                          {
                              return $('#form :input[name="old_password"]').val();
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
                				bootstrap_toast('Password lama yang anda isikan salah!','gagal');
                				return "\"" + "Password lama yang anda isikan salah" + "\"";
                			} else {
                				bootstrap_toast('Password tersedia','sukses');
                				return 'true';
                			}
                		}
                	}
                }, 
                'new_password': {
                	required:false,
                    minlength: 8,
                },
                're_password' : {
                required:false,
				minlength : 5,
				equalTo : "#new_password"
			}
			},
			messages: {
				'nama': {
                    required: 'Silahkan isi form',
                    minlength: 'Isian minimal 1 karakter',
                    maxlength: 'Isian maksimal 100 karakter',
                },
                'username': {
                    required: 'Silahkan isi form',
                    remote: $.validator.format("{0} is already taken.")
                },
                'email_username': {
                    required: 'Silahkan isi form',
                    remote: $.validator.format("{0} is already taken."),
                    email:"Silahkan masukkan format email, contoh john.doe@mail.com"
                },
                'old_password': {
                    remote: 'Password lama yang anda isikan salah',
                },
                'new_password': {
                    minlength: 'Isian minimal 8 karakter',
                },
                're_password': {
                	minlength: 'Isian minimal 8 karakter',
                    equalTo:'Password yang anda inputkan salah!'
                },
			}
		});

		$('.js-select2').on('change', e => {
			jQuery(e.currentTarget).valid();
		});

		$(".js-flatpickr").on("change", function (e) {  
			$(this).valid(); 
		});

	}

</script>
@endpush