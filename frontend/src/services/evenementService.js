import api from './api'

export default {
  liste(params = {}) {
    return api.get('/evenements', { params })
  },

  detail(id) {
    return api.get(`/evenements/${id}`)
  },

  creer(payload) {
    return api.post('/evenements', payload)
  },

  modifier(id, payload) {
    return api.put(`/evenements/${id}`, payload)
  },

  supprimer(id) {
    return api.delete(`/evenements/${id}`)
  },

  ajouterIntervenant(evenementId, intervenantId) {
    return api.post(`/evenements/${evenementId}/intervenants`, { intervenant_id: intervenantId })
  },

  retirerIntervenant(evenementId, intervenantId) {
    return api.delete(`/evenements/${evenementId}/intervenants/${intervenantId}`)
  },
}