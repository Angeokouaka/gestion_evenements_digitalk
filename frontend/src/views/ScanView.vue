<script setup>
import { ref } from 'vue'
import { useRoute } from 'vue-router'
import inscriptionService from '../services/inscriptionService'
import PageHeader from '../components/PageHeader.vue'

const route = useRoute()

const email = ref('')
const loading = ref(false)
const succes = ref(null)
const erreur = ref(null)

async function handleConfirmer() {
  erreur.value = null
  succes.value = null
  loading.value = true

  try {
    const response = await inscriptionService.scannerParEmail(route.params.qrCode, email.value)
    succes.value = {
      evenement: response.data.data.evenement.titre,
      participant: response.data.data.participant.prenom,
    }
  } catch (err) {
    if (err.response?.status === 404) {
      erreur.value = "Aucune inscription trouvee pour cet email sur cet evenement. Verifiez l'adresse saisie."
    } else if (err.response?.status === 422) {
      erreur.value = err.response.data.message || "Impossible de confirmer votre presence pour le moment."
    } else {
      erreur.value = "Une erreur est survenue. Reessayez."
    }
    console.error(err.response?.data || err)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="max-w-md mx-auto mt-16 p-6">
    <PageHeader title="Confirmer ma presence" />

    <div class="border border-gray-200 rounded-lg p-8 shadow-sm">
      <div v-if="succes" class="text-center">
        <div class="text-green-600 text-4xl mb-3">&#10003;</div>
        <p class="text-lg font-semibold text-gray-800 mb-1">Presence confirmee !</p>
        <p class="text-sm text-gray-500">
          Bienvenue {{ succes.participant }}, votre presence a
          <strong>{{ succes.evenement }}</strong> a bien ete enregistree.
        </p>
      </div>

      <form v-else @submit.prevent="handleConfirmer" class="space-y-4">
        <p class="text-sm text-gray-600 mb-4">
          Entrez l'email utilise lors de votre inscription pour confirmer votre presence.
        </p>

        <input
          v-model="email"
          type="email"
          placeholder="Votre email"
          required
          class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
        />

        <p v-if="erreur" class="text-red-600 text-sm">{{ erreur }}</p>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 disabled:opacity-50"
        >
          {{ loading ? 'Confirmation...' : 'Confirmer ma presence' }}
        </button>
      </form>
    </div>
  </div>
</template>