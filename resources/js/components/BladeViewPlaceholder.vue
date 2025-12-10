<template>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Migración en Progreso</h1>
                </div>
            </div>
        </div>
    </div>
    <div class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <p class="card-text">
                        Esta vista está siendo migrada de Blade a Vue.js.
                        <br>
                        Ruta: {{ $route.path }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
</script>
