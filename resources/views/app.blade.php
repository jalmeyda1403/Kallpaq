<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kallpaq</title>

    <link rel="shortcut icon" href="{{ asset('images/kallpaq_ico.png') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('images/kallpaq_ico.png') }}" type="image/x-icon">

    <!-- AdminLTE & Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('vendor/adminlte/dist/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <!-- Fonts -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">

    @vite(['resources/css/custom.css', 'resources/js/app.js'])
</head>

<body class="sidebar-mini layout-fixed">
    <div id="app" data-spa></div>

    <script>
        window.App = {
            @auth
            @php
                /** @var \App\Models\User $user */
                $user = Auth::user();
            @endphp
            user: @json($user->toArrayWithRoles())
        @else
            user: null
        @endauth
        };
    </script>

    <!-- AdminLTE & Plugins JS -->
    <script src="{{ asset('vendor/adminlte/dist/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>

    @routes
</body>

</html>
