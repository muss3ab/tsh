import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

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

  function logout() {
    user.value = null
    token.value = null
    localStorage.removeItem('token')
  }

  function initializeAuth() {
    const storedToken = localStorage.getItem('token')
    if (storedToken) {
      token.value = storedToken
      // TODO: Fetch user data from API
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
