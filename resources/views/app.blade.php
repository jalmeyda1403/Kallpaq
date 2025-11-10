@extends('layout.master')

@section('content')
    <div id="app" data-spa></div>
@endsection

@push('scripts')
    @routes
@endpush