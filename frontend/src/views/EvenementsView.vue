<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import api from '../services/api'
import PageHeader from '../components/PageHeader.vue'

const FILIERES = ['ESITEC', 'IST', 'PGE', 'IMAP', 'MERCURE', 'ECONOMIE', 'BBA', 'LEA', 'SCHOOL OF LAW']

const evenements = ref([])
const loading = ref(true)
const error = ref(null)
const filtreActif = ref('tous')
const filiereActive = ref('')
const anneeActive = ref('toutes')
const pageActuelle = ref(1)
const dernierePage = ref(1)
const total = ref(0)

const maintenant = new Date()

function estAujourdhui(dateStr) {
  const date = new Date(dateStr)
  return date.toDateString() === maintenant.toDateString()
}

function estAVenir(dateStr) {
  return new Date(dateStr) > maintenant
}

function estPasse(dateStr) {
  return new Date(dateStr) < maintenant
}

function anneeAcademiqueDe(dateStr) {
  const date = new Date(dateStr)
  const annee = date.getFullYear()
  const mois = date.getMonth()
  return mois >= 8 ? `${annee}-${annee + 1}` : `${annee - 1}-${annee}`
}

const anneesDisponibles = computed(() => {
  const anneeCourante = anneeAcademiqueDe(maintenant.toISOString())
  const [debut] = anneeCourante.split('-').map(Number)
  const annees = []
  for (let i = -1; i <= 2; i++) {
    annees.push(`${debut + i}-${debut + i + 1}`)
  }
  return annees
})

const evenementsFiltres = computed(() => {
  let liste = evenements.value

  if (filtreActif.value === 'aujourdhui') {
    liste = liste.filter((e) => estAujourdhui(e.date_debut))
  } else if (filtreActif.value === 'a-venir') {
    liste = liste.filter((e) => estAVenir(e.date_debut) && !estAujourdhui(e.date_debut))
  } else if (filtreActif.value === 'passes') {
    liste = liste.filter((e) => estPasse(e.date_debut))
  }

  if (anneeActive.value !== 'toutes') {
    liste = liste.filter((e) => anneeAcademiqueDe(e.date_debut) === anneeActive.value)
  }

  return liste
})

const filtres = [
  { valeur: 'aujourdhui', label: "Aujourd'hui" },
  { valeur: 'a-venir', label: 'A venir' },
  { valeur: 'passes', label: 'Passes' },
  { valeur: 'tous', label: 'Tous' },
]

async function chargerEvenements(page = 1) {
  loading.value = true
  try {
    const params = { page }
    if (filiereActive.value) {
      params.filiere = filiereActive.value
    }

    const response = await api.get('/evenements', { params })
    evenements.value = response.data.data
    pageActuelle.value = response.data.meta.current_page
    dernierePage.value = response.data.meta.last_page
    total.value = response.data.meta.total
  } catch (err) {
    error.value = "Impossible de charger les evenements."
    console.error(err)
  } finally {
    loading.value = false
  }
}

function changerFiliere() {
  chargerEvenements(1)
}

function pagePrecedente() {
  if (pageActuelle.value > 1) {
    chargerEvenements(pageActuelle.value - 1)
  }
}

function pageSuivante() {
  if (pageActuelle.value < dernierePage.value) {
    chargerEvenements(pageActuelle.value + 1)
  }
}

onMounted(() => chargerEvenements(1))
</script>

<template>
  <div class="p-8">
    <PageHeader title="Evenements" />

    <div class="flex flex-wrap gap-3 mb-4">
      <select v-model="filiereActive" @change="changerFiliere"
        class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">Toutes les filieres</option>
        <option v-for="f in FILIERES" :key="f" :value="f">{{ f }}</option>
      </select>

      <select v-model="anneeActive"
        class="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="toutes">Toutes les annees</option>
        <option v-for="annee in anneesDisponibles" :key="annee" :value="annee">{{ annee }}</option>
      </select>
    </div>

    <div class="flex gap-2 mb-6 border-b border-gray-200">
      <button
        v-for="filtre in filtres"
        :key="filtre.valeur"
        @click="filtreActif = filtre.valeur"
        class="px-4 py-2 text-sm font-medium border-b-2 -mb-px"
        :class="filtreActif === filtre.valeur
          ? 'border-blue-600 text-blue-600'
          : 'border-transparent text-gray-500 hover:text-gray-700'"
      >
        {{ filtre.label }}
      </button>
    </div>

    <p v-if="loading" class="text-gray-500">Chargement...</p>
    <p v-else-if="error" class="text-red-600">{{ error }}</p>
    <p v-else-if="evenementsFiltres.length === 0" class="text-gray-400 italic">
      Aucun evenement pour ces filtres sur cette page.
    </p>

    <div v-else class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
      <div
        v-for="evenement in evenementsFiltres"
        :key="evenement.id"
        class="border border-gray-200 rounded-lg p-4 shadow-sm flex flex-col"
      >
        <div class="flex gap-2 mb-2 flex-wrap">
          <span class="inline-block text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded self-start">
            {{ evenement.statut }}
          </span>
          <span v-if="evenement.filiere" class="inline-block text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded self-start">
            {{ evenement.filiere }}
          </span>
        </div>
        <h2 class="font-semibold text-lg text-gray-800">{{ evenement.titre }}</h2>
        <p class="text-sm text-gray-500 mb-2">{{ evenement.lieu }}</p>
        <p class="text-sm text-gray-600 flex-1">{{ evenement.description }}</p>
        <RouterLink
          :to="`/evenements/${evenement.id}`"
          class="mt-3 text-center bg-blue-600 text-white py-2 rounded hover:bg-blue-700 text-sm font-medium"
        >
          Voir details
        </RouterLink>
      </div>
    </div>

    <div v-if="!loading && !error" class="flex items-center justify-between mt-6 pt-4 border-t border-gray-200">
      <p class="text-sm text-gray-500">
        Page {{ pageActuelle }} sur {{ dernierePage }} ({{ total }} evenements au total)
      </p>
      <div class="flex gap-2">
        <button
          @click="pagePrecedente"
          :disabled="pageActuelle === 1"
          class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
        >
          Precedent
        </button>
        <button
          @click="pageSuivante"
          :disabled="pageActuelle === dernierePage"
          class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed"
        >
          Suivant
        </button>
      </div>
    </div>
  </div>
</template>