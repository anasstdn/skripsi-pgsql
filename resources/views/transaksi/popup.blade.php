<div class="modal-dialog modal-dialog-popin modal-xl" role="document">
    <form action="{{$url}}" id="form-config" method="POST">
        {{ csrf_field() }}
    <div class="modal-content">
        <div class="block block-themed block-transparent mb-0">
            <div class="block-header bg-primary-dark">
                <h3 class="block-title">{{$title}}</h3>
                <div class="block-options">
                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-fw fa-times"></i>
                    </button>
                </div>
            </div>
            <input type="hidden" name="mode" id="mode" value="{{$mode}}">
            <div class="block-content font-size-sm">
               <div class="form-group row">
                    <div class="col-2">
                        <label for="profile-settings-email">Tanggal Transaksi</label>
                        <input type="text" class="js-flatpickr form-control form-control-lg bg-white" id="tgl_transaksi" name="tgl_transaksi" placeholder="" data-date-format="d-m-Y" value="{{isset($data->tgl_transaksi)?date('d-m-Y',strtotime($data->tgl_transaksi)):''}}">
                    </div>
                    <div class="col-2">
                        <label for="profile-settings-email">No Nota</label>
                        <input type="text" class="form-control form-control-lg bg-white" id="no_nota" name="no_nota" placeholder="" value="{{isset($data->no_nota)?$data->no_nota:''}}">
                    </div>
                    <div class="col-1">
                        <label for="profile-settings-email">Pasir</label>
                        <input type="text" class="form-control form-control-lg bg-white" id="pasir" name="pasir" placeholder="" value="{{isset($data->pasir)?$data->pasir:''}}">
                    </div>
                    <div class="col-1">
                        <label for="profile-settings-email">Gendol</label>
                        <input type="text" class="form-control form-control-lg bg-white" id="gendol" name="gendol" placeholder="" value="{{isset($data->gendol)?$data->gendol:''}}">
                    </div>
                    <div class="col-1">
                        <label for="profile-settings-email">Abu</label>
                        <input type="text" class="form-control form-control-lg bg-white" id="abu" name="abu" placeholder="" value="{{isset($data->abu)?$data->abu:''}}">
                    </div>
                    <div class="col-1">
                        <label for="profile-settings-email">Split 2/3</label>
                        <input type="text" class="form-control form-control-lg bg-white" id="split2_3" name="split2_3" placeholder="" value="{{isset($data->split2_3)?$data->split2_3:''}}">
                    </div>
                    <div class="col-1">
                        <label for="profile-settings-email">Split 1/2</label>
                        <input type="text" class="form-control form-control-lg bg-white" id="split1_2" name="split1_2" placeholder="" value="{{isset($data->split1_2)?$data->split1_2:''}}">
                    </div>
                    <div class="col-1">
                        <label for="profile-settings-email">LPA</label>
                        <input type="text" class="form-control form-control-lg bg-white" id="lpa" name="lpa" placeholder="" value="{{isset($data->lpa)?$data->lpa:''}}">
                    </div>
                    <div class="col-2">
                        <label for="profile-settings-email">Campur?</label>
                        <div class="custom-control custom-checkbox custom-control-lg mb-1">
                            <input type="checkbox" class="custom-control-input" id="campur" value="Y" name="campur" {{isset($data->campur)?$data->campur=='Y'?'checked':'':''}}>
                            <label class="custom-control-label" for="campur"></label>
                        </div>
                    </div>
                </div>
            <div class="block-content block-content-full text-right border-top">
                <button type="submit" class="btn btn-sm btn-primary" id="simpan"><i class="fa fa-check mr-1"></i>Simpan</button>
                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</form>
</div>

<script>jQuery(function(){ One.helpers(['flatpickr']); });</script>
<script type="text/javascript">
    $(function() {
        One.helpers('validation');

        $.validator.addMethod("validDate", function(value, element) {
            return this.optional(element) || moment(value,"DD-MM-YYYY").isValid();
        }, "Entry true date format, exp: DD-MM-YYYY");

        $('#form-config').validate({
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
               'tgl_transaksi': {
                    required: true,
                    validDate:true,
                },
            },
            messages: {
                'tgl_transaksi': {
                    required: 'Silahkan isi form',
                },
            }
        });

        $(".js-flatpickr").on("change", function (e) {  
            $(this).valid(); 
        });

});
</script>