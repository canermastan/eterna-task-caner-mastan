import { ref, reactive, computed, isRef, unref } from 'vue';

/**
 * Form validation composable using Yup
 * @param {object|Ref} schema - Yup validation schema (can be reactive)
 * @param {object} initialValues - Initial form values
 */
export function useFormValidation(schema, initialValues = {}) {
  // Form values
  const values = reactive({ ...initialValues });
  
  // Form errors
  const errors = ref({});
  
  // Touched fields
  const touched = ref({});
  
  // Validation state
  const isValidating = ref(false);
  
  // Computed properties
  const isValid = computed(() => {
    return Object.keys(errors.value).length === 0;
  });
  
  const hasErrors = computed(() => {
    return Object.keys(errors.value).length > 0;
  });
  
  // Get current schema (reactive or static)
  const getCurrentSchema = () => {
    return isRef(schema) ? schema.value : schema;
  };
  
  /**
   * Validate a single field
   * @param {string} fieldName - Name of the field to validate
   */
  const validateField = async (fieldName) => {
    try {
      const currentSchema = getCurrentSchema();
      
      // Get the field schema
      const fieldSchema = currentSchema.fields[fieldName];
      if (!fieldSchema) return true;
      
      // Validate the field value
      await fieldSchema.validate(values[fieldName]);
      
      // Clear error if validation passes
      if (errors.value[fieldName]) {
        delete errors.value[fieldName];
        errors.value = { ...errors.value }; // Trigger reactivity
      }
      
      return true;
    } catch (error) {
      // Set error if validation fails
      errors.value = {
        ...errors.value,
        [fieldName]: error.message
      };
      
      return false;
    }
  };
  
  /**
   * Validate entire form
   */
  const validateForm = async () => {
    isValidating.value = true;
    errors.value = {};
    
    try {
      const currentSchema = getCurrentSchema();
      await currentSchema.validate(values, { abortEarly: false });
      return true;
    } catch (error) {
      if (error.inner) {
        // Yup validation errors
        const newErrors = {};
        error.inner.forEach(err => {
          if (err.path) {
            newErrors[err.path] = err.message;
          }
        });
        errors.value = newErrors;
      }
      return false;
    } finally {
      isValidating.value = false;
    }
  };
  
  /**
   * Set field value
   * @param {string} fieldName - Name of the field
   * @param {any} value - New value
   */
  const setFieldValue = (fieldName, value) => {
    values[fieldName] = value;
  };
  
  /**
   * Set field error
   * @param {string} fieldName - Name of the field
   * @param {string} errorMessage - Error message
   */
  const setFieldError = (fieldName, errorMessage) => {
    errors.value = {
      ...errors.value,
      [fieldName]: errorMessage
    };
  };
  
  /**
   * Clear field error
   * @param {string} fieldName - Name of the field
   */
  const clearFieldError = (fieldName) => {
    if (errors.value[fieldName]) {
      delete errors.value[fieldName];
      errors.value = { ...errors.value };
    }
  };
  
  /**
   * Clear all errors
   */
  const clearErrors = () => {
    errors.value = {};
  };
  
  /**
   * Reset form to initial values
   */
  const resetForm = () => {
    Object.keys(values).forEach(key => {
      values[key] = initialValues[key] || '';
    });
    errors.value = {};
    touched.value = {};
  };
  
  /**
   * Set multiple values at once
   * @param {object} newValues - Object with new values
   */
  const setValues = (newValues) => {
    Object.keys(newValues).forEach(key => {
      if (key in values) {
        values[key] = newValues[key];
      }
    });
  };
  
  /**
   * Mark field as touched
   * @param {string} fieldName - Name of the field
   */
  const markFieldAsTouched = (fieldName) => {
    touched.value = {
      ...touched.value,
      [fieldName]: true
    };
  };
  
  return {
    values,
    errors,
    touched,
    isValidating,
    
    isValid,
    hasErrors,
    
    validateField,
    validateForm,
    setFieldValue,
    setFieldError,
    clearFieldError,
    clearErrors,
    resetForm,
    setValues,
    markFieldAsTouched
  };
} 