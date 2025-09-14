<script setup lang="ts">
import { computed } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useCartStore } from '../stores/cart'
import { useWishlistStore } from '../stores/wishlist'
import { RouterLink } from 'vue-router'

const authStore = useAuthStore()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()

const logout = () => {
  authStore.logout()
  // Redirect to home or login
  window.location.href = '/'
}
</script>

<template>
  <nav class="bg-white shadow-lg">
    <div class="container mx-auto px-4">
      <div class="flex justify-between items-center py-4">
        <RouterLink to="/" class="text-xl font-bold text-gray-800">
          E-Commerce Store
        </RouterLink>

        <div class="flex items-center space-x-4">
          <RouterLink to="/products" class="text-gray-600 hover:text-gray-800">
            Products
          </RouterLink>

          <template v-if="authStore.isAuthenticated">
            <RouterLink to="/cart" class="text-gray-600 hover:text-gray-800 relative">
              Cart
              <span v-if="cartStore.itemCount > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                {{ cartStore.itemCount }}
              </span>
            </RouterLink>

            <RouterLink to="/wishlist" class="text-gray-600 hover:text-gray-800 relative">
              Wishlist
              <span v-if="wishlistStore.wishlist.length > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                {{ wishlistStore.wishlist.length }}
              </span>
            </RouterLink>

            <RouterLink to="/orders" class="text-gray-600 hover:text-gray-800">
              Orders
            </RouterLink>

            <template v-if="authStore.isAdmin">
              <RouterLink to="/admin/products" class="text-gray-600 hover:text-gray-800">
                Admin
              </RouterLink>
            </template>

            <button @click="logout" class="text-gray-600 hover:text-gray-800">
              Logout
            </button>
          </template>

          <template v-else>
            <RouterLink to="/login" class="text-gray-600 hover:text-gray-800">
              Login
            </RouterLink>
            <RouterLink to="/register" class="text-gray-600 hover:text-gray-800">
              Register
            </RouterLink>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>
