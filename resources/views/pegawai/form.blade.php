@extends('layouts.app')
@section('content')

<div class="bg-body-light">
	<div class="content content-full">
		<div class="d-flex flex-column flex-sm-row justify-content-sm-between align-items-sm-center">
			<h1 class="flex-sm-fill h3 my-2">
				Form Pegawai
			</h1>
		</div>
	</div>
</div>
@php
if($mode=='create')
{
    $url=url('pegawai/store');
}
else
{
    $url=action('PegawaiController@update', $id);
}
@endphp

<div class="content">
	<div class="block">
        <form action="{{$url}}" id="form" class="js-validation" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
		<div class="block-header">
			<h3 class="block-title">
                <i class="fa fa-user-circle mr-5 text-muted"></i> Informasi Pribadi
            </h3>
        </div>
        <div class="block-content block-content-full">
            <div class="row items-push">
              <div class="col-lg-3">
                <p style="color: red">
                  Silahkan isi informasi pegawai, tanda * wajib diisi.
              </p>
          </div>
          <div class="col-lg-7 offset-lg-1">
            <div class="form-group row">
              <div class="col-6">
                <label for="profile-settings-username">NIK&nbsp<span style="color: red">*</span></label>
                <input type="text" class="form-control form-control-lg" id="nik" name="nik" placeholder="Nomor NIK" value="{{isset($profile)?isset($profile->nik)?$profile->nik:'':''}}">
            </div>
        </div>
        <div class="form-group row">
          <div class="col-6">
            <label for="profile-settings-name">Nama Depan&nbsp<span style="color: red">*</span></label>
            <input type="text" class="form-control form-control-lg" id="nama_depan" name="nama_depan" placeholder="Nama Depan" value="{{isset($profile)?isset($profile->nama_depan)?$profile->nama_depan:'':''}}">
        </div>
        <div class="col-6">
            <label for="profile-settings-name">Nama Belakang&nbsp<span style="color: red">*</span></label>
            <input type="text" class="form-control form-control-lg" id="nama_belakang" name="nama_belakang" placeholder="Nama Belakang" value="{{isset($profile)?isset($profile->nama_belakang)?$profile->nama_belakang:'':''}}">
        </div>
    </div>
    <div class="form-group row">
        <div class="col-6">
            <label for="profile-settings-name">Jenis Kelamin</label>
            <select class="js-select2 form-control form-control-lg" name="id_jenis_kelamin" id="id_jenis_kelamin" style="width: 100%;" data-placeholder="Pilih Jenis Kelamin">
                <option value="">-Silahkan Pilih-</option>
                <?php 
                $jenis_kelamin=\App\Models\JenisKelamin::get();
                ?>
                @if(isset($jenis_kelamin) && !$jenis_kelamin->isEmpty())
                @foreach($jenis_kelamin as $a)
                <option value="{{$a->id}}" {{isset($profile->id_jenis_kelamin) && !empty($profile->id_jenis_kelamin)?$profile->id_jenis_kelamin==$a->id?'selected':'':''}}>{{$a->jenis_kelamin}}</option>
                @endforeach
                @endif
            </select>
        </div>
          <div class="col-6">
            <label for="profile-settings-name">Agama</label>
            <select class="js-select2 form-control form-control-lg" name="id_agama" id="id_agama" style="width: 100%;" data-placeholder="Pilih Agama">
                <option value="">-Silahkan Pilih-</option>
                <?php 
                $agama=\App\Models\Agama::get();
                ?>
                @if(isset($agama) && !$agama->isEmpty())
                @foreach($agama as $a)
                <option value="{{$a->id}}" {{isset($profile->id_agama) && !empty($profile->id_agama)?$profile->id_agama==$a->id?'selected':'':''}}>{{$a->nama_agama}}</option>
                @endforeach
                @endif
            </select>
        </div>
    </div>
    <div class="form-group row">
      <div class="col-6">
        <label for="profile-settings-email">Tempat Lahir</label>
        <input type="text" class="form-control form-control-lg" id="tempat_lahir" name="tempat_lahir" placeholder="Tempat Lahir" value="{{isset($profile)?isset($profile->tempat_lahir)?$profile->tempat_lahir:'':''}}">
    </div>
    <div class="col-6">
        <label for="profile-settings-email">Tanggal Lahir</label>
        <input type="text" class="js-flatpickr form-control form-control-lg bg-white" data-date-format="d-m-Y" id="tgl_lahir" name="tgl_lahir" placeholder="Tanggal Lahir" value="{{isset($profile)?isset($profile->tgl_lahir) && !empty($profile->tgl_lahir)?date('d-m-Y',strtotime($profile->tgl_lahir)):'':''}}">
    </div>
</div>
<div class="form-group row">
  <div class="col-6">
    <label for="profile-settings-email">Status Perkawinan</label>
    <select class="js-select2 form-control form-control-lg" name="id_status_perkawinan" id="id_status_perkawinan" style="width: 100%;" data-placeholder="Pilih Status Perkawinan">
        <option value="">-Silahkan Pilih-</option>
        <?php 
        $status_perkawinan=\App\Models\StatusPerkawinan::get();
        ?>
        @if(isset($status_perkawinan) && !$status_perkawinan->isEmpty())
        @foreach($status_perkawinan as $a)
        <option value="{{$a->id}}" {{isset($profile->id_status_perkawinan) && !empty($profile->id_status_perkawinan)?$profile->id_status_perkawinan==$a->id?'selected':'':''}}>{{$a->status_perkawinan}}</option>
        @endforeach
        @endif
    </select>
</div>
</div>
<div class="form-group row">
  <div class="col-6">
    <label for="profile-settings-email">Alamat KTP</label>
    <textarea class="form-control form-control-lg" name="alamat_ktp" id="alamat_ktp" rows="5" placeholder="Alamat KTP">{{isset($profile)?isset($profile->alamat_ktp)?$profile->alamat_ktp:'':''}}</textarea>
</div>
<div class="col-6">
    <label for="profile-settings-email">Wilayah KTP</label>
    <select class="js-select2 form-control form-control-lg" name="id_kelurahan_ktp" id="id_kelurahan_ktp" style="width: 100%;" data-placeholder="Pilih Wilayah KTP">
        <option value="">-Silahkan Pilih-</option>
        <?php 
        $kelurahan=\App\Models\Kelurahan::get();
        ?>
        @if(isset($kelurahan) && !$kelurahan->isEmpty())
        @foreach($kelurahan as $a)
        <option value="{{$a->id}}" {{isset($profile->id_kelurahan_ktp) && !empty($profile->id_kelurahan_ktp)?$profile->id_kelurahan_ktp==$a->id?'selected':'':''}}>{{$a->nama_kelurahan}},&nbsp{{$a->kecamatan->nama_kecamatan}},&nbsp{{$a->kecamatan->kabupaten->nama_kabupaten}},&nbsp{{$a->kecamatan->kabupaten->provinsi->nama_provinsi}}</option>
        @endforeach
        @endif
    </select>
</div>
</div>
<div class="form-group row">
  <div class="col-6">
    <label for="profile-settings-email">Alamat Domisili</label>
    <textarea class="form-control form-control-lg" name="alamat_domisili" id="alamat_domisili" rows="5" placeholder="Alamat Domisili">{{isset($profile)?isset($profile->alamat_domisili)?$profile->alamat_domisili:'':''}}</textarea>
</div>
<div class="col-6">
    <label for="profile-settings-email">Wilayah Domisili</label>
    <select class="js-select2 form-control form-control-lg" name="id_kelurahan_domisili" id="id_kelurahan_domisili" style="width: 100%;" data-placeholder="Pilih Wilayah Domisili">
        <option value="">-Silahkan Pilih-</option>
        <?php 
        $kelurahan=\App\Models\Kelurahan::get();
        ?>
        @if(isset($kelurahan) && !$kelurahan->isEmpty())
        @foreach($kelurahan as $a)
        <option value="{{$a->id}}" {{isset($profile->id_kelurahan_domisili) && !empty($profile->id_kelurahan_domisili)?$profile->id_kelurahan_domisili==$a->id?'selected':'':''}}>{{$a->nama_kelurahan}},&nbsp{{$a->kecamatan->nama_kecamatan}},&nbsp{{$a->kecamatan->kabupaten->nama_kabupaten}},&nbsp{{$a->kecamatan->kabupaten->provinsi->nama_provinsi}}</option>
        @endforeach
        @endif
    </select>
</div>
</div>
<div class="form-group row">
  <div class="col-6">
    <label for="profile-settings-email">No Telepon</label>
    <input type="text" class="form-control form-control-lg" id="no_telp" name="no_telp" placeholder="Nomor Telepon" value="{{isset($profile)?isset($profile->no_telp)?$profile->no_telp:'':''}}">
</div>
</div>
<div class="form-group row">
  <div class="col-md-10 col-xl-6">
    <div class="push">
      @if(isset($profile) && $profile->foto!==null)
      <img class="img-avatar" id="uploadPreview" src="{{asset('images/')}}/profile/{{$profile->foto}}" alt="">
      @else
      <img class="img-avatar" id="uploadPreview" src="{{asset('oneui/')}}/src/assets/media/avatars/avatar13.jpg" alt="">
      @endif
  </div>
  <div class="custom-file">
      <input type="file" class="custom-file-input" id="profile-settings-avatar" name="foto" data-toggle="custom-file-input">
      <label class="custom-file-label" for="profile-settings-avatar">Pilih Foto</label>
  </div>
</div>
</div>
<input type="hidden" name="nama_foto" id="nama_foto" value="{{isset($profile)?isset($profile->foto)?$profile->foto:'':''}}">
{{-- <div class="form-group row">
  <div class="col-12">
    <button type="submit" id="simpan" class="btn btn-primary">Simpan</button>
</div>
</div> --}}
</div>
</div>
</div>
<div class="block-header">
    <h3 class="block-title">
        <i class="fa fa-briefcase mr-5 text-muted"></i> Informasi Kepegawaian
    </h3>
</div>
     <div class="block-content block-content-full">
            <div class="row items-push">
              <div class="col-lg-3">
                <p style="color: red">
                  Silahkan isi informasi kepegawaian secara lengkap.
              </p>
          </div>
          <div class="col-lg-7 offset-lg-1">
            <div class="form-group row">
              <div class="col-6">
                <label for="profile-settings-username">NIP&nbsp<span style="color: red">*</span></label>
                <input type="text" class="form-control form-control-lg" id="nip" name="nip" placeholder="Nomor NIP" value="{{isset($profile)?isset($profile->nip)?$profile->nip:'':''}}">
            </div>
            <div class="col-6">
                <label for="profile-settings-email">Tanggal Bergabung&nbsp<span style="color: red">*</span></label>
                <input type="text" class="js-flatpickr form-control form-control-lg bg-white" data-date-format="d-m-Y" id="tgl_bergabung" name="tgl_bergabung" placeholder="Tanggal Bergabung" value="{{isset($profile)?isset($profile->tgl_bergabung)?date('d-m-Y',strtotime($profile->tgl_bergabung)):'':''}}">
            </div>
        </div>
    <div class="form-group row">
        <div class="col-6">
            <label for="profile-settings-name">Jabatan&nbsp<span style="color: red">*</span></label>
            <select class="js-select2 form-control form-control-lg" name="id_jabatan" id="id_jabatan" style="width: 100%;" data-placeholder="Pilih Jabatan">
                <option value="">-Silahkan Pilih-</option>
                <?php 
                $jabatan=\App\Models\Jabatan::get();
                ?>
                @if(isset($jabatan) && !$jabatan->isEmpty())
                @foreach($jabatan as $a)
                <option value="{{$a->id}}" {{isset($profile->id_jabatan) && !empty($profile->id_jabatan)?$profile->id_jabatan==$a->id?'selected':'':''}}>{{$a->nama_jabatan}}</option>
                @endforeach
                @endif
            </select>
        </div>
          <div class="col-6">
            <label for="profile-settings-name">Departement&nbsp<span style="color: red">*</span></label>
            <select class="js-select2 form-control form-control-lg" name="id_departement" id="id_departement" style="width: 100%;" data-placeholder="Pilih Departement">
                <option value="">-Silahkan Pilih-</option>
                <?php 
                $departement=\App\Models\Departement::get();
                ?>
                @if(isset($departement) && !$departement->isEmpty())
                @foreach($departement as $a)
                <option value="{{$a->id}}" {{isset($profile->id_departement) && !empty($profile->id_departement)?$profile->id_departement==$a->id?'selected':'':''}}>{{$a->nama_departement}}</option>
                @endforeach
                @endif
            </select>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-6">
            <label for="profile-settings-email">Golongan</label>
            <select class="js-select2 form-control form-control-lg" name="id_golongan" id="id_golongan" style="width: 100%;" data-placeholder="Pilih Golongan">
                <option value="">-Silahkan Pilih-</option>
                <?php 
                $golongan=\App\Models\Golongan::get();
                ?>
                @if(isset($golongan) && !$golongan->isEmpty())
                @foreach($golongan as $a)
                <option value="{{$a->id}}" {{isset($profile->id_golongan) && !empty($profile->id_golongan)?$profile->id_golongan==$a->id?'selected':'':''}}>{{$a->nama_golongan}}</option>
                @endforeach
                @endif
            </select>
        </div>
         <div class="col-6">
            <label for="profile-settings-email">Roles&nbsp<span style="color: red">*</span></label>
            <select class="js-select2 form-control form-control-lg" name="roles" id="roles" style="width: 100%;" data-placeholder="Pilih Roles">
                <option value="">-Silahkan Pilih-</option>
                <?php 
                $roles=\App\Role::whereNotIn('id',getConfigValues('ROLE_ADMIN'))->get();
                ?>
                @if(isset($roles) && !$roles->isEmpty())
                @foreach($roles as $a)
                <option value="{{$a->id}}" {{isset($profile->role_id) && !empty($profile->role_id)?$profile->role_id==$a->id?'selected':'':''}}>{{$a->display_name}}</option>
                @endforeach
                @endif
            </select>
        </div>
</div>
<div class="form-group row">
  <div class="col-12">
    <button type="submit" id="simpan" class="btn btn-primary">Simpan</button>
</div>
</div>
</div>
</div>
</div>
</form>
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
                'nik': {
                    required: true,
                    minlength: 16,
                    maxlength: 16,
                    number:true
                },
                'nama_depan': {
                    required: true,
                    minlength: 1
                },
                'nama_belakang': {
                    required: true,
                    minlength: 1
                },
                'id_jenis_kelamin': {
                    required: false,
                },
                'id_agama': {
                    required: false,
                },
                'tempat_lahir': {
                    required: false,
                    minlength: 1
                },
                'tgl_lahir': {
                    required: false,
                    validDate:true,
                },
                'id_status_perkawinan': {
                    required: false,
                },
                'alamat_ktp': {
                    required: false,
                    minlength: 1
                },
                'alamat_domisili': {
                    required: false,
                    minlength: 1
                },
                'id_kelurahan_ktp': {
                    required: false,
                },
                'id_kelurahan_domisili': {
                    required: false,
                },
                'no_telp': {
                    required: false,
                    minlength: 1
                },
                'nip': {
                    required: true,
                    minlength: 1
                },
                'tgl_bergabung': {
                    required: true,
                    validDate:true,
                },
                'id_jabatan': {
                    required: true,
                },
                'id_departement': {
                    required: true,
                },
                'id_golongan': {
                    required: false,
                },
                'roles': {
                    required: true,
                },
            },
            messages: {
                'nik': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal diisi 16',
                    maxlength: 'Karakter minimal diisi 16',
                    number:'Silahkan isi format angka'
                },
                'nama_depan': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal diisi 1'
                },
                'nama_belakang': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal diisi 1'
                },
                'id_jenis_kelamin': {
                    required: 'Silahkan isi form',
                },
                'id_agama': {
                    required: 'Silahkan isi form',
                },
                'tempat_lahir': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal diisi 1'
                },
                'tgl_lahir': {
                    required: 'Silahkan isi form',
                },
                'id_status_perkawinan': {
                    required: 'Silahkan isi form',
                },
                'alamat_ktp': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal diisi 1'
                },
                 'alamat_domisili': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal diisi 1'
                },
                'id_kelurahan_ktp': {
                    required: 'Silahkan isi form',
                },
                'id_kelurahan_domisili': {
                    required: 'Silahkan isi form',
                },
                'no_telp': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal diisi 1'
                },
                'nip': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal diisi 1'
                },
                'tgl_bergabung': {
                    required: 'Silahkan isi form',
                },
                'id_jabatan': {
                    required: 'Silahkan isi form',
                },
                'id_departement': {
                    required: 'Silahkan isi form',
                },
                'id_golongan': {
                    required: 'Silahkan isi form',
                },
                'roles': {
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

    });

       $("#profile-settings-avatar").change(function () {
        if(fileExtValidate(this)) { 
         if(fileSizeValidate(this)) { 
          var oFReader = new FileReader();
          oFReader.readAsDataURL($('#profile-settings-avatar').prop('files')[0]);
          oFReader.onload = function (oFREvent) {
              document.getElementById("uploadPreview").src = oFREvent.target.result;
          };
      }   
  }
});
});

  var validExt = ".jpg, .jpeg, .png";
  function fileExtValidate(fdata) {
     var filePath = fdata.value;
     var getFileExt = filePath.substring(filePath.lastIndexOf('.') + 1).toLowerCase();
     var pos = validExt.indexOf(getFileExt);
     console.log(getFileExt);
     if(pos < 0) {
        bootstrap_toast('Silahkan upload file ekstensi .jpg atau .jpeg atau .png','gagal');
      $("#profile-settings-avatar").val(null);
      return false;
  } else {
      return true;
  }
}

var maxSize = '20000';
function fileSizeValidate(fdata) 
{
 if (fdata.files && fdata.files[0]) 
 {
  var fsize = fdata.files[0].size/1024;
  if(fsize > maxSize) 
  {
    bootstrap_toast('Ukuran file maksimum melebihi 20000 KB. Ukuran file saat ini sebesar: '+fsize+' KB','gagal');
   $("#profile-settings-avatar").val(null);
   return false;
} 
else 
{
    return true;
}
}
}
</script>
@endpush