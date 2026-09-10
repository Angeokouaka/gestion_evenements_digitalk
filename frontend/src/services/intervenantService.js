import api from './api'

export default {
  liste() {
    return api.get('/intervenants')
  },

  creer(payload) {
    return api.post('/intervenants', payload)
  },
}