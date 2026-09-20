<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import evenementService from '../services/evenementService'
import categorieService from '../services/categorieService'
import intervenantService from '../services/intervenantService'
import { useAuthStore } from '../stores/auth'
import PageHeader from '../components/PageHeader.vue'

const router = useRouter()
const authStore = useAuthStore()

const CAPACITE_MAX_LIMITE = 1200
const HEURE_LIMITE = '20:00'
const DRAFT_KEY = 'digitalk_draft_evenement'
const FILIERES = ['ESITEC', 'IST', 'PGE', 'IMAP', 'MERCURE', 'ECONOMIE', 'BBA', 'LEA', 'SCHOOL OF LAW']
const ROLES_INTERVENTION = ['Intervenant', 'Moderateur', 'Animateur']

const categories = ref([])
const intervenantsExistants = ref([])
const intervenantExistantId = ref('')
const roleIntervenantExistant = ref('Intervenant')
const intervenantsExistantsSelectionnes = ref([])
const intervenantsSaisis = ref([])
const titre = ref('')
const description = ref('')
const categorieId = ref('')
const filiere = ref('')
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
const draftMessage = ref(null)
const loading = ref(false)

const affiche = ref(null)
const affichePreview = ref(null)

const categorieSelectionnee = computed(() =>
  categories.value.find((cat) => cat.id === categorieId.value)
)

const estEnLigne = computed(() => categorieSelectionnee.value?.en_ligne === true)

const intervenantsExistantsDisponibles = computed(() => {
  const idsDejaChoisis = intervenantsExistantsSelectionnes.value.map((i) => i.id)
  return intervenantsExistants.value.filter((i) => !idsDejaChoisis.includes(i.id))
})

onMounted(async () => {
  const [categoriesRes, intervenantsRes] = await Promise.all([
    categorieService.liste(),
    intervenantService.liste(),
  ])
  categories.value = categoriesRes.data.data
  intervenantsExistants.value = intervenantsRes.data.data

  const draft = localStorage.getItem(DRAFT_KEY)
  if (draft) {
    const data = JSON.parse(draft)
    titre.value = data.titre || ''
    description.value = data.description || ''
    categorieId.value = data.categorieId || ''
    filiere.value = data.filiere || ''
    dateDebut.value = data.dateDebut || ''
    heureDebut.value = data.heureDebut || ''
    dateFin.value = data.dateFin || ''
    heureFin.value = data.heureFin || ''
    lieu.value = data.lieu || ''
    lienVisio.value = data.lienVisio || ''
    capaciteMax.value = data.capaciteMax || ''
    intervenantsSaisis.value = data.intervenantsSaisis || []
    draftMessage.value = 'Brouillon charge depuis votre derniere session.'
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

function handleAfficheChange(event) {
  const fichier = event.target.files[0]
  if (!fichier) return

  affiche.value = fichier
  affichePreview.value = URL.createObjectURL(fichier)
}

function retirerAffiche() {
  affiche.value = null
  affichePreview.value = null
}

function ajouterIntervenantExistant() {
  if (!intervenantExistantId.value) return

  const intervenant = intervenantsExistants.value.find((i) => i.id === intervenantExistantId.value)
  if (!intervenant) return

  intervenantsExistantsSelectionnes.value.push({
    ...intervenant,
    role: roleIntervenantExistant.value,
  })

  intervenantExistantId.value = ''
  roleIntervenantExistant.value = 'Intervenant'
}

function retirerIntervenantExistant(index) {
  intervenantsExistantsSelectionnes.value.splice(index, 1)
}

function ajouterIntervenant() {
  intervenantsSaisis.value.push({ nom: '', prenom: '', poste: '', role: 'Intervenant' })
}

function retirerIntervenant(index) {
  intervenantsSaisis.value.splice(index, 1)
}

function getFormData() {
  return {
    titre: titre.value,
    description: description.value,
    categorieId: categorieId.value,
    filiere: filiere.value,
    dateDebut: dateDebut.value,
    heureDebut: heureDebut.value,
    dateFin: dateFin.value,
    heureFin: heureFin.value,
    lieu: lieu.value,
    lienVisio: lienVisio.value,
    capaciteMax: capaciteMax.value,
    intervenantsSaisis: intervenantsSaisis.value,
  }
}

function handleAnnuler() {
  router.push('/evenements')
}

function handleEnregistrer() {
  localStorage.setItem(DRAFT_KEY, JSON.stringify(getFormData()))
  draftMessage.value = 'Brouillon enregistre localement (non envoye au serveur).'
  error.value = null
}

async function handlePublier() {
  error.value = null
  draftMessage.value = null

  if (capaciteMax.value && Number(capaciteMax.value) > CAPACITE_MAX_LIMITE) {
    error.value = `Impossible de publier : la capacite depasse la limite de ${CAPACITE_MAX_LIMITE} places.`
    return
  }

  if (heureDebut.value > HEURE_LIMITE || heureFin.value > HEURE_LIMITE) {
    error.value = `Impossible de publier : aucun evenement ne peut depasser ${HEURE_LIMITE}.`
    return
  }

  if (estEnLigne.value && !lienVisio.value) {
    error.value = 'Un lien de visioconference est requis pour un evenement en ligne.'
    return
  }

  const intervenantsValides = intervenantsSaisis.value.filter((i) => i.nom && i.prenom)

  loading.value = true

  try {
    const response = await evenementService.creer({
      titre: titre.value,
      description: description.value,
      date_debut: `${dateDebut.value}T${heureDebut.value}`,
      date_fin: `${dateFin.value}T${heureFin.value}`,
      lieu: lieu.value,
      lien_visio: estEnLigne.value ? lienVisio.value : null,
      affiche: affiche.value,
      filiere: filiere.value || null,
      capacite_max: capaciteMax.value || null,
      categorie_id: categorieId.value,
      organisateur_id: authStore.organisateur.id,
    })

    const nouvelEvenementId = response.data.data.id

    for (const intervenant of intervenantsExistantsSelectionnes.value) {
      await evenementService.ajouterIntervenant(nouvelEvenementId, intervenant.id, intervenant.role)
    }

    for (const intervenant of intervenantsValides) {
      const intervenantResponse = await intervenantService.creer({
        nom: intervenant.nom,
        prenom: intervenant.prenom,
        specialite: intervenant.poste,
      })

      await evenementService.ajouterIntervenant(nouvelEvenementId, intervenantResponse.data.data.id, intervenant.role)
    }

    localStorage.removeItem(DRAFT_KEY)
    router.push('/evenements')
  } catch (err) {
    error.value = "Erreur lors de la publication. Verifiez les champs."
    console.error(err)
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="max-w-xl mx-auto mt-10 p-8">
    <PageHeader title="Creer un evenement" />

    <div class="border border-gray-200 rounded-lg p-8 shadow-sm">
      <p v-if="draftMessage" class="text-sm text-blue-700 bg-blue-50 border border-blue-200 rounded p-2 mb-4">
        {{ draftMessage }}
      </p>

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
          <label class="block text-sm font-medium text-gray-700 mb-1">Affiche de l'evenement (optionnel)</label>
          <div v-if="affichePreview" class="mb-2">
            <img :src="affichePreview" class="w-full max-h-48 object-cover rounded border border-gray-200" />
            <button type="button" @click="retirerAffiche" class="text-xs text-red-600 hover:underline mt-1">
              Retirer l'affiche
            </button>
          </div>
          <input type="file" accept="image/*" @change="handleAfficheChange"
            class="w-full text-sm text-gray-600 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
          <p class="text-xs text-gray-400 mt-1">Si aucune affiche n'est fournie, une banniere par defaut sera utilisee.</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Categorie *</label>
          <select v-model="categorieId" required
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="" disabled>Choisir une categorie</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.nom }}</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Filiere concernee (optionnel)</label>
          <select v-model="filiere"
            class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">Toutes les filieres</option>
            <option v-for="f in FILIERES" :key="f" :value="f">{{ f }}</option>
          </select>
        </div>

        <div v-if="estEnLigne" class="bg-purple-50 border border-purple-200 rounded p-3">
          <label class="block text-sm font-medium text-purple-800 mb-1">
            Lien Teams / Google Meet *
          </label>
          <input v-model="lienVisio" type="url" placeholder="https://teams.microsoft.com/..." required
            class="w-full border border-purple-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500" />
          <p class="text-xs text-purple-600 mt-1">Requis car cette categorie est en ligne.</p>
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
          <label class="block text-sm font-medium text-gray-700 mb-2">Ajouter un intervenant existant</label>

          <div v-if="intervenantsExistantsSelectionnes.length > 0" class="flex flex-wrap gap-2 mb-3">
            <span
              v-for="(intervenant, index) in intervenantsExistantsSelectionnes"
              :key="intervenant.id"
              class="flex items-center gap-1 text-xs bg-blue-50 text-blue-700 px-2 py-1 rounded"
            >
              {{ intervenant.prenom }} {{ intervenant.nom }} ({{ intervenant.role }})
              <button type="button" @click="retirerIntervenantExistant(index)"
                class="text-red-500 hover:text-red-700 font-bold ml-1">
                X
              </button>
            </span>
          </div>

          <div class="grid grid-cols-[2fr_1fr_auto] gap-2">
            <select v-model="intervenantExistantId"
              class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="" disabled>Choisir un intervenant</option>
              <option v-for="i in intervenantsExistantsDisponibles" :key="i.id" :value="i.id">
                {{ i.prenom }} {{ i.nom }}<span v-if="i.specialite"> - {{ i.specialite }}</span>
              </option>
            </select>
            <select v-model="roleIntervenantExistant"
              class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option v-for="r in ROLES_INTERVENTION" :key="r" :value="r">{{ r }}</option>
            </select>
            <button type="button" @click="ajouterIntervenantExistant"
              class="text-sm px-3 py-1 border border-blue-600 text-blue-600 rounded hover:bg-blue-50">
              Ajouter
            </button>
          </div>
        </div>

        <div>
          <div class="flex items-center justify-between mb-2">
            <label class="block text-sm font-medium text-gray-700">Nouvel intervenant (optionnel)</label>
            <button type="button" @click="ajouterIntervenant"
              class="text-sm text-blue-600 hover:text-blue-800">
              + Ajouter
            </button>
          </div>

          <div v-if="intervenantsSaisis.length === 0" class="text-sm text-gray-400 italic">
            Aucun nouvel intervenant ajoute.
          </div>

          <div
            v-for="(intervenant, index) in intervenantsSaisis"
            :key="index"
            class="grid grid-cols-[1fr_1fr_1fr_1fr_auto] gap-2 mb-2 items-center"
          >
            <input v-model="intervenant.nom" type="text" placeholder="Nom"
              class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <input v-model="intervenant.prenom" type="text" placeholder="Prenom"
              class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <input v-model="intervenant.poste" type="text" placeholder="Poste"
              class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <select v-model="intervenant.role"
              class="border border-gray-300 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option v-for="r in ROLES_INTERVENTION" :key="r" :value="r">{{ r }}</option>
            </select>
            <button type="button" @click="retirerIntervenant(index)"
              class="text-red-500 hover:text-red-700 px-2 font-bold">
              X
            </button>
          </div>
        </div>

        <p v-if="error" class="text-red-600 text-sm">{{ error }}</p>

        <div class="flex gap-3 pt-2">
          <button type="button" @click="handleAnnuler"
            class="flex-1 border border-gray-300 text-gray-700 py-2 rounded hover:bg-gray-50">
            Annuler
          </button>
          <button type="button" @click="handleEnregistrer"
            class="flex-1 border border-blue-600 text-blue-600 py-2 rounded hover:bg-blue-50">
            Enregistrer
          </button>
          <button type="button" @click="handlePublier" :disabled="loading"
            class="flex-1 bg-blue-600 text-white py-2 rounded hover:bg-blue-700 disabled:opacity-50">
            {{ loading ? 'Publication...' : 'Publier' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>