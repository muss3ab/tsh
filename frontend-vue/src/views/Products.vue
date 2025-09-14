<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
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

interface Category {
  id: number
  name: string
  slug: string
  children?: Category[]
}

const products = ref<Product[]>([])
const categories = ref<Category[]>([])
const loading = ref(false)
const selectedCategory = ref('')
const searchQuery = ref('')
const minPrice = ref('')
const maxPrice = ref('')

const cartStore = useCartStore()
const wishlistStore = useWishlistStore()
const authStore = useAuthStore()

const fetchProducts = async () => {
  loading.value = true
  try {
    const params = new URLSearchParams()
    if (selectedCategory.value) params.append('category_id', selectedCategory.value)
    if (searchQuery.value) params.append('search', searchQuery.value)
    if (minPrice.value) params.append('min_price', minPrice.value)
    if (maxPrice.value) params.append('max_price', maxPrice.value)

    const response = await fetch(`/api/products?${params}`)
    if (response.ok) {
      const data = await response.json()
      products.value = data.data
    }
  } catch (error) {
    console.error('Failed to fetch products:', error)
  } finally {
    loading.value = false
  }
}

const fetchCategories = async () => {
  try {
    const response = await fetch('/api/categories')
    if (response.ok) {
      categories.value = await response.json()
    }
  } catch (error) {
    console.error('Failed to fetch categories:', error)
  }
}

const addToCart = async (productId: number) => {
  if (!authStore.isAuthenticated) {
    // Redirect to login
    return
  }
  await cartStore.addToCart(productId)
}

const addToWishlist = async (productId: number) => {
  if (!authStore.isAuthenticated) {
    // Redirect to login
    return
  }
  await wishlistStore.addToWishlist(productId)
}

onMounted(() => {
  fetchProducts()
  fetchCategories()
})
</script>

<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Products</h1>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-lg shadow mb-8">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
          <select
            v-model="selectedCategory"
            @change="fetchProducts"
            class="w-full border border-gray-300 rounded-md px-3 py-2"
          >
            <option value="">All Categories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
          <input
            v-model="searchQuery"
            @input="fetchProducts"
            type="text"
            placeholder="Search products..."
            class="w-full border border-gray-300 rounded-md px-3 py-2"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Min Price</label>
          <input
            v-model="minPrice"
            @input="fetchProducts"
            type="number"
            placeholder="0"
            class="w-full border border-gray-300 rounded-md px-3 py-2"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Max Price</label>
          <input
            v-model="maxPrice"
            @input="fetchProducts"
            type="number"
            placeholder="1000"
            class="w-full border border-gray-300 rounded-md px-3 py-2"
          />
        </div>
      </div>
    </div>

    <!-- Products Grid -->
    <div v-if="loading" class="text-center py-8">Loading...</div>

    <div v-else-if="products.length === 0" class="text-center py-8">
      No products found
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div
        v-for="product in products"
        :key="product.id"
        class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow"
      >
        <div class="aspect-w-1 aspect-h-1 bg-gray-200">
          <img
            v-if="product.image_url"
            :src="product.image_url"
            :alt="product.name"
            class="w-full h-48 object-cover"
          />
          <div v-else class="w-full h-48 bg-gray-300 flex items-center justify-center">
            <span class="text-gray-500">No Image</span>
          </div>
        </div>

        <div class="p-4">
          <h3 class="text-lg font-semibold text-gray-900 mb-2">
            <RouterLink :to="`/products/${product.id}`" class="hover:text-blue-600">
              {{ product.name }}
            </RouterLink>
          </h3>

          <p class="text-gray-600 text-sm mb-2">{{ product.category.name }}</p>
          <p class="text-gray-800 font-bold mb-4">${{ product.price }}</p>

          <div class="flex space-x-2">
            <button
              @click="addToCart(product.id)"
              :disabled="!authStore.isAuthenticated"
              class="flex-1 bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 disabled:bg-gray-400"
            >
              Add to Cart
            </button>

            <button
              v-if="authStore.isAuthenticated"
              @click="addToWishlist(product.id)"
              class="p-2 text-gray-600 hover:text-red-600"
              :class="{ 'text-red-600': wishlistStore.isInWishlist(product.id) }"
            >
              ♥
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
