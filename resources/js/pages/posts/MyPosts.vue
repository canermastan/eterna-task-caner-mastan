<template>
  <div class="px-4 sm:px-0">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-8">
      <div class="min-w-0 flex-1">
        <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl">
          Yazılarım
        </h1>
        <p class="text-gray-600 mt-1">
          Tüm yazılarınızı yönetin, düzenleyin ve durumlarını değiştirin
        </p>
      </div>
      <div class="mt-4 flex md:ml-4 md:mt-0">
        <router-link
          to="/posts/create"
          class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
        >
          <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
          </svg>
          Yeni Yazı
        </router-link>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Toplam Yazı</dt>
                <dd class="text-lg font-medium text-gray-900">{{ totalPosts }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Yayında</dt>
                <dd class="text-lg font-medium text-gray-900">{{ publishedCount }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Taslak</dt>
                <dd class="text-lg font-medium text-gray-900">{{ draftCount }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <svg class="h-6 w-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
              </svg>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Bu Ay</dt>
                <dd class="text-lg font-medium text-gray-900">{{ thisMonthCount }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="mb-6">
      <div class="sm:hidden">
        <label for="tabs" class="sr-only">Select a tab</label>
        <select id="tabs" v-model="activeTab" class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
          <option value="all">Tümü</option>
          <option value="published">Yayında</option>
          <option value="draft">Taslak</option>
        </select>
      </div>
      <div class="hidden sm:block">
        <nav class="flex space-x-8" aria-label="Tabs">
          <button
            @click="activeTab = 'all'"
            :class="activeTab === 'all'
              ? 'border-indigo-500 text-indigo-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
          >
            Tümü ({{ totalPosts }})
          </button>
          <button
            @click="activeTab = 'published'"
            :class="activeTab === 'published'
              ? 'border-indigo-500 text-indigo-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
          >
            Yayında ({{ publishedCount }})
          </button>
          <button
            @click="activeTab = 'draft'"
            :class="activeTab === 'draft'
              ? 'border-indigo-500 text-indigo-600'
              : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
          >
            Taslak ({{ draftCount }})
          </button>
        </nav>
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
          <h3 class="text-sm font-medium text-red-800">
            Hata oluştu
          </h3>
          <p class="mt-2 text-sm text-red-700">
            {{ error.message || 'Yazılar yüklenirken bir hata oluştu.' }}
          </p>
        </div>
      </div>
    </div>

    <!-- Posts Table -->
    <div v-else-if="filteredPosts?.length" class="bg-white shadow rounded-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Yazı
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Durum
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Kategoriler
              </th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Tarih
              </th>
              <th scope="col" class="relative px-6 py-3">
                <span class="sr-only">İşlemler</span>
              </th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr
              v-for="post in filteredPosts"
              :key="post.id"
              class="hover:bg-gray-50"
            >
              <!-- Post Info -->
              <td class="px-6 py-4">
                <div class="flex items-start">
                  <div class="flex-shrink-0">
                    <div 
                      v-if="post.coverImage"
                      class="h-12 w-12 rounded-lg overflow-hidden"
                    >
                      <img 
                        :src="post.coverImage" 
                        :alt="post.title"
                        class="h-full w-full object-cover"
                      />
                    </div>
                    <div v-else class="h-12 w-12 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600"></div>
                  </div>
                  <div class="ml-4">
                    <div class="text-sm font-medium text-gray-900 line-clamp-1">
                      {{ post.title }}
                    </div>
                    <div class="text-sm text-gray-500 line-clamp-2 mt-1">
                      {{ truncateContent(post.content) }}
                    </div>
                  </div>
                </div>
              </td>

              <!-- Status -->
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="post.status === 'published' 
                    ? 'bg-green-100 text-green-800' 
                    : 'bg-yellow-100 text-yellow-800'"
                >
                  {{ post.status === 'published' ? 'Yayında' : 'Taslak' }}
                </span>
              </td>

              <!-- Categories -->
              <td class="px-6 py-4">
                <div class="flex flex-wrap gap-1">
                  <span
                    v-for="category in post.categories"
                    :key="category.id"
                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800"
                  >
                    {{ category.name }}
                  </span>
                  <span v-if="!post.categories?.length" class="text-sm text-gray-400">
                    Kategori yok
                  </span>
                </div>
              </td>

              <!-- Date -->
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="flex flex-col">
                  <span>{{ formatDate(post.createdAt) }}</span>
                  <span v-if="post.publishedAt" class="text-xs text-gray-400">
                    Yayın: {{ formatDate(post.publishedAt) }}
                  </span>
                </div>
              </td>

              <!-- Actions -->
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center justify-end space-x-2">
                  <!-- View Post -->
                  <router-link
                    :to="`/posts/${post.slug}`"
                    class="text-indigo-600 hover:text-indigo-900"
                    title="Görüntüle"
                  >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </router-link>

                  <!-- Edit Post -->
                  <router-link
                    :to="`/posts/${post.id}/edit`"
                    class="text-blue-600 hover:text-blue-900"
                    title="Düzenle"
                  >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </router-link>

                  <!-- Delete Post -->
                  <button
                    @click="confirmDelete(post)"
                    class="text-red-600 hover:text-red-900"
                    title="Sil"
                  >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12">
      <svg
        class="mx-auto h-12 w-12 text-gray-400"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          strokeLinecap="round"
          strokeLinejoin="round"
          strokeWidth={2}
          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
        />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">
        {{ activeTab === 'all' ? 'Henüz yazınız yok' : getEmptyStateTitle() }}
      </h3>
      <p class="mt-1 text-sm text-gray-500">
        {{ activeTab === 'all' ? 'İlk yazınızı oluşturarak başlayın.' : getEmptyStateDescription() }}
      </p>
      <div v-if="activeTab === 'all'" class="mt-6">
        <router-link
          to="/posts/create"
          class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
        >
          <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
          </svg>
          Yeni Yazı Oluştur
        </router-link>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="filteredPosts?.length && pagination.last_page > 1" class="mt-8 flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
      <div class="flex flex-1 justify-between sm:hidden">
        <button
          @click="prevPage"
          :disabled="currentPage <= 1"
          class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Önceki
        </button>
        <button
          @click="nextPage"
          :disabled="currentPage >= pagination.last_page"
          class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Sonraki
        </button>
      </div>
      <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
          <p class="text-sm text-gray-700">
            <span class="font-medium">{{ ((currentPage - 1) * pagination.per_page) + 1 }}</span>
            -
            <span class="font-medium">{{ Math.min(currentPage * pagination.per_page, pagination.total) }}</span>
            arası gösteriliyor, toplam
            <span class="font-medium">{{ pagination.total }}</span>
            yazı
          </p>
        </div>
        <div>
          <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
            <button
              @click="prevPage"
              :disabled="currentPage <= 1"
              class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span class="sr-only">Önceki</span>
              <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
              </svg>
            </button>
            
            <!-- Page numbers -->
            <template v-for="page in Math.min(pagination.last_page, 5)" :key="page">
              <button
                v-if="page >= currentPage - 2 && page <= currentPage + 2"
                @click="goToPage(page)"
                :class="page === currentPage 
                  ? 'relative z-10 inline-flex items-center bg-indigo-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600'
                  : 'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0'"
              >
                {{ page }}
              </button>
            </template>

            <button
              @click="nextPage"
              :disabled="currentPage >= pagination.last_page"
              class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <span class="sr-only">Sonraki</span>
              <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
              </svg>
            </button>
          </nav>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
          <svg class="mx-auto mb-4 text-gray-400 w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 48 48">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v9a2 2 0 002 2h8a2 2 0 002-2V9M8 9h8m-8 0h8m0 0V6a1 1 0 011-1h4a1 1 0 011 1v3m-6 0V4" />
          </svg>
          <h3 class="text-lg font-medium text-gray-900">Yazıyı Sil</h3>
          <div class="mt-2 px-7 py-3">
            <p class="text-sm text-gray-500">
              "<strong>{{ postToDelete?.title }}</strong>" yazısını silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.
            </p>
          </div>
          <div class="flex justify-center gap-4 px-4 py-3">
            <button
              @click="showDeleteModal = false"
              class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none"
            >
              İptal
            </button>
            <button
              @click="deletePost"
              :disabled="isDeleting"
              class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none disabled:opacity-50"
            >
              {{ isDeleting ? 'Siliniyor...' : 'Sil' }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useQuery, useQueryClient, useMutation } from '@tanstack/vue-query';
import { useRouter } from 'vue-router';
import PostService from '@/services/PostService';
import AuthService from '@/services/AuthService';

const router = useRouter();
const queryClient = useQueryClient();

const activeTab = ref('all');
const currentPage = ref(1);
const perPage = ref(15);
const showDeleteModal = ref(false);
const postToDelete = ref(null);
const isTogglingStatus = ref(null);

const currentUser = computed(() => AuthService.getCurrentUser());
const canToggleStatus = computed(() => {
  const user = currentUser.value;
  return user && (user.role === 'admin' || user.role === 'writer');
});

const { data: postsData, isLoading, error } = useQuery({
  queryKey: ['my-posts', currentPage, perPage],
  queryFn: () => PostService.getMyPosts({ 
    page: currentPage.value, 
    per_page: perPage.value 
  }),
});

const posts = computed(() => postsData.value?.data || []);
const pagination = computed(() => postsData.value?.pagination || {
  current_page: 1,
  last_page: 1,
  total: 0,
  per_page: 15,
});

const filteredPosts = computed(() => {
  if (activeTab.value === 'all') return posts.value;
  return posts.value.filter(post => post.status === activeTab.value);
});

const totalPosts = computed(() => posts.value.length);
const publishedCount = computed(() => posts.value.filter(post => post.status === 'published').length);
const draftCount = computed(() => posts.value.filter(post => post.status === 'draft').length);
const thisMonthCount = computed(() => {
  const currentMonth = new Date().getMonth();
  const currentYear = new Date().getFullYear();
  return posts.value.filter(post => {
    const createdDate = new Date(post.createdAt);
    return createdDate.getMonth() === currentMonth && createdDate.getFullYear() === currentYear;
  }).length;
});

const toggleStatusMutation = useMutation({
  mutationFn: (postId) => PostService.toggleDraftPublished(postId),
  onSuccess: () => {
    queryClient.invalidateQueries({ queryKey: ['my-posts'] });
    isTogglingStatus.value = null;
  },
  onError: (error) => {
    console.error('Status toggle error:', error);
    isTogglingStatus.value = null;
    alert('Durum değiştirilirken bir hata oluştu.');
  }
});

const deleteMutation = useMutation({
  mutationFn: (postId) => PostService.deletePost(postId),
  onSuccess: () => {
    queryClient.invalidateQueries({ queryKey: ['my-posts'] });
    showDeleteModal.value = false;
    postToDelete.value = null;
  },
  onError: (error) => {
    console.error('Delete error:', error);
    alert('Yazı silinirken bir hata oluştu.');
  }
});

const toggleStatus = async (post) => {
  isTogglingStatus.value = post.id;
  toggleStatusMutation.mutate(post.id);
};

const confirmDelete = (post) => {
  postToDelete.value = post;
  showDeleteModal.value = true;
};

const deletePost = () => {
  if (postToDelete.value) {
    deleteMutation.mutate(postToDelete.value.id);
  }
};

const isDeleting = computed(() => deleteMutation.isLoading);

// Pagination methods
const goToPage = (page) => {
  currentPage.value = page;
};

const nextPage = () => {
  if (currentPage.value < pagination.value.last_page) {
    currentPage.value++;
  }
};

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

const truncateContent = (content) => {
  if (!content || typeof content !== 'string') return '';
  const plainText = content.replace(/<[^>]*>/g, ''); // Remove HTML tags
  return plainText.length > 100 ? plainText.substring(0, 100) + '...' : plainText;
};

const formatDate = (dateString) => {
  try {
    if (!dateString || typeof dateString !== 'string') return '';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '';
    
    return date.toLocaleDateString('tr-TR', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  } catch (error) {
    console.error('Date formatting error:', error);
    return '';
  }
};

const getEmptyStateTitle = () => {
  return activeTab.value === 'published' 
    ? 'Yayında yazınız yok' 
    : 'Taslak yazınız yok';
};

const getEmptyStateDescription = () => {
  return activeTab.value === 'published' 
    ? 'Henüz yayınlanmış yazınız bulunmuyor.' 
    : 'Henüz taslak yazınız bulunmuyor.';
};
</script>

<style scoped>
.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style> 