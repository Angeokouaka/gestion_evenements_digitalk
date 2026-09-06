<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'
import { useAuthStore } from '../stores/auth'
import PageHeader from '../components/PageHeader.vue'

const authStore = useAuthStore()

const nom = ref('')
const email = ref('')
const telephone = ref('')
const structure = ref('')
const error = ref(null)
const successMessage = ref(null)
const loading = ref(false)
const modeEdition = ref(false)

const initiales = () => {
  const n = authStore.organisateur?.nom || ''
  return n.slice(0, 2).toUpperCase()
}

function chargerDonnees() {
  const organisateur = authStore.organisateur
  nom.value = organisateur?.nom || ''
  email.value = organisateur?.email || ''
  telephone.value = organisateur?.telephone || ''
  structure.value = organisateur?.structure || ''
}

onMounted(chargerDonnees)

function annulerEdition() {
  chargerDonnees()
  modeEdition.value = false
  error.value = null
}

async function handleEnregistrer() {
  error.value = null
  successMessage.value = null
  loading.value = true

  try {
    const response = await api.put(`/organisateurs/${authStore.organisateur.id}`, {
      nom: nom.value,
      email: email.value,
      telephone: telephone.value || null,
      structure: structure.value || null,
    })

    const organisateurMisAJour = response.data.data
    authStore.organisateur = organisateurMisAJour
    localStorage.setItem('organisateur', JSON.stringify(organisateurMisAJour))

    successMessage.value = 'Profil mis a jour avec succes.'
    modeEdition.value = false
  } catch (err) {
    if (err.response?.status === 422 && err.response.data.errors?.email) {
      error.value = 'Cet email est deja utilise par un autre compte.'
    } else {
      error.value = 'Erreur lors de la mise a jour du profil.'
    }
    console.error(err.response?.data || err)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="max-w-2xl mx-auto mt-10 p-8">
    <PageHeader title="Mon Profil" />

    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6 flex items-center gap-4">
      <span class="w-16 h-16 rounded-full bg-blue-600 text-white text-xl font-bold flex items-center justify-center shrink-0">
        {{ initiales() }}
      </span>
      <div>
        <h1 class="text-xl font-bold text-gray-800">{{ authStore.organisateur?.nom }}</h1>
        <p class="text-sm text-gray-500">{{ authStore.organisateur?.email }}</p>
        <p v-if="authStore.organisateur?.structure" class="text-sm text-gray-400">{{ authStore.organisateur?.structure }}</p>
      </div>
    </div>

    <div class="border border-gray-200 rounded-lg p-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="font-semibold text-gray-800">Informations personnelles</h2>
        <button
          v-if="!modeEdition"
          @click="modeEdition = true"
          class="text-sm px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          Modifier
        </button>
      </div>

      <p v-if="successMessage" class="text-green-700 bg-green-50 border border-green-200 rounded p-2 mb-4 text-sm">
        {{ successMessage }}
      </p>

      <form @submit.prevent="handleEnregistrer" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
          <input v-model="nom" type="text" required :disabled="!modeEdition"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            :class="modeEdition ? 'border-gray-300 bg-white' : 'border-gray-200 bg-gray-50 text-gray-500'" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
          <input v-model="email" type="email" required :disabled="!modeEdition"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            :class="modeEdition ? 'border-gray-300 bg-white' : 'border-gray-200 bg-gray-50 text-gray-500'" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Telephone</label>
          <input v-model="telephone" type="tel" maxlength="9" :disabled="!modeEdition"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            :class="modeEdition ? 'border-gray-300 bg-white' : 'border-gray-200 bg-gray-50 text-gray-500'" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Structure</label>
          <input v-model="structure" type="text" :disabled="!modeEdition"
            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            :class="modeEdition ? 'border-gray-300 bg-white' : 'border-gray-200 bg-gray-50 text-gray-500'" />
        </div>

        <p v-if="error" class="text-red-600 text-sm">{{ error }}</p>

        <div v-if="modeEdition" class="flex gap-3 pt-2">
          <button type="button" @click="annulerEdition"
            class="flex-1 border border-gray-300 text-gray-700 py-2 rounded hover:bg-gray-50">
            Annuler
          </button>
          <button type="submit" :disabled="loading"
            class="flex-1 bg-blue-600 text-white py-2 rounded hover:bg-blue-700 disabled:opacity-50">
            {{ loading ? 'Enregistrement...' : 'Enregistrer' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>