import api from './api'

export default {
  liste() {
    return api.get('/categories')
  },

  detail(id) {
    return api.get(`/categories/${id}`)
  },
}