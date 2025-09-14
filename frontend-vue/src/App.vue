<script setup lang="ts">
import { onMounted } from 'vue'
import { useAuthStore } from './stores/auth'
import { useCartStore } from './stores/cart'
import { useWishlistStore } from './stores/wishlist'
import Navbar from './components/Navbar.vue'
import NotificationContainer from './components/NotificationContainer.vue'
import { RouterView } from 'vue-router'

const authStore = useAuthStore()
const cartStore = useCartStore()
const wishlistStore = useWishlistStore()

onMounted(() => {
  authStore.initializeAuth()
  if (authStore.isAuthenticated) {
    cartStore.fetchCart()
    wishlistStore.fetchWishlist()
  }
})
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <Navbar />
    <NotificationContainer />
    <main class="container mx-auto px-4 py-8">
      <RouterView />
    </main>
  </div>
</template>

<style scoped></style>
