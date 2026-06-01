import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { post } from '../utils/api.js'

export const useAuthStore = defineStore('auth', () => {
  const token = ref(localStorage.getItem('token') || null)
  const user  = ref(JSON.parse(localStorage.getItem('user') || 'null'))

  const isLoggedIn = computed(() => !!token.value)
  const isAdmin    = computed(() => user.value?.role === 'admin')

  async function login(email, password) {
    const res  = await post('/auth/login', { email, password })
    const body = await res.json()
    if (!res.ok) throw new Error(body.error || 'Login failed')
    _persist(body)
  }

  async function register(name, email, password) {
    const res  = await post('/auth/register', { name, email, password })
    const body = await res.json()
    if (!res.ok) throw new Error(body.error || 'Registration failed')
    _persist(body)
  }

  function logout() {
    token.value = null
    user.value  = null
    localStorage.removeItem('token')
    localStorage.removeItem('user')
  }

  function _persist({ token: t, user: u }) {
    token.value = t
    user.value  = u
    localStorage.setItem('token', t)
    localStorage.setItem('user', JSON.stringify(u))
  }

  return { token, user, isLoggedIn, isAdmin, login, register, logout }
})
