<template>
    <div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Indicadores a Reportar (Mi OUO)</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Proceso</th>
                            <th>Indicador</th>
                            <th>Frecuencia</th>
                            <th>Meta</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="indicador in indicadores" :key="indicador.id">
                            <td>{{ indicador.proceso ? indicador.proceso.proceso_nombre : '-' }}</td>
                            <td>{{ indicador.nombre }}</td>
                            <td>{{ indicador.frecuencia }}</td>
                            <td>{{ indicador.meta }}</td>
                            <td>
                                <button class="btn btn-success btn-sm" @click="openReportModal(indicador)">
                                    <i class="fas fa-chart-line"></i> Reportar
                                </button>
                            </td>
                        </tr>
                        <tr v-if="indicadores.length === 0">
                            <td colspan="5" class="text-center">No hay indicadores pendientes de reporte.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal para Reportar -->
        <div class="modal fade" id="reportModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reportar Indicador</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" v-if="selectedIndicador">
                        <form @submit.prevent="submitReport">
                            <div class="form-group">
                                <label>Indicador:</label>
                                <p>{{ selectedIndicador.nombre }}</p>
                            </div>
                            <div class="form-group">
                                <label>Fórmula:</label>
                                <p><code>{{ selectedIndicador.formula }}</code></p>
                            </div>
                            
                            <!-- Variables Dinámicas -->
                            <div v-for="i in 6" :key="i">
                                <div class="form-group" v-if="selectedIndicador['var' + i]">
                                    <label>{{ selectedIndicador['var' + i] }} (var{{ i }})</label>
                                    <input type="number" step="any" class="form-control" v-model="reportData['var' + i]" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Resultado Calculado (Preliminar)</label>
                                <input type="text" class="form-control" :value="calculatedResult" readonly>
                            </div>

                            <button type="submit" class="btn btn-primary">Guardar Reporte</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            indicadores: [],
            selectedIndicador: null,
            reportData: {
                var1: 0, var2: 0, var3: 0, var4: 0, var5: 0, var6: 0
            }
        }
    },
    mounted() {
        this.fetchIndicadores();
    },
    computed: {
        calculatedResult() {
            // Simple eval implementation for demo purposes. 
            // In production, use a safer parser or the backend validation endpoint.
            if (!this.selectedIndicador || !this.selectedIndicador.formula) return 0;
            
            let formula = this.selectedIndicador.formula;
            for (let i = 1; i <= 6; i++) {
                const val = this.reportData['var' + i] || 0;
                formula = formula.replace(new RegExp('var' + i, 'g'), val);
            }
            
            try {
                return eval(formula); // Caution: eval is dangerous if formula is not sanitized
            } catch (e) {
                return 'Error en fórmula';
            }
        }
    },
    methods: {
        async fetchIndicadores() {
            try {
                const response = await axios.get('/api/indicadores-vue/ouo-reporte');
                this.indicadores = response.data;
            } catch (error) {
                console.error("Error fetching indicadores OUO", error);
            }
        },
        openReportModal(indicador) {
            this.selectedIndicador = indicador;
            this.reportData = { var1: 0, var2: 0, var3: 0, var4: 0, var5: 0, var6: 0 };
            $('#reportModal').modal('show');
        },
        async submitReport() {
            try {
                await axios.post(`/api/indicadores-vue/${this.selectedIndicador.id}/seguimiento`, {
                    ...this.reportData,
                    valor_calculado: this.calculatedResult
                });
                $('#reportModal').modal('hide');
                // Toast success
                alert('Reporte guardado');
            } catch (error) {
                console.error("Error saving report", error);
            }
        }
    }
}
</script>
