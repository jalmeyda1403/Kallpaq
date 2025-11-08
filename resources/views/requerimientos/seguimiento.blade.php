@extends('layout.master')
@section('title', 'SIG')
@push('styles')
    <style>
        .table-requerimientos {
            font-size: 13px;
        }
    </style>
@endpush
@section('content')
    <div class="container-fluid" id="app">

        <div class="row">
            <div class="col-md-12 mt-4">
                <resumen-general></resumen-general>
            </div>

            <div class="col-md-6">
                <div class="h-100 d-flex flex-column">
                    <resumen-grafico></resumen-grafico>
                </div>
            </div>
            <div class="col-md-6">
                <div class="h-100 d-flex flex-column">
                    <resumen-alertas></resumen-alertas>
                </div>
            </div>

            <div class="col-md-12 mt-4">
                <resumen-especialistas></resumen-especialistas>
            </div>
        </div>

        <detalle-especialista-modal></detalle-especialista-modal>

    </div>
@endsection
