<!DOCTYPE html>
<html>

<head>
    @include('layout.header')
    @vite(['resources/css/custom.css'])
    @stack('styles')
</head>

<body class="sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        @include('layout.navbar')
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        @include('layout.sidebar')


        <!-- Main content -->
        <div class="content-wrapper">
            @yield('content')
        </div>

        <!-- Control Sidebar
        <aside class="control-sidebar control-sidebar-dark">
        </aside>

         -->

        <!-- Footer -->
        <footer class="main-footer bg-secondary">
            <strong>¡Mejorando Juntos!</strong>
            <div class="float-right d-none d-sm-inline-block">
                Contraloría General de la República
            </div>
        </footer>


    </div>

    @include('layout.footer')

    @stack('scripts')

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

</body>

</html>
