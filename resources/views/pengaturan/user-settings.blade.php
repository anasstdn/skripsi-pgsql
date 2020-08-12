<div class="block-header">
    <h3 class="block-title">
        <i class="fa fa-user mr-5 text-muted"></i> Pengaturan Pengguna
    </h3>
</div>
<div class="block-content block-content-full">
    <div class="row items-push">
      <div class="col-lg-3">
        <p style="color: red">
          Anda dapat melakukan pergantian username, email dan password disini.
      </p>
  </div>
  <div class="col-lg-7 offset-lg-1">
    <div class="form-group row">
      <div class="col-12">
        <label for="profile-settings-username">Username&nbsp<span style="color: red">*</span></label>
        <input type="text" class="form-control form-control-lg" id="username" name="username" placeholder="Username" value="{{isset($user)?isset($user->username)?$user->username:'':''}}">
    </div>
</div>
<div class="form-group row">
      <div class="col-12">
        <label for="profile-settings-username">Email&nbsp<span style="color: red">* (contoh : nubeskop@mail.com)</span></label>
        <input type="text" class="form-control form-control-lg" id="email_username" name="email_username" placeholder="Email" value="{{isset($user)?isset($user->email)?$user->email:'':''}}">
    </div>
</div>
<div class="form-group row">
      <div class="col-12">
        <label for="profile-settings-username">Password Lama</label>
        <input type="password" class="form-control form-control-lg" id="old_password" name="old_password" placeholder="Password Lama" value="">
    </div>
</div>
<div class="form-group row">
      <div class="col-12">
        <label for="profile-settings-username">Password Baru</label>
        <input type="password" class="form-control form-control-lg" id="new_password" name="new_password" placeholder="Password Baru" value="">
    </div>
</div>
<div class="form-group row">
      <div class="col-12">
        <label for="profile-settings-username">Ketik Ulang Password Baru</label>
        <input type="password" class="form-control form-control-lg" id="re_password" name="re_password" placeholder="Re type new password" value="">
    </div>
</div>

</div>
</div>
</div>