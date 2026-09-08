import api from './api'

export default {
  creer(payload) {
    return api.post('/participants', payload)
  },
}