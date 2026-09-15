<script setup>
import { ref, onMounted } from 'vue'
import intervenantService from '../services/intervenantService'
import PageHeader from '../components/PageHeader.vue'

const intervenants = ref([])
const loading = ref(true)
const error = ref(null)

const editionId = ref(null)
const nom = ref('')
const prenom = ref('')
const email = ref('')
const specialite = ref('')
const bio = ref('')
const enregistrementLoading = ref(false)
const enregistrementError = ref(null)

async function chargerIntervenants() {
  loading.value = true
  try {
    const response = await intervenantService.liste()
    intervenants.value = response.data.data
  } catch (err) {
    error.value = "Impossible de charger les intervenants."
    console.error(err)
  } finally {
    loading.value = false
  }
}

onMounted(chargerIntervenants)

function commencerEdition(intervenant) {
  editionId.value = intervenant.id
  nom.value = intervenant.nom
  prenom.value = intervenant.prenom
  email.value = intervenant.email || ''
  specialite.value = intervenant.specialite || ''
  bio.value = intervenant.bio || ''
  enregistrementError.value = null
}

function annulerEdition() {
  editionId.value = null
  nom.value = ''
  prenom.value = ''
  email.value = ''
  specialite.value = ''
  bio.value = ''
  enregistrementError.value = null
}

async function handleEnregistrer() {
  enregistrementError.value = null
  enregistrementLoading.value = true

  try {
    await intervenantService.modifier(editionId.value, {
      nom: nom.value,
      prenom: prenom.value,
      email: email.value || null,
      specialite: specialite.value || null,
      bio: bio.value || null,
    })

    await chargerIntervenants()
    annulerEdition()
  } catch (err) {
    enregistrementError.value = "Erreur lors de l'enregistrement."
    console.error(err.response?.data || err)
  } finally {
    enregistrementLoading.value = false
  }
}

async function handleSupprimer(intervenant) {
  if (!confirm(`Supprimer "${intervenant.prenom} ${intervenant.nom}" ? Cette action est irreversible.`)) {
    return
  }

  try {
    await intervenantService.supprimer(intervenant.id)
    intervenants.value = intervenants.value.filter((i) => i.id !== intervenant.id)
  } catch (err) {
    alert("Erreur lors de la suppression. Cet intervenant est peut-etre lie a un ou plusieurs evenements.")
    console.error(err.response?.data || err)
  }
}
</script>

<template>
  <div class="p-8">
    <PageHeader title="Intervenants" />

    <p v-if="loading" class="text-gray-500">Chargement...</p>
    <p v-else-if="error" class="text-red-600">{{ error }}</p>

    <div v-else-if="intervenants.length === 0" class="text-gray-400 italic">
      Aucun intervenant enregistre pour le moment.
    </div>

    <div v-else class="space-y-3">
      <div
        v-for="intervenant in intervenants"
        :key="intervenant.id"
        class="border border-gray-200 rounded-lg p-4"
      >
        <div v-if="editionId === intervenant.id" class="space-y-3">
          <div class="grid grid-cols-2 gap-3">
            <input v-model="nom" type="text" placeholder="Nom"
              class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <input v-model="prenom" type="text" placeholder="Prenom"
              class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </div>
          <input v-model="email" type="email" placeholder="Email"
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
          <input v-model="specialite" type="text" placeholder="Specialite"
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
          <textarea v-model="bio" rows="2" placeholder="Bio"
            class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>

          <p v-if="enregistrementError" class="text-red-600 text-sm">{{ enregistrementError }}</p>

          <div class="flex gap-2">
            <button @click="handleEnregistrer" :disabled="enregistrementLoading"
              class="text-sm px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-50">
              {{ enregistrementLoading ? 'Enregistrement...' : 'Enregistrer' }}
            </button>
            <button @click="annulerEdition"
              class="text-sm px-3 py-1 border border-gray-300 rounded hover:bg-gray-50">
              Annuler
            </button>
          </div>
        </div>

        <div v-else class="flex items-start justify-between">
          <div>
            <p class="font-semibold text-gray-800">{{ intervenant.prenom }} {{ intervenant.nom }}</p>
            <p v-if="intervenant.specialite" class="text-sm text-blue-600">{{ intervenant.specialite }}</p>
            <p v-if="intervenant.email" class="text-sm text-gray-500">{{ intervenant.email }}</p>
            <p v-if="intervenant.bio" class="text-sm text-gray-600 mt-1">{{ intervenant.bio }}</p>
          </div>

          <div class="flex gap-2 shrink-0 ml-4">
            <button @click="commencerEdition(intervenant)"
              class="text-sm px-3 py-1 border border-blue-600 text-blue-600 rounded hover:bg-blue-50">
              Modifier
            </button>
            <button @click="handleSupprimer(intervenant)"
              class="text-sm px-3 py-1 border border-red-600 text-red-600 rounded hover:bg-red-50">
              Supprimer
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>