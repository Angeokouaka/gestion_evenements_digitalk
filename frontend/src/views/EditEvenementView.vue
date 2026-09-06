<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api'
import PageHeader from '../components/PageHeader.vue'

const route = useRoute()
const router = useRouter()

const CAPACITE_MAX_LIMITE = 1200
const HEURE_LIMITE = '20:00'

const categories = ref([])
const intervenantsExistants = ref([])
const nouveauxIntervenants = ref([])
const titre = ref('')
const description = ref('')
const categorieId = ref('')
const dateDebut = ref('')
const heureDebut = ref('')
const dateFin = ref('')
const heureFin = ref('')
const lieu = ref('')
const lienVisio = ref('')
const capaciteMax = ref('')
const capaciteError = ref(null)
const heureDebutError = ref(null)
const heureFinError = ref(null)
const error = ref(null)
const successMessage = ref(null)
const loading = ref(false)
const chargementInitial = ref(true)

const categorieSelectionnee = computed(() =>
  categories.value.find((cat) => cat.id === categorieId.value)
)

const estEnLigne = computed(() => categorieSelectionnee.value?.en_ligne === true)

function separerDateHeure(datetimeStr) {
  if (!datetimeStr) return { date: '', heure: '' }
  const [date, heureComplete] = datetimeStr.split('T')
  const heure = heureComplete ? heureComplete.slice(0, 5) : ''
  return { date, heure }
}

onMounted(async () => {
  try {
    const [categoriesRes, evenementRes] = await Promise.all([
      api.get('/categories'),
      api.get(`/evenements/${route.params.id}`),
    ])

    categories.value = categoriesRes.data.data
    const evenement = evenementRes.data.data

    titre.value = evenement.titre
    description.value = evenement.description || ''
    categorieId.value = evenement.categorie?.id || ''
    lieu.value = evenement.lieu || ''
    lienVisio.value = evenement.lien_visio || ''
    capaciteMax.value = evenement.capacite_max || ''

    const debut = separerDateHeure(evenement.date_debut)
    dateDebut.value = debut.date
    heureDebut.value = debut.heure

    const fin = separerDateHeure(evenement.date_fin)
    dateFin.value = fin.date
    heureFin.value = fin.heure

    intervenantsExistants.value = evenement.intervenants || []
  } catch (err) {
    error.value = "Impossible de charger l'evenement."
    console.error(err)
  } finally {
    chargementInitial.value = false
  }
})

watch(capaciteMax, (nouvelleValeur) => {
  if (nouvelleValeur && Number(nouvelleValeur) > CAPACITE_MAX_LIMITE) {
    capaciteError.value = `Depasse la limite de ${CAPACITE_MAX_LIMITE} places.`
  } else {
    capaciteError.value = null
  }
})

watch(heureDebut, (nouvelleValeur) => {
  if (nouvelleValeur && nouvelleValeur > HEURE_LIMITE) {
    heureDebutError.value = `Aucune organisation apres ${HEURE_LIMITE}.`
  } else {
    heureDebutError.value = null
  }
})

watch(heureFin, (nouvelleValeur) => {
  if (nouvelleValeur && nouvelleValeur > HEURE_LIMITE) {
    heureFinError.value = `Aucune organisation apres ${HEURE_LIMITE}.`
  } else {
    heureFinError.value = null
  }
})

watch(estEnLigne, (nouvelleValeur) => {
  if (!nouvelleValeur) {
    lienVisio.value = ''
  }
})

function ajouterIntervenant() {
  nouveauxIntervenants.value.push({ nom: '', prenom: '', poste: '' })
}

function retirerNouvelIntervenant(index) {
  nouveauxIntervenants.value.splice(index, 1)
}

async function retirerIntervenantExistant(intervenantId) {
  if (!confirm('Retirer cet intervenant de l\'evenement ?')) return

  try {
    await api.delete(`/evenements/${route.params.id}/intervenants/${intervenantId}`)
    intervenantsExistants.value = intervenantsExistants.value.filter((i) => i.id !== intervenantId)
  } catch (err) {
    error.value = "Erreur lors du retrait de l'intervenant."
    console.error(err)
  }
}

async function handleEnregistrer() {
  error.value = null
  successMessage.value = null

  if (capaciteMax.value && Number(capaciteMax.value) > CAPACITE_MAX_LIMITE) {
    error.value = `Impossible d'enregistrer : la capacite depasse la limite de ${CAPACITE_MAX_LIMITE} places.`
    return
  }

  if (heureDebut.value > HEURE_LIMITE || heureFin.value > HEURE_LIMITE) {
    error.value = `Impossible d'enregistrer : aucun evenement ne peut depasser ${HEURE_LIMITE}.`
    return
  }

  if (estEnLigne.value && !lienVisio.value) {
    error.value = 'Un lien de visioconference est requis pour un evenement en ligne.'
    return
  }

  const intervenantsValides = nouveauxIntervenants.value.filter((i) => i.nom && i.prenom)

  loading.value = true

  try {
    await api.put(`/evenements/${route.params.id}`, {
      titre: titre.value,
      description: description.value,
      date_debut: `${dateDebut.value}T${heureDebut.value}`,
      date_fin: `${dateFin.value}T${heureFin.value}`,
      lieu: lieu.value,
      lien_visio: estEnLigne.value ? lienVisio.value : null,
      capacite_max: capaciteMax.value || null,
      categorie_id: categorieId.value,
    })

    for (const intervenant of intervenantsValides) {
      const intervenantResponse = await api.post('/intervenants', {
        nom: intervenant.nom,
        prenom: intervenant.prenom,
        specialite: intervenant.poste,
      })

      await api.post(`/evenements/${route.params.id}/intervenants`, {
        intervenant_id: intervenantResponse.data.data.id,
      })
    }

    router.push(`/evenements/${route.params.id}`)
  } catch (err) {
    error.value = "Erreur lors de l'enregistrement. Verifiez les champs."
    console.error(err)
  } finally {
    loading.value = false
  }
}

function handleAnnuler() {
  router.push(`/evenements/${route.params.id}`)
}
</script>

<template>
  <div class="max-w-xl mx-auto mt-10 p-8">
    <PageHeader title="Modifier l'evenement" />

    <p v-if="chargementInitial" class="text-gray-500">Chargement...</p>

    <div v-else class="border border-gray-200 rounded-lg p-8 shadow-sm">
      <form class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Titre *</label>
          <input v-model="titre" type="text" required
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
          <textarea v-model="description" rows="3" required
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Categorie *</label>
          <select v-model="categorieId" required
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="" disabled>Choisir une categorie</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.nom }}</option>
          </select>
        </div>

        <div v-if="estEnLigne" class="bg-purple-50 border border-purple-200 rounded p-3">
          <label class="block text-sm font-medium text-purple-800 mb-1">
            Lien Teams / Google Meet *
          </label>
          <input v-model="lienVisio" type="url" placeholder="https://teams.microsoft.com/..." required
            class="w-full border border-purple-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Date de debut *</label>
          <div class="grid grid-cols-2 gap-3">
            <input v-model="dateDebut" type="date" required
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <input v-model="heureDebut" type="time" required
              class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2"
              :class="heureDebutError ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'" />
          </div>
          <p v-if="heureDebutError" class="text-red-600 text-sm mt-1">{{ heureDebutError }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Date de fin *</label>
          <div class="grid grid-cols-2 gap-3">
            <input v-model="dateFin" type="date" required
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <input v-model="heureFin" type="time" required
              class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2"
              :class="heureFinError ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'" />
          </div>
          <p v-if="heureFinError" class="text-red-600 text-sm mt-1">{{ heureFinError }}</p>
        </div>

        <div v-if="!estEnLigne">
          <label class="block text-sm font-medium text-gray-700 mb-1">Lieu</label>
          <input v-model="lieu" type="text"
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de places</label>
          <input v-model="capaciteMax" type="number" min="1"
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
          <p v-if="capaciteError" class="text-red-600 text-sm mt-1">{{ capaciteError }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Intervenants actuels</label>
          <div v-if="intervenantsExistants.length === 0" class="text-sm text-gray-400 italic mb-2">
            Aucun intervenant.
          </div>
          <div v-else class="flex flex-wrap gap-2 mb-3">
            <span
              v-for="intervenant in intervenantsExistants"
              :key="intervenant.id"
              class="flex items-center gap-1 text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded"
            >
              {{ intervenant.prenom }} {{ intervenant.nom }}
              <button type="button" @click="retirerIntervenantExistant(intervenant.id)"
                class="text-red-500 hover:text-red-700 font-bold ml-1">
                X
              </button>
            </span>
          </div>

          <div class="flex items-center justify-between mb-2">
            <label class="text-sm font-medium text-gray-700">Ajouter un intervenant</label>
            <button type="button" @click="ajouterIntervenant"
              class="text-sm text-blue-600 hover:text-blue-800">
              + Ajouter
            </button>
          </div>

          <div
            v-for="(intervenant, index) in nouveauxIntervenants"
            :key="index"
            class="grid grid-cols-[1fr_1fr_1fr_auto] gap-2 mb-2 items-center"
          >
            <input v-model="intervenant.nom" type="text" placeholder="Nom"
              class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <input v-model="intervenant.prenom" type="text" placeholder="Prenom"
              class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <input v-model="intervenant.poste" type="text" placeholder="Poste"
              class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <button type="button" @click="retirerNouvelIntervenant(index)"
              class="text-red-500 hover:text-red-700 px-2 font-bold">
              X
            </button>
          </div>
        </div>

        <p v-if="error" class="text-red-600 text-sm">{{ error }}</p>
        <p v-if="successMessage" class="text-green-600 text-sm">{{ successMessage }}</p>

        <div class="flex gap-3 pt-2">
          <button type="button" @click="handleAnnuler"
            class="flex-1 border border-gray-300 text-gray-700 py-2 rounded hover:bg-gray-50">
            Annuler
          </button>
          <button type="button" @click="handleEnregistrer" :disabled="loading"
            class="flex-1 bg-blue-600 text-white py-2 rounded hover:bg-blue-700 disabled:opacity-50">
            {{ loading ? 'Enregistrement...' : 'Enregistrer les modifications' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>