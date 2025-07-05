import { ref } from 'vue';
import { useRouter } from 'vue-router';
import AuthService from '@/services/AuthService';

export const LOGIN_TYPES = {
  EMAIL: 'email',
  PHONE: 'phone'
};

export const ERROR_MESSAGES = {
  INVALID_CREDENTIALS: 'E-posta/telefon veya şifre hatalı',
  VALIDATION_ERROR: 'Geçersiz veriler',
  GENERIC_ERROR: 'Bir hata oluştu. Lütfen tekrar deneyin.',
  REQUIRED_FIELDS: 'Lütfen tüm alanları doldurun',
  PASSWORD_MISMATCH: 'Şifreler uyuşmuyor',
  EMAIL_PHONE_EXISTS: 'Bu e-posta veya telefon numarası zaten kullanılıyor'
};

export function useAuth() {
  const router = useRouter();
  const isLoading = ref(false);
  const error = ref('');

  const clearError = () => {
    error.value = '';
  };

  const login = async (credentials) => {
    if (isLoading.value) return { success: false };
    
    isLoading.value = true;
    clearError();
    
    try {
      const response = await AuthService.login(credentials);
      
      if (response.success) {
        // Use modern redirect logic from AuthService
        if (response.shouldRedirect && response.redirectTo) {
          router.push(response.redirectTo);
        } else {
          // Fallback
          router.push('/');
        }
        return { success: true };
      } else {
        error.value = response.message || ERROR_MESSAGES.GENERIC_ERROR;
        return { success: false, message: error.value };
      }
    } catch (err) {
      const errorMessage = getErrorMessage(err);
      error.value = errorMessage;
      return { success: false, message: errorMessage };
    } finally {
      isLoading.value = false;
    }
  };

  const register = async (userData) => {
    if (isLoading.value) return { success: false };
    
    isLoading.value = true;
    clearError();
    
    try {
      const response = await AuthService.register(userData);
      
      if (response.success) {
        if (response.shouldRedirect && response.redirectTo) {
          router.push(response.redirectTo);
        } else {
          router.push('/auth/login');
        }
        return { success: true };
      } else {
        error.value = response.message || ERROR_MESSAGES.GENERIC_ERROR;
        return { success: false, message: error.value };
      }
    } catch (err) {
      const errorMessage = getRegisterErrorMessage(err);
      error.value = errorMessage;
      return { success: false, message: errorMessage };
    } finally {
      isLoading.value = false;
    }
  };

  const getErrorMessage = (err) => {
    if (err.response?.status === 401 || err.response?.status === 422) {
      return ERROR_MESSAGES.INVALID_CREDENTIALS;
    } else {
      return ERROR_MESSAGES.GENERIC_ERROR;
    }
  };

  const getRegisterErrorMessage = (err) => {
    if (err.response?.status === 422) {
      const errors = err.response.data?.errors;
      if (errors) {
        return Object.values(errors).flat().join(', ');
      } else {
        return ERROR_MESSAGES.VALIDATION_ERROR;
      }
    } else if (err.response?.status === 409) {
      return ERROR_MESSAGES.EMAIL_PHONE_EXISTS;
    } else {
      return ERROR_MESSAGES.GENERIC_ERROR;
    }
  };

  return {
    isLoading,
    error,
    
    login,
    register,
    clearError
  };
} 