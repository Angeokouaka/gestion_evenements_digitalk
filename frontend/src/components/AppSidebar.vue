<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const menuOuvert = ref(false)

const initiales = () => {
  const nom = authStore.organisateur?.nom || ''
  return nom.slice(0, 2).toUpperCase()
}

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}
</script>

<template>
  <aside class="w-56 bg-blue-950 text-blue-100 min-h-screen flex flex-col">
    <div class="p-6 border-b border-blue-900">
      <RouterLink to="/" class="text-xl font-bold text-white">
        Digitalk
      </RouterLink>
    </div>

    <nav class="flex-1 p-4 space-y-1">
      <RouterLink
        to="/dashboard"
        class="block px-3 py-2 rounded hover:bg-blue-900 hover:text-white"
        active-class="bg-blue-900 text-white"
      >
        Tableau de bord
      </RouterLink>
      <RouterLink
        to="/evenements"
        class="block px-3 py-2 rounded hover:bg-blue-900 hover:text-white"
        active-class="bg-blue-900 text-white"
      >
        Evenements
      </RouterLink>
      <RouterLink
        to="/creer-evenement"
        class="block px-3 py-2 rounded hover:bg-blue-900 hover:text-white"
        active-class="bg-blue-900 text-white"
      >
        Creer un evenement
      </RouterLink>
    </nav>

    <div class="relative p-3 border-t border-blue-900">
      <button
        @click="menuOuvert = !menuOuvert"
        class="w-full flex items-center gap-3 p-2 rounded hover:bg-blue-900"
      >
        <span class="w-8 h-8 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">
          {{ initiales() }}
        </span>
        <span class="text-sm text-left flex-1 truncate">
          {{ authStore.organisateur?.nom }}
        </span>
      </button>

      <div
        v-if="menuOuvert"
        class="absolute bottom-full left-3 right-3 mb-1 bg-white text-gray-800 rounded shadow-lg border border-gray-200 overflow-hidden"
      >
        <RouterLink
          to="/profil"
          class="block px-4 py-2 text-sm hover:bg-gray-50"
          @click="menuOuvert = false"
        >
          Mon Profil
        </RouterLink>
        <button
          @click="handleLogout"
          class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50"
        >
          Deconnexion
        </button>
      </div>
    </div>
  </aside>
</template>