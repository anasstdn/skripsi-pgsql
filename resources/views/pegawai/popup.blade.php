<div class="modal-dialog modal-dialog-popin" role="document">
    <form action="{{action('PegawaiController@status', $id)}}" id="form" class="js-validation" method="POST">
        {{ csrf_field() }}
    <div class="modal-content">
        <div class="block block-themed block-transparent mb-0">
            <div class="block-header bg-primary-dark">
                <h3 class="block-title">{{$title}} Pegawai</h3>
                <div class="block-options">
                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-fw fa-times"></i>
                    </button>
                </div>
            </div>
            <input type="hidden" name="mode" id="mode" value="{{$mode}}">
            <div class="block-content font-size-sm">
               <div class="form-group row">
                <div class="col-1">
                </div>
                <div class="col-8">
                    <label for="profile-settings-email">{{$mode=='nonaktifkan'?'Tanggal Resign':'Tanggal Aktivasi'}}</label>
                    <input type="text" class="js-flatpickr form-control form-control-lg bg-white" data-date-format="d-m-Y" id="tanggal" name="tanggal" placeholder="" value="">
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
                'tanggal': {
                    required: true,
                    validDate:true,
                },
            },
            messages: {
                'tanggal': {
                    required: 'Silahkan isi form',
                },
            }
        });

        $(".js-flatpickr").on("change", function (e) {  
            $(this).valid(); 
        });

});
</script>