import api from './api'

export default {
  statistiques() {
    return api.get('/dashboard')
  },
}