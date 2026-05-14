<!DOCTYPE html>
<html lang="en" data-topbar-color="brand">

<head>
    <meta charset="utf-8" />
    <title>@yield('title', env('APP_NAME'))</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- 1. TOUS LES CSS -->
    <link href="{{ asset('assets/libs/admin-resources/jquery.vectormap/jquery-jvectormap-1.2.2.css') }}"
        rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/mohithg-switchery/switchery.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-stylesheet" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- 2. LES SCRIPTS INDISPENSABLES (Dans l'ordre critique) -->
    <!-- Config en premier -->
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <!-- jQuery (via vendor) en DEUXIÈME : Sans lui, rien ne marche -->
    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>

    <!-- Les Plugins ensuite -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/libs/parsleyjs/parsley.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
    <style>
    /* L'arrondi global pour toutes les cartes de stats */
    .custom-card-rounded {
        border-radius: 15px !important; /* Arrondi moderne sur les 4 coins */
        border: 1px solid #eef2f7 !important; /* On remet une bordure légère partout */
        overflow: hidden;
        transition: transform 0.2s ease;
    }

    /* On crée la barre de couleur à gauche sans désactiver les autres bordures */
    .border-primary-sep { border-left: 5px solid #3283f6 !important; }
    .border-warning-sep { border-left: 5px solid #f7b84b !important; }
    .border-success-sep { border-left: 5px solid #43b06e !important; }
    .border-danger-sep  { border-left: 5px solid #f15b71 !important; }

    /* Forcer les icônes en cercles parfaits */
    .rounded-circle {
        border-radius: 50% !important;
    }

    /* Effet de survol doux */
    .custom-card-rounded:hover {
        transform: translateY(-4px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
    }
</style>


</head>

<body>

    <div id="wrapper">
        @include('includes.header')
        @include('includes.sidebar')

        <div class="content-page">
            <div class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    @include('includes.right-sidebar')
    <div class="rightbar-overlay"></div>

    <!-- Initialisations du thème (peuvent rester ici) -->
    <script src="{{ asset('assets/js/app.min.js') }}"></script>

    <script src="{{ asset('assets/js/pages/form-validation.init.js') }}"></script>

    @yield('scripts')

</body>

</html>
