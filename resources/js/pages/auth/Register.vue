<template>
  <div class="w-full space-y-6">
    <div>
      <h2 class="mt-4 text-center text-3xl font-extrabold text-gray-900">
        Hesap Oluştur
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Veya
        <router-link to="/auth/login" class="font-medium text-indigo-600 hover:text-indigo-500">
          mevcut hesabınıza giriş yapın
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
            Kayıt başarısız
          </h3>
          <div class="mt-2 text-sm text-red-700">
            <p>{{ error }}</p>
          </div>
        </div>
      </div>
    </div>

    <form @submit.prevent="onSubmit" class="mt-6 space-y-4">
      <div class="space-y-4">
        <!-- First Name -->
        <div>
          <label for="first_name" class="block text-sm font-medium text-gray-700">
            Ad *
          </label>
          <div class="mt-1">
            <input
              id="first_name"
              v-model="values.first_name"
              type="text"
              required
              class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              :class="{ 'border-red-300': showFieldError('first_name') }"
              placeholder="Adınız"
              maxlength="50"
              @blur="handleFieldTouch('first_name')"
            />
            <p v-if="showFieldError('first_name')" class="mt-1 text-sm text-red-600">
              {{ errors.first_name }}
            </p>
          </div>
        </div>

        <!-- Last Name -->
        <div>
          <label for="last_name" class="block text-sm font-medium text-gray-700">
            Soyad *
          </label>
          <div class="mt-1">
            <input
              id="last_name"
              v-model="values.last_name"
              type="text"
              required
              class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              :class="{ 'border-red-300': showFieldError('last_name') }"
              placeholder="Soyadınız"
              maxlength="50"
              @blur="handleFieldTouch('last_name')"
            />
            <p v-if="showFieldError('last_name')" class="mt-1 text-sm text-red-600">
              {{ errors.last_name }}
            </p>
          </div>
        </div>

        <!-- Phone -->
        <div>
          <label for="phone" class="block text-sm font-medium text-gray-700">
            Telefon Numarası *
          </label>
          <div class="mt-1">
            <input
              id="phone"
              v-model="values.phone"
              type="tel"
              required
              class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              :class="{ 'border-red-300': showFieldError('phone') }"
              placeholder="(0555) 123 45 67"
              @input="handlePhoneInput"
              @blur="handleFieldTouch('phone')"
            />
            <p v-if="showFieldError('phone')" class="mt-1 text-sm text-red-600">
              {{ errors.phone }}
            </p>
            <p class="mt-1 text-xs text-gray-500">
              Türk telefon numarası formatında girin
            </p>
          </div>
        </div>

        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">
            E-posta Adresi *
          </label>
          <div class="mt-1">
            <input
              id="email"
              v-model="values.email"
              type="email"
              required
              class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              :class="{ 'border-red-300': showFieldError('email') }"
              placeholder="ornek@email.com"
              @blur="handleFieldTouch('email')"
            />
            <p v-if="showFieldError('email')" class="mt-1 text-sm text-red-600">
              {{ errors.email }}
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
              class="appearance-none relative block w-full px-3 py-2 pr-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              :class="{ 'border-red-300': showFieldError('password') }"
              placeholder="En az 8 karakter"
              minlength="8"
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

        <!-- Password Confirmation -->
        <div>
          <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
            Şifre Tekrarı *
          </label>
          <div class="mt-1 relative">
            <input
              id="password_confirmation"
              v-model="values.password_confirmation"
              :type="showPasswordConfirmation ? 'text' : 'password'"
              required
              class="appearance-none relative block w-full px-3 py-2 pr-10 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
              :class="{ 'border-red-300': showFieldError('password_confirmation') }"
              placeholder="Şifrenizi tekrar girin"
              minlength="8"
              @blur="handleFieldTouch('password_confirmation')"
            />
            <button
              type="button"
              @click="showPasswordConfirmation = !showPasswordConfirmation"
              class="absolute inset-y-0 right-0 pr-3 flex items-center"
            >
              <svg v-if="showPasswordConfirmation" class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
              <svg v-else class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
              </svg>
            </button>
            <p v-if="showFieldError('password_confirmation')" class="mt-1 text-sm text-red-600">
              {{ errors.password_confirmation }}
            </p>
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
          {{ isLoading ? 'Hesap oluşturuluyor...' : 'Hesap Oluştur' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import * as yup from 'yup';
import { useAuth } from '@/composables/useAuth';
import { useFormValidation } from '@/composables/useFormValidation';

const { register, isLoading, error, clearError } = useAuth();
const router = useRouter();

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const touchedFields = ref({
  first_name: false,
  last_name: false,
  phone: false,
  email: false,
  password: false,
  password_confirmation: false
});

const validationSchema = yup.object({
  first_name: yup.string()
    .required('Ad alanı zorunludur')
    .min(2, 'Ad en az 2 karakter olmalıdır')
    .max(50, 'Ad en fazla 50 karakter olabilir'),
  last_name: yup.string()
    .required('Soyad alanı zorunludur')
    .min(2, 'Soyad en az 2 karakter olmalıdır')
    .max(50, 'Soyad en fazla 50 karakter olabilir'),
  phone: yup.string()
    .required('Telefon numarası zorunludur')
    .matches(/^\(\d{4}\) \d{3} \d{2} \d{2}$/, 'Geçerli bir telefon numarası giriniz'),
  email: yup.string()
    .required('E-posta alanı zorunludur')
    .email('Geçerli bir e-posta adresi giriniz')
    .max(255, 'E-posta en fazla 255 karakter olabilir'),
  password: yup.string()
    .required('Şifre zorunludur')
    .min(8, 'Şifre en az 8 karakter olmalıdır'),
  password_confirmation: yup.string()
    .required('Şifre tekrarı zorunludur')
    .oneOf([yup.ref('password')], 'Şifreler uyuşmuyor')
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
  first_name: '',
  last_name: '',
  phone: '',
  email: '',
  password: '',
  password_confirmation: ''
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
  
  // Apply mask: (0###) ### ## ##
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
  setFieldValue('phone', formatted);
  event.target.value = formatted;
};

const onSubmit = async () => {
  if (isLoading.value) return;
  
  clearError();
  clearErrors();
  
  touchedFields.value = {
    first_name: true,
    last_name: true,
    phone: true,
    email: true,
    password: true,
    password_confirmation: true
  };
  
  const isValid = await validateForm();
  
  if (!isValid) {
    return;
  }
  
  const cleanPhone = values.phone.replace(/[^\d]/g, '');
  
  await register({
    first_name: values.first_name.trim(),
    last_name: values.last_name.trim(),
    phone: cleanPhone,
    email: values.email.toLowerCase().trim(),
    password: values.password,
    password_confirmation: values.password_confirmation,
  });
};
</script> 