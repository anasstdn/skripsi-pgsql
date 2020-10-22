<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

        <title>Laravel Forecast</title>

        <meta name="description" content="OneUI - Bootstrap 4 Admin Template &amp; UI Framework created by pixelcave and published on Themeforest">
        <meta name="author" content="pixelcave">
        <meta name="robots" content="noindex, nofollow">

        <!-- Open Graph Meta -->
        <meta property="og:title" content="OneUI - Bootstrap 4 Admin Template &amp; UI Framework">
        <meta property="og:site_name" content="OneUI">
        <meta property="og:description" content="OneUI - Bootstrap 4 Admin Template &amp; UI Framework created by pixelcave and published on Themeforest">
        <meta property="og:type" content="website">
        <meta property="og:url" content="">
        <meta property="og:image" content="">

        <!-- Icons -->
        <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->

        <link rel="shortcut icon" href="{{asset('oneui/')}}/src/assets/media/favicons/favicon.png">
        <link rel="icon" type="image/png" sizes="192x192" href="{{asset('oneui/')}}/src/assets/media/favicons/favicon-192x192.png">
        <link rel="apple-touch-icon" sizes="180x180" href="{{asset('oneui/')}}/src/assets/media/favicons/apple-touch-icon-180x180.png">
        <!-- END Icons -->

        <link rel="stylesheet" href="{{asset('oneui/')}}/src/assets/js/plugins/datatables/dataTables.bootstrap4.css">
        <link rel="stylesheet" href="{{asset('oneui/')}}/src/assets/js/plugins/datatables/buttons-bs4/buttons.bootstrap4.min.css">
        <link rel="stylesheet" href="{{asset('oneui/')}}/src/assets/js/plugins/select2/css/select2.min.css">
        <link rel="stylesheet" href="{{asset('oneui/')}}/src/assets/js/plugins/sweetalert2/sweetalert2.min.css">
        <link rel="stylesheet" href="{{asset('oneui/')}}/src/assets/js/plugins/flatpickr/flatpickr.min.css">

        <!-- Stylesheets -->
        <!-- Fonts and OneUI framework -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400italic,600,700%7COpen+Sans:300,400,400italic,600,700">
        <link rel="stylesheet" id="css-main" href="{{asset('oneui/')}}/src/assets/css/oneui.min.css">
        <link href="{{asset('oneui/')}}/build/toastr.css" rel="stylesheet" type="text/css" />

        <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
        <!-- <link rel="stylesheet" id="css-theme" href="assets/css/themes/amethyst.min.css"> -->
        <!-- END Stylesheets -->
    </head>
    <style>
      .dataTables_wrapper .dataTables_processing {
        position: absolute;
        top: 30%;
        left: 50%;
        width: 30%;
        height: 40px;
        margin-left: -20%;
        margin-top: -25px;
        padding-top: 20px;
        text-align: center;
        font-size: 1.2em;
        background-color:lightyellow;
        font-family: sans-serif;
        font-size:10pt;
    }

    .ajax-loader{
        position:fixed;
        top:0px;
        right:0px;
        width:100%;
        height:auto;
        background-color:#A9A9A9;
        background-repeat:no-repeat;
        background-position:center;
        z-index:10000000;
        opacity: 0.7;
        filter: alpha(opacity=40);
    }
    #page-container.page-header-dark #page-header{
        color:#d6d6d6;
        background-color: #4caf50;
    }
</style>
    <body>
        <!-- Page Container -->
        <!--
            Available classes for #page-container:

        GENERIC

            'enable-cookies'                            Remembers active color theme between pages (when set through color theme helper Template._uiHandleTheme())

        SIDEBAR & SIDE OVERLAY

            'sidebar-r'                                 Right Sidebar and left Side Overlay (default is left Sidebar and right Side Overlay)
            'sidebar-mini'                              Mini hoverable Sidebar (screen width > 991px)
            'sidebar-o'                                 Visible Sidebar by default (screen width > 991px)
            'sidebar-o-xs'                              Visible Sidebar by default (screen width < 992px)
            'sidebar-dark'                              Dark themed sidebar

            'side-overlay-hover'                        Hoverable Side Overlay (screen width > 991px)
            'side-overlay-o'                            Visible Side Overlay by default

            'enable-page-overlay'                       Enables a visible clickable Page Overlay (closes Side Overlay on click) when Side Overlay opens

            'side-scroll'                               Enables custom scrolling on Sidebar and Side Overlay instead of native scrolling (screen width > 991px)

        HEADER

            ''                                          Static Header if no class is added
            'page-header-fixed'                         Fixed Header

        HEADER STYLE

            ''                                          Light themed Header
            'page-header-dark'                          Dark themed Header

        MAIN CONTENT LAYOUT

            ''                                          Full width Main Content if no class is added
            'main-content-boxed'                        Full width Main Content with a specific maximum width (screen width > 1200px)
            'main-content-narrow'                       Full width Main Content with a percentage width (screen width > 1200px)
        -->
        <div id="page-container" class="sidebar-o enable-page-overlay side-scroll page-header-fixed page-header-dark">

            @include('layouts.aside')
            
            @include('layouts.sidebar')

            @include('layouts.header')

            <!-- Main Container -->
            <main id="main-container">
                <div class="ajax-loader text-center" style="display:none">
                  <div class="progress">
                    <div class="progress-bar progress-bar-striped active" aria-valuenow="100" aria-valuemin="1000"
                    aria-valuemax="100" style="width: 100%;" id="loader" role="progressbar">
                </div>
            </div>
            <div id="" style="font-size:11pt;font-family: sans-serif;color: white">Silahkan tunggu sesaat...</div>
        </div>
            <meta name="csrf-token" content="{{ csrf_token() }}">
            @yield('content')
            </main>
            <!-- END Main Container -->

            <!-- Footer -->
            <footer id="page-footer" class="bg-body-light">
                <div class="content py-3">
                    <div class="row font-size-sm">
                        <div class="col-sm-6 order-sm-2 py-1 text-center text-sm-right">
                            {{-- Crafted with <i class="fa fa-heart text-danger"></i> by <a class="font-w600" href="https://1.envato.market/ydb" target="_blank">pixelcave</a> --}}
                            <a class="font-w600" href="#" target="_blank">Anas Setyadin</a>
                        </div>
                        <div class="col-sm-6 order-sm-1 py-1 text-center text-sm-left">
                            <a class="font-w600" href="https://1.envato.market/xWy" target="_blank">Laravel Forecast Version 1.0 </a> &copy; <span data-toggle="year-copy"></span>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- END Footer -->

            <!-- Apps Modal -->
            <!-- Opens from the modal toggle button in the header -->
            <div class="modal fade" id="one-modal-apps" tabindex="-1" role="dialog" aria-labelledby="one-modal-apps" aria-hidden="true">
                <div class="modal-dialog modal-dialog-top modal-sm" role="document">
                    <div class="modal-content">
                        <div class="block block-themed block-transparent mb-0">
                            <div class="block-header bg-primary-dark">
                                <h3 class="block-title">Apps</h3>
                                <div class="block-options">
                                    <button type="button" class="btn-block-option" data-dismiss="modal" aria-label="Close">
                                        <i class="si si-close"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="block-content block-content-full">
                                <div class="row gutters-tiny">
                                    <div class="col-6">
                                        <!-- CRM -->
                                        <a class="block block-rounded block-themed bg-default" href="javascript:void(0)">
                                            <div class="block-content text-center">
                                                <i class="si si-speedometer fa-2x text-white-75"></i>
                                                <p class="font-w600 font-size-sm text-white mt-2 mb-3">
                                                    CRM
                                                </p>
                                            </div>
                                        </a>
                                        <!-- END CRM -->
                                    </div>
                                    <div class="col-6">
                                        <!-- Products -->
                                        <a class="block block-rounded block-themed bg-danger" href="javascript:void(0)">
                                            <div class="block-content text-center">
                                                <i class="si si-rocket fa-2x text-white-75"></i>
                                                <p class="font-w600 font-size-sm text-white mt-2 mb-3">
                                                    Products
                                                </p>
                                            </div>
                                        </a>
                                        <!-- END Products -->
                                    </div>
                                    <div class="col-6">
                                        <!-- Sales -->
                                        <a class="block block-rounded block-themed bg-success mb-0" href="javascript:void(0)">
                                            <div class="block-content text-center">
                                                <i class="si si-plane fa-2x text-white-75"></i>
                                                <p class="font-w600 font-size-sm text-white mt-2 mb-3">
                                                    Sales
                                                </p>
                                            </div>
                                        </a>
                                        <!-- END Sales -->
                                    </div>
                                    <div class="col-6">
                                        <!-- Payments -->
                                        <a class="block block-rounded block-themed bg-warning mb-0" href="javascript:void(0)">
                                            <div class="block-content text-center">
                                                <i class="si si-wallet fa-2x text-white-75"></i>
                                                <p class="font-w600 font-size-sm text-white mt-2 mb-3">
                                                    Payments
                                                </p>
                                            </div>
                                        </a>
                                        <!-- END Payments -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END Apps Modal -->
        </div>
        <!-- END Page Container -->

        <!--
            OneUI JS Core

            Vital libraries and plugins used in all pages. You can choose to not include this file if you would like
            to handle those dependencies through webpack. Please check out assets/_es6/main/bootstrap.js for more info.

            If you like, you could also include them separately directly from the assets/js/core folder in the following
            order. That can come in handy if you would like to include a few of them (eg jQuery) from a CDN.

            assets/js/core/jquery.min.js
            assets/js/core/bootstrap.bundle.min.js
            assets/js/core/simplebar.min.js
            assets/js/core/jquery-scrollLock.min.js
            assets/js/core/jquery.appear.min.js
            assets/js/core/js.cookie.min.js
        -->
        <script src="{{asset('oneui/')}}/src/assets/js/oneui.core.min.js"></script>

        <!--
            OneUI JS

            Custom functionality including Blocks/Layout API as well as other vital and optional helpers
            webpack is putting everything together at assets/_es6/main/app.js
        -->
        <script src="{{asset('oneui/')}}/src/assets/js/oneui.app.min.js"></script>

         <!-- Page JS Plugins -->
        <script src="{{asset('oneui/')}}/src/assets/js/plugins/datatables/jquery.dataTables.min.js"></script>
        <script src="{{asset('oneui/')}}/src/assets/js/plugins/datatables/dataTables.bootstrap4.min.js"></script>
        <script src="{{asset('oneui/')}}/src/assets/js/plugins/datatables/buttons/dataTables.buttons.min.js"></script>
        <script src="{{asset('oneui/')}}/src/assets/js/plugins/datatables/buttons/buttons.print.min.js"></script>
        <script src="{{asset('oneui/')}}/src/assets/js/plugins/datatables/buttons/buttons.html5.min.js"></script>
        <script src="{{asset('oneui/')}}/src/assets/js/plugins/datatables/buttons/buttons.flash.min.js"></script>
        <script src="{{asset('oneui/')}}/src/assets/js/plugins/datatables/buttons/buttons.colVis.min.js"></script>

        <script src="{{asset('oneui/')}}/src/assets/js/plugins/select2/js/select2.full.min.js"></script>
        <script src="{{asset('oneui/')}}/src/assets/js/plugins/jquery-validation/jquery.validate.min.js"></script>
        <script src="{{asset('oneui/')}}/src/assets/js/plugins/jquery-validation/additional-methods.js"></script>
        <script src="{{asset('oneui/')}}/build/toastr.min.js"></script>

        <script src="{{asset('oneui/')}}/src/assets/js/pages/be_tables_datatables.min.js"></script>

        <script src="{{asset('oneui/')}}/src/assets/js/plugins/bootstrap-notify/bootstrap-notify.min.js"></script>

        <script src="{{asset('oneui/')}}/src/assets/js/plugins/es6-promise/es6-promise.auto.min.js"></script>
        <script src="{{asset('oneui/')}}/src/assets/js/plugins/sweetalert2/sweetalert2.min.js"></script>

        <script src="{{asset('oneui/')}}/src/assets/js/pages/be_comp_dialogs.min.js"></script>

        <script src="{{asset('oneui/')}}/src/assets/js/plugins/flatpickr/flatpickr.min.js"></script>

        <script src="{{asset('oneui/')}}/src/assets/apexcharts/apexcharts.js"></script>
        <script src="{{asset('oneui/')}}/src/assets/apexcharts/apex-custom-script.js"></script>

        <script src="{{asset('oneui/')}}/src/assets/js/plugins/moment/moment.min.js"></script>

        <script>jQuery(function(){ One.helpers(['flatpickr']); });</script>

        <script type="text/javascript">
            @if ($errors->any())
            @foreach ($errors->all() as $error)
            toastr_notif("{!! $error !!}","gagal");
            @endforeach
            @endif
            @if(Session::get('messageType'))
            toastr_notif("{!! Session::get('message') !!}","{!! Session::get('messageType') !!}");
            <?php
            Session::forget('messageType');
            Session::forget('message');
            ?>
            @endif

            function bootstrap_toast(pesan,type)
            {
                if(type=='sukses')
                {
                    One.helpers('notify', {type: 'success', icon: 'fa fa-check mr-1', message: pesan});
                }
                else
                {
                    One.helpers('notify', {type: 'warning', icon: 'fa fa-exclamation mr-1', message: pesan});
                }
            }

            function toastr_notif(message,type)
            {
              if(type=='sukses')
              {
                var opts = {
                  "closeButton": true,
                  "debug": false,
                  "positionClass": "toast-top-right",
                  "onclick": null,
                  "showDuration": "300",
                  "hideDuration": "1000",
                  "timeOut": "5000",
                  "extendedTimeOut": "1000",
                  "showEasing": "swing",
                  "hideEasing": "linear",
                  "showMethod": "fadeIn",
                  "hideMethod": "fadeOut"
              };
              toastr.success(message, "Berhasil", opts);
          }
          else
          {
            var opts = {
              "closeButton": true,
              "debug": false,
              "positionClass": "toast-top-right",
              "onclick": null,
              "showDuration": "300",
              "hideDuration": "1000",
              "timeOut": "5000",
              "extendedTimeOut": "1000",
              "showEasing": "swing",
              "hideEasing": "linear",
              "showMethod": "fadeIn",
              "hideMethod": "fadeOut"
          };
          toastr.warning(message, 'Peringatan', opts);
      }
  }

  function show_modal(url) { 
  $.ajax({
    url:url,
    dataType: 'text',
    success: function(data) {
      $("#formModal").html(data);
      $("#formModal").modal('show');
      }
    });
};

  var tday=["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"];
  var tmonth=["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];

  function GetClock(){
    var d=new Date();
    var nday=d.getDay(),nmonth=d.getMonth(),ndate=d.getDate(),nyear=d.getFullYear();
    var nhour=d.getHours(),nmin=d.getMinutes(),nsec=d.getSeconds();
    if(nmin<=9) nmin="0"+nmin;
    if(nsec<=9) nsec="0"+nsec;

    var clocktext=""+tday[nday]+", "+tmonth[nmonth]+" "+ndate+" "+nyear+" "+nhour+":"+nmin+":"+nsec+"";
    document.getElementById('clockbox').innerHTML=clocktext;
}

GetClock();
setInterval(GetClock,1000);

</script>

<script type="text/javascript">
    function hapus(url)
    {
        let toast = Swal.mixin({
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-success m-1',
                cancelButton: 'btn btn-danger m-1',
                input: 'form-control'
            }
        });

       var token = $("meta[name='csrf-token']").attr("content");
       toast.fire({
        title: 'Hapus Data!',
          text: 'Apakah anda yakin akan menghapus data?',
          type: 'warning',
          showCancelButton: true,
          customClass: {
            confirmButton: 'btn btn-danger m-1',
            cancelButton: 'btn btn-secondary m-1'
        },
        confirmButtonText: 'Yes',
        html: false,
      })
       .then(result => {
          if (result.value) {
              $.ajax({
                url : url,
                type: 'GET',
                headers: {
                  'X-CSRF-TOKEN': token
              },
              success:function(){
                toast.fire('Data berhasil dihapus!', ' ', 'success');

                  setTimeout(function() {
                      location.reload();
                  }, 1000);
              },
          });
          } else if (result.dismiss === 'cancel') {
              toast.fire("Data batal dihapus!");
              setTimeout(function() {
                      location.reload();
                }, 1000);
          }
      });
    }

    function reset(url)
    {
        let toast = Swal.mixin({
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-success m-1',
                cancelButton: 'btn btn-danger m-1',
                input: 'form-control'
            }
        });

       var token = $("meta[name='csrf-token']").attr("content");
       toast.fire({
        title: 'Reset Password!',
          text: 'Apakah anda yakin?',
          type: 'warning',
          showCancelButton: true,
          customClass: {
            confirmButton: 'btn btn-danger m-1',
            cancelButton: 'btn btn-secondary m-1'
        },
        confirmButtonText: 'Yes',
        html: false,
      })
       .then(result => {
          if (result.value) {
              $.ajax({
                url : url,
                type: 'GET',
                headers: {
                  'X-CSRF-TOKEN': token
              },
              success:function(){
                toast.fire('Password berhasil direset!', ' ', 'success');

                  setTimeout(function() {
                      location.reload();
                  }, 1000);
              },
          });
          } else if (result.dismiss === 'cancel') {
              toast.fire("Password batal direset!");
              setTimeout(function() {
                      location.reload();
                }, 1000);
          }
      });
   }

   function restore(url)
   {
    let toast = Swal.mixin({
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-success m-1',
            cancelButton: 'btn btn-danger m-1',
            input: 'form-control'
        }
    });

    var token = $("meta[name='csrf-token']").attr("content");
    toast.fire({
        title: 'Restore Data!',
        text: 'Apakah anda yakin?',
        type: 'warning',
        showCancelButton: true,
        customClass: {
            confirmButton: 'btn btn-danger m-1',
            cancelButton: 'btn btn-secondary m-1'
        },
        confirmButtonText: 'Yes',
        html: false,
    })
    .then(result => {
      if (result.value) {
          $.ajax({
            url : url,
            type: 'GET',
            headers: {
              'X-CSRF-TOKEN': token
          },
          success:function(){
            toast.fire('Data berhasil direstore!', ' ', 'success');

            setTimeout(function() {
              location.reload();
          }, 1000);
        },
    });
      } else if (result.dismiss === 'cancel') {
          toast.fire("Data batal direstore!");
          setTimeout(function() {
              location.reload();
          }, 1000);
      }
  });
}

   function nonaktifkan(url,status_aktif)
    {
        var title;
        var msg;
        var msg_alert_success;
        var msg_alert_failed;
        if(status_aktif==true)
        {
            title='Nonaktifkan User';
            msg='Apakah anda yakin akan menonaktifkan user?';
            msg_alert_success='User berhasil dinonaktifkan';
            msg_alert_failed='User batal dinonaktifkan';
        }
        else
        {
            title='Aktifkan User';
            msg='Apakah anda yakin akan mengaktifkan user?';
            msg_alert_success='User berhasil diaktifkan';
            msg_alert_failed='User batal diaktifkan';
        }

        let toast = Swal.mixin({
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-success m-1',
                cancelButton: 'btn btn-danger m-1',
                input: 'form-control'
            }
        });

       var token = $("meta[name='csrf-token']").attr("content");
       toast.fire({
        title: title,
          text: msg,
          type: 'warning',
          showCancelButton: true,
          customClass: {
            confirmButton: 'btn btn-danger m-1',
            cancelButton: 'btn btn-secondary m-1'
        },
        confirmButtonText: 'Yes',
        html: false,
      })
       .then(result => {
          if (result.value) {
              $.ajax({
                url : url,
                type: 'GET',
                headers: {
                  'X-CSRF-TOKEN': token
              },
              success:function(){
                toast.fire(msg_alert_success, ' ', 'success');
                setTimeout(function() {
                      location.reload();
                }, 1000);
              },
          });
          } else if (result.dismiss === 'cancel') {
              toast.fire(msg_alert_failed);
              setTimeout(function() {
                      location.reload();
                }, 1000);
          }
      });
   }
</script>

<script>
    function uploadProgressHandler(event) {
        var percent = (event.loaded / event.total) * 100;
        var progress = Math.round(percent);
        $("#percent").html(progress + "%");
        $(".progress-bar").css("width", progress + "%");
        $("#status").html(progress + "% uploaded... please wait");
    }

    function loadHandler(event) {
        $("#status").html('Load Completed');
        setTimeout(function(){
          $('.ajax-loader').fadeOut();
          $("#percent").html("0%");
          $(".progress-bar").css("width", "100%");
      }, 500);
    }

    function errorHandler(event) {
        $("#status").html("Send Data Failed");
    }

    function abortHandler(event) {
        $("#status").html("Send Data Aborted");
    }
</script>

        @yield('js')  
        @stack('js')
    </body>
</html>
