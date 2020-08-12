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
        <link rel="shortcut icon" href="{{asset('images/')}}/logo/logo160.png">
        <link rel="icon" type="image/png" sizes="192x192" href="{{asset('images/')}}/logo/logo160.png">
        <link rel="apple-touch-icon" sizes="180x180" href="{{asset('images/')}}/logo/logo160.png">
        <!-- END Icons -->

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
        .block.block-themed>.block-header {
            border-bottom: none;
            color: #fff;
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
        <div id="page-container">

            <!-- Main Container -->
            <main id="main-container">

                <!-- Page Content -->
                <div class="bg-image" style="background-image: url('{{asset('images/')}}/4063304.jpg');">
                    <div class="hero-static bg-white-95">
                        <div class="content">
                            <div class="row justify-content-center">
                                <div class="col-md-8 col-lg-6 col-xl-4">
                                    <!-- Sign In Block -->
                                    <div class="block block-themed block-fx-shadow mb-0">
                                        <div class="block-header">
                                            <h3 class="block-title">Masuk</h3>
                                            <div class="block-options">
                                                {{-- <a class="btn-block-option font-size-sm" href="op_auth_reminder.html">Lupa PIN?</a> --}}
                                                {{-- <a class="btn-block-option" href="op_auth_signup.html" data-toggle="tooltip" data-placement="left" title="New Account"> --}}
                                                    {{-- <i class="fa fa-user-plus"></i> --}}
                                                {{-- </a> --}}
                                            </div>
                                        </div>
                                        <div class="block-content">
                                            <div class="p-sm-3 px-lg-4 py-lg-5">
                                                <h1 class="mb-2"><img class="rounded" src="{{asset('images/')}}/logo_laravel.png" alt="Header Avatar" style="width: 300px;"></h1>
                                                <p>Selamat datang, silahkan masukkan nama pengguna dan pin.</p>

                                                <!-- Sign In Form -->
                                                <!-- jQuery Validation (.js-validation-signin class is initialized in js/pages/op_auth_signin.min.js which was auto compiled from _es6/pages/op_auth_signin.js) -->
                                                <!-- For more info and examples you can check out https://github.com/jzaefferer/jquery-validation -->
                                                <form class="js-validation-signin" action="{{ route('login') }}" method="POST">
                                                    {{ csrf_field() }}
                                                    @if(session('status'))
                                                    <div class="alert alert-success">
                                                        {{session('status')}}
                                                    </div>
                                                    @endif
                                                    @if(session('warning'))
                                                    <div class="alert alert-warning">
                                                        {{session('warning')}}
                                                    </div>
                                                    @endif
                                                    <div class="py-3">
                                                        <div class="form-group {{$errors->has('username') || $errors->has('email')?'has-error':''}}">
                                                            <input type="text" class="form-control form-control-alt form-control-lg" id="login" name="login" placeholder="Nama Pengguna atau Surel" value="{{ old('username')?:old('email') }}">
                                                            @if ($errors->has('email') || $errors->has('username'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('username')?:$errors->first('email') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>
                                                        <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                                                            <input type="password" class="form-control form-control-alt form-control-lg" id="password" name="password" placeholder="PIN">
                                                            @if ($errors->has('password'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('password') }}</strong>
                                                            </span>
                                                            @endif
                                                        </div>
                                                       {{--  <div class="form-group">
                                                            <div class="d-md-flex align-items-md-center justify-content-md-between">
                                                                <div class="custom-control custom-switch">
                                                                    <input type="checkbox" class="custom-control-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                                                    <label class="custom-control-label font-w400" for="remember">Ingat Saya</label>
                                                                </div>
                                                                <div class="py-2">
                                                                    <a class="font-size-sm" href="op_auth_reminder2.html">Lupa PIN?</a>
                                                                </div>
                                                            </div>
                                                        </div> --}}
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="col-md-6 col-xl-5">
                                                            <button type="submit" class="btn btn-block btn-success">
                                                                <i class="fa fa-fw fa-sign-in-alt mr-1"></i> Masuk
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <!-- END Sign In Form -->
                                            </div>
                                        </div>
                                    </div>
                                    <!-- END Sign In Block -->
                                </div>
                            </div>
                        </div>
                        <div class="content content-full font-size-sm text-muted text-center">
                            <strong>Anas Setyadin</strong> &copy; <span data-toggle="year-copy"></span>
                        </div>
                    </div>
                </div>
                <!-- END Page Content -->

            </main>
            <!-- END Main Container -->
        </div>
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
        <script src="{{asset('oneui/')}}/src/assets/js/plugins/jquery-validation/jquery.validate.min.js"></script>

        <!-- Page JS Code -->
        <script src="{{asset('oneui/')}}/src/assets/js/pages/op_auth_signin.min.js"></script>
        <script src="{{asset('oneui/')}}/build/toastr.min.js"></script>

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
        </script>
    </body>
</html>
