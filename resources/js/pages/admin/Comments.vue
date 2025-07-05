<template>
  <div class="px-4 sm:px-0">
    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between mb-8">
      <div class="min-w-0 flex-1">
        <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl">
          Yorum Yönetimi
        </h1>
        <p class="text-gray-600 mt-1">
          Yorumları onaylayın, reddedin veya silin
        </p>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
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
                <dt class="text-sm font-medium text-gray-500 truncate">Bekleyen</dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ stats.pending_count || 0 }}
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
                <dt class="text-sm font-medium text-gray-500 truncate">Onaylanan</dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ stats.approved_count || 0 }}
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
              <div class="w-8 h-8 bg-red-100 rounded-md flex items-center justify-center">
                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate">Reddedilen</dt>
                <dd class="text-lg font-medium text-gray-900">
                  {{ stats.rejected_count || 0 }}
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
                <dt class="text-sm font-medium text-gray-500 truncate">Toplam</dt>
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
          @click="activeTab = 'pending'"
          :class="activeTab === 'pending' 
            ? 'border-yellow-500 text-yellow-600' 
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
          class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
        >
          Bekleyen ({{ stats.pending_count || 0 }})
        </button>
        <button
          @click="activeTab = 'approved'"
          :class="activeTab === 'approved' 
            ? 'border-green-500 text-green-600' 
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
          class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
        >
          Onaylanan ({{ stats.approved_count || 0 }})
        </button>
        <button
          @click="activeTab = 'rejected'"
          :class="activeTab === 'rejected' 
            ? 'border-red-500 text-red-600' 
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
          class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm"
        >
          Reddedilen ({{ stats.rejected_count || 0 }})
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
            {{ error.message || 'Yorumlar yüklenirken bir hata oluştu.' }}
          </p>
        </div>
      </div>
    </div>

    <!-- Comments List -->
    <div v-else-if="filteredComments?.length" class="bg-white shadow overflow-hidden sm:rounded-md">
      <ul class="divide-y divide-gray-200">
        <li v-for="comment in filteredComments" :key="comment.id" class="px-6 py-5">
          <div class="flex space-x-3">
            <!-- User Avatar -->
            <div class="h-10 w-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
              <span class="text-sm font-medium text-white">
                {{ comment.user?.first_name?.charAt(0) }}{{ comment.user?.last_name?.charAt(0) }}
              </span>
            </div>
            
            <!-- Comment Content -->
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <!-- User Info -->
                  <div class="flex items-center space-x-2">
                    <p class="text-sm font-medium text-gray-900">
                      {{ comment.user?.first_name }} {{ comment.user?.last_name }}
                    </p>
                    <span class="text-sm text-gray-500">•</span>
                    <p class="text-sm text-gray-500">
                      {{ formatDate(comment.created_at) }}
                    </p>
                    <span v-if="comment.parent_id" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                      Yanıt
                    </span>
                  </div>
                  
                  <!-- Comment Content -->
                  <div class="mt-2">
                    <p class="text-sm text-gray-900 line-clamp-3">
                      {{ formatCommentContent(comment.content) }}
                    </p>
                  </div>
                  
                  <!-- Post Info -->
                  <div class="mt-2 text-sm text-gray-500">
                    <span class="font-medium">Post:</span>
                    <a 
                      :href="`/posts/${comment.post?.slug}`" 
                      class="text-indigo-600 hover:text-indigo-500 ml-1"
                    >
                      {{ comment.post?.title }}
                    </a>
                  </div>
                </div>
                
                <!-- Status Badge -->
                <span
                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ml-4"
                  :class="{
                    'bg-yellow-100 text-yellow-800': comment.status === 'pending',
                    'bg-green-100 text-green-800': comment.status === 'approved',
                    'bg-red-100 text-red-800': comment.status === 'rejected'
                  }"
                >
                  {{ getStatusLabel(comment.status) }}
                </span>
              </div>
              
              <!-- Actions -->
              <div class="mt-3 flex items-center space-x-3">
                <!-- Approve Button -->
                <button
                  v-if="comment.status !== 'approved'"
                  @click="approveComment(comment)"
                  :disabled="isProcessing === comment.id"
                  class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                >
                  <svg v-if="isProcessing === comment.id" class="animate-spin -ml-1 mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Onayla
                </button>
                
                <!-- Reject Button -->
                <button
                  v-if="comment.status !== 'rejected'"
                  @click="rejectComment(comment)"
                  :disabled="isProcessing === comment.id"
                  class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50"
                >
                  <svg v-if="isProcessing === comment.id" class="animate-spin -ml-1 mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Reddet
                </button>
                
                <!-- Delete Button -->
                <button
                  @click="confirmDelete(comment)"
                  class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Sil
                </button>
                
                <!-- View Post Button -->
                <button
                  @click="$router.push(`/posts/${comment.post?.slug}`)"
                  class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                  Postu Görüntüle
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
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.405L3 21l1.405-5.094A8.959 8.959 0 013 12a8 8 0 018-8 8 8 0 018 8z"/>
      </svg>
      <h3 class="mt-2 text-sm font-medium text-gray-900">
        {{ getEmptyStateTitle() }}
      </h3>
      <p class="mt-1 text-sm text-gray-500">
        {{ getEmptyStateDescription() }}
      </p>
    </div>
  </div>

  <!-- Delete Comment Modal -->
  <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
      <!-- Background overlay -->
      <div 
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        @click="cancelDelete"
      ></div>

      <!-- Modal positioning -->
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

      <!-- Modal content -->
      <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
        <div class="sm:flex sm:items-start">
          <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
          </div>
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Yorumu Sil
            </h3>
            <div class="mt-2">
              <p class="text-sm text-gray-500">
                Bu yorumu silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.
              </p>
              <div v-if="commentToDelete" class="mt-3 p-3 bg-gray-50 rounded-md">
                <p class="text-sm text-gray-600 font-medium">
                  {{ commentToDelete.user?.first_name }} {{ commentToDelete.user?.last_name }}
                </p>
                <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                  {{ formatCommentContent(commentToDelete.content) }}
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
          <button
            @click="executeDelete"
            :disabled="isDeletingComment"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
          >
            <svg v-if="isDeletingComment" class="animate-spin -ml-1 mr-1 h-4 w-4" fill="none" viewBox="0 0 24 24">
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
import { computed, ref } from 'vue';
import useAdminComments from '@/composables/useAdminComments';
import { formatDate } from '@/utils/dateUtils';
import { formatCommentContent } from '@/utils/commentUtils';

const activeTab = ref('pending');
const isProcessing = ref(null);
const isDeletingComment = ref(false);
const showDeleteModal = ref(false);
const commentToDelete = ref(null);

const {
  comments,
  stats,
  isLoading,
  error,
  approveComment: approveCommentApi,
  rejectComment: rejectCommentApi,
  deleteComment: deleteCommentApi,
} = useAdminComments();

const filteredComments = computed(() => comments.value.filter(c => c.status === activeTab.value));

const approveComment = async (comment) => {
  try {
    isProcessing.value = comment.id;
    await approveCommentApi(comment);
  } catch (error) {
    console.error('Error approving comment:', error);
  } finally {
    isProcessing.value = null;
  }
};

const rejectComment = async (comment) => {
  try {
    isProcessing.value = comment.id;
    await rejectCommentApi(comment);
  } catch (error) {
    console.error('Error rejecting comment:', error);
  } finally {
    isProcessing.value = null;
  }
};

const confirmDelete = (comment) => {
  commentToDelete.value = comment;
  showDeleteModal.value = true;
};

const cancelDelete = () => {
  commentToDelete.value = null;
  showDeleteModal.value = false;
};

const executeDelete = async () => {
  if (!commentToDelete.value) return;
  
  try {
    isDeletingComment.value = true;
    const comment = commentToDelete.value;
    
    await deleteCommentApi(comment);
    
    showDeleteModal.value = false;
    commentToDelete.value = null;
    
  } catch (error) {
    console.error('Error deleting comment:', error);
  } finally {
    isDeletingComment.value = false;
  }
};

const getStatusLabel = (status) => {
  const labels = {
    pending: 'Bekliyor',
    approved: 'Onaylandı',
    rejected: 'Reddedildi'
  };
  return labels[status] || status;
};

const getEmptyStateTitle = () => {
  const titles = {
    pending: 'Bekleyen yorum yok',
    approved: 'Onaylanmış yorum yok',
    rejected: 'Reddedilmiş yorum yok'
  };
  return titles[activeTab.value] || 'Yorum yok';
};

const getEmptyStateDescription = () => {
  const descriptions = {
    pending: 'Henüz onay bekleyen yorum bulunmuyor.',
    approved: 'Henüz onaylanmış yorum bulunmuyor.',
    rejected: 'Henüz reddedilmiş yorum bulunmuyor.'
  };
  return descriptions[activeTab.value] || 'Henüz yorum bulunmuyor.';
};
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