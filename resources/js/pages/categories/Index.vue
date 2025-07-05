<template>
  <div class="px-4 sm:px-0">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-8">
      <div class="min-w-0 flex-1">
        <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl">
          Kategori Yönetimi
        </h1>
        <p class="text-gray-600 mt-1">
          Kategorileri ekleyin, düzenleyin ve yönetin
        </p>
      </div>
      <div class="mt-4 md:mt-0 md:ml-4">
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Yeni Kategori
        </button>
      </div>
    </div>



    <!-- Search and Filter -->
    <div class="bg-white shadow rounded-lg mb-6">
      <div class="px-4 py-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
          <div class="flex-1 min-w-0">
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>
              <input
                v-model="searchTerm"
                type="text"
                placeholder="Kategori ara..."
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
              />
            </div>
          </div>
                     <div class="mt-3 sm:mt-0 sm:ml-4">
             <select
               v-model="sortBy"
               class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
             >
               <option value="name">İsme Göre (A-Z)</option>
               <option value="name_desc">İsme Göre (Z-A)</option>
               <option value="created_at">Yeniden Eskiye</option>
               <option value="created_at_desc">Eskiden Yeniye</option>
             </select>
           </div>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div v-if="isLoading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="rounded-md bg-red-50 p-4 mb-6">
      <div class="flex">
        <div class="ml-3">
          <h3 class="text-sm font-medium text-red-800">Hata oluştu</h3>
          <p class="mt-2 text-sm text-red-700">
            {{ error.message || 'Kategoriler yüklenirken bir hata oluştu.' }}
          </p>
        </div>
      </div>
    </div>

    <!-- Categories List -->
    <div v-else-if="filteredCategories?.length" class="bg-white shadow overflow-hidden sm:rounded-md">
      <ul class="divide-y divide-gray-200">
        <li v-for="category in filteredCategories" :key="category.id" class="px-6 py-4">
          <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
              <div class="flex items-center">
                <div class="flex-shrink-0 mr-4">
                  <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                    <span class="text-sm font-medium text-white">
                      {{ category.name.charAt(0).toUpperCase() }}
                    </span>
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm font-medium text-gray-900 truncate">
                    {{ category.name }}
                  </p>
                                     <div class="flex items-center mt-1 text-sm text-gray-500">
                     <span>{{ formatDate(category.created_at) }}</span>
                   </div>
                </div>
              </div>
            </div>
            
                         <div class="flex items-center space-x-2">
               <!-- Actions -->
              <div class="flex items-center space-x-2">
                <button
                  @click="editCategory(category)"
                  class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                >
                  <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                  </svg>
                  Düzenle
                </button>
                
                                 <button
                   @click="confirmDelete(category)"
                   class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                 >
                  <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                  </svg>
                  Sil
                </button>
              </div>
            </div>
          </div>
        </li>
      </ul>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 7a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V9a2 2 0 00-2-2H5z"/>
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">Kategori bulunamadı</h3>
      <p class="mt-1 text-sm text-gray-500">
        {{ searchTerm ? 'Arama kriterlerinize uygun kategori bulunamadı.' : 'Henüz kategori bulunmuyor.' }}
      </p>
      <div class="mt-6">
        <button
          @click="openCreateModal"
          class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
          <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          İlk Kategoriyi Ekle
        </button>
      </div>
    </div>
  </div>

  <!-- Create/Edit Category Modal -->
  <div v-if="showModal" class="fixed inset-0 z-[9999] overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div 
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        @click="cancelModal"
      ></div>

      <!-- Modal positioning -->
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <!-- Modal content -->
      <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-[10000]">
        <div>
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100">
            <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 7a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V9a2 2 0 00-2-2H5z"/>
            </svg>
          </div>
          <div class="mt-3 text-center sm:mt-5">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              {{ isEditMode ? 'Kategori Düzenle' : 'Yeni Kategori Ekle' }}
            </h3>
            <div class="mt-6">
              <form @submit.prevent="saveCategory" class="space-y-6">
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700 text-left">
                    Kategori Adı
                  </label>
                  <div class="mt-1">
                    <input
                      v-model="form.name"
                      type="text"
                      name="name"
                      id="name"
                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                      :class="{ 'border-red-300': errors.name }"
                      placeholder="Kategori adını girin"
                    />
                    <p v-if="errors.name" class="mt-2 text-sm text-red-600">{{ errors.name[0] }}</p>
                  </div>
                </div>

                

                
              </form>
            </div>
          </div>
        </div>
        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
          <button
            @click="saveCategory"
            :disabled="isSaving"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm disabled:opacity-50"
          >
            <svg v-if="isSaving" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ isEditMode ? 'Güncelle' : 'Kaydet' }}
          </button>
          <button
            @click="cancelModal"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
          >
            İptal
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Category Modal -->
  <div v-if="showDeleteModal" class="fixed inset-0 z-[9999] overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div 
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        @click="cancelDelete"
      ></div>

      <!-- Modal positioning -->
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <!-- Modal content -->
      <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6 relative z-[10000]">
        <div class="sm:flex sm:items-start">
          <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
          </div>
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Kategoriyi Sil
            </h3>
            <div class="mt-2">
              <p class="text-sm text-gray-500">
                Bu kategoriyi silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.
              </p>
                             <div v-if="categoryToDelete" class="mt-3 p-3 bg-gray-50 rounded-md">
                 <p class="text-sm text-gray-600 font-medium">
                   {{ categoryToDelete.name }}
                 </p>
               </div>
            </div>
          </div>
        </div>
        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
          <button
            @click="executeDelete"
            :disabled="isDeleting"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
          >
            <svg v-if="isDeleting" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Sil
          </button>
          <button
            @click="cancelDelete"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
          >
            İptal
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue';
import { useQuery, useQueryClient } from '@tanstack/vue-query';
import CategoryService from '@/services/CategoryService';

const searchTerm = ref('');
const sortBy = ref('name');
const showModal = ref(false);
const showDeleteModal = ref(false);
const isEditMode = ref(false);
const isSaving = ref(false);
const isDeleting = ref(false);
const categoryToDelete = ref(null);
const errors = ref({});

const form = ref({
  name: ''
});

const queryClient = useQueryClient();

const { data: categoriesData, isLoading, error } = useQuery({
  queryKey: ['categories'],
  queryFn: () => CategoryService.getCategories(),
});

const categories = computed(() => categoriesData.value?.data || []);

const filteredCategories = computed(() => {
  let filtered = categories.value;

  // Filter by search term
  if (searchTerm.value) {
    const term = searchTerm.value.toLowerCase();
    filtered = filtered.filter(category => 
      category.name.toLowerCase().includes(term)
    );
  }

  // Sort categories
  if (sortBy.value === 'name') {
    filtered = [...filtered].sort((a, b) => a.name.localeCompare(b.name));
  } else if (sortBy.value === 'name_desc') {
    filtered = [...filtered].sort((a, b) => b.name.localeCompare(a.name));
  } else if (sortBy.value === 'created_at') {
    filtered = [...filtered].sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
  } else if (sortBy.value === 'created_at_desc') {
    filtered = [...filtered].sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
  }

  return filtered;
});

const openCreateModal = () => {
  isEditMode.value = false;
  form.value = {
    name: ''
  };
  errors.value = {};
  showModal.value = true;
};

const editCategory = (category) => {
  isEditMode.value = true;
  form.value = {
    id: category.id,
    name: category.name
  };
  errors.value = {};
  showModal.value = true;
};

const cancelModal = () => {
  showModal.value = false;
  errors.value = {};
  form.value = {
    name: ''
  };
};

const saveCategory = async () => {
  if (isSaving.value) return;
  
  isSaving.value = true;
  errors.value = {};

  try {
    if (isEditMode.value) {
      await CategoryService.updateCategory(form.value.id, form.value);
    } else {
      await CategoryService.createCategory(form.value);
    }
    
    queryClient.invalidateQueries({ queryKey: ['categories'] });
    showModal.value = false;
    
  } catch (error) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors;
    }
    console.error('Error saving category:', error);
  } finally {
    isSaving.value = false;
  }
};

const confirmDelete = (category) => {
  categoryToDelete.value = category;
  showDeleteModal.value = true;
};

const cancelDelete = () => {
  categoryToDelete.value = null;
  showDeleteModal.value = false;
};

const executeDelete = async () => {
  if (!categoryToDelete.value || isDeleting.value) return;
  
  isDeleting.value = true;
  
  try {
    await CategoryService.deleteCategory(categoryToDelete.value.id);
    queryClient.invalidateQueries({ queryKey: ['categories'] });
    
    showDeleteModal.value = false;
    categoryToDelete.value = null;
    
  } catch (error) {
    console.error('Error deleting category:', error);
  } finally {
    isDeleting.value = false;
  }
};

const formatDate = (dateString) => {
  try {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('tr-TR', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  } catch (error) {
    return '';
  }
};
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style> 