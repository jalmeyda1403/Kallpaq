   {{-- Base Scripts --}}
   @if(!config('adminlte.enabled_laravel_mix'))
   <script src="{{ asset('vendor/adminlte/dist/plugins/jquery/jquery.min.js') }}"></script>
   <script src="{{ asset('vendor/adminlte/dist/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
   <script src="{{ asset('vendor/adminlte/dist/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
   <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
@else
   <script src="{{ mix(config('adminlte.laravel_mix_js_path', 'js/app.js')) }}"></script>
@endif

{{-- Extra Configured Plugins Scripts --}}
@include('adminlte::plugins', ['type' => 'js'])

{{-- Livewire Script --}}
@if(config('adminlte.livewire'))
   @if(app()->version() >= 7)
       @livewireScripts
   @else
       <livewire:scripts />
   @endif
@endif

{{-- Custom Scripts --}}
@yield('js')
