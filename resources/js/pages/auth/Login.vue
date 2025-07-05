<template>
  <div class="w-full space-y-6">
    <div>
      <h2 class="mt-4 text-center text-3xl font-extrabold text-gray-900">
        Hesabınıza giriş yapın
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Veya
        <router-link to="/auth/register" class="font-medium text-indigo-600 hover:text-indigo-500">
          yeni hesap oluşturun
        </router-link>
      </p>
    </div>

    <!-- Error Alert -->
    <div v-if="error" class="rounded-md bg-red-50 p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">
            Giriş başarısız
          </h3>
          <div class="mt-2 text-sm text-red-700">
            <p>{{ error }}</p>
          </div>
        </div>
      </div>
    </div>

    <form @submit.prevent="handleSubmit" class="mt-6 space-y-4" :key="formKey">
      <div class="space-y-4">
        <!-- Login Field (Email or Phone) with Toggle -->
        <div>
          <div class="flex items-center justify-between mb-1">
            <label for="login" class="block text-sm font-medium text-gray-700">
              {{ loginType === LOGIN_TYPES.EMAIL ? 'E-posta Adresi' : 'Telefon Numarası' }} *
            </label>
            <div class="flex rounded-md shadow-sm">
              <button
                type="button"
                @click="handleLoginTypeChange(LOGIN_TYPES.EMAIL)"
                :class="[
                  'px-3 py-1 text-xs font-medium rounded-l-md border border-r-0 transition-colors',
                  loginType === LOGIN_TYPES.EMAIL 
                    ? 'bg-indigo-600 text-white border-indigo-600 z-10' 
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                ]"
              >
                E-posta
              </button>
              <button
                type="button"
                @click="handleLoginTypeChange(LOGIN_TYPES.PHONE)"
                :class="[
                  'px-3 py-1 text-xs font-medium rounded-r-md border transition-colors',
                  loginType === LOGIN_TYPES.PHONE 
                    ? 'bg-indigo-600 text-white border-indigo-600 z-10' 
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                ]"
              >
                Telefon
              </button>
            </div>
          </div>
          <div class="mt-1">
            <!-- Phone input with mask -->
            <input
              v-if="loginType === LOGIN_TYPES.PHONE"
              :key="`login-phone-${formKey}`"
              id="login"
              v-model="values.login"
              type="tel"
              required
              autocomplete="username"
              class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              :class="{ 'border-red-300': showFieldError('login') }"
              placeholder="(0555) 123 45 67"
              @input="handlePhoneInput"
              @blur="handleFieldTouch('login')"
            />
            
            <!-- Email input without mask -->
            <input
              v-else
              :key="`login-email-${formKey}`"
              id="login"
              v-model="values.login"
              type="email"
              required
              autocomplete="username"
              class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              :class="{ 'border-red-300': showFieldError('login') }"
              placeholder="ornek@email.com"
              @blur="handleFieldTouch('login')"
            />
            <p v-if="showFieldError('login')" class="mt-1 text-sm text-red-600">
              {{ errors.login }}
            </p>
            <p class="mt-1 text-xs text-gray-500">
              {{ loginType === LOGIN_TYPES.PHONE ? 'Telefon numaranızı girin' : 'E-posta adresinizi girin' }}
            </p>
          </div>
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">
            Şifre *
          </label>
          <div class="mt-1 relative">
            <input
              id="password"
              v-model="values.password"
              :type="showPassword ? 'text' : 'password'"
              required
              autocomplete="current-password"
              class="appearance-none relative block w-full px-3 py-2 pr-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              :class="{ 'border-red-300': showFieldError('password') }"
              placeholder="Şifrenizi girin"
              @blur="handleFieldTouch('password')"
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute inset-y-0 right-0 pr-3 flex items-center"
            >
              <svg v-if="showPassword" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              <svg v-else class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
              </svg>
            </button>
            <p v-if="showFieldError('password')" class="mt-1 text-sm text-red-600">
              {{ errors.password }}
            </p>
          </div>
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <input
              id="remember"
              v-model="values.remember"
              type="checkbox"
              class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              @blur="handleFieldTouch('remember')"
            />
            <label for="remember" class="ml-2 block text-sm text-gray-900">
              Beni hatırla
            </label>
          </div>
        </div>
      </div>

      <div class="pt-2">
        <button
          type="submit"
          :disabled="isLoading"
          class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200"
        >
          <span v-if="isLoading" class="absolute left-0 inset-y-0 flex items-center pl-3">
            <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </span>
          {{ isLoading ? 'Giriş yapılıyor...' : 'Giriş Yap' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, nextTick, computed } from 'vue';
import * as yup from 'yup';
import { useAuth, LOGIN_TYPES } from '@/composables/useAuth';
import { useFormValidation } from '@/composables/useFormValidation';

const { isLoading, error, login, clearError } = useAuth();

const showPassword = ref(false);
const loginType = ref(LOGIN_TYPES.EMAIL);
const formKey = ref(0);

const touchedFields = ref({
  login: false,
  password: false
});

const validationSchema = computed(() => {
  return yup.object({
    login: loginType.value === LOGIN_TYPES.PHONE 
      ? yup.string()
          .required('Telefon numarası gereklidir')
          .matches(/^\(\d{4}\) \d{3} \d{2} \d{2}$/, 'Geçerli bir telefon numarası giriniz')
      : yup.string()
          .required('E-posta gereklidir')
          .email('Geçerli bir e-posta adresi giriniz'),
    password: yup.string()
      .required('Şifre gereklidir')
      .min(8, 'Şifre en az 8 karakter olmalıdır'),
    remember: yup.boolean()
  });
});

const {
  values,
  errors,
  isValidating,
  validateField,
  validateForm,
  setFieldValue,
  clearErrors,
  resetForm
} = useFormValidation(validationSchema, {
  login: '',
  password: '',
  remember: false
});

const handleFieldTouch = (fieldName) => {
  touchedFields.value[fieldName] = true;
  validateField(fieldName);
};

const showFieldError = (fieldName) => {
  return touchedFields.value[fieldName] && errors.value[fieldName];
};

const formatPhoneNumber = (value) => {
  const cleanValue = value.replace(/\D/g, '');
  
  if (cleanValue.length >= 4) {
    let formatted = `(${cleanValue.slice(0, 4)})`;
    if (cleanValue.length > 4) {
      formatted += ` ${cleanValue.slice(4, 7)}`;
      if (cleanValue.length > 7) {
        formatted += ` ${cleanValue.slice(7, 9)}`;
        if (cleanValue.length > 9) {
          formatted += ` ${cleanValue.slice(9, 11)}`;
        }
      }
    }
    return formatted;
  }
  return cleanValue;
};

const handlePhoneInput = (event) => {
  const formatted = formatPhoneNumber(event.target.value);
  setFieldValue('login', formatted);
  event.target.value = formatted;
};

const handleLoginTypeChange = async (newType) => {
  const currentPassword = values.password;
  const currentRemember = values.remember;
  
  loginType.value = newType;
  touchedFields.value.login = false;
  
  setFieldValue('login', '');
  setFieldValue('password', currentPassword);
  setFieldValue('remember', currentRemember);
  
  formKey.value++;
  clearErrors();
  clearError();
};

const handleSubmit = async () => {
  touchedFields.value = {
    login: true,
    password: true
  };
  
  const isValid = await validateForm();
  
  if (!isValid) {
    return;
  }
  
  const cleanLogin = loginType.value === LOGIN_TYPES.PHONE 
    ? values.login.replace(/[^\d]/g, '')
    : values.login;
  
  await login({
    login: cleanLogin,
    password: values.password,
    remember: values.remember,
  });
};
</script> 