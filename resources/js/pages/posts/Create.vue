<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-0">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-8">
      <div class="min-w-0 flex-1">
        <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl">
          Yeni Yazı Oluştur
        </h1>
        <p class="text-gray-600 mt-1">
          Blog yazınızı oluşturun ve yayınlayın
        </p>
      </div>
      <div class="mt-4 flex md:ml-4 md:mt-0 space-x-3">
        <button
          @click="handleSubmit"
          :disabled="isLoading || !canPublish"
          class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
          </svg>
          Yayınla
        </button>
      </div>
    </div>

    <!-- Error Alert -->
    <div v-if="submitError" class="rounded-md bg-red-50 p-4 mb-6">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">
            Hata oluştu
          </h3>
          <div class="mt-2 text-sm text-red-700">
            {{ submitError }}
          </div>
        </div>
      </div>
    </div>

    <!-- Success Alert -->
    <div v-if="success" class="rounded-md bg-green-50 p-4 mb-6">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.236 4.53L8.465 10.5a.75.75 0 00-1.06 1.061l2.154 2.154a.75.75 0 001.137-.089l4.161-5.825z" clip-rule="evenodd" />
          </svg>
        </div>
        <div class="ml-3">
          <p class="text-sm font-medium text-green-800">
            {{ success }}
          </p>
        </div>
      </div>
    </div>

    <!-- Main Form -->
    <form @submit.prevent="handleSubmit">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Fields -->
        <div class="lg:col-span-2 space-y-6">
          <!-- Title -->
          <div>
            <label for="title" class="block text-sm font-medium text-gray-900 mb-2">
              Başlık *
            </label>
            <input
              id="title"
              v-model="values.title"
              type="text"
              maxlength="255"
              class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-lg sm:leading-6"
              :class="{ 'ring-red-500 focus:ring-red-500': errors.title }"
              placeholder="Yazınızın başlığını girin..."
              @blur="validateField('title')"
            />
            <p v-if="errors.title" class="mt-1 text-sm text-red-600">{{ errors.title }}</p>
            <p class="mt-1 text-sm text-gray-500">{{ (values.title || '').length }}/255 karakter</p>
          </div>

          <!-- Content -->
          <div>
            <label for="content" class="block text-sm font-medium text-gray-900 mb-2">
              İçerik *
            </label>
            <textarea
              id="content"
              v-model="values.content"
              rows="20"
              class="block w-full rounded-md border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
              :class="{ 'ring-red-500 focus:ring-red-500': errors.content }"
              placeholder="Yazınızın içeriğini buraya yazın... Markdown formatını kullanabilirsiniz."
              @blur="validateField('content')"
            ></textarea>
            <p v-if="errors.content" class="mt-1 text-sm text-red-600">{{ errors.content }}</p>
            <p class="mt-1 text-sm text-gray-500">
              {{ (values.content || '').length }} karakter
            </p>
          </div>

          <!-- Cover Image -->
          <div>
            <label for="coverImage" class="block text-sm font-medium text-gray-900 mb-2">
              Kapak Görseli
            </label>
            
            <!-- Image Upload Area -->
            <div class="mt-2">
              <!-- File Input (Hidden) -->
              <input
                ref="fileInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleImageUpload"
              />
              
              <!-- Upload Area -->
              <div
                v-if="!imagePreview"
                @click="triggerFileInput"
                @dragover.prevent
                @drop.prevent="handleImageDrop"
                class="relative cursor-pointer rounded-lg border-2 border-dashed border-gray-300 p-6 text-center hover:border-gray-400 transition-colors"
                :class="{ 'border-red-500': errors.coverImage }"
              >
                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                  <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="mt-4">
                  <p class="text-sm font-medium text-gray-900">Kapak görseli yükleyin</p>
                  <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF formatları desteklenir (Maks. 2MB)</p>
                </div>
              </div>
              
              <!-- Image Preview -->
              <div v-else class="relative">
                <img
                  :src="imagePreview"
                  alt="Kapak görseli önizleme"
                  class="w-full h-48 object-cover rounded-lg border border-gray-300"
                />
                <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center space-x-2">
                  <button
                    type="button"
                    @click="triggerFileInput"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Değiştir
                  </button>
                  <button
                    type="button"
                    @click="removeImage"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Kaldır
                  </button>
                </div>
              </div>
            </div>
            
            <p v-if="errors.coverImage" class="mt-1 text-sm text-red-600">{{ errors.coverImage }}</p>
            <p class="mt-1 text-sm text-gray-500">
              Opsiyonel. Yazınız için bir kapak görseli yükleyin. (PNG, JPG, GIF - Maks. 2MB)
            </p>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
          <!-- Categories -->
          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Kategoriler *</h3>
            
            <!-- Loading Categories -->
            <div v-if="categoriesLoading" class="flex justify-center py-4">
              <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600"></div>
            </div>

            <!-- Categories Error -->
            <div v-else-if="categoriesError" class="text-sm text-red-600 py-4">
              Kategoriler yüklenirken hata oluştu.
            </div>

            <!-- Categories List -->
            <div v-else>
              <v-select
                v-model="values.categoryIds"
                :options="categories"
                :reduce="category => category.id"
                label="name"
                placeholder="Kategorileri seçin..."
                multiple
                :searchable="true"
                :close-on-select="false"
                :clear-on-select="false"
                :preserve-search="true"
                :class="['vue-select-custom', { 'vue-select-error': errors.categoryIds }]"
                @input="validateField('categoryIds')"
              >
                <template #no-options="{ search, searching }">
                  <template v-if="searching">
                    "{{ search }}" için sonuç bulunamadı.
                  </template>
                  <em v-else>Aramaya başlayın...</em>
                </template>
                
                <template #option="{ name }">
                  <div class="flex items-center">
                    <span>{{ name }}</span>
                  </div>
                </template>
                
                <template #selected-option="{ name }">
                  <div class="selected-tag">
                    {{ name }}
                  </div>
                </template>
              </v-select>
            </div>
            
            <p v-if="errors.categoryIds" class="mt-2 text-sm text-red-600">{{ errors.categoryIds }}</p>
            <p class="mt-2 text-sm text-gray-500">
              En az bir kategori seçmelisiniz.
            </p>
          </div>

          <!-- Preview -->
          <div v-if="values.title || values.content" class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Önizleme</h3>
            
            <!-- Cover Image Preview -->
            <div v-if="imagePreview" class="mb-4">
              <img
                :src="imagePreview"
                :alt="values.title || 'Kapak görseli'"
                class="w-full h-32 object-cover rounded-md"
              />
            </div>
            
            <!-- Title Preview -->
            <h4 v-if="values.title" class="font-semibold text-gray-900 mb-2 line-clamp-2">
              {{ values.title }}
            </h4>
            
            <!-- Content Preview -->
            <div v-if="values.content" class="text-sm text-gray-600 line-clamp-4">
              {{ truncatedContent }}
            </div>
            
            <!-- Selected Categories -->
            <div v-if="selectedCategories.length" class="flex flex-wrap gap-1 mt-3">
              <span
                v-for="category in selectedCategories"
                :key="category.id"
                class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-indigo-100 text-indigo-800"
              >
                {{ category.name }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useQuery } from '@tanstack/vue-query';
import * as yup from 'yup';
import { useFormValidation } from '@/composables/useFormValidation';
import PostService from '@/services/PostService';
import CategoryService from '@/services/CategoryService';
import AuthService from '@/services/AuthService';

const router = useRouter();

const validationSchema = yup.object({
  title: yup
    .string()
    .required('Başlık gereklidir.')
    .max(255, 'Başlık en fazla 255 karakter olabilir.')
    .trim(),
  content: yup
    .string()
    .required('İçerik gereklidir.')
    .trim(),
  coverImage: yup
    .mixed()
    .nullable()
    .test('fileSize', 'Dosya boyutu 2MB\'dan küçük olmalıdır.', (value) => {
      if (!value) return true;
      return value.size <= 2 * 1024 * 1024; // 2MB
    })
    .test('fileType', 'Sadece PNG, JPG, JPEG, GIF formatları desteklenir.', (value) => {
      if (!value) return true;
      return ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'].includes(value.type);
    }),
  categoryIds: yup
    .array()
    .of(yup.number())
    .min(1, 'En az bir kategori seçmelisiniz.')
    .required('En az bir kategori seçmelisiniz.')
});

const {
  values,
  errors,
  isValid,
  validateField,
  validateForm,
  resetForm,
  setFieldValue
} = useFormValidation(validationSchema, {
  title: '',
  content: '',
  coverImage: null,
  categoryIds: []
});

const isLoading = ref(false);
const submitError = ref('');
const success = ref('');
const imagePreview = ref('');
const fileInput = ref(null);

const { data: categoriesData, isLoading: categoriesLoading, error: categoriesError } = useQuery({
  queryKey: ['categories'],
  queryFn: () => CategoryService.getCategories(),
});

const categories = computed(() => categoriesData.value || []);

const canSave = computed(() => {
  return values.title?.trim() && values.content?.trim();
});

const canPublish = computed(() => {
  return canSave.value && values.categoryIds?.length > 0 && isValid.value;
});

const selectedCategories = computed(() => {
  if (!categories.value || !values.categoryIds) return [];
  return categories.value.filter(cat => values.categoryIds.includes(cat.id));
});

const truncatedContent = computed(() => {
  if (!values.content) return '';
  const plainText = values.content.replace(/[#*`>\-\+]/g, '').trim();
  return plainText.length > 200 ? plainText.substring(0, 200) + '...' : plainText;
});

const triggerFileInput = () => {
  fileInput.value?.click();
};

const handleImageUpload = (event) => {
  const file = event.target.files?.[0];
  if (file) {
    processImageFile(file);
  }
};

const handleImageDrop = (event) => {
  const file = event.dataTransfer.files?.[0];
  if (file && file.type.startsWith('image/')) {
    processImageFile(file);
  }
};

const processImageFile = (file) => {
  setFieldValue('coverImage', file);
  
  const reader = new FileReader();
  reader.onload = (e) => {
    imagePreview.value = e.target?.result;
  };
  reader.readAsDataURL(file);
  
  validateField('coverImage');
};

const removeImage = () => {
  setFieldValue('coverImage', null);
  imagePreview.value = '';
  
  if (fileInput.value) {
    fileInput.value.value = '';
  }
  
  if (errors.value.coverImage) {
    validateField('coverImage');
  }
};

const handleSubmit = async () => {
  if (!canPublish.value) return;
  
  success.value = '';
  submitError.value = '';

  const isFormValid = await validateForm();
  if (!isFormValid) {
    submitError.value = 'Lütfen form hatalarını düzeltin.';
    return;
  }
  
  isLoading.value = true;
  
  try {
    const formData = new FormData();
    formData.append('title', values.title.trim());
    formData.append('content', values.content.trim());

    values.categoryIds.forEach((id) => {
      formData.append('categoryIds[]', id);
    });
    
    if (values.coverImage) {
      formData.append('coverImage', values.coverImage);
    }
    
    const response = await PostService.createPost(formData);
    
    if (response.success) {
      success.value = 'Yazı başarıyla yayınlandı!';
      resetFormAndPreview();
      setTimeout(() => {
        router.push('/');
      }, 2000);
    } else {
      submitError.value = response.message || 'Yazı yayınlanırken hata oluştu.';
    }
  } catch (err) {
    console.error('Publish post error:', err);
    if (err.response?.status === 422) {
      const validationErrors = err.response.data?.errors;
      if (validationErrors) {
        Object.keys(validationErrors).forEach(field => {
          if (errors.value[field] !== undefined) {
            errors.value[field] = validationErrors[field][0];
          }
        });
      } else {
        submitError.value = 'Form verilerinde hata var.';
      }
    } else {
      submitError.value = 'Yazı yayınlanırken bir hata oluştu.';
    }
  } finally {
    isLoading.value = false;
  }
};

const resetFormAndPreview = () => {
  resetForm();
  imagePreview.value = '';
  
  if (fileInput.value) {
    fileInput.value.value = '';
  }
};

onMounted(() => {
  if (!AuthService.isAuthenticated()) {
    router.push('/auth/login');
  }
});
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-4 {
  display: -webkit-box;
  -webkit-line-clamp: 4;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

textarea {
  resize: vertical;
  min-height: 300px;
}

input:focus, textarea:focus {
  outline: none;
}

.vue-select-custom {
  font-family: inherit;
}

.vue-select-custom .vs__dropdown-toggle {
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  padding: 0.5rem 0.75rem;
  min-height: 2.5rem;
}

.vue-select-custom .vs__dropdown-toggle:hover {
  border-color: #9ca3af;
}

.vue-select-custom.vs--open .vs__dropdown-toggle {
  border-color: #4f46e5;
  box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
}

.vue-select-custom .vs__selected-options {
  flex-wrap: wrap;
  gap: 0.25rem;
}

.vue-select-custom .vs__selected {
  background-color: #eef2ff;
  color: #4338ca;
  border: 1px solid #c7d2fe;
  border-radius: 0.375rem;
  padding: 0.125rem 0.5rem;
  margin: 0.125rem 0.25rem 0.125rem 0;
  font-size: 0.875rem;
  font-weight: 500;
}

.vue-select-custom .vs__deselect {
  color: #6366f1;
}

.vue-select-custom .vs__deselect:hover {
  color: #4338ca;
}

.vue-select-custom .vs__search {
  font-size: 0.875rem;
  padding: 0.25rem 0;
}

.vue-select-custom .vs__search::placeholder {
  color: #9ca3af;
}

.vue-select-custom .vs__dropdown-menu {
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  max-height: 200px;
}

.vue-select-custom .vs__dropdown-option {
  padding: 0.5rem 0.75rem;
  color: #374151;
  font-size: 0.875rem;
}

.vue-select-custom .vs__dropdown-option--highlight {
  background-color: #4f46e5;
  color: white;
}

.vue-select-custom .vs__dropdown-option--selected {
  background-color: #eef2ff;
  color: #4338ca;
  font-weight: 500;
}

.vue-select-custom .vs__no-options {
  padding: 1rem;
  text-align: center;
  color: #9ca3af;
  font-style: italic;
}

.selected-tag {
  font-size: 0.75rem;
  font-weight: 500;
}

/* Error state */
.vue-select-error .vs__dropdown-toggle {
  border-color: #ef4444;
}

.vue-select-error.vs--open .vs__dropdown-toggle {
  border-color: #ef4444;
  box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.2);
}
</style> 