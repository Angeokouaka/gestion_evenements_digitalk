<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import supdecoLogo from '../assets/supdeco-logo.png'

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
    <RouterLink to="/" class="flex flex-col items-center gap-2 p-6 border-b border-blue-900">
      <img :src="supdecoLogo" alt="Supdeco Dakar" class="h-14 w-auto" />
      <span class="text-lg font-bold text-white">Digitalk</span>
    </RouterLink>

    <nav class="flex-1 p-4 space-y-1">
      <RouterLink
        to="/dashboard"
        class="flex items-center gap-3 px-3 py-2 rounded hover:bg-blue-900 hover:text-white"
        active-class="bg-blue-900 text-white"
      >
        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M3 13h4v8H3v-8zm7-6h4v14h-4V7zm7 3h4v11h-4V10z" />
        </svg>
        Tableau de bord
      </RouterLink>

      <RouterLink
        to="/evenements"
        class="flex items-center gap-3 px-3 py-2 rounded hover:bg-blue-900 hover:text-white"
        active-class="bg-blue-900 text-white"
      >
        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
        </svg>
        Evenements
      </RouterLink>

      <RouterLink
        to="/intervenants"
        class="flex items-center gap-3 px-3 py-2 rounded hover:bg-blue-900 hover:text-white"
        active-class="bg-blue-900 text-white"
      >
        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
          <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-4a4 4 0 100-8 4 4 0 000 8zm6 2a4 4 0 10-4-4" />
        </svg>
        Intervenants
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