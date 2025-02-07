@extends('layout.master')
@section('title', 'SIG')
@section('content')
   
<div class="row justify-content-center">
    <div class="col-md-8"> 
        <div class="card">
            <div class="card-header ui-sortable-handle">
                <h3 class="card-title">Subir Archivos</h3>
                <div class="card-tools">
                
                </div>
            </div>
        <div class="card-body">
        <form action="{{ url('sharepoint/create-folder/'.$id) }}" method="POST">
            @csrf
            <button type="submit">Create Folder</button>
        </form>

        <form action="{{ url('sharepoint/upload-file/'.$id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file">
            <button type="submit">Upload File</button>
        </form>
        </div>
    </div>
    </div>
</div>
@stop
