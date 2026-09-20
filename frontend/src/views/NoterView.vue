<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import avisService from '../services/avisService'
import StarRating from '../components/StarRating.vue'
import PageHeader from '../components/PageHeader.vue'

const route = useRoute()

const inscription = ref(null)
const loading = ref(true)
const erreur = ref(null)

const noteEvenement = ref(0)
const commentaireEvenement = ref('')
const avisEvenementEnvoye = ref(false)

const notesIntervenants = ref({})
const avisIntervenantsEnvoyes = ref({})

async function charger() {
  loading.value = true
  try {
    const response = await avisService.pageNotation(route.params.qrCode)
    inscription.value = response.data.data
  } catch (err) {
    if (err.response?.status === 403) {
      erreur.value = "Seuls les participants ayant confirme leur presence peuvent noter."
    } else {
      erreur.value = "Lien invalide ou expire."
    }
    console.error(err.response?.data || err)
  } finally {
    loading.value = false
  }
}

onMounted(charger)

async function envoyerAvisEvenement() {
  if (!noteEvenement.value) return

  try {
    await avisService.noterEvenementParQrCode(route.params.qrCode, {
      note: noteEvenement.value,
      commentaire: commentaireEvenement.value || null,
    })
    avisEvenementEnvoye.value = true
  } catch (err) {
    console.error(err.response?.data || err)
  }
}

async function envoyerAvisIntervenant(intervenantId) {
  const note = notesIntervenants.value[intervenantId]
  if (!note) return

  try {
    await avisService.noterIntervenantParQrCode(route.params.qrCode, intervenantId, { note })
    avisIntervenantsEnvoyes.value = { ...avisIntervenantsEnvoyes.value, [intervenantId]: true }
  } catch (err) {
    console.error(err.response?.data || err)
  }
}
</script>

<template>
  <div class="max-w-md mx-auto mt-16 p-6">
    <PageHeader title="Donner mon avis" />

    <p v-if="loading" class="text-gray-500 text-center">Chargement...</p>
    <p v-else-if="erreur" class="text-red-600 text-center">{{ erreur }}</p>

    <div v-else-if="inscription" class="border border-gray-200 rounded-lg p-8 shadow-sm">
      <p class="text-sm text-gray-600 text-center mb-6">
        Merci d'avoir participe a <strong>{{ inscription.evenement?.titre }}</strong>.
      </p>

      <div class="text-left">
        <p class="text-sm font-medium text-gray-700 mb-2 text-center">Notez cet evenement</p>

        <div v-if="avisEvenementEnvoye" class="text-center text-green-600 text-sm">
          Merci pour votre avis !
        </div>
        <div v-else class="flex flex-col items-center gap-2">
          <StarRating v-model="noteEvenement" />
          <textarea
            v-model="commentaireEvenement"
            rows="2"
            placeholder="Commentaire (optionnel)"
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
          ></textarea>
          <button
            @click="envoyerAvisEvenement"
            :disabled="!noteEvenement"
            class="text-sm px-4 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50"
          >
            Envoyer
          </button>
        </div>
      </div>

      <div v-if="inscription.evenement?.intervenants?.length" class="mt-6 pt-6 border-t border-gray-200 text-left">
        <p class="text-sm font-medium text-gray-700 mb-3 text-center">Notez les intervenants</p>

        <div
          v-for="intervenant in inscription.evenement.intervenants"
          :key="intervenant.id"
          class="flex items-center justify-between mb-3"
        >
          <span class="text-sm text-gray-600">{{ intervenant.prenom }} {{ intervenant.nom }}</span>

          <span v-if="avisIntervenantsEnvoyes[intervenant.id]" class="text-xs text-green-600">
            Merci !
          </span>
          <div v-else class="flex items-center gap-2">
            <StarRating
              :model-value="notesIntervenants[intervenant.id] || 0"
              @update:model-value="(v) => (notesIntervenants[intervenant.id] = v)"
              taille="text-lg"
            />
            <button
              @click="envoyerAvisIntervenant(intervenant.id)"
              :disabled="!notesIntervenants[intervenant.id]"
              class="text-xs px-2 py-1 border border-blue-600 text-blue-600 rounded hover:bg-blue-50 disabled:opacity-50"
            >
              OK
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>