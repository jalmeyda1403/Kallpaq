@extends('layout.master')

@section('content')
    <div id="app">
        <indicadores-index></indicadores-index>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/app.js'])
@endpush
