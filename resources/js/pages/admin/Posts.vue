<template>
  <div class="px-4 sm:px-0">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-8">
      <div class="min-w-0 flex-1">
        <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl">
          Post Yönetimi
        </h1>
        <p class="text-gray-600 mt-1">
          Draft postları yönetin ve yayınlayın
        </p>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-8">
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-yellow-100 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                  <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 2.5a1 1 0 000 2h1a1 1 0 100-2H7z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Taslaklar</dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ stats.draft_count || 0 }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-100 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Yayınlanmışlar</dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ stats.published_count || 0 }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-blue-100 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Toplam Postlar</dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ stats.total_count || 0 }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 mb-6">
      <nav class="-mb-px flex space-x-8">
        <button
          @click="activeTab = 'draft'"
          :class="activeTab === 'draft' 
            ? 'border-indigo-500 text-indigo-600' 
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
          class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
        >
          Taslaklar ({{ stats.draft_count || 0 }})
        </button>
        <button
          @click="activeTab = 'published'"
          :class="activeTab === 'published' 
            ? 'border-indigo-500 text-indigo-600' 
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
          class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
        >
          Yayınlanmışlar ({{ stats.published_count || 0 }})
        </button>
      </nav>
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
            {{ error.message || 'Postlar yüklenirken bir hata oluştu.' }}
          </p>
        </div>
      </div>
    </div>

    <!-- Posts Table -->
    <div v-else-if="filteredPosts?.length" class="bg-white shadow overflow-hidden sm:rounded-md">
      <ul class="divide-y divide-gray-200">
        <li v-for="post in filteredPosts" :key="post.id" class="px-6 py-4">
          <div class="flex items-center justify-between">
            <div class="flex-1 min-w-0">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <p class="text-sm font-medium text-gray-900 truncate">
                    {{ post.title }}
                  </p>
                  <div class="flex items-center mt-1 text-sm text-gray-500">
                    <span>{{ post.user?.first_name }} {{ post.user?.last_name }}</span>
                    <span class="mx-2">•</span>
                    <span>{{ formatDate(post.created_at) }}</span>
                  </div>
                </div>
                <div class="flex items-center space-x-2">
                  <!-- Status Badge -->
                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="post.status === 'published' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-yellow-100 text-yellow-800'"
                  >
                    {{ post.status === 'published' ? 'Yayında' : 'Taslak' }}
                  </span>
                  
                  <!-- Categories -->
                  <div v-if="post.categories?.length" class="flex flex-wrap gap-1">
                    <span
                      v-for="category in post.categories.slice(0, 2)"
                      :key="category.id"
                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"
                    >
                      {{ category.name }}
                    </span>
                    <span
                      v-if="post.categories.length > 2"
                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"
                    >
                      +{{ post.categories.length - 2 }}
                    </span>
                  </div>
                </div>
              </div>
              
              <!-- Content Preview -->
              <p class="mt-2 text-sm text-gray-600 line-clamp-2">
                {{ truncateContent(post.content) }}
              </p>
            </div>
            
            <!-- Actions -->
            <div class="ml-4 flex items-center space-x-2">
              <!-- View Button -->
              <button
                @click="$router.push(`/posts/${post.slug}`)"
                class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Görüntüle
              </button>
              
              <!-- Toggle Status Button -->
              <button
                @click="togglePostStatus(post)"
                :disabled="isTogglingStatus === post.id"
                class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50"
                :class="post.status === 'draft' 
                  ? 'bg-green-600 hover:bg-green-700 focus:ring-green-500' 
                  : 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500'"
              >
                <svg v-if="isTogglingStatus === post.id" class="animate-spin -ml-1 mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ post.status === 'draft' ? 'Yayınla' : 'Taslak Yap' }}
              </button>
            </div>
          </div>
        </li>
      </ul>
    </div>

    <!-- Empty State -->
    <div v-else class="text-center py-12">
      <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">
        {{ activeTab === 'draft' ? 'Taslak post yok' : 'Yayınlanmış post yok' }}
      </h3>
      <p class="mt-1 text-sm text-gray-500">
        {{ activeTab === 'draft' 
          ? 'Henüz taslak durumda post bulunmuyor.' 
          : 'Henüz yayınlanmış post bulunmuyor.' 
        }}
      </p>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import { useQuery, useQueryClient } from '@tanstack/vue-query';
import PostService from '@/services/PostService';

const activeTab = ref('draft');
const isTogglingStatus = ref(null);
const queryClient = useQueryClient();

const { data: postsData, isLoading, error } = useQuery({
  queryKey: ['admin-posts'],
  queryFn: () => PostService.getAllPosts(),
});

const posts = computed(() => postsData.value?.data || []);

const stats = computed(() => {
  const draftCount = posts.value.filter(post => post.status === 'draft').length;
  const publishedCount = posts.value.filter(post => post.status === 'published').length;
  
  return {
    draft_count: draftCount,
    published_count: publishedCount,
    total_count: posts.value.length
  };
});

const filteredPosts = computed(() => {
  return posts.value.filter(post => post.status === activeTab.value);
});

const togglePostStatus = async (post) => {
  try {
    isTogglingStatus.value = post.id;
    
    await PostService.toggleDraftPublished(post.id);
    
    // Invalidate queries to refetch data
    queryClient.invalidateQueries({ queryKey: ['admin-posts'] });
    queryClient.invalidateQueries({ queryKey: ['posts'] }); // Also invalidate public posts
    
  } catch (error) {
    console.error('Error toggling post status:', error);
  } finally {
    isTogglingStatus.value = null;
  }
};

const truncateContent = (content) => {
  if (!content || typeof content !== 'string') return '';
  const plainText = content.replace(/<[^>]*>/g, ''); // Remove HTML tags
  return plainText.length > 100 ? plainText.substring(0, 100) + '...' : plainText;
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