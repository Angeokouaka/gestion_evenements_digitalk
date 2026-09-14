<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import dashboardService from '../services/dashboardService'
import PageHeader from '../components/PageHeader.vue'

const stats = ref(null)
const loading = ref(true)
const error = ref(null)

const heuresLabels = {
  6: '6h', 7: '7h', 8: '8h', 9: '9h', 10: '10h', 11: '11h',
  12: '12h', 13: '13h', 14: '14h', 15: '15h', 16: '16h',
  17: '17h', 18: '18h', 19: '19h', 20: '20h',
}

async function chargerStats() {
  loading.value = true
  try {
    const response = await dashboardService.statistiques()
    stats.value = response.data
  } catch (err) {
    error.value = 'Impossible de charger le tableau de bord.'
    console.error(err)
  } finally {
    loading.value = false
  }
}

function maxHeure() {
  if (!stats.value?.repartition_heures_arrivee?.length) return 0
  return Math.max(...stats.value.repartition_heures_arrivee.map((h) => h.total))
}

onMounted(chargerStats)
</script>

<template>
  <div class="p-8">
    <PageHeader title="Tableau de bord" />

    <p v-if="loading" class="text-gray-500">Chargement...</p>
    <p v-else-if="error" class="text-red-600">{{ error }}</p>

    <div v-else-if="stats" class="space-y-6">
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="border border-gray-200 rounded-lg p-4">
          <p class="text-sm text-gray-500">Evenements a venir</p>
          <p class="text-2xl font-bold text-blue-700">{{ stats.evenements_a_venir }}</p>
        </div>
        <div class="border border-gray-200 rounded-lg p-4">
          <p class="text-sm text-gray-500">Evenements passes</p>
          <p class="text-2xl font-bold text-gray-700">{{ stats.evenements_passes }}</p>
        </div>
        <div class="border border-gray-200 rounded-lg p-4">
          <p class="text-sm text-gray-500">Inscriptions totales</p>
          <p class="text-2xl font-bold text-gray-700">{{ stats.inscriptions_total }}</p>
        </div>
        <div class="border border-gray-200 rounded-lg p-4">
          <p class="text-sm text-gray-500">Presences confirmees</p>
          <p class="text-2xl font-bold text-green-700">{{ stats.presences_confirmees }}</p>
        </div>
      </div>

      <div class="border border-gray-200 rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Repartition des heures d'arrivee</h2>

        <p v-if="stats.repartition_heures_arrivee.length === 0" class="text-sm text-gray-400 italic">
          Aucune presence enregistree pour le moment.
        </p>

        <div v-else class="space-y-2">
          <div
            v-for="creneau in stats.repartition_heures_arrivee"
            :key="creneau.heure"
            class="flex items-center gap-3"
          >
            <span class="text-sm text-gray-500 w-12">{{ heuresLabels[creneau.heure] || creneau.heure + 'h' }}</span>
            <div class="flex-1 bg-gray-100 rounded h-6 overflow-hidden">
              <div
                class="bg-blue-600 h-full flex items-center justify-end px-2 text-xs text-white"
                :style="{ width: (creneau.total / maxHeure() * 100) + '%' }"
              >
                {{ creneau.total }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="border border-gray-200 rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Evenements recents</h2>

        <table class="w-full text-sm">
          <thead>
            <tr class="text-left text-gray-500 border-b border-gray-200">
              <th class="pb-2">Titre</th>
              <th class="pb-2">Date</th>
              <th class="pb-2">Inscrits</th>
              <th class="pb-2">Remplissage</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="evenement in stats.evenements_recents"
              :key="evenement.id"
              class="border-b border-gray-100"
            >
              <td class="py-2">
                <RouterLink :to="`/evenements/${evenement.id}`" class="text-blue-600 hover:underline">
                  {{ evenement.titre }}
                </RouterLink>
              </td>
              <td class="py-2 text-gray-500">{{ new Date(evenement.date_debut).toLocaleDateString('fr-FR') }}</td>
              <td class="py-2">{{ evenement.inscrits }} / {{ evenement.capacite_max || '\u221e' }}</td>
              <td class="py-2">
                <span v-if="evenement.taux_remplissage !== null">{{ evenement.taux_remplissage }}%</span>
                <span v-else class="text-gray-400">-</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>