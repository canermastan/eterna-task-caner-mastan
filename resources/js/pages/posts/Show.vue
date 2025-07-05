<template>
  <div v-if="isLoading" class="flex justify-center py-12">
    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
  </div>

  <div v-else-if="error" class="rounded-md bg-red-50 p-4 mb-6">
    <div class="flex">
      <div class="ml-3">
        <h3 class="text-sm font-medium text-red-800">
          Hata oluştu
        </h3>
        <p class="mt-2 text-sm text-red-700">
          {{ error.message || 'Blog yazısı yüklenirken bir hata oluştu.' }}
        </p>
      </div>
    </div>
  </div>

  <article v-else-if="post" class="bg-white rounded-lg shadow-sm overflow-hidden">
    <!-- Cover Image -->
    <div v-if="post.coverImage.cover" class="aspect-w-16 aspect-h-9">
      <img
        :src="post.coverImage.cover"
        :alt="post.title"
        class="w-full h-96 object-cover"
      />
    </div>
    <div v-else class="h-96 bg-gradient-to-r from-indigo-500 to-purple-600"></div>

    <!-- Content -->
    <div class="px-6 py-8 lg:px-12">
      <!-- Categories -->
      <div v-if="post.categories?.length" class="flex flex-wrap gap-2 mb-4">
        <span
          v-for="category in post.categories"
          :key="category.id"
          class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800"
        >
          {{ category.name }}
        </span>
      </div>

      <!-- Title -->
      <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4 leading-tight">
        {{ post.title }}
      </h1>

      <!-- Meta Info -->
      <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-200">
        <div class="flex items-center">
          <div class="h-12 w-12 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center mr-4">
            <span class="text-lg font-medium text-white">
              {{ post.user?.first_name?.charAt(0) }}{{ post.user?.last_name?.charAt(0) }}
            </span>
          </div>
          <div>
            <p class="text-lg font-medium text-gray-900">
              {{ post.user?.first_name }} {{ post.user?.last_name }}
            </p>
            <div class="flex items-center text-sm text-gray-500 space-x-4">
              <!-- Published Date -->
              <div v-if="post?.publishedAt" class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clipRule="evenodd" />
                </svg>
                <span class="font-medium text-green-600">Yayınlandı:</span>
                <span class="ml-1">{{ formatDate(post.publishedAt) }}</span>
              </div>
              <!-- Created Date (if different from published) -->
              <div v-if="post?.createdAt && post.createdAt !== post.publishedAt" class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clipRule="evenodd" />
                </svg>
                <span class="text-gray-400">Oluşturuldu:</span>
                <span class="ml-1">{{ formatDate(post.createdAt) }}</span>
              </div>
              <!-- Fallback to created date only -->
              <div v-if="!post?.publishedAt && post?.createdAt" class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clipRule="evenodd" />
                </svg>
                <span>{{ formatDate(post.createdAt) }}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="flex flex-col items-end space-y-2">
          <div class="flex items-center space-x-2">
            <!-- Edit button - only for post owner and admins -->
            <button
              v-if="canEditPost"
              @click="openEditModal"
              class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium text-indigo-600 bg-indigo-50 hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
            >
              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
              Düzenle
            </button>
            
            <span
              class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
              :class="post.status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
            >
              {{ post.status === 'published' ? 'Yayında' : 'Taslak' }}
            </span>
          </div>
          <!-- Relative time -->
          <span v-if="post?.publishedAt" class="text-xs text-gray-400">
            {{ getRelativeTime(post.publishedAt) }}
          </span>
        </div>
      </div>

      <!-- Content -->
      <div class="prose prose-lg max-w-none">
        <div v-html="post.content"></div>
      </div>
    </div>

    <!-- Comments Section -->
    <div class="bg-gray-50 px-6 py-8 lg:px-12">
      <h2 class="text-2xl font-bold text-gray-900 mb-6">
        Yorumlar
        <span v-if="comments.length > 0" class="text-lg font-normal text-gray-500 ml-2">
          ({{ comments.length }})
        </span>
      </h2>

      <!-- Add Comment Form (if authenticated) -->
      <div v-if="isAuthenticated" class="bg-white rounded-lg p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
          Yorum Ekle
        </h3>
        <form @submit.prevent="submitComment">
          <div class="mb-4">
            <label for="comment" class="sr-only">Yorumunuz</label>
            <textarea
              id="comment"
              v-model="newComment"
              rows="4"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
              placeholder="Yorumunuzu yazın..."
              required
            ></textarea>
          </div>
          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="!newComment.trim() || isSubmittingComment"
              class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="isSubmittingComment" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Yorum Ekle
            </button>
          </div>
        </form>
      </div>

      <!-- Login prompt for guests -->
      <div v-else class="bg-white rounded-lg p-6 mb-6 text-center">
        <p class="text-gray-600 mb-4">Yorum yapabilmek için giriş yapmanız gerekiyor.</p>
        <router-link
          to="/auth/login"
          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700"
        >
          Giriş Yap
        </router-link>
      </div>

      <!-- Comments List -->
      <div class="space-y-6">
        <!-- Loading initial comments -->
        <div v-if="commentsLoading && !comments.length" class="flex justify-center py-8">
          <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
        </div>

        <!-- Empty state -->
        <div v-else-if="!comments?.length && !commentsLoading" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 48 48">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 16c0 2.5 3 4.5 7 4.5s7-2 7-4.5M21 23h14" />
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">Henüz yorum yok</h3>
          <p class="mt-1 text-sm text-gray-500">
            Bu yazıya ilk yorumu yapan siz olun!
          </p>
        </div>

        <!-- Comment items -->
        <div
          v-for="comment in comments"
          :key="comment.id"
          class="bg-white rounded-lg p-6 border border-gray-200 hover:border-gray-300 transition-colors"
        >
          <!-- Comment header -->
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
              <!-- User avatar -->
              <div class="h-10 w-10 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center mr-3">
                <span class="text-sm font-medium text-white">
                  {{ comment.user?.first_name?.charAt(0) }}{{ comment.user?.last_name?.charAt(0) }}
                </span>
              </div>
              <!-- User info -->
              <div>
                <p class="font-medium text-gray-900">
                  {{ comment.user?.first_name }} {{ comment.user?.last_name }}
                </p>
                <div class="flex items-center text-sm text-gray-500 space-x-2">
                  <time :datetime="comment.created_at">
                    {{ formatDate(comment.created_at) }}
                  </time>
                  <span v-if="comment.created_at_human" class="text-xs">
                    ({{ comment.created_at_human }})
                  </span>
                </div>
              </div>
            </div>
            
            <!-- Comment status (for admin) -->
            <div v-if="isAdmin" class="flex items-center space-x-2">
              <span
                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                :class="{
                  'bg-green-100 text-green-800': comment.status === 'approved',
                  'bg-yellow-100 text-yellow-800': comment.status === 'pending',
                  'bg-red-100 text-red-800': comment.status === 'rejected'
                }"
              >
                {{ comment.status_label || comment.status }}
              </span>
              
              <!-- Admin actions -->
              <div v-if="comment.status === 'pending'" class="flex space-x-1">
                <button
                  @click="approveComment(comment)"
                  class="text-green-600 hover:text-green-800 p-1 rounded"
                  title="Onayla"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  </svg>
                </button>
                <button
                  @click="rejectComment(comment)"
                  class="text-red-600 hover:text-red-800 p-1 rounded"
                  title="Reddet"
                >
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- ADDED: Pending status indicator for regular users (their own comments) -->
            <div v-else-if="comment.status === 'pending' && comment.user?.id === currentUser?.id" class="flex items-center">
              <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                </svg>
                Onay Bekliyor
              </span>
            </div>
          </div>

          <!-- Comment content -->
          <div class="prose prose-sm max-w-none">
            <p class="text-gray-700 leading-relaxed">{{ formatCommentContent(comment.content) }}</p>
          </div>

          <!-- Comment actions -->
          <div class="mt-4 flex items-center justify-between">
            <div class="flex items-center space-x-4 text-sm">
              <button
                v-if="isAuthenticated && editingComment !== comment.id"
                @click="startReply(comment)"
                class="text-gray-500 hover:text-indigo-600 font-medium"
              >
                Yanıtla
              </button>
            <button
              v-if="isAdmin && replyingTo !== comment.id"
              @click="startEdit(comment)"
              class="text-gray-500 hover:text-orange-600 font-medium"
            >
              Düzenle
            </button>
            <button
              v-if="isAdmin"
              @click="confirmDelete(comment)"
              class="text-gray-500 hover:text-red-600 font-medium"
            >
              Sil
            </button>
            </div>
            
            <!-- Reply count indicator -->
            <div v-if="comment.children && comment.children.length > 0" class="text-xs text-gray-500">
              <span class="bg-gray-100 px-2 py-1 rounded-full">
                {{ comment.children.length }} yanıt
              </span>
            </div>
          </div>

          <!-- Edit Form -->
          <div v-if="editingComment === comment.id" class="mt-4 bg-gray-50 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Yorumu Düzenle</h4>
            <form @submit.prevent="submitEdit(comment)">
              <div class="mb-3">
                <textarea
                  v-model="editContent"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                  placeholder="Yorumunuzu düzenleyin..."
                  required
                ></textarea>
              </div>
              <div class="flex justify-end space-x-2">
                <button
                  type="button"
                  @click="cancelEdit"
                  class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                >
                  İptal
                </button>
                <button
                  type="submit"
                  :disabled="!editContent.trim() || isSubmittingEdit"
                  class="px-3 py-1.5 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg v-if="isSubmittingEdit" class="animate-spin -ml-1 mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Güncelle
                </button>
              </div>
            </form>
          </div>

          <!-- Reply Form -->
          <div v-if="replyingTo === comment.id" class="mt-4 bg-blue-50 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-900 mb-3">
              <span class="text-indigo-600">{{ comment.user?.first_name }}</span> kullanıcısına yanıt ver
            </h4>
            <form @submit.prevent="submitReply(comment)">
              <div class="mb-3">
                <textarea
                  v-model="replyContent"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                  placeholder="Yanıtınızı yazın..."
                  required
                ></textarea>
              </div>
              <div class="flex justify-end space-x-2">
                <button
                  type="button"
                  @click="cancelReply"
                  class="px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
                >
                  İptal
                </button>
                <button
                  type="submit"
                  :disabled="!replyContent.trim() || isSubmittingReply"
                  class="px-3 py-1.5 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <svg v-if="isSubmittingReply" class="animate-spin -ml-1 mr-1 h-3 w-3" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  Yanıtla
                </button>
              </div>
                         </form>
           </div>

          <!-- Child Comments (Replies) -->
          <div v-if="comment.children && comment.children.length > 0" class="mt-4 ml-12 space-y-4">
            <div class="border-l-2 border-gray-200 pl-4">
              <div
                v-for="reply in comment.children"
                :key="reply.id"
                class="bg-gray-50 rounded-lg p-4 border border-gray-100"
              >
                <!-- Reply header -->
                <div class="flex items-center justify-between mb-3">
                  <div class="flex items-center">
                    <!-- Reply user avatar -->
                    <div class="h-8 w-8 rounded-full bg-gradient-to-r from-green-500 to-blue-600 flex items-center justify-center mr-3">
                      <span class="text-xs font-medium text-white">
                        {{ reply.user?.first_name?.charAt(0) }}{{ reply.user?.last_name?.charAt(0) }}
                      </span>
                    </div>
                    <!-- Reply user info -->
                    <div>
                      <p class="text-sm font-medium text-gray-900">
                        {{ reply.user?.first_name }} {{ reply.user?.last_name }}
                      </p>
                      <div class="flex items-center text-xs text-gray-500 space-x-1">
                        <time :datetime="reply.created_at">
                          {{ formatDate(reply.created_at) }}
                        </time>
                        <span v-if="reply.created_at_human" class="text-xs">
                          ({{ reply.created_at_human }})
                        </span>
                        <span class="bg-green-100 text-green-700 px-1 py-0.5 rounded text-xs font-medium ml-2">
                          Yanıt
                        </span>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Reply status (for admin) -->
                  <div v-if="isAdmin" class="flex items-center space-x-1">
                    <span
                      class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium"
                      :class="{
                        'bg-green-100 text-green-800': reply.status === 'approved',
                        'bg-yellow-100 text-yellow-800': reply.status === 'pending',
                        'bg-red-100 text-red-800': reply.status === 'rejected'
                      }"
                    >
                      {{ reply.status_label || reply.status }}
                    </span>
                  </div>

                  <!-- ADDED: Pending status indicator for regular users (their own replies) -->
                  <div v-else-if="reply.status === 'pending' && reply.user?.id === currentUser?.id" class="flex items-center">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                      <svg class="w-2.5 h-2.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                      </svg>
                      Onay Bekliyor
                    </span>
                  </div>
                </div>

                <!-- Reply content -->
                <div class="prose prose-sm max-w-none">
                  <p class="text-gray-700 leading-relaxed text-sm">{{ formatCommentContent(reply.content) }}</p>
                </div>

                <!-- Reply actions -->
                <div class="mt-3 flex items-center space-x-3 text-xs">
                  <button
                    v-if="isAdmin"
                    @click="startEdit(reply)"
                    class="text-gray-500 hover:text-orange-600 font-medium"
                  >
                    Düzenle
                  </button>
                  <button
                    v-if="isAdmin"
                    @click="confirmDelete(reply)"
                    class="text-gray-500 hover:text-red-600 font-medium"
                  >
                    Sil
                  </button>
                </div>
              </div>
            </div>
          </div>
         </div>

         <!-- Load more indicator -->
        <div v-if="commentsLoading && comments.length > 0" class="flex justify-center py-6">
          <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600"></div>
        </div>

        <!-- Load more button (alternative to auto-scroll) -->
        <div v-if="commentsPagination.has_more_pages && !commentsLoading" class="text-center py-6">
          <button
            @click="loadMoreComments"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            <svg class="mr-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
            Daha Fazla Yorum Yükle (Sayfa {{ commentsPagination.current_page + 1 }})
          </button>
        </div>

        <!-- End of comments indicator -->
        <div v-if="!commentsPagination.has_more_pages && comments.length > 0" class="text-center py-6">
          <p class="text-sm text-gray-500">
            Tüm yorumlar yüklendi ({{ commentsPagination.total }} yorum)
          </p>
        </div>
      </div>
    </div>
  </article>

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
                  {{ commentToDelete.content }}
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
          <button
            @click="executeDelete"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
          >
            Sil
          </button>
          <button
            @click="cancelDelete"
            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm"
          >
            İptal
          </button>
        </div>
      </div>
    </div>
  </div>

    <!-- Edit Post Modal -->
  <div v-if="showEditModal" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
      <!-- Background overlay -->
      <div 
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
        @click="closeEditModal"
      ></div>

      <!-- Modal content -->
      <div class="relative bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <form @submit.prevent="submitPostEdit" class="p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
              Yazıyı Düzenle
            </h3>
            <button
              type="button"
              @click="closeEditModal"
              class="text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-md p-1"
            >
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-4">
              <!-- Title -->
              <div>
                <label for="edit-title" class="block text-sm font-medium text-gray-700 mb-2">
                  Başlık *
                </label>
                <input
                  id="edit-title"
                  v-model="editValues.title"
                  @blur="markEditFieldAsTouched('title')"
                  type="text"
                  placeholder="Yazı başlığınızı girin..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                  :class="{ 'border-red-500': editTouched.title && editErrors.title }"
                />
                <p v-if="editTouched.title && editErrors.title" class="text-red-500 text-sm mt-1">
                  {{ editErrors.title }}
                </p>
              </div>

              <!-- Content -->
              <div>
                <label for="edit-content" class="block text-sm font-medium text-gray-700 mb-2">
                  İçerik *
                </label>
                <textarea
                  id="edit-content"
                  v-model="editValues.content"
                  @blur="markEditFieldAsTouched('content')"
                  rows="12"
                  placeholder="Yazınızın içeriğini buraya yazın..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                  :class="{ 'border-red-500': editTouched.content && editErrors.content }"
                ></textarea>
                <p v-if="editTouched.content && editErrors.content" class="text-red-500 text-sm mt-1">
                  {{ editErrors.content }}
                </p>
              </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-4">
              <!-- Categories -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Kategoriler *
                </label>
                <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-300 rounded-md p-3">
                  <div
                    v-for="category in availableCategories"
                    :key="category.id"
                    class="flex items-center"
                  >
                    <input
                      :id="`edit-category-${category.id}`"
                      v-model="editValues.categoryIds"
                      :value="category.id"
                      type="checkbox"
                      class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    />
                    <label
                      :for="`edit-category-${category.id}`"
                      class="ml-3 text-sm text-gray-700 cursor-pointer"
                    >
                      {{ category.name }}
                    </label>
                  </div>
                </div>
                <p v-if="editTouched.categoryIds && editErrors.categoryIds" class="text-red-500 text-sm mt-1">
                  {{ editErrors.categoryIds }}
                </p>
              </div>

              <!-- Cover Image -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Kapak Resmi
                </label>
                
                <!-- Hidden file input -->
                <input
                  ref="editFileInput"
                  type="file"
                  accept="image/*"
                  @change="handleEditImageUpload"
                  class="hidden"
                />
                
                <!-- Image preview or upload area -->
                <div v-if="editImagePreview" class="relative group">
                  <img
                    :src="editImagePreview"
                    alt="Kapak resmi önizleme"
                    class="w-full h-48 object-cover rounded-lg"
                  />
                  <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center space-x-3">
                    <button
                      type="button"
                      @click="triggerEditFileInput"
                      class="px-3 py-1 bg-white text-gray-700 rounded-md text-sm font-medium hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                      Değiştir
                    </button>
                    <button
                      type="button"
                      @click="removeEditImage"
                      class="px-3 py-1 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                    >
                      Kaldır
                    </button>
                  </div>
                </div>
                
                <!-- Upload area -->
                <div
                  v-else
                  @click="triggerEditFileInput"
                  @dragover.prevent
                  @drop.prevent="handleEditImageDrop"
                  class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-indigo-500 hover:bg-indigo-50 transition-colors"
                >
                  <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  <div class="mt-4">
                    <p class="text-sm text-gray-600">
                      <span class="font-medium text-indigo-600">Resim seçmek için tıklayın</span>
                      <br />
                      veya sürükleyip bırakın
                    </p>
                    <p class="text-xs text-gray-500 mt-2">
                      PNG, JPG, JPEG, GIF (max 2MB)
                    </p>
                  </div>
                </div>
                
                <p v-if="editTouched.coverImage && editErrors.coverImage" class="text-red-500 text-sm mt-1">
                  {{ editErrors.coverImage }}
                </p>
              </div>
            </div>
          </div>

          <!-- Modal Actions -->
          <div class="mt-6 flex justify-end space-x-3">
            <button
              type="button"
              @click="closeEditModal"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              İptal
            </button>
            <button
              type="submit"
              :disabled="!editIsValid || isSubmittingPostEdit"
              class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <svg v-if="isSubmittingPostEdit" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Güncelle
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRoute } from 'vue-router';
import { useQuery, useQueryClient } from '@tanstack/vue-query';
import PostService from '@/services/PostService';
import CommentService from '@/services/CommentService';
import AuthService from '@/services/AuthService';
import CategoryService from '@/services/CategoryService';
import { formatDate, getRelativeTime } from '@/utils/dateUtils';
import { formatCommentContent } from '@/utils/commentUtils';
import useInfiniteScroll from '@/composables/useInfiniteScroll';
import useComments from '@/composables/useComments';
import { useFormValidation } from '@/composables/useFormValidation';
import * as yup from 'yup';

const route = useRoute();
const queryClient = useQueryClient();

const newComment = ref('');
const isSubmittingComment = ref(false);

// Reply, Edit, Delete states
const replyingTo = ref(null);
const editingComment = ref(null);
const replyContent = ref('');
const editContent = ref('');
const isSubmittingReply = ref(false);
const isSubmittingEdit = ref(false);
const showDeleteModal = ref(false);
const commentToDelete = ref(null);

// Edit Post Modal states
const showEditModal = ref(false);
const isSubmittingPostEdit = ref(false);
const availableCategories = ref([]);
const editImagePreview = ref(null);
const editFileInput = ref(null);

const editValidationSchema = yup.object({
  title: yup.string().required('Başlık zorunludur').min(3, 'Başlık en az 3 karakter olmalıdır'),
  content: yup.string().required('İçerik zorunludur').min(10, 'İçerik en az 10 karakter olmalıdır'),
  categoryIds: yup.array().min(1, 'En az bir kategori seçmelisiniz'),
  coverImage: yup.mixed().nullable()
    .test('fileSize', 'Dosya boyutu 2MB\'dan küçük olmalıdır', (value) => {
      if (!value) return true;
      return value.size <= 2 * 1024 * 1024;
    })
    .test('fileType', 'Sadece PNG, JPG, JPEG, GIF dosyaları desteklenir', (value) => {
      if (!value) return true;
      const validTypes = ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'];
      return validTypes.includes(value.type);
    })
});

const {
  values: editValues,
  errors: editErrors,
  touched: editTouched,
  isValid: editIsValid,
  setFieldValue: setEditFieldValue,
  setValues: setEditValues,
  validateForm: validateEdit,
  resetForm: resetEditForm,
  markFieldAsTouched: markEditFieldAsTouched
} = useFormValidation(editValidationSchema, {
  title: '',
  content: '',
  categoryIds: [],
  coverImage: null
});

const isAuthenticated = computed(() => AuthService.isAuthenticated());
const currentUser = computed(() => AuthService.getCurrentUser());
const isAdmin = computed(() => currentUser.value?.role === 'admin');
const canEditPost = computed(() => {
  if (!isAuthenticated.value || !post.value) return false;
  return isAdmin.value || post.value.user?.id === currentUser.value?.id;
});

// Fetch post data
const { data: post, isLoading, error } = useQuery({
  queryKey: ['post', route.params.id], // route.params.id is the slug of the post
  queryFn: () => PostService.getPostBySlug(route.params.id),
});

const postId = computed(() => post.value?.id);

const {
  comments,
  commentsLoading,
  commentsError,
  pagination: commentsPagination,
  loadMore,
} = useComments(postId, isAdmin, currentUser);

// Alias for template compatibility
const loadMoreComments = loadMore;

const submitComment = async () => {
  if (!newComment.value.trim() || !post.value?.id) return;

  isSubmittingComment.value = true;
  
  // Create optimistic comment with correct status logic
  const optimisticComment = {
    id: `temp-${Date.now()}`, // Temporary ID
    content: newComment.value.trim(),
    user: currentUser.value,
    post_id: post.value.id,
    parent_id: null,
    status: isAdmin.value ? 'approved' : 'pending',
    status_label: isAdmin.value ? 'approved' : 'pending',
    created_at: new Date().toISOString(),
    created_at_human: 'şimdi',
    children: [],
    isOptimistic: true // Flag to identify optimistic comments
  };
  
  // Add to UI immediately (only if admin or user's own comment)
  const shouldShowOptimistic = isAdmin.value || optimisticComment.status === 'pending';
  
  if (shouldShowOptimistic) {
    const currentData = queryClient.getQueryData(['comments', 'post', postId.value]);
    if (currentData) {
      const optimisticData = {
        ...currentData,
        data: [optimisticComment, ...currentData.data],
        pagination: {
          ...currentData.pagination,
          total: currentData.pagination.total + 1
        }
      };
      
      queryClient.setQueryData(['comments', 'post', postId.value], optimisticData);
      console.log('⚡ Optimistic comment added to UI');
    }
  }

  try {
    const commentData = {
      post_id: post.value.id,
      content: newComment.value.trim(),
      parent_id: null
    };

    const response = await CommentService.createComment(commentData);
    
    // Clear form
    newComment.value = '';
    
    // Handle response based on comment status
    const realComment = response.data || response;
    
    if (shouldShowOptimistic) {
      if (realComment.status === 'approved') {
        // Remove optimistic comment, Echo event will add the real one
        const currentDataAfter = queryClient.getQueryData(['comments', 'post', postId.value]);
        
        if (currentDataAfter) {
          const updatedData = {
            ...currentDataAfter,
            data: currentDataAfter.data.filter(comment => comment.id !== optimisticComment.id),
            pagination: {
              ...currentDataAfter.pagination,
              total: currentDataAfter.pagination.total - 1
            }
          };
          
          queryClient.setQueryData(['comments', 'post', postId.value], updatedData);
          console.log('✅ Optimistic comment removed, waiting for Echo event');
        }
      } else {
        // For pending comments, replace optimistic with real data
        const currentDataAfter = queryClient.getQueryData(['comments', 'post', postId.value]);
        
        if (currentDataAfter) {
          const updatedData = {
            ...currentDataAfter,
            data: currentDataAfter.data.map(comment => 
              comment.id === optimisticComment.id ? { 
                ...realComment, 
                children: [],
                isOptimistic: false 
              } : comment
            )
          };
          
          queryClient.setQueryData(['comments', 'post', postId.value], updatedData);
          console.log('✅ Pending comment updated with real data');
        }
      }
    }
    
    console.log('✅ Comment created successfully');
    
    // Show success message for non-admin users
    if (!isAdmin.value) {
      // TODO: Show brief success notification instead of alert
      console.log('📝 Comment submitted and pending approval');
    }
    
  } catch (error) {
    console.error('Comment submission error:', error);
    
    // Remove optimistic comment on error (only if it was added)
    if (shouldShowOptimistic) {
      const currentDataError = queryClient.getQueryData(['comments', 'post', postId.value]);
      if (currentDataError) {
        const rollbackData = {
          ...currentDataError,
          data: currentDataError.data.filter(comment => comment.id !== optimisticComment.id),
          pagination: {
            ...currentDataError.pagination,
            total: currentDataError.pagination.total - 1
          }
        };
        
        queryClient.setQueryData(['comments', 'post', postId.value], rollbackData);
        console.log('🔄 Optimistic comment removed due to error');
      }
    }
    
    alert('Yorum gönderilirken bir hata oluştu.');
  } finally {
    isSubmittingComment.value = false;
  }
};

const submitReply = async (parentComment) => {
  if (!replyContent.value.trim() || !post.value?.id) return;

  isSubmittingReply.value = true;
  
  // Create optimistic reply with correct status
  const optimisticReply = {
    id: `temp-${Date.now()}`,
    content: replyContent.value.trim(),
    user: currentUser.value,
    post_id: post.value.id,
    parent_id: parentComment.id,
    status: isAdmin.value ? 'approved' : 'pending',
    status_label: isAdmin.value ? 'approved' : 'pending',
    created_at: new Date().toISOString(),
    created_at_human: 'şimdi',
    isOptimistic: true
  };
  
  // Add optimistic reply to UI immediately (only if admin or user's own reply)
  const shouldShowOptimistic = isAdmin.value || optimisticReply.status === 'pending';
  
  if (shouldShowOptimistic) {
    const currentData = queryClient.getQueryData(['comments', 'post', postId.value]);
    if (currentData) {
      const parentIndex = currentData.data.findIndex(c => c.id === parentComment.id);
      if (parentIndex !== -1) {
        const optimisticData = { ...currentData };
        optimisticData.data = [...optimisticData.data];
        optimisticData.data[parentIndex] = {
          ...optimisticData.data[parentIndex],
          children: [
            ...(optimisticData.data[parentIndex].children || []),
            optimisticReply
          ]
        };
        optimisticData.pagination = {
          ...optimisticData.pagination,
          total: optimisticData.pagination.total + 1
        };
        
        queryClient.setQueryData(['comments', 'post', postId.value], optimisticData);
        console.log('⚡ Optimistic reply added to UI');
      }
    }
  }

  try {
    const replyData = {
      post_id: post.value.id,
      content: replyContent.value.trim(),
      parent_id: parentComment.id
    };

    const response = await CommentService.createComment(replyData);
    
    // Clear form and close reply
    replyContent.value = '';
    replyingTo.value = null;
    
    // Handle response based on reply status
    const realReply = response.data || response;
    
    if (shouldShowOptimistic) {
      const currentDataAfter = queryClient.getQueryData(['comments', 'post', postId.value]);
      
      if (currentDataAfter) {
        const parentIndex = currentDataAfter.data.findIndex(c => c.id === parentComment.id);
        if (parentIndex !== -1) {
          if (realReply.status === 'approved') {
            // Remove optimistic reply, Echo event will add the real one
            const updatedData = { ...currentDataAfter };
            updatedData.data = [...updatedData.data];
            updatedData.data[parentIndex] = {
              ...updatedData.data[parentIndex],
              children: updatedData.data[parentIndex].children.filter(
                child => child.id !== optimisticReply.id
              )
            };
            updatedData.pagination = {
              ...updatedData.pagination,
              total: updatedData.pagination.total - 1
            };
            
            queryClient.setQueryData(['comments', 'post', postId.value], updatedData);
            console.log('✅ Optimistic reply removed, waiting for Echo event');
          } else {
            // For pending replies, replace optimistic with real data
            const updatedData = { ...currentDataAfter };
            updatedData.data = [...updatedData.data];
            updatedData.data[parentIndex] = {
              ...updatedData.data[parentIndex],
              children: updatedData.data[parentIndex].children.map(child =>
                child.id === optimisticReply.id ? { ...realReply, isOptimistic: false } : child
              )
            };
            
            queryClient.setQueryData(['comments', 'post', postId.value], updatedData);
            console.log('✅ Pending reply updated with real data');
          }
        }
      }
    }
    
    console.log('✅ Reply created successfully');
    
    if (!isAdmin.value) {
      console.log('📝 Reply submitted and pending approval');
    }
    
  } catch (error) {
    console.error('Reply submission error:', error);
    
    // Remove optimistic reply on error (only if it was added)
    if (shouldShowOptimistic) {
      const currentDataError = queryClient.getQueryData(['comments', 'post', postId.value]);
      if (currentDataError) {
        const parentIndex = currentDataError.data.findIndex(c => c.id === parentComment.id);
        if (parentIndex !== -1) {
          const rollbackData = { ...currentDataError };
          rollbackData.data = [...rollbackData.data];
          rollbackData.data[parentIndex] = {
            ...rollbackData.data[parentIndex],
            children: rollbackData.data[parentIndex].children.filter(
              child => child.id !== optimisticReply.id
            )
          };
          rollbackData.pagination = {
            ...rollbackData.pagination,
            total: rollbackData.pagination.total - 1
          };
          
          queryClient.setQueryData(['comments', 'post', postId.value], rollbackData);
          console.log('🔄 Optimistic reply removed due to error');
        }
      }
    }
    
    alert('Yanıt gönderilirken bir hata oluştu.');
  } finally {
    isSubmittingReply.value = false;
  }
};

const startReply = (comment) => {
  replyingTo.value = comment.id;
  replyContent.value = '';
  editingComment.value = null; // Close edit if open
};

const cancelReply = () => {
  replyingTo.value = null;
  replyContent.value = '';
};

const startEdit = (comment) => {
  editingComment.value = comment.id;
  editContent.value = comment.content;
  replyingTo.value = null; // Close reply if open
};

const cancelEdit = () => {
  editingComment.value = null;
  editContent.value = '';
};

const submitEdit = async (comment) => {
  if (!editContent.value.trim()) return;

  isSubmittingEdit.value = true;
  try {
    const updateData = {
      content: editContent.value.trim()
    };

    const response = await CommentService.updateComment(comment.id, updateData);
    
    // Clear form and close edit first
    editContent.value = '';
    editingComment.value = null;
    
    // OPTIMIZED: Update comment in cache directly instead of invalidating
    const updatedComment = response.data || response;
    const currentData = queryClient.getQueryData(['comments', 'post', postId.value]);
    
    if (currentData) {
      const updatedData = { ...currentData };
      updatedData.data = updatedData.data.map(c => {
        // Update root comment
        if (c.id === comment.id) {
          return { ...c, ...updatedComment };
        }
        
        // Update reply comment
        if (c.children) {
          const updatedChildren = c.children.map(child => 
            child.id === comment.id ? { ...child, ...updatedComment } : child
          );
          return { ...c, children: updatedChildren };
        }
        
        return c;
      });
      
      queryClient.setQueryData(['comments', 'post', postId.value], updatedData);
      console.log('✅ Comment edit applied instantly');
    }
    
  } catch (error) {
    console.error('❌ Edit submission error:', error);
    alert('Yorum düzenlenirken bir hata oluştu.');
  } finally {
    isSubmittingEdit.value = false;
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
    await CommentService.deleteComment(commentToDelete.value.id);
    
    // OPTIMIZED: Remove comment from cache directly
    const currentData = queryClient.getQueryData(['comments', 'post', postId.value]);
    if (currentData) {
      const deletedId = commentToDelete.value.id;
      const updatedData = { ...currentData };
      
      // Remove from root comments or from children
      updatedData.data = updatedData.data.reduce((acc, comment) => {
        // Skip if this root comment was deleted
        if (comment.id === deletedId) {
          updatedData.pagination.total--;
          return acc;
        }
        
        // Remove from children if reply was deleted
        if (comment.children) {
          const beforeLength = comment.children.length;
          const filteredChildren = comment.children.filter(child => child.id !== deletedId);
          if (filteredChildren.length < beforeLength) {
            updatedData.pagination.total--;
          }
          comment.children = filteredChildren;
        }
        
        acc.push(comment);
        return acc;
      }, []);
      
      queryClient.setQueryData(['comments', 'post', postId.value], updatedData);
      console.log('✅ Comment deletion applied instantly');
    }
    
    // Close modal
    cancelDelete();
    
  } catch (error) {
    console.error('❌ Error deleting comment:', error);
    alert('Yorum silinirken bir hata oluştu.');
  }
};

// OPTIMIZED: Admin actions with instant cache updates
const approveComment = async (comment) => {
  try {
    await CommentService.approveComment(comment.id);
    
    // Update comment status in cache directly
    const currentData = queryClient.getQueryData(['comments', 'post', postId.value]);
    if (currentData) {
      const updatedData = { ...currentData };
      updatedData.data = updatedData.data.map(c => {
        // Update root comment
        if (c.id === comment.id) {
          return { ...c, status: 'approved', status_label: 'approved' };
        }
        
        // Update reply comment
        if (c.children) {
          const updatedChildren = c.children.map(child => 
            child.id === comment.id ? { ...child, status: 'approved', status_label: 'approved' } : child
          );
          return { ...c, children: updatedChildren };
        }
        
        return c;
      });
      
      queryClient.setQueryData(['comments', 'post', postId.value], updatedData);
      console.log('✅ Comment approval applied instantly');
    }
    
  } catch (error) {
    console.error('❌ Error approving comment:', error);
    alert('Yorum onaylanırken bir hata oluştu.');
  }
};

const rejectComment = async (comment) => {
  try {
    await CommentService.rejectComment(comment.id);
    
    // Yorum reddedildikten sonra listeden tamamen kaldır
    const currentData = queryClient.getQueryData(['comments', 'post', postId.value]);
    if (currentData) {
      const updatedData = { ...currentData };
      updatedData.data = updatedData.data.reduce((acc, c) => {
        if (c.id === comment.id) {
          updatedData.pagination.total--;
          return acc; // skip
        }
        if (c.children) {
          const before = c.children.length;
          c.children = c.children.filter(child => child.id !== comment.id);
          if (c.children.length < before) {
            updatedData.pagination.total--;
          }
        }
        acc.push(c);
        return acc;
      }, []);

      queryClient.setQueryData(['comments', 'post', postId.value], updatedData);
      console.log('✅ Comment rejection: kaldırıldı');
    }

  } catch (error) {
    console.error('❌ Error rejecting comment:', error);
    alert('Yorum reddedilirken bir hata oluştu.');
  }
};

// Edit Post Modal Methods
const openEditModal = async () => {
  try {
    // Fetch categories
    const categoriesResponse = await CategoryService.getCategories();
    availableCategories.value = categoriesResponse;
    
    // Populate form with current post data
    const currentCategoryIds = post.value.categories?.map(cat => cat.id) || [];
    setEditValues({
      title: post.value.title || '',
      content: post.value.content || '',
      categoryIds: currentCategoryIds,
      coverImage: null
    });
    
    // Set image preview if exists
    if (post.value.coverImage?.cover) {
      editImagePreview.value = post.value.coverImage.cover;
    }
    
    showEditModal.value = true;
  } catch (error) {
    console.error('Error opening edit modal:', error);
    alert('Düzenleme modalı açılırken bir hata oluştu.');
  }
};

const closeEditModal = () => {
  showEditModal.value = false;
  resetEditForm();
  editImagePreview.value = null;
  if (editFileInput.value) {
    editFileInput.value.value = '';
  }
};

const triggerEditFileInput = () => {
  editFileInput.value?.click();
};

const handleEditImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    processEditImageFile(file);
  }
};

const handleEditImageDrop = (event) => {
  event.preventDefault();
  const file = event.dataTransfer.files[0];
  if (file) {
    processEditImageFile(file);
  }
};

const processEditImageFile = (file) => {
  // Validate file type
  const validTypes = ['image/png', 'image/jpg', 'image/jpeg', 'image/gif'];
  if (!validTypes.includes(file.type)) {
    alert('Sadece PNG, JPG, JPEG, GIF dosyaları desteklenir.');
    return;
  }
  
  // Validate file size (2MB)
  if (file.size > 2 * 1024 * 1024) {
    alert('Dosya boyutu 2MB\'dan küçük olmalıdır.');
    return;
  }
  
  // Set file to form
  setEditFieldValue('coverImage', file);
  
  // Create preview
  const reader = new FileReader();
  reader.onload = (e) => {
    editImagePreview.value = e.target.result;
  };
  reader.readAsDataURL(file);
};

const removeEditImage = () => {
  setEditFieldValue('coverImage', null);
  editImagePreview.value = null;
  if (editFileInput.value) {
    editFileInput.value.value = '';
  }
};

const submitPostEdit = async () => {
  if (!editIsValid.value) {
    // Mark all fields as touched to show validation errors
    Object.keys(editValues).forEach(key => {
      markEditFieldAsTouched(key);
    });
    return;
  }
  
  isSubmittingPostEdit.value = true;
  
  try {
    // Prepare form data
    const formData = new FormData();
    formData.append('_method', 'PUT');
    formData.append('title', editValues.title.trim());
    formData.append('content', editValues.content.trim());
    // Add categories
    editValues.categoryIds.forEach((id) => {
      formData.append('categoryIds[]', id);
    });
    
    // Add cover image if new one is selected
    if (editValues.coverImage) {
      formData.append('coverImage', editValues.coverImage);
    }
    // Update post
    const response = await PostService.updatePost(post.value.id, formData);
    
    // Update cached post data
    queryClient.setQueryData(['post', route.params.id], response.data || response);
    
    // Close modal
    closeEditModal();
    
    console.log('✅ Post updated successfully');
    
  } catch (error) {
    console.error('❌ Error updating post:', error);
    alert('Yazı güncellenirken bir hata oluştu.');
  } finally {
    isSubmittingPostEdit.value = false;
  }
};

// Infinite scroll composable
const { stop: stopScroll } = useInfiniteScroll(() => {
  if (commentsPagination.value.has_more_pages && !commentsLoading.value) {
    loadMoreComments();
  }
});

// Lifecycle hooks
onUnmounted(() => {
  stopScroll();
});
</script>

<style scoped>
.prose {
  color: #374151;
  line-height: 1.75;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
  color: #111827;
  font-weight: 600;
  line-height: 1.25;
}

.prose h1 { font-size: 2.25em; margin-top: 0; margin-bottom: 0.8888889em; }
.prose h2 { font-size: 1.5em; margin-top: 2em; margin-bottom: 1em; }
.prose h3 { font-size: 1.25em; margin-top: 1.6em; margin-bottom: 0.6em; }

.prose p { margin-top: 1.25em; margin-bottom: 1.25em; }
.prose strong { color: #111827; font-weight: 600; }
.prose blockquote { font-style: italic; color: #111827; border-left: 0.25rem solid #e5e7eb; padding-left: 1em; }

.prose ul, .prose ol { margin-top: 1.25em; margin-bottom: 1.25em; padding-left: 1.625em; }
.prose li { margin-top: 0.5em; margin-bottom: 0.5em; }

.prose pre { background-color: #f3f4f6; overflow-x: auto; padding: 0.875em 1.25em; border-radius: 0.375rem; }
.prose code { color: #111827; background-color: #f3f4f6; padding: 0.125rem 0.25rem; border-radius: 0.25rem; font-size: 0.875em; }

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
