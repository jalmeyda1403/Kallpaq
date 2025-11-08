@extends('adminlte::master')

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

@section('classes_body', $layoutHelper->makeBodyClasses())

@section('body_data', $layoutHelper->makeBodyData())

@section('body')
    <div class="wrapper">

        {{-- Preloader Animation --}}
        @if($layoutHelper->isPreloaderEnabled())
            @include('adminlte::partials.common.preloader')
        @endif

        {{-- Top Navbar --}}
        @if($layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.navbar.navbar-layout-topnav')
        @else
            @include('adminlte::partials.navbar.navbar')
        @endif

        {{-- Left Main Sidebar --}}
        @if(!$layoutHelper->isLayoutTopnavEnabled())
            @include('adminlte::partials.sidebar.left-sidebar')
        @endif

        {{-- Content Wrapper --}}
        @empty($iFrameEnabled)
            @include('adminlte::partials.cwrapper.cwrapper-default')
        @else
            @include('adminlte::partials.cwrapper.cwrapper-iframe')
        @endempty

        {{-- Footer --}}
        @hasSection('footer')
            @include('adminlte::partials.footer.footer')
        @endif

        {{-- Right Control Sidebar --}}
        @if(config('adminlte.right_sidebar'))
            @include('adminlte::partials.sidebar.right-sidebar')
        @endif

    </div>
@stop

@section('adminlte_js')
    <script>
        $(document).ready(function() {
            var currentUrl = window.location.href;

            // Loop through each link to find a match
            $('ul.nav-sidebar a.nav-link').each(function () {
                // Check for an exact match
                if (this.href === currentUrl) {
                    // Add active class to the link itself
                    $(this).addClass('active');

                    // Add active class to the parent li for proper highlighting
                    $(this).closest('li.nav-item').addClass('active');

                    // Check for and open the parent treeview
                    var treeview = $(this).parents('.has-treeview').first();
                    if (treeview.length > 0) {
                        treeview.addClass('menu-open');
                        // Also activate the main link of the treeview
                        treeview.find('> a.nav-link').addClass('active');
                    }
                }
            });
        });
    </script>
    @stack('js')
    @yield('js')
@stop
