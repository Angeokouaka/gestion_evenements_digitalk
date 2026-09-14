import api from './api'

export default {
  liste() {
    return api.get('/inscriptions')
  },

  detail(id) {
    return api.get(`/inscriptions/${id}`)
  },

  creer(payload) {
    return api.post('/inscriptions', payload)
  },

  modifier(id, payload) {
    return api.put(`/inscriptions/${id}`, payload)
  },

  supprimer(id) {
    return api.delete(`/inscriptions/${id}`)
  },

  scannerArrivee(id) {
    return api.post(`/inscriptions/${id}/scanner-arrivee`)
  },

  scannerParEmail(qrCode, email) {
    return api.post(`/scan/${qrCode}`, { email })
  },

  parEvenement(evenementId) {
    return api.get(`/evenements/${evenementId}/inscriptions`)
  },
}