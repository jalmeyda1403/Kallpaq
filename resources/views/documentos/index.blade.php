@extends('layout.master')
@section('title', 'SIG')
@push('styles')
    @endpush
@section('content')
    <div id="app">
        <router-view></router-view>
    </div>
@endsection
@push('scripts')
    @routes
    @vite(['resources/js/app.js'])
    <script>
        // Los scripts de jQuery y DataTables aún pueden funcionar aquí
        // si se cargan globalmente, pero la lógica de navegación
        // con los botones ahora es manejada por Vue Router
    </script>
@endpush