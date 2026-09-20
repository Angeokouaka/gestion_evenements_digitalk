<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import evenementService from '../services/evenementService'
import participantService from '../services/participantService'
import inscriptionService from '../services/inscriptionService'
import { useAuthStore } from '../stores/auth'
import QrCodeDisplay from '../components/QrCodeDisplay.vue'
import supdecoBanner from '../assets/supdeco-banner.png'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const evenement = ref(null)
const loading = ref(true)
const error = ref(null)
const suppressionLoading = ref(false)

const nom = ref('')
const prenom = ref('')
const email = ref('')
const telephone = ref('')
const matricule = ref('')
const telephoneError = ref(null)
const matriculeError = ref(null)
const inscriptionMessage = ref(null)
const inscriptionError = ref(null)
const inscriptionLoading = ref(false)

const inscriptions = ref([])
const inscriptionsLoading = ref(false)

const estProprietaire = computed(() => {
  return authStore.isAuthenticated &&
    evenement.value &&
    authStore.organisateur?.id === evenement.value.organisateur?.id
})

const nombrePresents = computed(() => {
  return inscriptions.value.filter((i) => i.presence_arrivee).length
})

const urlScan = computed(() => {
  return evenement.value?.qr_code ? `${window.location.origin}/scan/${evenement.value.qr_code}` : ''
})

const imageBanniere = computed(() => {
  return evenement.value?.affiche_url || supdecoBanner
})

async function chargerEvenement() {
  loading.value = true
  try {
    const response = await evenementService.detail(route.params.id)
    evenement.value = response.data.data

    if (estProprietaire.value) {
      chargerInscriptions()
    }
  } catch (err) {
    error.value = "Evenement introuvable."
    console.error(err)
  } finally {
    loading.value = false
  }
}

async function chargerInscriptions() {
  inscriptionsLoading.value = true
  try {
    const response = await inscriptionService.parEvenement(route.params.id)
    inscriptions.value = response.data.data
  } catch (err) {
    console.error(err)
  } finally {
    inscriptionsLoading.value = false
  }
}

onMounted(chargerEvenement)

async function handleSupprimer() {
  if (!confirm(`Supprimer definitivement "${evenement.value.titre}" ? Cette action est irreversible.`)) {
    return
  }

  suppressionLoading.value = true
  try {
    await evenementService.supprimer(evenement.value.id)
    router.push('/evenements')
  } catch (err) {
    error.value = "Erreur lors de la suppression."
    console.error(err)
  } finally {
    suppressionLoading.value = false
  }
}

function validerTelephone() {
  if (telephone.value && !/^\d{1,9}$/.test(telephone.value)) {
    telephoneError.value = 'Le telephone doit contenir au maximum 9 chiffres.'
    return false
  }
  telephoneError.value = null
  return true
}

function validerMatricule() {
  if (matricule.value && matricule.value.length > 9) {
    matriculeError.value = 'Le matricule doit contenir au maximum 9 caracteres.'
    return false
  }
  matriculeError.value = null
  return true
}

async function handleInscription() {
  inscriptionError.value = null
  inscriptionMessage.value = null

  const telephoneOk = validerTelephone()
  const matriculeOk = validerMatricule()
  if (!telephoneOk || !matriculeOk) {
    return
  }

  inscriptionLoading.value = true

  try {
    const participantResponse = await participantService.creer({
      nom: nom.value,
      prenom: prenom.value,
      email: email.value,
      telephone: telephone.value || null,
      matricule: matricule.value || null,
    })

    await inscriptionService.creer({
      participant_id: participantResponse.data.data.id,
      evenement_id: evenement.value.id,
    })

    inscriptionMessage.value = "Inscription reussie ! Vous recevrez les details par email."
    nom.value = ''
    prenom.value = ''
    email.value = ''
    telephone.value = ''
    matricule.value = ''
  } catch (err) {
    if (err.response?.status === 422) {
      const erreurs = err.response.data.errors
      if (erreurs?.email) {
        inscriptionError.value = "Cet email est deja enregistre. Utilisez un autre email ou contactez l'organisateur."
      } else if (erreurs?.matricule) {
        inscriptionError.value = "Ce matricule est deja enregistre."
      } else {
        inscriptionError.value = "Erreur de validation. Verifiez vos informations."
      }
    } else {
      inscriptionError.value = "Erreur lors de l'inscription. Verifiez vos informations."
    }
    console.error(err.response?.data || err)
  } finally {
    inscriptionLoading.value = false
  }
}
</script>

<template>
  <div>
    <p v-if="loading" class="p-8 text-gray-500">Chargement...</p>
    <p v-else-if="error" class="p-8 text-red-600">{{ error }}</p>

    <div v-else-if="evenement">
      <div
        class="relative h-56 bg-blue-950 bg-cover bg-center flex items-center justify-center text-center"
        :style="{ backgroundImage: `url(${imageBanniere})` }"
      >
        <div class="absolute inset-0 bg-blue-950/60"></div>
        <div class="relative z-10 text-white px-6">
          <p class="text-sm uppercase tracking-wide text-blue-200 mb-1">Evenement officiel - Campus SUPDECO</p>
          <h1 class="text-3xl font-bold">{{ evenement.titre }}</h1>
        </div>
      </div>

      <div class="p-6">
        <div class="grid md:grid-cols-3 gap-6 mb-6">
          <div class="md:col-span-2 border border-gray-200 rounded-lg p-6">
            <div class="flex items-start justify-between mb-2">
              <span class="inline-block text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded">
                {{ evenement.categorie?.nom }}
              </span>

              <div v-if="estProprietaire" class="flex gap-2">
                <RouterLink
                  :to="`/evenements/${evenement.id}/modifier`"
                  class="text-sm px-3 py-1 border border-blue-600 text-blue-600 rounded hover:bg-blue-50"
                >
                  Modifier
                </RouterLink>
                <button
                  @click="handleSupprimer"
                  :disabled="suppressionLoading"
                  class="text-sm px-3 py-1 border border-red-600 text-red-600 rounded hover:bg-red-50 disabled:opacity-50"
                >
                  {{ suppressionLoading ? 'Suppression...' : 'Supprimer' }}
                </button>
              </div>
            </div>

            <p class="text-gray-600 mb-4">{{ evenement.description }}</p>

            <div class="text-sm text-gray-500 space-y-1">
              <p>Debut : {{ evenement.date_debut }}</p>
              <p>Fin : {{ evenement.date_fin }}</p>
              <p v-if="evenement.lieu">Lieu : {{ evenement.lieu }}</p>
              <p v-if="evenement.lien_visio">
                Lien : <a :href="evenement.lien_visio" target="_blank" class="text-blue-600 underline">{{ evenement.lien_visio }}</a>
              </p>
              <p v-if="evenement.capacite_max">Places disponibles : {{ evenement.capacite_max }}</p>
              <p>Organise par : {{ evenement.organisateur?.nom }}</p>
            </div>

            <div v-if="evenement.intervenants?.length" class="mt-4 pt-4 border-t border-gray-100">
              <p class="text-sm font-medium text-gray-700 mb-2">Intervenants</p>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="intervenant in evenement.intervenants"
                  :key="intervenant.id"
                  class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded"
                >
                  {{ intervenant.prenom }} {{ intervenant.nom }}
                  <span v-if="intervenant.specialite" class="text-gray-400">- {{ intervenant.specialite }}</span>
                  <span v-if="intervenant.role" class="text-blue-400"> ({{ intervenant.role }})</span>
                </span>
              </div>
            </div>
          </div>

          <div v-if="estProprietaire && evenement.qr_code" class="border border-gray-200 rounded-lg p-6 flex flex-col items-center">
            <h2 class="text-sm font-semibold text-gray-800 mb-1 text-center">QR de presence</h2>
            <p class="text-xs text-gray-500 mb-4 text-center">
              A afficher a l'entree. Chaque participant le scanne pour confirmer sa presence.
            </p>
            <QrCodeDisplay :value="urlScan" />
          </div>
        </div>

        <div v-if="estProprietaire" class="border border-gray-200 rounded-lg p-6 mb-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Liste de presence</h2>
            <span class="text-sm text-gray-500">
              {{ nombrePresents }} present(s) / {{ inscriptions.length }} inscrit(s)
            </span>
          </div>

          <p v-if="inscriptionsLoading" class="text-sm text-gray-500">Chargement...</p>
          <p v-else-if="inscriptions.length === 0" class="text-sm text-gray-400 italic">
            Aucune inscription pour le moment.
          </p>

          <table v-else class="w-full text-sm">
            <thead>
              <tr class="text-left text-gray-500 border-b border-gray-200">
                <th class="pb-2">Participant</th>
                <th class="pb-2">Email</th>
                <th class="pb-2">Presence</th>
                <th class="pb-2">Heure d'arrivee</th>
                <th class="pb-2">Certificat</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="inscription in inscriptions"
                :key="inscription.id"
                class="border-b border-gray-100"
              >
                <td class="py-2">{{ inscription.participant?.prenom }} {{ inscription.participant?.nom }}</td>
                <td class="py-2 text-gray-500">{{ inscription.participant?.email }}</td>
                <td class="py-2">
                  <span v-if="inscription.presence_arrivee" class="text-green-700 bg-green-50 px-2 py-1 rounded text-xs">
                    Present
                  </span>
                  <span v-else class="text-gray-400 bg-gray-50 px-2 py-1 rounded text-xs">
                    En attente
                  </span>
                </td>
                <td class="py-2 text-gray-500">
                  {{ inscription.date_presence_arrivee
                    ? new Date(inscription.date_presence_arrivee).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })
                    : '-' }}
                </td>
                <td class="py-2">
                  <span v-if="inscription.presence_arrivee" class="text-green-600 text-xs">Envoye</span>
                  <span v-else class="text-gray-400 text-xs">-</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="border border-gray-200 rounded-lg p-6">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">S'inscrire a cet evenement</h2>

          <p v-if="inscriptionMessage" class="text-green-700 bg-green-50 border border-green-200 rounded p-2 mb-4 text-sm">
            {{ inscriptionMessage }}
          </p>

          <form @submit.prevent="handleInscription" class="space-y-3">
            <div class="grid grid-cols-2 gap-3">
              <input v-model="nom" type="text" placeholder="Nom *" required
                class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
              <input v-model="prenom" type="text" placeholder="Prenom *" required
                class="border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            </div>
            <input v-model="email" type="email" placeholder="Email *" required
              class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <div class="grid grid-cols-2 gap-3">
              <div>
                <input v-model="telephone" @input="validerTelephone" type="tel" placeholder="Telephone" maxlength="9"
                  class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2"
                  :class="telephoneError ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'" />
                <p v-if="telephoneError" class="text-red-600 text-xs mt-1">{{ telephoneError }}</p>
              </div>
              <div>
                <input v-model="matricule" @input="validerMatricule" type="text" placeholder="Matricule (si etudiant)" maxlength="9"
                  class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2"
                  :class="matriculeError ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-blue-500'" />
                <p v-if="matriculeError" class="text-red-600 text-xs mt-1">{{ matriculeError }}</p>
              </div>
            </div>

            <p v-if="inscriptionError" class="text-red-600 text-sm">{{ inscriptionError }}</p>

            <button type="submit" :disabled="inscriptionLoading"
              class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 disabled:opacity-50">
              {{ inscriptionLoading ? 'Inscription...' : "S'inscrire" }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>