<!DOCTYPE html>
<html>
<head>
       @include('facilitador.layout.header')
</head>
<body class="sidebar-mini layout-fixed" style="height: auto;">
<div class="wrapper">

    <!-- Navbar -->
    @include('facilitador.layout.main_header')
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
   @include('facilitador.layout.main_sidebar')



        <!-- Main content -->
        <div class="content-wrapper" style="min-height: 669px;">
        @yield('content')
        <!-- /.content -->
        </div>
    <!-- /.content-wrapper -->
    @include('facilitador.layout.footer')

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
@include('facilitador.layout.footer_scripts')
</body>
</html>