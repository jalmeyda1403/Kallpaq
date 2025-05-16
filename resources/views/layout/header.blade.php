    {{-- Base Meta Tags --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Custom Meta Tags --}}
    @yield('meta_tags')

    {{-- Title --}}
    <title>
        @yield('title_prefix', config('adminlte.title_prefix', ''))
        @yield('title', config('adminlte.title', 'AdminLTE 3'))
        @yield('title_postfix', config('adminlte.title_postfix', ''))
    </title>
    {{-- Custom stylesheets (pre AdminLTE) --}}
    @yield('adminlte_css_pre')


    {{-- Base Stylesheets --}}
    @if (!config('adminlte.enabled_laravel_mix'))

        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/plugins/fontawesome-free/css/all.min.css') }}">
        <link rel="stylesheet"
            href="{{ asset('vendor/adminlte/dist/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
        <!--datatables-->
        <link rel="stylesheet"
            href="{{ asset('vendor/adminlte/dist/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet"
            href="{{ asset('vendor/adminlte/dist/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
        <link rel="stylesheet"
            href="{{ asset('vendor/adminlte/dist/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <!--dropzone-->
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/plugins/dropzone/min/dropzone.min.css') }}">

        <!--Select2-->
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet"
            href="{{ asset('vendor/adminlte/dist/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">


        @if (config('adminlte.google_fonts.allowed', true))
            <link rel="stylesheet"
                href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
        @endif
        <!-- Incluir el archivo CSS de lightGallery -->
        <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/plugins/lightgallery/css/lightgallery.min.css')}}">

    @else
        <link rel="stylesheet" href="{{ mix(config('adminlte.laravel_mix_css_path', 'css/app.css')) }}">
    @endif

    {{-- Extra Configured Plugins Stylesheets --}}
    @include('adminlte::plugins', ['type' => 'css'])



    {{-- Favicon --}}

    <link rel="shortcut icon" href="{{ asset('images/kallpaq_ico.png') }}" />
