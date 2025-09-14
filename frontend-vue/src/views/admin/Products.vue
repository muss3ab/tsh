<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useNotificationStore } from '../../stores/notification'

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
  }
}

interface Category {
  id: number
  name: string
  slug: string
}

const products = ref<Product[]>([])
const categories = ref<Category[]>([])
const loading = ref(false)
const showForm = ref(false)
const editingProduct = ref<Product | null>(null)
const notificationStore = useNotificationStore()

const form = ref({
  name: '',
  description: '',
  price: '',
  image_url: '',
  inventory_count: '',
  category_id: '',
})

const resetForm = () => {
  form.value = {
    name: '',
    description: '',
    price: '',
    image_url: '',
    inventory_count: '',
    category_id: '',
  }
  editingProduct.value = null
  showForm.value = false
}

const fetchProducts = async () => {
  loading.value = true
  try {
    const response = await fetch('/api/admin/products', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Accept': 'application/json',
      },
    })
    if (response.ok) {
      const data = await response.json()
      products.value = data.data
    } else {
      notificationStore.addNotification({
        type: 'error',
        message: 'Failed to fetch products',
      })
    }
  } catch (error) {
    console.error('Failed to fetch products:', error)
    notificationStore.addNotification({
      type: 'error',
      message: 'Failed to fetch products',
    })
  } finally {
    loading.value = false
  }
}

const fetchCategories = async () => {
  try {
    const response = await fetch('/api/admin/categories', {
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Accept': 'application/json',
      },
    })
    if (response.ok) {
      categories.value = await response.json()
    } else {
      notificationStore.addNotification({
        type: 'error',
        message: 'Failed to fetch categories',
      })
    }
  } catch (error) {
    console.error('Failed to fetch categories:', error)
    notificationStore.addNotification({
      type: 'error',
      message: 'Failed to fetch categories',
    })
  }
}

const saveProduct = async () => {
  try {
    const url = editingProduct.value
      ? `/api/admin/products/${editingProduct.value.id}`
      : '/api/admin/products'
    const method = editingProduct.value ? 'PATCH' : 'POST'

    const response = await fetch(url, {
      method,
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(form.value),
    })

    if (response.ok) {
      await fetchProducts()
      resetForm()
      notificationStore.addNotification({
        type: 'success',
        message: `Product ${editingProduct.value ? 'updated' : 'created'} successfully`,
      })
    } else {
      notificationStore.addNotification({
        type: 'error',
        message: `Failed to ${editingProduct.value ? 'update' : 'create'} product`,
      })
    }
  } catch (error) {
    console.error('Failed to save product:', error)
    notificationStore.addNotification({
      type: 'error',
      message: `Failed to ${editingProduct.value ? 'update' : 'create'} product`,
    })
  }
}

const editProduct = (product: Product) => {
  editingProduct.value = product
  form.value = {
    name: product.name,
    description: product.description,
    price: product.price.toString(),
    image_url: product.image_url || '',
    inventory_count: product.inventory_count.toString(),
    category_id: product.category.id.toString(),
  }
  showForm.value = true
}

const deleteProduct = async (productId: number) => {
  if (!confirm('Are you sure you want to delete this product?')) return

  try {
    const response = await fetch(`/api/admin/products/${productId}`, {
      method: 'DELETE',
      headers: {
        'Authorization': `Bearer ${localStorage.getItem('token')}`,
        'Accept': 'application/json',
      },
    })

    if (response.ok) {
      await fetchProducts()
      notificationStore.addNotification({
        type: 'success',
        message: 'Product deleted successfully',
      })
    } else {
      notificationStore.addNotification({
        type: 'error',
        message: 'Failed to delete product',
      })
    }
  } catch (error) {
    console.error('Failed to delete product:', error)
    notificationStore.addNotification({
      type: 'error',
      message: 'Failed to delete product',
    })
  }
}

onMounted(() => {
  fetchProducts()
  fetchCategories()
})
</script>

<template>
  <div>
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Manage Products</h1>
      <button
        @click="showForm = true"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        Add Product
      </button>
    </div>

    <!-- Product Form -->
    <div v-if="showForm" class="bg-white p-6 rounded-lg shadow mb-8">
      <h2 class="text-xl font-semibold mb-4">
        {{ editingProduct ? 'Edit Product' : 'Add Product' }}
      </h2>

      <form @submit.prevent="saveProduct" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
          <input
            v-model="form.name"
            type="text"
            required
            class="w-full border border-gray-300 rounded-md px-3 py-2"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
          <select
            v-model="form.category_id"
            required
            class="w-full border border-gray-300 rounded-md px-3 py-2"
          >
            <option value="">Select Category</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
          <input
            v-model="form.price"
            type="number"
            step="0.01"
            required
            class="w-full border border-gray-300 rounded-md px-3 py-2"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Inventory Count</label>
          <input
            v-model="form.inventory_count"
            type="number"
            required
            class="w-full border border-gray-300 rounded-md px-3 py-2"
          />
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Image URL</label>
          <input
            v-model="form.image_url"
            type="url"
            class="w-full border border-gray-300 rounded-md px-3 py-2"
          />
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
          <textarea
            v-model="form.description"
            required
            rows="3"
            class="w-full border border-gray-300 rounded-md px-3 py-2"
          ></textarea>
        </div>

        <div class="md:col-span-2 flex space-x-4">
          <button
            type="submit"
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
          >
            {{ editingProduct ? 'Update' : 'Create' }}
          </button>
          <button
            type="button"
            @click="resetForm"
            class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700"
          >
            Cancel
          </button>
        </div>
      </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Product
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Category
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Price
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Inventory
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="product in products" :key="product.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                  <img
                    v-if="product.image_url"
                    :src="product.image_url"
                    :alt="product.name"
                    class="h-10 w-10 rounded-full object-cover"
                  />
                  <div v-else class="h-10 w-10 rounded-full bg-gray-300"></div>
                </div>
                <div class="ml-4">
                  <div class="text-sm font-medium text-gray-900">{{ product.name }}</div>
                  <div class="text-sm text-gray-500 truncate max-w-xs">{{ product.description }}</div>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ product.category.name }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              ${{ product.price }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ product.inventory_count }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button
                @click="editProduct(product)"
                class="text-blue-600 hover:text-blue-900 mr-4"
              >
                Edit
              </button>
              <button
                @click="deleteProduct(product.id)"
                class="text-red-600 hover:text-red-900"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
