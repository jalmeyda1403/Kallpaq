<template>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mt-4">
                    <div class="card-header">Bienvenido al Sistema Integrado de Gestión (SIG)</div>

                    <div class="card-body">
                        <div v-if="status" class="alert alert-success" role="alert">
                            {{ status }}
                        </div>

                        <p>¡Bienvenido al Sistema Integrado de Gestión (SIG)! Este sistema está diseñado para optimizar y automatizar los procesos de nuestra organización, facilitando la toma de decisiones y mejorando la eficiencia operativa.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const status = ref('');
</script>
