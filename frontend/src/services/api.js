import axios from 'axios'
import { staticDataService } from './staticData'

const API_BASE_URL = import.meta.env.VITE_API_BASE || 'http://localhost:8000'

const api = axios.create({
  baseURL: `${API_BASE_URL}/api`,
  headers: {
    'Content-Type': 'application/json',
  },
  timeout: 5000, // 5 second timeout
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

// Helper function to handle API fallback to static data
const withStaticFallback = async (
  apiCall,
  staticCall,
  errorMessage = 'API unavailable, using static data',
) => {
  try {
    const result = await apiCall()
    return result
  } catch (error) {
    console.warn(`${errorMessage}:`, error.message)
    return await staticCall()
  }
}

// Products API
export const productsApi = {
  // Get all products (public) with advanced filtering
  getProducts: (params = {}) => {
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

    return withStaticFallback(
      () => api.get(url),
      () => staticDataService.getProducts(params),
      'Backend API unavailable for products, falling back to static data',
    )
  },

  // Get product by ID (public)
  getProduct: (id) =>
    withStaticFallback(
      () => api.get(`/products/${id}`),
      () => staticDataService.getProduct(id),
      'Backend API unavailable for product details, falling back to static data',
    ),

  // Get products by category (public)
  getProductsByCategory: (categoryId) =>
    withStaticFallback(
      () => api.get(`/public/products?category_id=${categoryId}`),
      () => staticDataService.getProducts({ category_id: categoryId }),
      'Backend API unavailable for category products, falling back to static data',
    ),

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
  getCategories: () =>
    withStaticFallback(
      () => api.get('/public/categories'),
      () => staticDataService.getCategories(),
      'Backend API unavailable for categories, falling back to static data',
    ),

  // Get category by ID (public)
  getCategory: (id) =>
    withStaticFallback(
      () => api.get(`/public/categories/${id}`),
      () => staticDataService.getCategory(id),
      'Backend API unavailable for category details, falling back to static data',
    ),

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
  getProductReviews: (productId) =>
    withStaticFallback(
      () => api.get(`/products/${productId}/reviews`),
      () => staticDataService.getProductReviews(productId),
      'Backend API unavailable for product reviews, using static data',
    ),

  // Submit a review for a product (authenticated user)
  submitReview: (productId, reviewData) => api.post(`/products/${productId}/reviews`, reviewData),

  // Submit a guest review for a product (public)
  submitGuestReview: (productId, reviewData) =>
    api.post(`/products/${productId}/reviews/guest`, reviewData),

  // Admin: Get all reviews with filters
  getAdminReviews: (params = {}) =>
    withStaticFallback(
      () => {
        const queryString = new URLSearchParams(params).toString()
        return api.get(`/admin/reviews${queryString ? `?${queryString}` : ''}`)
      },
      () => Promise.resolve({ data: [] }),
      'Backend API unavailable for admin reviews, returning empty list',
    ),

  // Admin: Delete a review
  deleteAdminReview: (reviewId) =>
    withStaticFallback(
      () => api.delete(`/admin/reviews/${reviewId}`),
      () => Promise.reject(new Error('Admin operations not available in static mode')),
      'Backend API unavailable for admin operations',
    ),

  // Update a review (admin only)
  updateReview: (productId, reviewId, reviewData) =>
    withStaticFallback(
      () => api.patch(`/admin/products/${productId}/reviews/${reviewId}`, reviewData),
      () => Promise.reject(new Error('Admin operations not available in static mode')),
      'Backend API unavailable for admin operations',
    ),

  // Delete a review (admin only)
  deleteReview: (productId, reviewId) =>
    withStaticFallback(
      () => api.delete(`/admin/products/${productId}/reviews/${reviewId}`),
      () => Promise.reject(new Error('Admin operations not available in static mode')),
      'Backend API unavailable for admin operations',
    ),
}

// Cart API
export const cartApi = {
  getCart: () =>
    withStaticFallback(
      () => api.get('/cart'),
      () => Promise.resolve({ data: [] }),
      'Backend API unavailable for cart, returning empty cart',
    ),
  addToCart: (productId, quantity) =>
    withStaticFallback(
      () => api.post('/cart', { product_id: productId, quantity }),
      () => Promise.resolve({ data: { message: 'Added to cart (static mode)' } }),
      'Backend API unavailable for cart operations, operation not persisted',
    ),
  updateCartItem: (cartId, quantity) =>
    withStaticFallback(
      () => api.patch(`/cart/${cartId}`, { quantity }),
      () => Promise.resolve({ data: { message: 'Cart updated (static mode)' } }),
      'Backend API unavailable for cart operations, operation not persisted',
    ),
  removeFromCart: (cartId) =>
    withStaticFallback(
      () => api.delete(`/cart/${cartId}`),
      () => Promise.resolve({ data: { message: 'Removed from cart (static mode)' } }),
      'Backend API unavailable for cart operations, operation not persisted',
    ),
  clearCart: () =>
    withStaticFallback(
      () => api.delete('/cart'),
      () => Promise.resolve({ data: { message: 'Cart cleared (static mode)' } }),
      'Backend API unavailable for cart operations, operation not persisted',
    ),
  getCartCount: () =>
    withStaticFallback(
      () => api.get('/cart-count'),
      () => Promise.resolve({ data: { count: 0 } }),
      'Backend API unavailable for cart count, returning 0',
    ),
}

// Favorites API
export const favoritesApi = {
  // Get user's favorites
  getFavorites: () =>
    withStaticFallback(
      () => api.get('/favorites'),
      () => Promise.resolve({ data: [] }),
      'Backend API unavailable for favorites, returning empty list',
    ),

  // Add product to favorites
  addToFavorites: (productId) =>
    withStaticFallback(
      () => api.post('/favorites', { product_id: productId }),
      () => Promise.resolve({ data: { message: 'Added to favorites (static mode)' } }),
      'Backend API unavailable for favorites, operation not persisted',
    ),

  // Remove product from favorites
  removeFromFavorites: (productId) =>
    withStaticFallback(
      () => api.delete(`/favorites/${productId}`),
      () => Promise.resolve({ data: { message: 'Removed from favorites (static mode)' } }),
      'Backend API unavailable for favorites, operation not persisted',
    ),

  // Toggle favorite status
  toggleFavorite: (productId) =>
    withStaticFallback(
      () => api.post('/favorites/toggle', { product_id: productId }),
      () => Promise.resolve({ data: { message: 'Toggled favorite (static mode)' } }),
      'Backend API unavailable for favorites, operation not persisted',
    ),

  // Check if product is favorited
  checkFavorite: (productId) =>
    withStaticFallback(
      () => api.post('/favorites/check', { product_id: productId }),
      () => Promise.resolve({ data: { is_favorited: false } }),
      'Backend API unavailable for favorites check, returning false',
    ),
}

// Orders API
export const ordersApi = {
  createOrder: (orderData) =>
    withStaticFallback(
      () => api.post('/orders', orderData),
      () => Promise.reject(new Error('Order creation not available in static mode')),
      'Backend API unavailable for order creation',
    ),
  listOrders: () =>
    withStaticFallback(
      () => api.get('/orders'),
      () => Promise.resolve({ data: [] }),
      'Backend API unavailable for orders, returning empty list',
    ),
  getOrder: (orderId) =>
    withStaticFallback(
      () => api.get(`/orders/${orderId}`),
      () => Promise.reject(new Error('Order details not available in static mode')),
      'Backend API unavailable for order details',
    ),
  updateOrderStatus: (orderId, statusData) =>
    withStaticFallback(
      () => api.patch(`/orders/${orderId}/status`, statusData),
      () => Promise.reject(new Error('Order status update not available in static mode')),
      'Backend API unavailable for order status updates',
    ),
  // Admin orders API
  adminListOrders: (params = {}) =>
    withStaticFallback(
      () => {
        const queryString = new URLSearchParams(params).toString()
        return api.get(`/admin/orders${queryString ? `?${queryString}` : ''}`)
      },
      () => Promise.resolve({ data: [] }),
      'Backend API unavailable for admin orders, returning empty list',
    ),
  adminGetOrder: (orderId) =>
    withStaticFallback(
      () => api.get(`/admin/orders/${orderId}`),
      () => Promise.reject(new Error('Admin order details not available in static mode')),
      'Backend API unavailable for admin order details',
    ),
}

// Dashboard API
export const dashboardApi = {
  // Get dashboard statistics
  getStats: () =>
    withStaticFallback(
      () => api.get('/admin/dashboard/stats'),
      () =>
        Promise.resolve({
          data: { total_products: 6, total_orders: 0, total_users: 0, total_revenue: 0 },
        }),
      'Backend API unavailable for dashboard stats, returning static stats',
    ),

  // Get stock alerts
  getStockAlerts: (params = {}) =>
    withStaticFallback(
      () => {
        const queryParams = new URLSearchParams()
        if (params.threshold) {
          queryParams.append('threshold', params.threshold)
        }
        const queryString = queryParams.toString()
        return api.get(`/admin/dashboard/stock-alerts${queryString ? `?${queryString}` : ''}`)
      },
      () => Promise.resolve({ data: [] }),
      'Backend API unavailable for stock alerts, returning empty list',
    ),

  // Get category distribution
  getCategoryDistribution: () =>
    withStaticFallback(
      () => api.get('/admin/dashboard/category-distribution'),
      () => Promise.resolve({ data: [] }),
      'Backend API unavailable for category distribution, returning empty data',
    ),
}

// Users API (Admin)
export const usersApi = {
  // Get all users with filters
  getUsers: (params = {}) =>
    withStaticFallback(
      () => {
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
      () => Promise.resolve({ data: [] }),
      'Backend API unavailable for users, returning empty list',
    ),

  // Get user by ID
  getUser: (id) =>
    withStaticFallback(
      () => api.get(`/admin/users/${id}`),
      () => Promise.reject(new Error('User details not available in static mode')),
      'Backend API unavailable for user details',
    ),

  // Delete user
  deleteUser: (id) =>
    withStaticFallback(
      () => api.delete(`/admin/users/${id}`),
      () => Promise.reject(new Error('User operations not available in static mode')),
      'Backend API unavailable for user operations',
    ),
}

// Auth API
export const authApi = {
  // Login
  login: (credentials) =>
    withStaticFallback(
      () => api.post('/auth/login', credentials),
      () => Promise.reject(new Error('Authentication not available in static mode')),
      'Backend API unavailable for authentication',
    ),

  // Register
  register: (userData) =>
    withStaticFallback(
      () => api.post('/auth/register', userData),
      () => Promise.reject(new Error('Registration not available in static mode')),
      'Backend API unavailable for registration',
    ),

  // Logout
  logout: () =>
    withStaticFallback(
      () => api.post('/auth/logout'),
      () => Promise.resolve({ data: { message: 'Logged out (static mode)' } }),
      'Backend API unavailable for logout',
    ),

  // Get current user
  getCurrentUser: () =>
    withStaticFallback(
      () => api.get('/auth/user'),
      () => Promise.reject(new Error('User data not available in static mode')),
      'Backend API unavailable for user data',
    ),
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
