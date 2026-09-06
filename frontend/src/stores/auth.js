import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../services/api'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('token'))
  const organisateur = ref(JSON.parse(localStorage.getItem('organisateur') || 'null'))

  const isAuthenticated = computed(() => !!token.value)

  async function login(email, password) {
    const response = await api.post('/login', { email, password })
    token.value = response.data.token
    organisateur.value = response.data.organisateur

    localStorage.setItem('token', token.value)
    localStorage.setItem('organisateur', JSON.stringify(organisateur.value))
  }

  async function register(payload) {
    const response = await api.post('/register', payload)
    token.value = response.data.token
    organisateur.value = response.data.organisateur

    localStorage.setItem('token', token.value)
    localStorage.setItem('organisateur', JSON.stringify(organisateur.value))
  }

  async function logout() {
    try {
      await api.post('/logout')
    } catch (err) {
      console.error(err)
    }
    token.value = null
    organisateur.value = null
    localStorage.removeItem('token')
    localStorage.removeItem('organisateur')
  }

  return { token, organisateur, isAuthenticated, login, register, logout }
})