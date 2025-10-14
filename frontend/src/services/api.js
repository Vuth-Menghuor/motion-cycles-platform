import axios from 'axios'

const API_BASE_URL = import.meta.env.VITE_API_BASE || 'http://localhost:8100'

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
  // Get all products (public)
  getProducts: () => api.get('/public/products'),

  // Get product by ID (public)
  getProduct: (id) => api.get(`/public/products/${id}`),

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
  getCategories: () => api.get('/public/categories'),

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
  getProductReviews: (productId) => api.get(`/products/${productId}/reviews`),

  // Submit a review for a product (authenticated user)
  submitReview: (productId, reviewData) => api.post(`/products/${productId}/reviews`, reviewData),

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
  getCartCount: () => api.get('/cart-count')
}

export default api
