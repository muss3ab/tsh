import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { authAPI } from '../services/api'

interface User {
  id: number
  name: string
  email: string
  role: 'user' | 'admin'
}

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const token = ref<string | null>(null)

  const isAuthenticated = computed(() => !!token.value)
  const isAdmin = computed(() => user.value?.role === 'admin')
  const isUser = computed(() => user.value?.role === 'user')

  function setUser(newUser: User) {
    user.value = newUser
  }

  function setToken(newToken: string) {
    token.value = newToken
    localStorage.setItem('token', newToken)
  }

  async function logout() {
    try {
      await authAPI.logout()
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      user.value = null
      token.value = null
      localStorage.removeItem('token')
    }
  }

  async function initializeAuth() {
    const storedToken = localStorage.getItem('token')
    if (storedToken) {
      token.value = storedToken
      try {
        const response = await authAPI.getUser()
        user.value = response.data
      } catch (error) {
        // Token might be invalid, clear it
        console.error('Failed to fetch user:', error)
        token.value = null
        localStorage.removeItem('token')
      }
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    isAdmin,
    isUser,
    setUser,
    setToken,
    logout,
    initializeAuth,
  }
})
