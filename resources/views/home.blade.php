@extends('layout.master')
@section('title', 'SIG')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Bienvenido al Sistema Integrado de Gestión (SIG)') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p>{{ __('¡Bienvenido al Sistema Integrado de Gestión (SIG)! Este sistema está diseñado para optimizar y automatizar los procesos de nuestra, facilitando la toma de decisiones y mejorando la eficiencia operativa.') }}</p>
                    
                    <p>
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <img src="{{ asset('images/login-icon.png') }}" alt="Iniciar sesión" style="width: 20px; height: 20px; margin-right: 5px;">
                                {{ __('Iniciar sesión') }}
                            </a>
                        @else
                            <a href="{{ route('logout') }}" class="btn btn-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <img src="{{ asset('images/logout-icon.png') }}" alt="Cerrar sesión" style="width: 20px; height: 20px; margin-right: 5px;">
                                {{ __('Cerrar sesión') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @endguest
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection