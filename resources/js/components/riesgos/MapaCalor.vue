<template>
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0 text-primary">
                <i class="fas fa-th mr-2"></i>Mapa de Calor de Riesgos
            </h5>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-center">
                <div class="position-relative" style="width: 600px; height: 600px;">
                    <!-- Eje Y Label -->
                    <div class="position-absolute" style="left: -40px; top: 50%; transform: rotate(-90deg) translateX(50%); font-weight: bold;">
                        Probabilidad
                    </div>
                    
                    <!-- Eje X Label -->
                    <div class="position-absolute" style="bottom: -30px; left: 50%; transform: translateX(-50%); font-weight: bold;">
                        Impacto
                    </div>

                    <!-- Matriz -->
                    <div class="d-flex flex-column h-100 w-100 border">
                        <div v-for="y in 10" :key="'row-'+(11-y)" class="d-flex flex-grow-1">
                            <div v-for="x in 10" :key="'cell-'+x+'-'+(11-y)" 
                                 class="flex-grow-1 border-right border-bottom position-relative cell-hover"
                                 :class="getCellClass(x, 11-y)"
                                 :title="`Imp: ${x}, Prob: ${11-y}`">
                                
                                <!-- Riesgos en esta celda -->
                                <div v-if="getRiesgosEnCelda(x, 11-y).length > 0" 
                                     class="d-flex justify-content-center align-items-center h-100 w-100">
                                    <span class="badge badge-light border shadow-sm rounded-circle p-2" 
                                          style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-weight: bold; cursor: pointer;"
                                          @click="mostrarRiesgos(x, 11-y)">
                                        {{ getRiesgosEnCelda(x, 11-y).length }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Escalas Eje Y -->
                    <div class="position-absolute h-100 d-flex flex-column justify-content-between py-3" style="left: -20px; top: 0;">
                        <span v-for="n in 10" :key="'y-'+(11-n)" class="small">{{ 11-n }}</span>
                    </div>

                    <!-- Escalas Eje X -->
                    <div class="position-absolute w-100 d-flex justify-content-between px-3" style="bottom: -20px; left: 0;">
                        <span v-for="n in 10" :key="'x-'+n" class="small">{{ n }}</span>
                    </div>
                </div>
            </div>

            <!-- Leyenda -->
            <div class="mt-4 d-flex justify-content-center">
                <div class="mr-3"><span class="badge badge-success mr-1">&nbsp;&nbsp;</span> Bajo</div>
                <div class="mr-3"><span class="badge badge-info mr-1">&nbsp;&nbsp;</span> Medio</div>
                <div class="mr-3"><span class="badge badge-warning mr-1">&nbsp;&nbsp;</span> Alto</div>
                <div><span class="badge badge-danger mr-1">&nbsp;&nbsp;</span> Muy Alto</div>
            </div>
        </div>
    </div>

    <!-- Modal para ver riesgos de una celda -->
    <div class="modal fade" id="celdaModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Riesgos (Impacto: {{ celdaSeleccionada.x }}, Probabilidad: {{ celdaSeleccionada.y }})</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <li v-for="riesgo in riesgosCelda" :key="riesgo.id" class="list-group-item">
                            <strong>{{ riesgo.riesgo_cod }}</strong>: {{ riesgo.nombre }}
                            <br>
                            <small class="text-muted">{{ riesgo.proceso?.proceso_nombre }}</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    riesgos: {
        type: Array,
        required: true,
        default: () => []
    }
});

const celdaSeleccionada = ref({ x: 0, y: 0 });
const riesgosCelda = ref([]);

const getRiesgosEnCelda = (impacto, probabilidad) => {
    return props.riesgos.filter(r => r.impacto === impacto && r.probabilidad === probabilidad);
};

const mostrarRiesgos = (x, y) => {
    celdaSeleccionada.value = { x, y };
    riesgosCelda.value = getRiesgosEnCelda(x, y);
    $('#celdaModal').modal('show');
};

const getCellClass = (impacto, probabilidad) => {
    const valor = impacto * probabilidad;
    if (valor >= 80) return 'bg-danger-light';
    if (valor >= 48) return 'bg-warning-light';
    if (valor >= 32) return 'bg-info-light';
    return 'bg-success-light';
};
</script>

<style scoped>
.bg-danger-light { background-color: rgba(220, 53, 69, 0.2); }
.bg-warning-light { background-color: rgba(255, 193, 7, 0.2); }
.bg-info-light { background-color: rgba(23, 162, 184, 0.2); }
.bg-success-light { background-color: rgba(40, 167, 69, 0.2); }

.cell-hover:hover {
    filter: brightness(0.95);
    cursor: default;
}
</style>
