<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { ordersAPI } from '../services/api'

interface Order {
  id: number
  status: string
  total_price: number
  shipping_address: string
  shipping_phone: string
  created_at: string
  items: Array<{
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
  }>
}

const orders = ref<Order[]>([])
const loading = ref(false)

const fetchOrders = async () => {
  loading.value = true
  try {
    const response = await ordersAPI.getOrders()
    orders.value = response.data.data
  } catch (error) {
    console.error('Failed to fetch orders:', error)
  } finally {
    loading.value = false
  }
}

const getStatusColor = (status: string) => {
  switch (status) {
    case 'pending': return 'bg-yellow-100 text-yellow-800'
    case 'completed': return 'bg-green-100 text-green-800'
    case 'cancelled': return 'bg-red-100 text-red-800'
    default: return 'bg-gray-100 text-gray-800'
  }
}

onMounted(() => {
  fetchOrders()
})
</script>

<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-900 mb-8">My Orders</h1>

    <div v-if="loading" class="text-center py-8">Loading...</div>

    <div v-else-if="orders.length === 0" class="text-center py-8">
      <p class="text-gray-600 mb-4">You haven't placed any orders yet</p>
      <RouterLink
        to="/products"
        class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700"
      >
        Start Shopping
      </RouterLink>
    </div>

    <div v-else class="space-y-6">
      <div
        v-for="order in orders"
        :key="order.id"
        class="bg-white rounded-lg shadow overflow-hidden"
      >
        <div class="p-6 border-b border-gray-200">
          <div class="flex justify-between items-start">
            <div>
              <h3 class="text-lg font-semibold text-gray-900">Order #{{ order.id }}</h3>
              <p class="text-gray-600">{{ new Date(order.created_at).toLocaleDateString() }}</p>
            </div>
            <div class="text-right">
              <span
                :class="getStatusColor(order.status)"
                class="px-2 py-1 rounded-full text-xs font-medium capitalize"
              >
                {{ order.status }}
              </span>
              <p class="text-lg font-bold text-gray-900 mt-1">${{ order.total_price }}</p>
            </div>
          </div>
        </div>

        <div class="p-6">
          <h4 class="font-medium text-gray-900 mb-4">Items</h4>
          <div class="space-y-4">
            <div
              v-for="item in order.items"
              :key="item.id"
              class="flex items-center space-x-4"
            >
              <div class="flex-shrink-0 w-16 h-16 bg-gray-200 rounded">
                <img
                  v-if="item.product.image_url"
                  :src="item.product.image_url"
                  :alt="item.product.name"
                  class="w-full h-full object-cover rounded"
                />
              </div>
              <div class="flex-1">
                <RouterLink
                  :to="`/products/${item.product.id}`"
                  class="text-gray-900 hover:text-blue-600"
                >
                  {{ item.product.name }}
                </RouterLink>
                <p class="text-gray-600">Quantity: {{ item.quantity }}</p>
              </div>
              <div class="text-right">
                <p class="font-medium text-gray-900">${{ item.subtotal }}</p>
              </div>
            </div>
          </div>
        </div>

        <div class="px-6 py-4 bg-gray-50">
          <div class="flex justify-between items-center text-sm text-gray-600">
            <div>
              <p><strong>Shipping Address:</strong> {{ order.shipping_address }}</p>
              <p><strong>Phone:</strong> {{ order.shipping_phone }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
