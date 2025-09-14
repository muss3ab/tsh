import axios, { type AxiosInstance, type AxiosResponse } from 'axios'

// Create axios instance with base configuration
const api: AxiosInstance = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
})

// Request interceptor to add auth token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// Response interceptor for error handling
api.interceptors.response.use(
  (response: AxiosResponse) => {
    return response
  },
  (error) => {
    if (error.response?.status === 401) {
      // Token expired or invalid
      localStorage.removeItem('token')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export default api

// Auth API
export const authAPI = {
  login: (credentials: { email: string; password: string }) =>
    api.post('/login', credentials),

  register: (userData: { name: string; email: string; password: string; password_confirmation: string }) =>
    api.post('/register', userData),

  logout: () =>
    api.post('/logout'),

  getUser: () =>
    api.get('/user'),
}

// Products API
export const productsAPI = {
  getAll: (params?: { page?: number; per_page?: number; category?: number; search?: string }) =>
    api.get('/products', { params }),

  getById: (id: number) =>
    api.get(`/products/${id}`),

  create: (product: FormData) =>
    api.post('/admin/products', product, {
      headers: { 'Content-Type': 'multipart/form-data' }
    }),

  update: (id: number, product: FormData) =>
    api.patch(`/admin/products/${id}`, product, {
      headers: { 'Content-Type': 'multipart/form-data' }
    }),

  delete: (id: number) =>
    api.delete(`/admin/products/${id}`),
}

// Categories API
export const categoriesAPI = {
  getAll: () =>
    api.get('/categories'),

  getById: (id: number) =>
    api.get(`/categories/${id}`),

  create: (category: { name: string; slug: string; parent_id?: number | null }) =>
    api.post('/admin/categories', category),

  update: (id: number, category: { name: string; slug: string; parent_id?: number | null }) =>
    api.patch(`/admin/categories/${id}`, category),

  delete: (id: number) =>
    api.delete(`/admin/categories/${id}`),
}

// Cart API
export const cartAPI = {
  getCart: () =>
    api.get('/cart'),

  addItem: (item: { product_id: number; quantity: number }) =>
    api.post('/cart', item),

  updateItem: (id: number, quantity: number) =>
    api.patch(`/cart/${id}`, { quantity }),

  removeItem: (id: number) =>
    api.delete(`/cart/${id}`),

  clearCart: () =>
    api.delete('/cart'),
}

// Wishlist API
export const wishlistAPI = {
  getWishlist: () =>
    api.get('/wishlist'),

  addItem: (productId: number) =>
    api.post('/wishlist', { product_id: productId }),

  removeItem: (productId: number) =>
    api.delete(`/wishlist/${productId}`),
}

// Orders API
export const ordersAPI = {
  getOrders: () =>
    api.get('/orders'),

  getOrder: (id: number) =>
    api.get(`/orders/${id}`),

  createOrder: (orderData: {
    shipping_address: string
    shipping_phone: string
  }) =>
    api.post('/checkout', orderData),

  cancelOrder: (id: number) =>
    api.patch(`/orders/${id}/cancel`),
}
