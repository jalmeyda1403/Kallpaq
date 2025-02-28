@extends('layout.master')
@section('title', 'SIG')
@section ('css')
<style type="text/css">
/* Estilo para los bloques */
.small-box {
    display: block;
    margin-bottom: 20px;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
    border-radius: 2px;
    height: 100%;
    top: 10px;
    
}

/* Estilo para el contenido interno del bloque */
.small-box .inner {
    padding: 5px;
    text-align: center;
    font-size: 10px;
}
/* Estilo para el footer del bloque */
.small-box .small-box-footer {
    position: absolute;
    bottom: 0; /* Coloca el footer al final del bloque */
    left: 0;
    width: 100%;
    background: rgba(0, 0, 0, 0.1);
    padding: 2px 0;
    color: #333;
    text-align: center;
    border-radius: 0 0 2px 2px;
}
.col-md-1 .card .clientes {
  writing-mode: vertical-rl;
  transform: rotate(180deg);
  height: 100%;
  text-align: center;
  align-content: center;
  background-color:#F7AA39;
  color: floralwhite;
  font-size: 22px;
}

.col-md-1 .card .productos{
  writing-mode: vertical-rl;
  transform: rotate(180deg);
  height: 100%;
  text-align: center;
  align-content: center;
  background-color:#676363;
  color: floralwhite;
  font-size: 22px;
}
.card-title {
   color: rgb(64, 64, 64);
  
}
</style>
@section('content')
<div class="content-header">
    <div class="container-fluid">
    <div class="row mb-2">
    <div class="col-sm-6">
    <h4 class="m-0">Mapa de Procesos de la CGR</h4>
    </div>
    <div class="col-sm-6">
    <ol class="breadcrumb float-sm-right">
    <li class="breadcrumb-item"><a href="#">Home</a></li>
    <li class="breadcrumb-item active">Mapa Procesos v2024</li>
    </ol>
    </div>
    </div>
    </div>
</div>
<div class="container-fluid">
<div class="row">
    <div class="col-md-1">
        <div class="card">           
            <div class="card-body clientes">Requisitos del Cliente <p>  <i class="fa fa-arrow-circle-left fa-2x" aria-hidden="true"></i>            
            </div>
    
        </div>
        
    </div>
    <div class="col-md-10">    
    <div class="card">
    <div class="card-header"> 
    <h2 class="card-title">Procesos Estratégicos</h2>   
    </div>
    <div class="card-body">
    <div class="row justify-content-center">
    @foreach ($procesos as $proceso)
        @if ($proceso->proceso_tipo ==="Estratégico")
        <div class="col-lg-2 col-md-2 col-sm-4 col-6">
            <div class="small-box bg-gray-dark">
                <div class="inner">
                <h6>{{$proceso->proceso_nombre}}</h6>                
                </div>
                <a href="#" class="small-box-footer" style="position:absolute;">{{$proceso->cod_proceso}} <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        @endif
    @endforeach
    </div>
    </div>
    <p>
    </div>
  
    <div class="text-center mt-3">
        <div class="circle-arrow">
            <i class="fa fa-arrow-circle-down fa-2x" aria-hidden="true"></i>
        </div>
    </div>
    <div class="card">
        <div class="card-header"> 
        <h2 class="card-title">Procesos Misionales</h2>   
        </div>
        <div class="card-body">
        <div class="row justify-content-center">
         @foreach ($procesos as $proceso)
            @if ($proceso->proceso_tipo ==="Misional")
            <div class="col-lg-2 col-md-2 col-sm-4 col-6" >
            <div class="small-box bg-danger" style="top: 10px;margin-top: 10px;">
                <div class="inner">
                <h6>{{$proceso->proceso_nombre}}</h6>
                
                </div>
                <a href="#" class="small-box-footer" style="position:absolute;">{{$proceso->cod_proceso}} <i class="fas fa-arrow-circle-right"></i></a>
            </div>
             </div>
             @endif  
        @endforeach
        </div>
        <p>
        </div>
    </div>
    
    <div class="text-center mt-3">
        <div class="circle-arrow">
            <i class="fa fa-arrow-circle-up fa-2x" aria-hidden="true"></i>
        </div>
    </div>
    <div class="card">
        <div class="card-header"> 
        <h2 class="card-title">Procesos de Apoyo</h2>   
        </div>
        <div class="card-body">
        <div class="row justify-content-center">
        @foreach ($procesos as $proceso)
            @if ($proceso->proceso_tipo ==="Apoyo")
            <div class="col-lg-2 col-md-2 col-sm-4 col-6" style="top: 10px;margin-top: 10px;">
                <div class="small-box bg-warning" style="top: 10px;">
                    <div class="inner">
                    <h6>{{$proceso->proceso_nombre}}</h6>             
                    </div>
                    <a href="#" class="small-box-footer" style="position:absolute;">{{$proceso->cod_proceso}} <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            @endif
        
        @endforeach
        </div>
        </div>
    </div>
    </div>
    <div class="col-md-1">
        <div class="card">           
            <div class="card-body productos"><i class="fa fa-arrow-circle-left fa-2x" aria-hidden="true"></i><p>Productos de la CGR           
            </div>
        </div>
    </div>
</div>

@stop
