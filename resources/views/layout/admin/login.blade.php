<!DOCTYPE html>
<html>
<head>
    <title>AOL - Admin</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('public/admin/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('public/admin/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('public/admin/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('public/admin/css/pages/auth.css') }}">
    <link href="{{ asset('public/css/sweet-alert.css') }}" rel="stylesheet" />
    <!-- plugin js -->
    <script src="{{ asset('public/admin/js/jquery-3.6.0.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/admin/js/bootstrap.bundle.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/js/sweet-alert.min.js') }}" ></script>
</head>
<body data-base-url="{{ url('/') }}">
    @yield('content')
    @include('element.admin.footer')
</body>
</html>
