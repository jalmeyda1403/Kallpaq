<template>
    <Teleport to="body">
        <div class="modal fade show"
            style="display: block; background: rgba(0,0,0,0.5); overflow-y: auto; z-index: 2000;" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Constructor de Fórmula</h5>
                        <button type="button" class="close text-white" @click="$emit('close')">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="font-weight-bold">1. Definición de Variables</h6>
                                    <button type="button" class="btn btn-primary btn-sm" @click="addVariableRow">
                                        <i class="fas fa-plus"></i> Agregar Variable
                                    </button>
                                </div>
                                <small class="form-text text-muted mb-1">
                                    Defina las variables (ej. V1, V2) y úselas en la fórmula.
                                </small>
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th style="width: 100px;">Código</th>
                                                <th>Nombre de la Variable</th>
                                                <th style="width: 150px;">Valor de Prueba</th>
                                                <th style="width: 50px;" v-if="variables.length > 1">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(variable, index) in variables"
                                                :key="variable.id || `var-${index}`">
                                                <td class="align-middle text-center font-weight-bold">{{ variable.code
                                                }}
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control form-control-sm"
                                                        v-model="variable.name"
                                                        :placeholder="'Nombre variable ' + (index + 1)">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm"
                                                        v-model.number="variable.testValue" placeholder="0.00">
                                                </td>
                                                <td class="text-center" v-if="variables.length > 1">
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        @click="removeVariableRow(index)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h6 class="font-weight-bold">2. Fórmula</h6>
                                <div class="form-group">
                                    <small class="text-muted d-block mb-1">
                                        Use las variables definidas (V1, V2) en la fórmula, junto a los operadores
                                        permitidos: + ,- ,* ,/ ,() </small>
                                    <div class="input-group">
                                        <input type="text" class="form-control" v-model="formulaString"
                                            placeholder="Ej: (V1 / V2) * 100">
                                        <div class="input-group-append">
                                            <button class="btn btn-warning" type="button" @click="simular">
                                                <i class="fas fa-calculator"></i> Simular
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row" v-if="simulationResult !== null">
                            <div class="col-md-12">
                                <div
                                    :class="['alert', simulationError ? 'alert-danger' : 'alert-success', 'd-flex', 'justify-content-between', 'align-items-center']">
                                    <span>
                                        <strong>Resultado:</strong> {{ simulationResult }}
                                    </span>
                                    <span v-if="!simulationError">
                                        <i class="fas fa-check-circle fa-lg"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="$emit('close')">Cancelar</button>
                        <button type="button" class="btn btn-danger" @click="confirmar"
                            :disabled="simulationError || simulationResult === null">
                            Aceptar y Usar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import Swal from 'sweetalert2';

const props = defineProps({
    initialFormula: String,
    initialVariables: Object // { indicador_var1: 'Name', ... }
});

const emit = defineEmits(['close', 'update-formula']);

// Function to generate a unique ID for each variable
let nextId = 0;

// Initialize variables - start with only one if no initial variables are provided
const variables = ref([]);

const formulaString = ref('');
const simulationResult = ref(null);
const simulationError = ref(false);

onMounted(() => {
    if (props.initialFormula) {
        formulaString.value = props.initialFormula;
    }

    // Check if there are any initial variables defined
    if (props.initialVariables) {
        // If there are initial variables, use them
        let hasVariables = false;
        for (let i = 1; i <= 6; i++) {
            const key = `indicador_var${i}`;
            if (props.initialVariables[key] && props.initialVariables[key] !== '') {
                hasVariables = true;
                break;
            }
        }

        if (hasVariables) {
            // Add existing variables
            for (let i = 1; i <= 6; i++) {
                const key = `indicador_var${i}`;
                const name = props.initialVariables[key] || '';
                if (name || i <= 2) { // Include at least the first 2 for UI
                    variables.value.push({
                        id: nextId++,
                        code: `V${i}`,
                        name: name,
                        testValue: 0
                    });
                }
            }
        } else {
            // If no initial variables but there's a formula, add at least one
            variables.value.push({
                id: nextId++,
                code: 'V1',
                name: '',
                testValue: 0
            });
        }
    } else {
        // If no initial variables provided, start with one empty variable
        variables.value.push({
            id: nextId++,
            code: 'V1',
            name: '',
            testValue: 0
        });
    }
});

const addVariableRow = () => {
    const nextIndex = variables.value.length + 1;
    if (nextIndex <= 6) { // Limit to V6
        variables.value.push({
            id: nextId++,
            code: `V${nextIndex}`,
            name: '',
            testValue: 0
        });
    } else {
        Swal.fire('Límite alcanzado', 'Solo puedes agregar hasta 6 variables (V1 a V6)', 'info');
    }
};

const removeVariableRow = (index) => {
    if (variables.value.length > 1) {
        variables.value.splice(index, 1);
    } else {
        Swal.fire('No permitido', 'Debe haber al menos una variable', 'warning');
    }
};

const simular = () => {
    simulationError.value = false;
    simulationResult.value = null;

    if (!formulaString.value.trim()) {
        simulationError.value = true;
        simulationResult.value = "La fórmula está vacía";
        return;
    }

    try {
        // Create scope with test values
        const scope = {};
        variables.value.forEach(v => {
            scope[v.code] = v.testValue;
            // Also allow lowercase
            scope[v.code.toLowerCase()] = v.testValue;
        });

        // Simple and safer evaluation replacing variables with values
        // Note: For a real production app, use a library like mathjs. 
        // Here we will do a basic replacement and eval, but sanitizing inputs.
        // Ideally we should use 'mathjs' if installed. If not, we'll try a basic parser approach.

        // Let's try to use a Function constructor for basic math, strictly limiting scope
        // Replace V1..V6 with actual values in the string
        let evalString = formulaString.value.toUpperCase();

        // Check for invalid characters (only allow digits, operators, parens, V1-V6)
        if (!/^[0-9V\+\-\*\/\(\)\.\s]+$/.test(evalString)) {
            throw new Error("Caracteres no permitidos");
        }

        variables.value.forEach(v => {
            // Replace V1 with value, ensuring we don't replace V11 when looking for V1
            // Regex lookahead/behind or word boundaries
            const regex = new RegExp(`\\b${v.code}\\b`, 'g');
            evalString = evalString.replace(regex, v.testValue);
        });

        // Eval
        // eslint-disable-next-line no-new-func
        const result = new Function(`return ${evalString}`)();

        if (!isFinite(result) || isNaN(result)) {
            throw new Error("Resultado no numérico (división por cero?)");
        }

        simulationResult.value = Number(result).toFixed(2);

    } catch (error) {
        simulationError.value = true;
        simulationResult.value = "Error en la fórmula: " + error.message;
    }
};

const confirmar = () => {
    if (simulationError.value) {
        Swal.fire('Error', 'Debe validar una fórmula correcta antes de aceptar.', 'error');
        return;
    }

    // Prepare variables object to emit (ensure it's up to V6)
    const vars = {
        indicador_var1: '',
        indicador_var2: '',
        indicador_var3: '',
        indicador_var4: '',
        indicador_var5: '',
        indicador_var6: ''
    };

    variables.value.forEach((v, i) => {
        if (i < 6) { // Only allow up to V6
            vars[`indicador_var${i + 1}`] = v.name;
        }
    });

    emit('update-formula', {
        formula: formulaString.value,
        variables: vars
    });
    emit('close');
};

</script>

<style scoped>
.modal.show {
    padding-right: 17px;
}
</style>
