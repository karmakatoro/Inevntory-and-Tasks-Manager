<!DOCTYPE html>
<html lang="en" data-topbar-color="brand">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', env('APP_NAME'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- App css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-stylesheet" />

    <!-- icons -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Theme Config Js -->
    <script src="{{ asset('assets/js/config.js') }}"></script>


</head>

<body class="loading">

    <div class="account-pages mt-5 mb-5">
        <div class="container">
            <div class="row justify-content-center">
                @yield('content')
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->
    <!-- Vendor js -->
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.min.js') }}"></script>

</body>

</html>
