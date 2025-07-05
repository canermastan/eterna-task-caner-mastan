<template>
  <div class="px-4 sm:px-0">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-8">
      <div class="min-w-0 flex-1">
        <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl">
          Blog Yazıları
        </h1>
        <p class="text-gray-600 mt-1">
          En son blog yazılarını keşfedin
        </p>
      </div>
      <div class="mt-4 flex md:ml-4 md:mt-0" v-if="canCreatePost">
        <router-link
          to="/posts/create"
          class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
        >
          <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
          </svg>
          Yeni Yazı
        </router-link>
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

    <!-- Posts Grid -->
    <div v-else-if="posts?.length" class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
      <article
        v-for="post in posts"
        :key="post.id"
        class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-all duration-200 cursor-pointer transform hover:scale-105"
        @click="$router.push(`/posts/${post.slug}`)"
      >
        <!-- Cover Image -->
        <div
          v-if="post.coverImage.cover"
          class="aspect-w-16 aspect-h-9 bg-gray-200"
        >
          <img
            :src="post.coverImage.cover"
            :alt="post.title"
            class="w-full h-48 object-cover"
          />
        </div>
        <div v-else class="h-48 bg-gradient-to-r from-indigo-500 to-purple-600"></div>

        <!-- Content -->
        <div class="p-6">
          <!-- Categories -->
          <div v-if="post.categories?.length" class="flex flex-wrap gap-1 mb-3">
            <span
              v-for="category in post.categories"
              :key="category.id"
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
            >
              {{ category.name }}
            </span>
          </div>

          <!-- Title -->
          <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
            {{ post.title }}
          </h3>

          <!-- Content Preview -->
          <p class="text-gray-600 text-sm mb-4 line-clamp-3">
            {{ truncateContent(post.content) }}
          </p>

          <!-- Meta Info -->
          <div class="flex items-center justify-between text-sm text-gray-500">
            <div class="flex items-center">
              <div class="h-6 w-6 rounded-full bg-gray-300 flex items-center justify-center mr-2">
                <span class="text-xs font-medium">
                  {{ post.user?.first_name?.charAt(0) }}{{ post.user?.last_name?.charAt(0) }}
                </span>
              </div>
              <span>{{ post.user?.first_name }} {{ post.user?.last_name }}</span>
            </div>
            <div class="flex items-center space-x-4">
              <span class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clipRule="evenodd" />
                </svg>
                {{ formatDate(post.publishedAt || post.createdAt) }}
              </span>
              <span
                class="inline-flex items-center px-2 py-1 rounded text-xs font-medium"
                :class="post.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
              >
                {{ post.status === 'published' ? 'Yayında' : 'Taslak' }}
              </span>
            </div>
          </div>
        </div>
      </article>
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
          d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 01-2-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"
        />
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">Henüz yazı yok</h3>
      <p class="mt-1 text-sm text-gray-500">
        {{ canCreatePost ? 'İlk yazınızı oluşturarak başlayın.' : 'Henüz yayınlanmış yazı bulunmuyor.' }}
      </p>
      <div v-if="canCreatePost" class="mt-6">
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
    <div v-if="posts?.length && pagination.last_page > 1" class="mt-8 flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
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
  </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { useQuery, useQueryClient } from '@tanstack/vue-query';
import PostService from '@/services/PostService';
import AuthService from '@/services/AuthService';

const currentPage = ref(1);
const perPage = ref(15);

const queryClient = useQueryClient();

const currentUser = computed(() => AuthService.getCurrentUser());
const canCreatePost = computed(() => {
  const user = currentUser.value;
  return user && (user.role === 'admin' || user.role === 'writer');
});

const { data: postsData, isLoading, error } = useQuery({
  queryKey: ['posts', currentPage, perPage],
  queryFn: () => PostService.getPosts({ 
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
  return plainText.length > 150 ? plainText.substring(0, 150) + '...' : plainText;
};

const formatDate = (dateString) => {
  try {
    if (!dateString || typeof dateString !== 'string') return '';
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '';
    
    return date.toLocaleDateString('tr-TR', {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  } catch (error) {
    console.error('Date formatting error:', error);
    return '';
  }
};

let postsChannel = null;

onMounted(() => {
  if (window.Echo) {
    postsChannel = window.Echo.channel('posts');
    
    postsChannel.listen('.post.published', (event) => {
      queryClient.invalidateQueries({ queryKey: ['posts'] });
    });
    
    postsChannel.listen('.post.status.updated', (event) => {
      queryClient.invalidateQueries({ queryKey: ['posts'] });
      
       // TODO: Show notification for writer (`Post published: ${event.post.title}`);
    });
  } else {
    console.error('Laravel Echo is NOT available. Real-time updates disabled.');
  }
});

onUnmounted(() => {
  if (postsChannel) {
    postsChannel.stopListening('.post.published');
    postsChannel.stopListening('.post.status.updated');
    window.Echo.leaveChannel('posts');
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

.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
