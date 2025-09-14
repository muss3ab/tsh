import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { cartAPI } from '../services/api'

interface CartItem {
  id: number
  product: {
    id: number
    name: string
    price: number
    image_url: string | null
  }
  quantity: number
  price: number
  subtotal: number
}

interface Cart {
  id: number
  status: string
  total_price: number
  items: CartItem[]
  item_count: number
}

export const useCartStore = defineStore('cart', () => {
  const cart = ref<Cart | null>(null)
  const loading = ref(false)

  const itemCount = computed(() => cart.value?.item_count || 0)
  const totalPrice = computed(() => cart.value?.total_price || 0)

  async function fetchCart() {
    loading.value = true
    try {
      const response = await cartAPI.getCart()
      cart.value = response.data
    } catch (error) {
      console.error('Failed to fetch cart:', error)
    } finally {
      loading.value = false
    }
  }

  async function addToCart(productId: number, quantity: number = 1) {
    try {
      const response = await cartAPI.addItem({ product_id: productId, quantity })
      cart.value = response.data
    } catch (error) {
      console.error('Failed to add to cart:', error)
    }
  }

  async function updateCartItem(itemId: number, quantity: number) {
    try {
      const response = await cartAPI.updateItem(itemId, quantity)
      cart.value = response.data
    } catch (error) {
      console.error('Failed to update cart item:', error)
    }
  }

  async function removeFromCart(itemId: number) {
    try {
      const response = await cartAPI.removeItem(itemId)
      cart.value = response.data
    } catch (error) {
      console.error('Failed to remove from cart:', error)
    }
  }

  return {
    cart,
    loading,
    itemCount,
    totalPrice,
    fetchCart,
    addToCart,
    updateCartItem,
    removeFromCart,
  }
})
