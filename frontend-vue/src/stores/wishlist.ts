import { ref } from 'vue'
import { defineStore } from 'pinia'

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
      const response = await fetch('/api/wishlist', {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Accept': 'application/json',
        },
      })
      if (response.ok) {
        const data = await response.json()
        wishlist.value = data.data || []
      }
    } catch (error) {
      console.error('Failed to fetch wishlist:', error)
    } finally {
      loading.value = false
    }
  }

  async function addToWishlist(productId: number) {
    try {
      const response = await fetch('/api/wishlist', {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Content-Type': 'application/json',
          'Accept': 'application/json',
        },
        body: JSON.stringify({ product_id: productId }),
      })
      if (response.ok) {
        await fetchWishlist() // Refresh wishlist
      }
    } catch (error) {
      console.error('Failed to add to wishlist:', error)
    }
  }

  async function removeFromWishlist(productId: number) {
    try {
      const response = await fetch(`/api/wishlist/${productId}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Accept': 'application/json',
        },
      })
      if (response.ok) {
        wishlist.value = wishlist.value.filter(product => product.id !== productId)
      }
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
