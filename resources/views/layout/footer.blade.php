   {{-- Base Scripts --}}
   @if (!config('adminlte.enabled_laravel_mix'))
       <script src="{{ asset('vendor/adminlte/dist/plugins/jquery/jquery.min.js') }}"></script>
       <script src="{{ asset('vendor/adminlte/dist/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>




       <!--Datatables-->
       <script src="{{ asset('vendor/adminlte/dist/plugins/datatables/jquery.dataTables.min.js') }}"></script>
       <script src="{{ asset('vendor/adminlte/dist/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
       <script src="{{ asset('vendor/adminlte/dist/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}">
       </script>
       <script src="{{ asset('vendor/adminlte/dist/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}">
       </script>

       <script src="{{ asset('vendor/adminlte/dist/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
       <script src="{{ asset('vendor/adminlte/dist/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>

       <script src="{{ asset('vendor/adminlte/dist/plugins/jszip/jszip.js') }}"></script>
       <script src="{{ asset('vendor/adminlte/dist/plugins/pdfmake/pdfmake.min.js') }}"></script>
       <script src="{{ asset('vendor/adminlte/dist/plugins/pdfmake/vfs_fonts.js') }}"></script>


       <script src="{{ asset('vendor/adminlte/dist/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
       <script src="{{ asset('vendor/adminlte/dist/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
       <script src="{{ asset('vendor/adminlte/dist/plugins/datatables-buttons/js/buttons.flash.min.js') }}"></script>
       <script src="{{ asset('vendor/adminlte/dist/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
       <script src="{{ asset('vendor/adminlte/dist/js/adminlte.min.js') }}"></script>
       <!--Dropzone-->
       <script src="{{ asset('vendor/adminlte/dist/plugins/dropzone/min/dropzone.min.js') }}"></script>

       <!--Select2-->
       <script src="{{ asset('vendor/adminlte/dist/plugins/select2/js/select2.min.js') }}"></script>

       <!--Moment-->
       <script src="{{ asset('vendor/adminlte/dist/plugins/moment/moment.min.js') }}"></script>

       <!-- Incluir el archivo JS de lightGallery -->
       <script src="{{ asset('vendor/adminlte/dist/plugins/lightgallery/js/lightgallery.min.js') }}"></script>

       <!--Summernote-->
       <script src="{{ asset('vendor/adminlte/dist/plugins/summernote/summernote-bs4.min.js') }}"></script>
   @else
       <script src="{{ mix(config('adminlte.laravel_mix_js_path', 'js/app.js')) }}"></script>
   @endif
