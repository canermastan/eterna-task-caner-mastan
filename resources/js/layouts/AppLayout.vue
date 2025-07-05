<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <!-- Logo and main nav -->
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <router-link to="/" class="text-xl font-bold text-gray-900">
                Eterna Blog
              </router-link>
            </div>
            <div class="hidden sm:ml-8 sm:flex sm:space-x-8">
              <router-link
                to="/"
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                active-class="border-indigo-500 text-gray-900"
              >
                Ana Sayfa
              </router-link>
              <router-link
                v-if="canManagePosts"
                to="/posts/my-posts"
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                active-class="border-indigo-500 text-gray-900"
              >
                Yazılarım
              </router-link>
              <router-link
                v-if="isAdmin"
                to="/categories"
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                active-class="border-indigo-500 text-gray-900"
              >
                Kategoriler
              </router-link>
              <router-link
                v-if="isAdmin"
                to="/admin/posts"
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                active-class="border-indigo-500 text-gray-900"
              >
                Post Yönetimi
              </router-link>
              <router-link
                v-if="isAdmin"
                to="/admin/comments"
                class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
                active-class="border-indigo-500 text-gray-900"
              >
                Yorum Yönetimi
              </router-link>
            </div>
          </div>

          <!-- User menu -->
          <div class="flex items-center space-x-4">
            <div v-if="isAuthenticated" class="flex items-center space-x-4">
              <span class="text-sm text-gray-700">
                Hoş geldin, {{ currentUser?.first_name }}
              </span>
              <div class="relative" data-user-menu>
                <button
                  @click="showUserMenu = !showUserMenu"
                  class="flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                  data-user-menu
                >
                  <div class="h-8 w-8 rounded-full bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                    <span class="text-sm font-medium text-white">
                      {{ currentUser?.first_name?.charAt(0) }}{{ currentUser?.last_name?.charAt(0) }}
                    </span>
                  </div>
                </button>

                <!-- User menu dropdown -->
                <transition
                  enter-active-class="transition ease-out duration-200"
                  enter-from-class="transform opacity-0 scale-95"
                  enter-to-class="transform opacity-100 scale-100"
                  leave-active-class="transition ease-in duration-75"
                  leave-from-class="transform opacity-100 scale-100"
                  leave-to-class="transform opacity-0 scale-95"
                >
                  <div
                    v-if="showUserMenu"
                    class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
                    data-user-menu
                  >
                    <div class="py-1" data-user-menu>
                      <button
                        @click="handleLogout"
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                        data-user-menu
                      >
                        Çıkış Yap
                      </button>
                    </div>
                  </div>
                </transition>
              </div>
            </div>
            <div v-else class="flex items-center space-x-2">
              <router-link
                to="/auth/login"
                class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm font-medium rounded-md hover:bg-gray-100"
              >
                Giriş Yap
              </router-link>
              <router-link
                to="/auth/register"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium transition duration-200"
              >
                Kayıt Ol
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <router-view :key="$route.fullPath" />
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import AuthService from '@/services/AuthService';

const router = useRouter();
const showUserMenu = ref(false);

const isAuthenticated = computed(() => AuthService.isAuthenticated());
const currentUser = computed(() => AuthService.getCurrentUser());
const isAdmin = computed(() => currentUser.value?.role === 'admin');
const canManagePosts = computed(() => {
  const user = currentUser.value;
  return user && (user.role === 'admin' || user.role === 'writer');
});

const handleLogout = async () => {
  try {
    await AuthService.logout();
    router.push('/');
  } catch (error) {
    AuthService.clearAuthData();
    router.push('/auth/login');
  }
};

const handleClickOutside = (event) => {
  if (!event.target.closest('[data-user-menu]')) {
    showUserMenu.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>
