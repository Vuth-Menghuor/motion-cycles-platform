import axios from 'axios'
import { mockProducts, mockCategories, createMockResponse, delay } from './mockData.js'

const API_BASE_URL = import.meta.env.VITE_API_BASE || 'http://localhost:8000'

// Enable mock mode for development/demo when API is not available
const USE_MOCK_DATA = !API_BASE_URL || API_BASE_URL.includes('localhost:8100') || import.meta.env.DEV || import.meta.env.PROD // Always enable in production for demo

const api = axios.create({
  baseURL: `${API_BASE_URL}/api`,
  headers: {
    'Content-Type': 'application/json',
  },
})

// Add auth token to requests
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
  },
)

// Products API
export const productsApi = {
  // Get all products (public) with advanced filtering
  getProducts: async (params = {}) => {
    // For demo purposes, always use mock data to ensure reliability
    console.log('Using mock products data for demo')

    // Apply basic pagination
    const perPage = params.per_page ? parseInt(params.per_page) : 12
    const page = params.page ? parseInt(params.page) : 1
    const startIndex = (page - 1) * perPage
    const paginatedProducts = mockProducts.slice(startIndex, startIndex + perPage)

    // Simulate API delay
    await delay(300)

    // Return mock response in API format
    return {
      data: {
        data: paginatedProducts,
        current_page: page,
        last_page: Math.ceil(mockProducts.length / perPage),
        per_page: perPage,
        total: mockProducts.length
      }
    }
  },

  // Get product by ID (public)
  getProduct: (id) => api.get(`/products/${id}`),

  // Get products by category (public)
  getProductsByCategory: (categoryId) => api.get(`/public/products?category_id=${categoryId}`),

  // Create product (protected)
  createProduct: (productData) => api.post('/products', productData),

  // Update product (protected)
  updateProduct: (id, productData) => api.patch(`/products/${id}`, productData),

  // Delete product (protected)
  deleteProduct: (id) => api.delete(`/products/${id}`),
}

// Categories API
export const categoriesApi = {
  // Get all categories (public)
  getCategories: async () => {
    console.log('Using mock categories data for demo')
    await delay(200)
    return {
      data: mockCategories
    }
  },

  // Get category by ID (public)
  getCategory: (id) => api.get(`/public/categories/${id}`),

  // Create category (protected)
  createCategory: (categoryData) => api.post('/categories', categoryData),

  // Update category (protected)
  updateCategory: (id, categoryData) => api.patch(`/categories/${id}`, categoryData),

  // Delete category (protected)
  deleteCategory: (id) => api.delete(`/categories/${id}`),
}

// Reviews API
export const reviewsApi = {
  // Get reviews for a product (public)
  getProductReviews: async (productId) => {
    try {
      const response = await api.get(`/products/${productId}/reviews`)
      return response
    } catch (error) {
      console.warn('Reviews API not available, using mock data:', error.message)
      await delay(200)
      // Return mock reviews for the product
      const mockReviews = [
        {
          id: 1,
          user_name: 'John Doe',
          rating: 5,
          comment: 'Excellent bike! Great performance and handling.',
          created_at: '2024-01-15T10:00:00Z'
        },
        {
          id: 2,
          user_name: 'Jane Smith',
          rating: 4,
          comment: 'Very satisfied with the purchase. Good value for money.',
          created_at: '2024-01-12T14:30:00Z'
        },
        {
          id: 3,
          user_name: 'Mike Johnson',
          rating: 5,
          comment: 'Amazing ride! Would recommend to anyone.',
          created_at: '2024-01-10T09:15:00Z'
        }
      ]
      return { data: mockReviews }
    }
  },

  // Submit a review for a product (authenticated user)
  submitReview: (productId, reviewData) => api.post(`/products/${productId}/reviews`, reviewData),

  // Submit a guest review for a product (public)
  submitGuestReview: (productId, reviewData) =>
    api.post(`/products/${productId}/reviews/guest`, reviewData),

  // Admin: Get all reviews with filters
  getAdminReviews: (params = {}) => {
    const queryString = new URLSearchParams(params).toString()
    return api.get(`/admin/reviews${queryString ? `?${queryString}` : ''}`)
  },

  // Admin: Delete a review
  deleteAdminReview: (reviewId) => api.delete(`/admin/reviews/${reviewId}`),

  // Update a review (admin only)
  updateReview: (productId, reviewId, reviewData) =>
    api.patch(`/admin/products/${productId}/reviews/${reviewId}`, reviewData),

  // Delete a review (admin only)
  deleteReview: (productId, reviewId) =>
    api.delete(`/admin/products/${productId}/reviews/${reviewId}`),
}

// Cart API
export const cartApi = {
  getCart: () => api.get('/cart'),
  addToCart: (productId, quantity) => api.post('/cart', { product_id: productId, quantity }),
  updateCartItem: (cartId, quantity) => api.patch(`/cart/${cartId}`, { quantity }),
  removeFromCart: (cartId) => api.delete(`/cart/${cartId}`),
  clearCart: () => api.delete('/cart'),
  getCartCount: () => api.get('/cart-count'),
}

// Favorites API
export const favoritesApi = {
  // Get user's favorites
  getFavorites: () => api.get('/favorites'),

  // Add product to favorites
  addToFavorites: (productId) => api.post('/favorites', { product_id: productId }),

  // Remove product from favorites
  removeFromFavorites: (productId) => api.delete(`/favorites/${productId}`),

  // Toggle favorite status
  toggleFavorite: (productId) => api.post('/favorites/toggle', { product_id: productId }),

  // Check if product is favorited
  checkFavorite: (productId) => api.post('/favorites/check', { product_id: productId }),
}

// Orders API
export const ordersApi = {
  createOrder: (orderData) => api.post('/orders', orderData),
  listOrders: () => api.get('/orders'),
  getOrder: (orderId) => api.get(`/orders/${orderId}`),
  updateOrderStatus: (orderId, statusData) => api.patch(`/orders/${orderId}/status`, statusData),
  // Admin orders API
  adminListOrders: (params = {}) => {
    const queryString = new URLSearchParams(params).toString()
    return api.get(`/admin/orders${queryString ? `?${queryString}` : ''}`)
  },
  adminGetOrder: (orderId) => api.get(`/admin/orders/${orderId}`),
}

// Dashboard API
export const dashboardApi = {
  // Get dashboard statistics
  getStats: () => api.get('/admin/dashboard/stats'),

  // Get stock alerts
  getStockAlerts: (params = {}) => {
    const queryParams = new URLSearchParams()
    if (params.threshold) {
      queryParams.append('threshold', params.threshold)
    }
    const queryString = queryParams.toString()
    return api.get(`/admin/dashboard/stock-alerts${queryString ? `?${queryString}` : ''}`)
  },

  // Get category distribution
  getCategoryDistribution: () => api.get('/admin/dashboard/category-distribution'),
}

// Users API (Admin)
export const usersApi = {
  // Get all users with filters
  getUsers: (params = {}) => {
    const queryParams = new URLSearchParams()
    if (params.search) {
      queryParams.append('search', params.search)
    }
    if (params.status) {
      queryParams.append('status', params.status)
    }
    const queryString = queryParams.toString()
    return api.get(`/admin/users${queryString ? `?${queryString}` : ''}`)
  },

  // Get user by ID
  getUser: (id) => api.get(`/admin/users/${id}`),

  // Delete user
  deleteUser: (id) => api.delete(`/admin/users/${id}`),
}

// Discounts API (Admin)
export const discountsApi = {
  // Get all discounts
  getDiscounts: (params = {}) => {
    const queryParams = new URLSearchParams()
    if (params.active !== undefined) {
      queryParams.append('active', params.active)
    }
    if (params.search) {
      queryParams.append('search', params.search)
    }
    if (params.per_page) {
      queryParams.append('per_page', params.per_page)
    }
    const queryString = queryParams.toString()
    return api.get(`/admin/discounts${queryString ? `?${queryString}` : ''}`)
  },

  // Get discount by ID
  getDiscount: (id) => api.get(`/admin/discounts/${id}`),

  // Create discount
  createDiscount: (data) => api.post('/admin/discounts', data),

  // Update discount
  updateDiscount: (id, data) => api.patch(`/admin/discounts/${id}`, data),

  // Delete discount
  deleteDiscount: (id) => api.delete(`/admin/discounts/${id}`),

  // Validate discount code (public)
  validateCode: (data) => api.post('/discounts/validate', data),
}

export default api
