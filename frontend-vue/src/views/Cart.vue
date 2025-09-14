<script setup lang="ts">
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useCartStore } from '../stores/cart'

const cartStore = useCartStore()

const updateQuantity = async (itemId: number, quantity: number) => {
  if (quantity <= 0) {
    await cartStore.removeFromCart(itemId)
  } else {
    await cartStore.updateCartItem(itemId, quantity)
  }
}

const removeItem = async (itemId: number) => {
  await cartStore.removeFromCart(itemId)
}

onMounted(() => {
  cartStore.fetchCart()
})
</script>

<template>
  <div>
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

    <div v-if="cartStore.loading" class="text-center py-8">Loading...</div>

    <div v-else-if="!cartStore.cart || cartStore.cart.items.length === 0" class="text-center py-8">
      <p class="text-gray-600 mb-4">Your cart is empty</p>
      <RouterLink
        to="/products"
        class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700"
      >
        Continue Shopping
      </RouterLink>
    </div>

    <div v-else>
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="divide-y divide-gray-200">
          <div
            v-for="item in cartStore.cart.items"
            :key="item.id"
            class="p-6 flex items-center"
          >
            <div class="flex-shrink-0 w-20 h-20 bg-gray-200 rounded">
              <img
                v-if="item.product.image_url"
                :src="item.product.image_url"
                :alt="item.product.name"
                class="w-full h-full object-cover rounded"
              />
            </div>

            <div class="ml-6 flex-1">
              <h3 class="text-lg font-medium text-gray-900">
                <RouterLink :to="`/products/${item.product.id}`" class="hover:text-blue-600">
                  {{ item.product.name }}
                </RouterLink>
              </h3>
              <p class="text-gray-600">${{ item.product.price }}</p>
            </div>

            <div class="ml-6 flex items-center">
              <button
                @click="updateQuantity(item.id, item.quantity - 1)"
                class="p-1 text-gray-600 hover:text-gray-800"
              >
                -
              </button>
              <input
                :value="item.quantity"
                @input="updateQuantity(item.id, parseInt($event.target.value) || 0)"
                type="number"
                min="0"
                class="w-16 text-center border border-gray-300 rounded mx-2"
              />
              <button
                @click="updateQuantity(item.id, item.quantity + 1)"
                class="p-1 text-gray-600 hover:text-gray-800"
              >
                +
              </button>
            </div>

            <div class="ml-6 text-right">
              <p class="text-lg font-medium text-gray-900">${{ item.subtotal }}</p>
              <button
                @click="removeItem(item.id)"
                class="text-red-600 hover:text-red-800 text-sm"
              >
                Remove
              </button>
            </div>
          </div>
        </div>

        <div class="bg-gray-50 px-6 py-4">
          <div class="flex justify-between items-center">
            <span class="text-lg font-medium text-gray-900">Total: ${{ cartStore.totalPrice }}</span>
            <RouterLink
              to="/checkout"
              class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700"
            >
              Proceed to Checkout
            </RouterLink>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
