import api from './api'

export default {
  liste() {
    return api.get('/intervenants')
  },

  detail(id) {
    return api.get(`/intervenants/${id}`)
  },

  creer(payload) {
    return api.post('/intervenants', payload)
  },

  modifier(id, payload) {
    return api.put(`/intervenants/${id}`, payload)
  },

  supprimer(id) {
    return api.delete(`/intervenants/${id}`)
  },
}