<template>
    <div class="evaluation-container">

        <div v-if="!readOnly">
            <p class="text-muted small mb-4">
                Complete la siguiente rúbrica para determinar la criticidad de la obligación.
                El nivel de criticidad determinará los requisitos de control necesarios.
            </p>

            <div v-for="(settings, key) in rubricaDefinitions" :key="key" class="mb-4">
                <label class="font-weight-bold text-dark">{{ settings.label }}</label>
                <p class="small text-muted mb-2">{{ settings.description }}</p>

                <select class="form-control custom-select" v-model.number="form[key]">
                    <option :value="null" disabled>Seleccione una opción...</option>
                    <option v-for="opcion in settings.options" :key="opcion.value" :value="opcion.value">
                        {{ opcion.label }} ({{ opcion.value }} pts)
                    </option>
                </select>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <div style="flex: 1; margin-right: 20px;">
                    <div class="p-2 rounded d-flex justify-content-between align-items-center" :class="previewClass">
                        <span class="font-weight-bold ml-2">NIVEL: {{ (nivelEstimado ||
                            '').replace('_', '').toUpperCase() }}</span>
                        <span class="small font-weight-bold mr-2">({{ puntajeTotal }} PTS)</span>
                    </div>
                </div>
                <div>
                    <button class="btn btn-danger shadow-sm" @click="saveEvaluation" :disabled="loading || !isComplete">
                        <i class="fas fa-save mr-1"></i> Guardar Evaluación
                    </button>
                </div>
            </div>
        </div>

        <!-- Read Only View -->
        <div v-else class="alert alert-light border">
            <h6 class="alert-heading font-weight-bold">Evaluación Completada</h6>
            <hr>
            <div class="d-flex justify-content-between align-items-center p-3 rounded" :class="previewClass">
                <strong>NIVEL CRITICIDAD: {{ localEvaluation?.oe_nivel_criticidad?.toUpperCase() }}</strong>
                <strong>PUNTAJE: {{ localEvaluation?.oe_puntaje_total }}</strong>
            </div>
            <p class="mt-3 small text-muted">La evaluación no se puede modificar en el estado actual.</p>
        </div>

    </div>
</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    props: {
        obligacionId: {
            required: true
        },
        evaluation: {
            type: Object,
            default: null
        },
        readOnly: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            loading: false,
            localEvaluation: this.evaluation,
            form: {
                impacto_legal: null,
                impacto_institucional: null,
                alcance: null,
                frecuencia: null,
                exigibilidad: null
            },
            rubricaDefinitions: {
                impacto_legal: {
                    label: 'Impacto Legal',
                    description: 'Consecuencias legales ante incumplimiento (Multas, sanciones penales, clausuras).',
                    options: [
                        { value: 1, label: 'Bajo: Observación administrativa leve' },
                        { value: 2, label: 'Medio: Multa menor (< 1 UIT)' },
                        { value: 3, label: 'Alto: Multa significativa o suspensión temporal' },
                        { value: 4, label: 'Muy Alto: Multa grave, clausura, responsabilidad penal' },
                        { value: 5, label: 'Crítico: Cierre definitivo, responsabilidad penal de alta dirección' }
                    ]
                },
                impacto_institucional: {
                    label: 'Impacto Institucional / Reputacional',
                    description: 'Efecto en la imagen, confianza de stakeholders y continuidad operativa.',
                    options: [
                        { value: 1, label: 'Bajo: Impacto interno mínimo' },
                        { value: 2, label: 'Medio: Quejas aisladas de clientes' },
                        { value: 3, label: 'Alto: Cobertura mediática local negativa, pérdida de clientes importantes' },
                        { value: 4, label: 'Muy Alto: Escándalo nacional, pérdida masiva de confianza' },
                        { value: 5, label: 'Crítico: Daño irreparable a la marca' }
                    ]
                },
                alcance: {
                    label: 'Alcance',
                    description: 'Extensión de la obligación dentro de la organización.',
                    options: [
                        { value: 1, label: 'Puntual: Un solo puesto o tarea específica' },
                        { value: 2, label: 'Limitado: Un equipo o sub-área' },
                        { value: 3, label: 'Amplio: Un área completa o proceso crítico' },
                        { value: 4, label: 'Muy Amplio: Múltiples áreas o procesos transversales' },
                        { value: 5, label: 'Corporativo: Toda la organización' }
                    ]
                },
                frecuencia: {
                    label: 'Frecuencia de Cumplimiento',
                    description: 'Regularidad con la que se debe evidenciar el cumplimiento.',
                    options: [
                        { value: 1, label: 'Anual o menor' },
                        { value: 2, label: 'Semestral / Trimestral' },
                        { value: 3, label: 'Mensual' },
                        { value: 4, label: 'Semanal / Diario' },
                        { value: 5, label: 'Permanente / En tiempo real' }
                    ]
                },
                exigibilidad: {
                    label: 'Grado de Exigibilidad / Fiscalización',
                    description: 'Probabilidad de ser auditado o fiscalizado externamente.',
                    options: [
                        { value: 1, label: 'Informe voluntario / Interno' },
                        { value: 2, label: 'Bajo requerimiento externo' },
                        { value: 3, label: 'Fiscalización reactiva (por denuncia)' },
                        { value: 4, label: 'Fiscalización programada regular' },
                        { value: 5, label: 'Fiscalización automática / Sistemas en línea' }
                    ]
                }
            }
        };
    },
    computed: {
        puntajeTotal() {
            return Object.values(this.form).reduce((a, b) => a + (b || 0), 0);
        },
        nivelEstimado() {
            const p = this.puntajeTotal;
            if (p <= 9) return 'baja';
            if (p <= 14) return 'media';
            if (p <= 19) return 'alta';
            return 'muy_alta';
        },
        isComplete() {
            return Object.values(this.form).every(v => v !== null);
        },

        badgeClass() {
            const map = {
                baja: 'badge-success',
                media: 'badge-warning',
                alta: 'badge-danger',
                muy_alta: 'badge-dark text-danger font-weight-bold'
            };
            return map[this.localEvaluation?.oe_nivel_criticidad] || 'badge-secondary';
        },
        previewClass() {
            const mapBootstrap = {
                baja: 'bg-success text-white',
                media: 'bg-warning text-dark',
                alta: 'bg-danger text-white',
                muy_alta: 'bg-dark text-danger border border-danger font-weight-bold'
            };
            return mapBootstrap[this.nivelEstimado];
        }
    },
    filters: {
        capitalize(value) {
            if (!value) return '';
            return value.toString().replace('_', ' ').toUpperCase();
        }
    },
    methods: {
        async saveEvaluation() {
            if (!this.isComplete) return;

            this.loading = true;
            try {
                const payload = {
                    oe_puntaje_total: this.puntajeTotal,
                    oe_criterios_json: JSON.stringify(this.form)
                };

                const response = await axios.post(`/api/obligaciones/${this.obligacionId}/evaluar`, payload);

                this.localEvaluation = response.data.evaluacion;

                Swal.fire({
                    icon: 'success',
                    title: 'Evaluación Guardada',
                    text: `La obligación ha sido evaluada como criticidad: ${this.localEvaluation.oe_nivel_criticidad.toUpperCase()}`,
                    confirmButtonText: 'Aceptar'
                });

                this.$emit('evaluated', response.data);

            } catch (error) {
                console.error(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo guardar la evaluación. ' + (error.response?.data?.message || '')
                });
            } finally {
                this.loading = false;
            }
        }
    },
    watch: {
        evaluation: {
            immediate: true,
            handler(val) {
                this.localEvaluation = val;
                // Si existe evaluación, precargar el form (opcional, si se quiere editar)
                // Pero como es histórico, tal vez mejor dejar limpio o mostrar solo si se habilita edición.
                // Por ahora, mostrar valores actuales si existen
                if (val && val.oe_criterios_json) {
                    try {
                        // Parse si es string, o asignar si es objeto (depende de cómo venga de BD)
                        const criterios = typeof val.oe_criterios_json === 'string'
                            ? JSON.parse(val.oe_criterios_json)
                            : val.oe_criterios_json;
                        this.form = { ...this.form, ...criterios };
                    } catch (e) { console.error("Error parsing criterios json", e); }
                }
            }
        }
    }
};
</script>

<style scoped>
.bg-success-light {
    background-color: #d4edda;
}

.text-orange {
    color: #fd7e14;
}

.badge-orange {
    background-color: #fd7e14;
}
</style>
