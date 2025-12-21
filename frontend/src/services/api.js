import axios from 'axios'
import {
  getFilteredProducts,
  mockProducts,
  mockCategories,
  getMockReviewsForProduct,
} from './mockData.js'

const API_BASE_URL = import.meta.env.VITE_API_BASE || 'http://localhost:8000'

// Check if we should use mock data (when API is not available)
const shouldUseMockData = () => {
  // Use mock data by default for development/demo
  // Only use real API if explicitly set to a non-localhost URL
  const useRealApi =
    API_BASE_URL &&
    !API_BASE_URL.includes('localhost') &&
    !API_BASE_URL.includes('127.0.0.1') &&
    API_BASE_URL !== 'http://localhost:8000' &&
    API_BASE_URL !== 'http://localhost:8100'

  console.log('API_BASE_URL:', API_BASE_URL)
  console.log('shouldUseMockData (inverted logic):', !useRealApi)

  return !useRealApi // Use mock data unless we have a real API URL
}

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
    // Use mock data if API is not available
    if (shouldUseMockData()) {
      console.log('Using mock data for products')
      const result = { data: getFilteredProducts(params) }
      console.log('Mock data result:', result)
      return result
    }

    try {
      const queryParams = new URLSearchParams()

      // Search parameter
      if (params.search) {
        queryParams.append('search', params.search)
      }

      // Category filter
      if (params.category_id) {
        queryParams.append('category_id', params.category_id)
      }

      // Brand filter
      if (params.brand) {
        queryParams.append('brand', params.brand)
      }

      // Color filter
      if (params.color) {
        queryParams.append('color', params.color)
      }

      // Price range filters
      if (params.min_price !== undefined && params.min_price !== null) {
        queryParams.append('min_price', params.min_price)
      }
      if (params.max_price !== undefined && params.max_price !== null) {
        queryParams.append('max_price', params.max_price)
      }

      // Rating filter
      if (params.min_rating !== undefined && params.min_rating !== null) {
        queryParams.append('min_rating', params.min_rating)
      }

      // Discount filter
      if (params.has_discount !== undefined) {
        queryParams.append('has_discount', params.has_discount)
      }

      // Sorting
      if (params.sort_by) {
        queryParams.append('sort_by', params.sort_by)
      }
      if (params.sort_order) {
        queryParams.append('sort_order', params.sort_order)
      }

      // Pagination
      if (params.per_page) {
        queryParams.append('per_page', params.per_page)
      }
      if (params.page) {
        queryParams.append('page', params.page)
      }

      const queryString = queryParams.toString()
      const url = queryString ? `/public/products?${queryString}` : '/public/products'

      const response = await api.get(url)
      return response
    } catch (error) {
      console.warn('API call failed, using mock data:', error.message)
      // Return mock data as fallback
      return {
        data: getFilteredProducts(params),
      }
    }
  },

  // Get product by ID (public)
  getProduct: async (id) => {
    // Use mock data if API is not available
    if (shouldUseMockData()) {
      console.log('Using mock data for single product')
      const product = mockProducts.find((p) => p.id === parseInt(id))
      if (product) {
        return { data: product }
      } else {
        throw new Error('Product not found')
      }
    }

    return api.get(`/products/${id}`)
  },

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
    // Use mock data if API is not available
    if (shouldUseMockData()) {
      console.log('Using mock data for categories')
      return {
        data: mockCategories,
      }
    }

    try {
      const response = await api.get('/public/categories')
      return response
    } catch (error) {
      console.warn('Categories API call failed, using mock data:', error.message)
      // Return mock data as fallback
      return {
        data: mockCategories,
      }
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
    // Use mock data if API is not available
    if (shouldUseMockData()) {
      console.log('Using mock data for reviews')
      return {
        data: getMockReviewsForProduct(productId),
      }
    }

    try {
      const response = await api.get(`/products/${productId}/reviews`)
      return response
    } catch (error) {
      console.warn('Reviews API call failed, using mock data:', error.message)
      // Return mock data as fallback
      return {
        data: getMockReviewsForProduct(productId),
      }
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
