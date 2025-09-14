<script setup lang="ts">
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useWishlistStore } from '../stores/wishlist'
import { useCartStore } from '../stores/cart'

const wishlistStore = useWishlistStore()
const cartStore = useCartStore()

const addToCart = async (productId: number) => {
  await cartStore.addToCart(productId)
  await wishlistStore.removeFromWishlist(productId)
}

onMounted(() => {
  wishlistStore.fetchWishlist()
})
</script>

<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-900 mb-8">My Wishlist</h1>

    <div v-if="wishlistStore.loading" class="text-center py-8">Loading...</div>

    <div v-else-if="wishlistStore.wishlist.length === 0" class="text-center py-8">
      <p class="text-gray-600 mb-4">Your wishlist is empty</p>
      <RouterLink
        to="/products"
        class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700"
      >
        Browse Products
      </RouterLink>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div
        v-for="product in wishlistStore.wishlist"
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
              class="flex-1 bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700"
            >
              Add to Cart
            </button>

            <button
              @click="wishlistStore.removeFromWishlist(product.id)"
              class="p-2 text-red-600 hover:text-red-800 border border-red-300 rounded"
            >
              ✕
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
