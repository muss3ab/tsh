import { ref } from 'vue'
import { defineStore } from 'pinia'
import { wishlistAPI } from '../services/api'

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

export const useWishlistStore = defineStore('wishlist', () => {
  const wishlist = ref<Product[]>([])
  const loading = ref(false)

  async function fetchWishlist() {
    loading.value = true
    try {
      const response = await wishlistAPI.getWishlist()
      wishlist.value = response.data.data || []
    } catch (error) {
      console.error('Failed to fetch wishlist:', error)
    } finally {
      loading.value = false
    }
  }

  async function addToWishlist(productId: number) {
    try {
      await wishlistAPI.addItem(productId)
      await fetchWishlist() // Refresh wishlist
    } catch (error) {
      console.error('Failed to add to wishlist:', error)
    }
  }

  async function removeFromWishlist(productId: number) {
    try {
      await wishlistAPI.removeItem(productId)
      wishlist.value = wishlist.value.filter(product => product.id !== productId)
    } catch (error) {
      console.error('Failed to remove from wishlist:', error)
    }
  }

  function isInWishlist(productId: number): boolean {
    return wishlist.value.some(product => product.id === productId)
  }

  return {
    wishlist,
    loading,
    fetchWishlist,
    addToWishlist,
    removeFromWishlist,
    isInWishlist,
  }
})
