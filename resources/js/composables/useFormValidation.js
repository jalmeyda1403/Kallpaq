/**
 * Composable para validación de formularios
 * Proporciona funciones reutilizables para validar campos de formularios
 */

import { ref, reactive, computed } from 'vue';

export function useFormValidation(initialRules = {}) {
    const errors = reactive({});
    const touched = reactive({});
    const isSubmitting = ref(false);

    /**
     * Reglas de validación disponibles
     */
    const validators = {
        required: (value, message = 'Este campo es obligatorio') => {
            if (value === null || value === undefined || value === '') return message;
            if (Array.isArray(value) && value.length === 0) return message;
            return null;
        },

        email: (value, message = 'Ingrese un email válido') => {
            if (!value) return null;
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(value) ? null : message;
        },

        minLength: (value, min, message) => {
            if (!value) return null;
            const msg = message || `Mínimo ${min} caracteres`;
            return value.length >= min ? null : msg;
        },

        maxLength: (value, max, message) => {
            if (!value) return null;
            const msg = message || `Máximo ${max} caracteres`;
            return value.length <= max ? null : msg;
        },

        min: (value, min, message) => {
            if (value === null || value === undefined || value === '') return null;
            const msg = message || `El valor mínimo es ${min}`;
            return Number(value) >= min ? null : msg;
        },

        max: (value, max, message) => {
            if (value === null || value === undefined || value === '') return null;
            const msg = message || `El valor máximo es ${max}`;
            return Number(value) <= max ? null : msg;
        },

        numeric: (value, message = 'Solo se permiten números') => {
            if (!value) return null;
            return /^\d+$/.test(value) ? null : message;
        },

        decimal: (value, message = 'Ingrese un número válido') => {
            if (!value) return null;
            return /^\d+(\.\d+)?$/.test(value) ? null : message;
        },

        date: (value, message = 'Ingrese una fecha válida') => {
            if (!value) return null;
            const date = new Date(value);
            return !isNaN(date.getTime()) ? null : message;
        },

        dateAfter: (value, minDate, message) => {
            if (!value || !minDate) return null;
            const date = new Date(value);
            const min = new Date(minDate);
            const msg = message || `La fecha debe ser posterior a ${minDate}`;
            return date > min ? null : msg;
        },

        dateBefore: (value, maxDate, message) => {
            if (!value || !maxDate) return null;
            const date = new Date(value);
            const max = new Date(maxDate);
            const msg = message || `La fecha debe ser anterior a ${maxDate}`;
            return date < max ? null : msg;
        },

        pattern: (value, regex, message = 'Formato inválido') => {
            if (!value) return null;
            return regex.test(value) ? null : message;
        },

        custom: (value, validatorFn, message = 'Valor inválido') => {
            if (!value) return null;
            return validatorFn(value) ? null : message;
        }
    };

    /**
     * Valida un campo específico
     */
    const validateField = (fieldName, value, rules) => {
        if (!rules || rules.length === 0) {
            errors[fieldName] = null;
            return true;
        }

        for (const rule of rules) {
            let error = null;

            if (typeof rule === 'string') {
                // Regla simple: 'required', 'email', etc.
                if (validators[rule]) {
                    error = validators[rule](value);
                }
            } else if (typeof rule === 'object') {
                // Regla con parámetros: { type: 'minLength', value: 5, message: '...' }
                const { type, value: ruleValue, message } = rule;
                if (validators[type]) {
                    error = validators[type](value, ruleValue, message);
                }
            } else if (typeof rule === 'function') {
                // Función de validación personalizada
                error = rule(value);
            }

            if (error) {
                errors[fieldName] = error;
                return false;
            }
        }

        errors[fieldName] = null;
        return true;
    };

    /**
     * Valida todos los campos del formulario
     */
    const validateAll = (formData, rules) => {
        let isValid = true;

        for (const [fieldName, fieldRules] of Object.entries(rules)) {
            const value = formData[fieldName];
            touched[fieldName] = true;

            if (!validateField(fieldName, value, fieldRules)) {
                isValid = false;
            }
        }

        return isValid;
    };

    /**
     * Marca un campo como tocado (para mostrar errores)
     */
    const touch = (fieldName) => {
        touched[fieldName] = true;
    };

    /**
     * Limpia todos los errores
     */
    const clearErrors = () => {
        Object.keys(errors).forEach(key => {
            errors[key] = null;
        });
        Object.keys(touched).forEach(key => {
            touched[key] = false;
        });
    };

    /**
     * Limpia error de un campo específico
     */
    const clearFieldError = (fieldName) => {
        errors[fieldName] = null;
    };

    /**
     * Maneja el submit del formulario
     */
    const handleSubmit = async (formData, rules, submitFn) => {
        isSubmitting.value = true;

        try {
            const isValid = validateAll(formData, rules);

            if (!isValid) {
                return { success: false, errors };
            }

            const result = await submitFn(formData);
            return { success: true, data: result };
        } catch (error) {
            // Manejar errores del servidor (422 Validation)
            if (error.response?.status === 422) {
                const serverErrors = error.response.data.errors;
                for (const [field, messages] of Object.entries(serverErrors)) {
                    errors[field] = Array.isArray(messages) ? messages[0] : messages;
                }
            }
            throw error;
        } finally {
            isSubmitting.value = false;
        }
    };

    /**
     * Computed para verificar si hay errores
     */
    const hasErrors = computed(() => {
        return Object.values(errors).some(error => error !== null);
    });

    /**
     * Obtener clase CSS para input según estado
     */
    const getInputClass = (fieldName) => {
        if (!touched[fieldName]) return '';
        return errors[fieldName] ? 'is-invalid' : 'is-valid';
    };

    return {
        errors,
        touched,
        isSubmitting,
        hasErrors,
        validators,
        validateField,
        validateAll,
        touch,
        clearErrors,
        clearFieldError,
        handleSubmit,
        getInputClass
    };
}

/**
 * Reglas de validación predefinidas comunes
 */
export const commonRules = {
    required: ['required'],
    email: ['required', 'email'],
    optionalEmail: ['email'],
    password: [
        'required',
        { type: 'minLength', value: 8, message: 'La contraseña debe tener al menos 8 caracteres' }
    ],
    numeric: ['required', 'numeric'],
    decimal: ['required', 'decimal'],
    date: ['required', 'date'],
    futureDate: [
        'required',
        'date',
        { type: 'dateAfter', value: new Date().toISOString().split('T')[0], message: 'La fecha debe ser futura' }
    ]
};

export default useFormValidation;
