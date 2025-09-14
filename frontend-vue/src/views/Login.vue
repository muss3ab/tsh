<script setup lang="ts">
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useCartStore } from '../stores/cart'
import { useWishlistStore } from '../stores/wishlist'

const router = useRouter()
const authStore = useAuthStore()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()

const form = ref({
  email: '',
  password: '',
})
const loading = ref(false)
const error = ref('')

const login = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await fetch('/api/login', {
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
      await cartStore.fetchCart()
      await wishlistStore.fetchWishlist()
      router.push('/products')
    } else {
      error.value = data.message || 'Login failed'
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
    <h1 class="text-3xl font-bold text-center text-gray-900 mb-8">Login</h1>

    <form @submit.prevent="login" class="bg-white p-6 rounded-lg shadow">
      <div v-if="error" class="mb-4 p-3 bg-red-100 text-red-700 rounded">
        {{ error }}
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

      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <input
          v-model="form.password"
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
        {{ loading ? 'Logging in...' : 'Login' }}
      </button>
    </form>

    <p class="text-center mt-4">
      Don't have an account?
      <RouterLink to="/register" class="text-blue-600 hover:text-blue-800">
        Register here
      </RouterLink>
    </p>
  </div>
</template>
