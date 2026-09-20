import api from './api'

export default {
  liste(params = {}) {
    return api.get('/evenements', { params })
  },

  detail(id) {
    return api.get(`/evenements/${id}`)
  },

  creer(payload) {
    const formData = new FormData()
    for (const cle in payload) {
      if (payload[cle] !== null && payload[cle] !== undefined) {
        formData.append(cle, payload[cle])
      }
    }
    return api.post('/evenements', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
  },

  modifier(id, payload) {
    const formData = new FormData()
    for (const cle in payload) {
      if (payload[cle] !== null && payload[cle] !== undefined) {
        formData.append(cle, payload[cle])
      }
    }
    formData.append('_method', 'PUT')
    return api.post(`/evenements/${id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
  },

  supprimer(id) {
    return api.delete(`/evenements/${id}`)
  },

  ajouterIntervenant(evenementId, intervenantId, role = 'Intervenant') {
    return api.post(`/evenements/${evenementId}/intervenants`, { intervenant_id: intervenantId, role })
  },

  retirerIntervenant(evenementId, intervenantId) {
    return api.delete(`/evenements/${evenementId}/intervenants/${intervenantId}`)
  },
}