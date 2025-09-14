<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useNotificationStore } from '../../stores/notification'
import { categoriesAPI } from '../../services/api'

interface Category {
  id: number
  name: string
  slug: string
  parent_id: number | null
  parent?: {
    id: number
    name: string
  }
  children?: Category[]
  products_count?: number
}

const categories = ref<Category[]>([])
const loading = ref(false)
const showForm = ref(false)
const editingCategory = ref<Category | null>(null)
const notificationStore = useNotificationStore()

const form = ref({
  name: '',
  slug: '',
  parent_id: '',
})

const resetForm = () => {
  form.value = {
    name: '',
    slug: '',
    parent_id: '',
  }
  editingCategory.value = null
  showForm.value = false
}

const fetchCategories = async () => {
  loading.value = true
  try {
    const response = await categoriesAPI.getAll()
    categories.value = response.data.data || response.data
  } catch (error) {
    console.error('Failed to fetch categories:', error)
    notificationStore.addNotification({
      type: 'error',
      message: 'Failed to fetch categories',
    })
  } finally {
    loading.value = false
  }
}

const generateSlug = (name: string) => {
  return name
    .toLowerCase()
    .replace(/[^a-z0-9\s-]/g, '')
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
    .trim()
}

const updateSlug = () => {
  if (form.value.name && !editingCategory.value) {
    form.value.slug = generateSlug(form.value.name)
  }
}

const saveCategory = async () => {
  try {
    const categoryData = {
      ...form.value,
      parent_id: form.value.parent_id || null,
    }

    if (editingCategory.value) {
      await categoriesAPI.update(editingCategory.value.id, categoryData)
    } else {
      await categoriesAPI.create(categoryData)
    }

    await fetchCategories()
    resetForm()
    notificationStore.addNotification({
      type: 'success',
      message: `Category ${editingCategory.value ? 'updated' : 'created'} successfully`,
    })
  } catch (error) {
    console.error('Failed to save category:', error)
    notificationStore.addNotification({
      type: 'error',
      message: `Failed to ${editingCategory.value ? 'update' : 'create'} category`,
    })
  }
}

const editCategory = (category: Category) => {
  editingCategory.value = category
  form.value = {
    name: category.name,
    slug: category.slug,
    parent_id: category.parent_id?.toString() || '',
  }
  showForm.value = true
}

const deleteCategory = async (categoryId: number) => {
  if (!confirm('Are you sure you want to delete this category? This may affect products.')) return

  try {
    await categoriesAPI.delete(categoryId)
    await fetchCategories()
    notificationStore.addNotification({
      type: 'success',
      message: 'Category deleted successfully',
    })
  } catch (error) {
    console.error('Failed to delete category:', error)
    notificationStore.addNotification({
      type: 'error',
      message: 'Failed to delete category',
    })
  }
}

const getParentName = (category: Category) => {
  return category.parent?.name || 'None'
}

const getChildrenCount = (category: Category) => {
  return category.children?.length || 0
}

onMounted(() => {
  fetchCategories()
})
</script>

<template>
  <div>
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-900">Manage Categories</h1>
      <button
        @click="showForm = true"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        Add Category
      </button>
    </div>

    <!-- Category Form -->
    <div v-if="showForm" class="bg-white p-6 rounded-lg shadow mb-8">
      <h2 class="text-xl font-semibold mb-4">
        {{ editingCategory ? 'Edit Category' : 'Add Category' }}
      </h2>

      <form @submit.prevent="saveCategory" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
          <input
            v-model="form.name"
            @input="updateSlug"
            type="text"
            required
            class="w-full border border-gray-300 rounded-md px-3 py-2"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
          <input
            v-model="form.slug"
            type="text"
            required
            class="w-full border border-gray-300 rounded-md px-3 py-2"
          />
        </div>

        <div class="md:col-span-2">
          <label class="block text-sm font-medium text-gray-700 mb-1">Parent Category</label>
          <select
            v-model="form.parent_id"
            class="w-full border border-gray-300 rounded-md px-3 py-2"
          >
            <option value="">No Parent (Top Level)</option>
            <option
              v-for="category in categories"
              :key="category.id"
              :value="category.id"
              :disabled="editingCategory && category.id === editingCategory.id"
            >
              {{ category.name }}
            </option>
          </select>
        </div>

        <div class="md:col-span-2 flex space-x-4">
          <button
            type="submit"
            class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
          >
            {{ editingCategory ? 'Update' : 'Create' }}
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

    <!-- Categories Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Category
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Slug
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Parent
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Subcategories
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Products
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="category in categories" :key="category.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ category.name }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
              {{ category.slug }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ getParentName(category) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ getChildrenCount(category) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
              {{ category.products_count || 0 }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <button
                @click="editCategory(category)"
                class="text-blue-600 hover:text-blue-900 mr-4"
              >
                Edit
              </button>
              <button
                @click="deleteCategory(category.id)"
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
