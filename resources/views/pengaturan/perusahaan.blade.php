<div class="block-header">
    <h3 class="block-title">
        <i class="fa fa-briefcase mr-5 text-muted"></i> Perusahaan
    </h3>
</div>
<div class="block-content block-content-full">
    <div class="row items-push">
      <div class="col-lg-3">
        <p style="color: red">
          Silahkan isi informasi perusahaan / koperasi secara lengkap. Isian wajib ditandai dengan tanda *.
      </p>
  </div>
  <div class="col-lg-7 offset-lg-1">
    <div class="form-group row">
      <div class="col-12">
        <label for="profile-settings-username">Nama Perusahaan&nbsp<span style="color: red">*</span></label>
        <input type="text" class="form-control form-control-lg" id="nama_ps" name="nama_ps" placeholder="Nama Perusahaan" value="{{isset($perusahaan)?isset($perusahaan->nama_ps)?$perusahaan->nama_ps:'':''}}">
    </div>
</div>
<div class="form-group row">
      <div class="col-12">
        <label for="profile-settings-username">Alamat Perusahaan&nbsp<span style="color: red">*</span></label>
        <textarea class="form-control form-control-lg" name="alamat_ps" placeholder="Alamat Perusahaan" id="alamat_ps">{{isset($perusahaan)?isset($perusahaan->alamat_ps)?$perusahaan->alamat_ps:'':''}}</textarea>
    </div>
</div>
<div class="form-group row">
      <div class="col-12">
        <label for="profile-settings-username">Email&nbsp<span style="color: red">* (contoh : nubeskop@mail.com)</span></label>
        <input type="text" class="form-control form-control-lg" id="email_ps" name="email_ps" placeholder="Email" value="{{isset($perusahaan)?isset($perusahaan->email_ps)?$perusahaan->email_ps:'':''}}">
    </div>
</div>
<div class="form-group row">
      <div class="col-12">
        <label for="profile-settings-username">Telepon&nbsp<span style="color: red">* (contoh : 0274-xxxxxx)</span></label>
        <input type="text" class="form-control form-control-lg" id="telp_ps" name="telp_ps" placeholder="Telepon" value="{{isset($perusahaan)?isset($perusahaan->telp_ps)?$perusahaan->telp_ps:'':''}}">
    </div>
</div>
<div class="form-group row">
      <div class="col-12">
        <label for="profile-settings-username">Fax&nbsp<span style="color: red">(contoh : 0274-xxxxxx)</span></label>
        <input type="text" class="form-control form-control-lg" id="fax_ps" name="fax_ps" placeholder="Fax" value="{{isset($perusahaan)?isset($perusahaan->fax_ps)?$perusahaan->fax_ps:'':''}}">
    </div>
</div>
<div class="form-group row">
      <div class="col-12">
        <label for="profile-settings-username">Website&nbsp<span style="color: red">(contoh : http://www.google.com, www.google.com)</span></label>
        <input type="text" class="form-control form-control-lg" id="website_ps" name="website_ps" placeholder="Website" value="{{isset($perusahaan)?isset($perusahaan->website_ps)?$perusahaan->website_ps:'':''}}">
    </div>
</div>
<div class="form-group row">
      <div class="col-12">
        <label for="profile-settings-username">Tanggal Berdiri Perusahaan&nbsp<span style="color: red">*</span></label>
        <input type="text" class="form-control form-control-lg js-flatpickr bg-white" data-date-format="d-m-Y" id="tgl_berdiri_ps" name="tgl_berdiri_ps" placeholder="Tanggal Berdiri Perusahaan" value="{{isset($perusahaan)?isset($perusahaan->tgl_berdiri_ps)?date('d-m-Y',strtotime($perusahaan->tgl_berdiri_ps)):'':''}}">
    </div>
</div>
{{-- @php
$flag_mode=null;
if(isset($perusahaan->flag_ksp) && isset($perusahaan->flag_ksu))
{
  if($perusahaan->flag_ksu=='Y' && $perusahaan->flag_ksp=='N')
  {
    $flag_mode='ksu';
  }
  else
  {
    $flag_mode='ksp';
  }
}
@endphp --}}
{{-- <div class="form-group row">
  <div class="col-12">
    <label for="profile-settings-username">Mode Operasi&nbsp<span style="color: red">*</span></label>&nbsp
    <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" class="custom-control-input" id="flag_aktif_Y" name="flag_aktif" value="KSU" {{isset($flag_mode)?$flag_mode=='ksu'?'checked':'':'checked'}}>
        <label class="custom-control-label" for="flag_aktif_Y">KSU</label>
    </div>
    <div class="custom-control custom-radio custom-control-inline">
        <input type="radio" class="custom-control-input" id="flag_aktif_N" name="flag_aktif" value="KSP" {{isset($flag_mode)?$flag_mode=='ksp'?'checked':'':''}}>
        <label class="custom-control-label" for="flag_aktif_N">KSP</label>
    </div>
</div>
</div> --}}

</div>
</div>
</div>