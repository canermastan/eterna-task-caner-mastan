<template>
  <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-6 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <div class="text-center">
        <router-link to="/" class="text-2xl font-bold text-gray-900">
          Eterna Blog
        </router-link>
      </div>
    </div>

    <div class="mt-6 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-6 px-4 shadow sm:rounded-lg sm:px-10">
        <router-view :key="$route.fullPath" />
      </div>

      <div class="mt-6 text-center">
        <router-link
          to="/"
          class="text-sm text-gray-600 hover:text-gray-900"
        >
          ← Ana sayfaya dön
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
</script> 