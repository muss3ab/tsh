<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { useCartStore } from '../stores/cart'
import { useWishlistStore } from '../stores/wishlist'
import { useAuthStore } from '../stores/auth'

interface Product {
  id: number
  name: string
  description: string
  price: number
  image_url: string | null
  inventory_count: number
  category: {
    id: number
    name: string
    slug: string
  }
}

const route = useRoute()
const product = ref<Product | null>(null)
const loading = ref(false)
const quantity = ref(1)

const cartStore = useCartStore()
const wishlistStore = useWishlistStore()
const authStore = useAuthStore()

const fetchProduct = async () => {
  loading.value = true
  try {
    const response = await fetch(`/api/products/${route.params.id}`)
    if (response.ok) {
      product.value = await response.json()
    }
  } catch (error) {
    console.error('Failed to fetch product:', error)
  } finally {
    loading.value = false
  }
}

const addToCart = async () => {
  if (!product.value || !authStore.isAuthenticated) return
  await cartStore.addToCart(product.value.id, quantity.value)
}

const addToWishlist = async () => {
  if (!product.value || !authStore.isAuthenticated) return
  await wishlistStore.addToWishlist(product.value.id)
}

onMounted(() => {
  fetchProduct()
})
</script>

<template>
  <div v-if="loading" class="text-center py-8">Loading...</div>

  <div v-else-if="!product" class="text-center py-8">Product not found</div>

  <div v-else class="max-w-4xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- Product Image -->
      <div>
        <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg overflow-hidden">
          <img
            v-if="product.image_url"
            :src="product.image_url"
            :alt="product.name"
            class="w-full h-96 object-cover"
          />
          <div v-else class="w-full h-96 bg-gray-300 flex items-center justify-center">
            <span class="text-gray-500 text-lg">No Image</span>
          </div>
        </div>
      </div>

      <!-- Product Details -->
      <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ product.name }}</h1>
        <p class="text-gray-600 mb-2">Category: {{ product.category.name }}</p>
        <p class="text-2xl font-bold text-gray-900 mb-4">${{ product.price }}</p>
        <p class="text-gray-700 mb-6">{{ product.description }}</p>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
          <input
            v-model.number="quantity"
            type="number"
            min="1"
            :max="product.inventory_count"
            class="border border-gray-300 rounded-md px-3 py-2 w-20"
          />
          <p class="text-sm text-gray-600 mt-1">{{ product.inventory_count }} in stock</p>
        </div>

        <div class="flex space-x-4">
          <button
            @click="addToCart"
            :disabled="!authStore.isAuthenticated || product.inventory_count === 0"
            class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 disabled:bg-gray-400"
          >
            {{ product.inventory_count === 0 ? 'Out of Stock' : 'Add to Cart' }}
          </button>

          <button
            v-if="authStore.isAuthenticated"
            @click="addToWishlist"
            class="p-3 text-gray-600 hover:text-red-600 border border-gray-300 rounded-lg"
            :class="{ 'text-red-600 border-red-300': wishlistStore.isInWishlist(product.id) }"
          >
            ♥
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
