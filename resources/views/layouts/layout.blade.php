@php
    // session_start();
    $isLogin = false;
    if(isset($_SESSION['rapidx_user_id'])){
        $isLogin = true;
    }

    $isAuthorized = false;
    $user_level = 0;
@endphp

@if($isLogin)
    <!DOCTYPE html>
    <html>
        <head>
            <meta charset="utf-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <title>Training Record Database System v2 | @yield('title')</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="shortcut icon" type="image/png" href="{{ asset('public/images/favicon.ico') }}">

            <!-- CSS LINKS -->
            @include('shared.css_links.css_links')
            <style>
                .modal-xl-custom{
                    width: 95% !important;
                    min-width: 90% !important;
                }
            </style>
        </head>
        <body class="hold-transition sidebar-mini">
            <div class="modal" tabindex="-1" role="dialog" id="modalOnGoing">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-dark">
                            <h4 class="modal-title"><i class="fas fa-info-circle"></i>&nbsp;On-going Module</h4>
                            <button type="button" style="color: #fff;" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>This is an On-Going Feature</p>
                        </div>
                        <div class="modal-footer">

                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wrapper">
                @include('shared.pages.header')
                @include('shared.pages.nav')
                @yield('content_page')
                @include('shared.pages.footer')
            </div>

            <!-- JS LINKS -->
            @include('shared.js_links.js_links')
            @yield('js_content')
        </body>
    </html>
@else
    <script type="text/javascript">
        window.location = "../RapidX/";
    </script>
@endif
