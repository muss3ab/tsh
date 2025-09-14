<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})
const loading = ref(false)
const error = ref('')

const register = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await fetch('/api/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(form.value),
    })

    const data = await response.json()

    if (response.ok) {
      authStore.setToken(data.token)
      authStore.setUser(data.user)
      router.push('/products')
    } else {
      error.value = data.message || 'Registration failed'
    }
  } catch (err) {
    error.value = 'An error occurred. Please try again.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="max-w-md mx-auto">
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-8">Register</h1>

    <form @submit.prevent="register" class="bg-white p-6 rounded-lg shadow">
      <div v-if="error" class="mb-4 p-3 bg-red-100 text-red-700 rounded">
        {{ error }}
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
        <input
          v-model="form.name"
          type="text"
          required
          class="w-full border border-gray-300 rounded-md px-3 py-2"
        />
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input
          v-model="form.email"
          type="email"
          required
          class="w-full border border-gray-300 rounded-md px-3 py-2"
        />
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input
          v-model="form.password"
          type="password"
          required
          class="w-full border border-gray-300 rounded-md px-3 py-2"
        />
      </div>

      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
        <input
          v-model="form.password_confirmation"
          type="password"
          required
          class="w-full border border-gray-300 rounded-md px-3 py-2"
        />
      </div>

      <button
        type="submit"
        :disabled="loading"
        class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 disabled:bg-gray-400"
      >
        {{ loading ? 'Registering...' : 'Register' }}
      </button>
    </form>

    <p class="text-center mt-4">
      Already have an account?
      <RouterLink to="/login" class="text-blue-600 hover:text-blue-800">
        Login here
      </RouterLink>
    </p>
  </div>
</template>
