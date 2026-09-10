<script setup>
import { RouterView } from 'vue-router'
import { useAuthStore } from './stores/auth'
import AppHeader from './components/AppHeader.vue'
import AppSidebar from './components/AppSidebar.vue'

const authStore = useAuthStore()
</script>

<template>
  <div v-if="authStore.isAuthenticated" class="flex">
    <AppSidebar />
    <main class="flex-1">
      <RouterView />
    </main>
  </div>

  <div v-else>
    <AppHeader />
    <RouterView />
  </div>
</template>