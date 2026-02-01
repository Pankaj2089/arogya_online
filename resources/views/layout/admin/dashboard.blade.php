    <!DOCTYPE html>
    <html>
    <head>

        <title>AOL - Admin Dashboard</title>

        <meta charset="utf-8">

        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!--Favicons-->

        <link href="{{ asset('public/admin/images/logo/favicon.png') }}" rel="apple-touch-icon" sizes="180x180">

        <link href="{{ asset('public/admin/images/logo/favicon.png') }}" rel="icon" sizes="32x32" type="image/png">

        <link href="{{ asset('public/admin/images/logo/favicon.png') }}" rel="icon" sizes="16x16" type="image/png">

        <!-- plugin css -->

        <link rel="preconnect" href="https://fonts.gstatic.com">

        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('public/admin/vendors/choices.js/choices.min.css') }}">

        <link rel="stylesheet" href="{{ asset('public/admin/css/bootstrap.css') }}">

        <link rel="stylesheet" href="{{ asset('public/admin/vendors/iconly/bold.css') }}">

        <link rel="stylesheet" href="{{ asset('public/admin/vendors/perfect-scrollbar/perfect-scrollbar.css') }}">

        <link rel="stylesheet" href="{{ asset('public/admin/vendors/bootstrap-icons/bootstrap-icons.css') }}">

        <link rel="stylesheet" href="{{ asset('public/admin/css/app.css') }}">

        <link rel="stylesheet" href="{{ asset('public/admin/vendors/summernote/summernote-lite.min.css') }}">

        <link rel="stylesheet" href="{{ asset('public/admin/css/jquery-ui.css') }}">

        <link href="{{ asset('public/css/sweet-alert.css') }}" rel="stylesheet" />

        <!-- plugin js -->

        <script src="{{ asset('public/admin/js/jquery-3.6.0.min.js') }}" type="text/javascript"></script>

    </head>

    <body data-base-url="{{ url('/') }}">

        <div id="app">

            @include('element.admin.sidebar')

            <div id="main">

                @include('element.admin.header')

                @include('element.admin.jquery')

                @yield('content')

                @include('element.admin.footer')

            </div>

        </div>

        <script src="{{ asset('public/admin/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>

        <script src="{{ asset('public/admin/js/bootstrap.bundle.min.js') }}"></script>

        <script src="{{ asset('public/admin/vendors/summernote/summernote-lite.min.js') }}"></script>

        <script>

        $('.editorBox').summernote({

            tabsize: 2,

            height: 220,

        });

        </script>

        <script src="{{ asset('public/admin/vendors/choices.js/choices.min.js') }}"></script>

        <script src="{{ asset('public/admin/js/main.js') }}"></script>

        <script src="{{ asset('public/js/sweet-alert.min.js') }}" ></script>

        <script src="{{ asset('public/admin/js/jquery-ui.js') }}" ></script>

        <style>

            tr td [class*=" bi-"]::before{

                line-height: revert;

            }

            </style>

        </body>

    </html>