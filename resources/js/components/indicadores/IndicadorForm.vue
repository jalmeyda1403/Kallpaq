<template>
    <div>
        <form @submit.prevent="submitForm">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Proceso</label>
                        <input type="text" class="form-control" :value="proceso.proceso_nombre" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nombre del Indicador</label>
                        <input type="text" class="form-control" v-model="form.nombre" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Descripción</label>
                <textarea class="form-control" v-model="form.descripcion" rows="2"></textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Frecuencia</label>
                        <select class="form-control" v-model="form.frecuencia" required>
                            <option value="Mensual">Mensual</option>
                            <option value="Trimestral">Trimestral</option>
                            <option value="Semestral">Semestral</option>
                            <option value="Anual">Anual</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Meta</label>
                        <input type="number" step="0.01" class="form-control" v-model="form.meta" required>
                    </div>
                </div>
            </div>

            <hr>
            <h5>Definición de Fórmula</h5>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Fórmula (use var1, var2, etc.)</label>
                        <div class="input-group">
                            <input type="text" class="form-control" v-model="form.formula" placeholder="Ej: (var1 / var2) * 100">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" @click="testFormula">Probar</button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Defina las variables abajo.</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4" v-for="i in 6" :key="i">
                    <div class="form-group">
                        <label>Variable {{ i }} (var{{ i }})</label>
                        <input type="text" class="form-control" v-model="form['var' + i]" :placeholder="'Nombre de var' + i">
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12 text-right">
                    <button type="button" class="btn btn-secondary mr-2" @click="$emit('cancel')">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        proceso: {
            type: Object,
            required: true
        },
        indicadorData: {
            type: Object,
            default: null
        }
    },
    data() {
        return {
            form: {
                nombre: '',
                descripcion: '',
                frecuencia: 'Mensual',
                meta: 0,
                formula: '',
                var1: '', var2: '', var3: '', var4: '', var5: '', var6: ''
            }
        }
    },
    mounted() {
        if (this.indicadorData) {
            this.form = { ...this.indicadorData };
        }
    },
    methods: {
        testFormula() {
            // Simple test logic
            alert('Funcionalidad de prueba pendiente de implementación completa. Asegúrese de que la fórmula sea válida JS.');
        },
        async submitForm() {
            try {
                const payload = {
                    ...this.form,
                    proceso_id: this.proceso.id
                };

                if (this.indicadorData) {
                    await axios.put(`/api/indicadores-vue/${this.indicadorData.id}`, payload);
                } else {
                    await axios.post('/api/indicadores-vue', payload);
                }

                // Toast success
                this.$emit('saved');
            } catch (error) {
                console.error("Error saving indicador", error);
                alert('Error al guardar el indicador');
            }
        }
    }
}
</script>
