<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '../stores/cart'
import { ordersAPI } from '../services/api'

const router = useRouter()
const cartStore = useCartStore()

const form = ref({
  shipping_address: '',
  shipping_phone: '',
})
const loading = ref(false)
const error = ref('')

const checkout = async () => {
  loading.value = true
  error.value = ''

  try {
    const response = await ordersAPI.createOrder(form.value)

    // Clear cart and redirect to orders
    await cartStore.fetchCart()
    router.push('/orders')
  } catch (err: any) {
    error.value = err.response?.data?.message || 'An error occurred. Please try again.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  if (!cartStore.cart || cartStore.cart.items.length === 0) {
    router.push('/cart')
  }
})
</script>

<template>
  <div class="max-w-2xl mx-auto">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

    <div v-if="!cartStore.cart || cartStore.cart.items.length === 0" class="text-center py-8">
      Your cart is empty
    </div>

    <div v-else>
      <!-- Order Summary -->
      <div class="bg-white rounded-lg shadow mb-8">
        <div class="p-6">
          <h2 class="text-xl font-semibold mb-4">Order Summary</h2>

          <div class="space-y-4">
            <div
              v-for="item in cartStore.cart.items"
              :key="item.id"
              class="flex justify-between"
            >
              <span>{{ item.product.name }} (x{{ item.quantity }})</span>
              <span>${{ item.subtotal }}</span>
            </div>

            <hr class="my-4" />

            <div class="flex justify-between text-lg font-semibold">
              <span>Total</span>
              <span>${{ cartStore.totalPrice }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Shipping Information -->
      <form @submit.prevent="checkout" class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-xl font-semibold mb-4">Shipping Information</h2>

        <div v-if="error" class="mb-4 p-3 bg-red-100 text-red-700 rounded">
          {{ error }}
        </div>

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-1">Shipping Address</label>
          <textarea
            v-model="form.shipping_address"
            required
            rows="3"
            class="w-full border border-gray-300 rounded-md px-3 py-2"
            placeholder="Enter your full shipping address"
          ></textarea>
        </div>

        <div class="mb-6">
          <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
          <input
            v-model="form.shipping_phone"
            type="tel"
            required
            class="w-full border border-gray-300 rounded-md px-3 py-2"
            placeholder="Enter your phone number"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-green-600 text-white py-3 px-4 rounded hover:bg-green-700 disabled:bg-gray-400"
        >
          {{ loading ? 'Processing...' : 'Complete Order' }}
        </button>
      </form>
    </div>
  </div>
</template>
