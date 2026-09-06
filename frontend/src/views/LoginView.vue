<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const error = ref(null)
const loading = ref(false)

async function handleSubmit() {
  error.value = null
  loading.value = true

  try {
    await authStore.login(email.value, password.value)
    router.push('/evenements')
  } catch (err) {
    error.value = "Identifiants incorrects."
    console.error(err)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="max-w-md mx-auto mt-16 p-8 border border-gray-200 rounded-lg shadow-sm">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Connexion Organisateur</h1>

    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input
          v-model="email"
          type="email"
          required
          class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
        <input
          v-model="password"
          type="password"
          required
          class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
      </div>

      <p v-if="error" class="text-red-600 text-sm">{{ error }}</p>

      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 disabled:opacity-50"
      >
        {{ loading ? 'Connexion...' : 'Se connecter' }}
      </button>
    </form>
  </div>
</template>