@extends('layout.master')


@section(section: 'content')
@push('styles')
    <style>
        body {
            background-color: red !important;
        }
    </style>
@endpush
    <h1>Test Page</h1>
    <p>If the background is red, then @push('styles') is working.</p>
@endsection

