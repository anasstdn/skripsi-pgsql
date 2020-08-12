<div class="modal-dialog modal-dialog-popin" role="document">
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
                    <div class="col-1">
                    </div>
                    <div class="col-8">
                        <label for="profile-settings-email">Config Name</label>
                        <input type="text" class="form-control form-control-lg bg-white" id="config_name" name="config_name" placeholder="" value="{{isset($data->config_name)?$data->config_name:''}}">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-1">
                    </div>
                    <div class="col-8">
                        <label for="profile-settings-email">Table Source</label>
                        <input type="text" class="form-control form-control-lg bg-white" id="table_source" name="table_source" placeholder="" value="{{isset($data->table_source)?$data->table_source:''}}">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-1">
                    </div>
                    <div class="col-8">
                        <label for="profile-settings-email">Config Values</label>
                        <input type="text" class="form-control form-control-lg bg-white" id="config_value" name="config_value" placeholder="" value="{{isset($data->config_value)?$data->config_value:''}}">
                    </div>
                </div>
                <div class="form-group row">
                   <div class="col-1">
                   </div>
                   <div class="col-8">
                    <label for="profile-settings-email">Description</label>
                    <textarea class="form-control form-control-lg bg-white" name="description" id="description">{{isset($data->description)?$data->description:''}}</textarea>
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
               'config_name': {
                    required: true,
                    minlength: 1,
                    maxlength: 100,
                },
                'table_source': {
                    required: false,
                    minlength: 1,
                    maxlength: 100,
                },
                'config_value': {
                    required: true,
                    minlength: 1,
                    maxlength: 100,
                },
                'description': {
                    required: false,
                    minlength: 1,
                },
            },
            messages: {
                'config_name': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal diisi 1',
                    maxlength: 'Karakter minimal diisi 100',
                },
                'table_source': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal diisi 1',
                    maxlength: 'Karakter minimal diisi 100',
                },
                'config_value': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal diisi 1',
                    maxlength: 'Karakter minimal diisi 100',
                },
                'description': {
                    required: 'Silahkan isi form',
                    minlength: 'Karakter minimal diisi 1',
                },
            }
        });

        $(".js-flatpickr").on("change", function (e) {  
            $(this).valid(); 
        });

});
</script>