import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { reviewsApi } from '@/services/api.js'

/**
 * Reviews Store - Manages product reviews and ratings
 *
 * Features:
 * - Submit reviews for products
 * - Fetch reviews for products
 * - Calculate average ratings and review counts
 * - Get rating distributions
 * - Update and delete reviews
 *
 * Usage:
 * ```js
 * import { useReviewsStore } from '@/stores/reviews'
 *
 * const reviewsStore = useReviewsStore()
 *
 * // Submit a review
 * await reviewsStore.submitReview(productId, {
 *   user: 'John Doe',
 *   rating: 5,
 *   comment: 'Great product!',
 *   email: 'john@example.com'
 * })
 *
 * // Get reviews for a product
 * const reviews = reviewsStore.getProductReviews(productId)
 * const averageRating = reviewsStore.getProductAverageRating(productId)
 * ```
 */
export const useReviewsStore = defineStore('reviews', () => {
  // State: Object to hold reviews organized by product ID
  const reviews = ref({})

  // State: Loading states for different operations
  const loading = ref({
    submitting: false,
    fetching: false,
  })

  // State: Error messages
  const errors = ref({
    submit: null,
    fetch: null,
  })

  // Getters: Computed properties based on state

  // Get reviews for a specific product
  const getProductReviews = computed(() => {
    return (productId) => reviews.value[productId] || []
  })

  // Get average rating for a specific product
  const getProductAverageRating = computed(() => {
    return (productId) => {
      const productReviews = getProductReviews.value(productId)
      if (productReviews.length === 0) return 0

      const totalRating = productReviews.reduce((sum, review) => sum + review.rating, 0)
      return Math.round((totalRating / productReviews.length) * 10) / 10 // Round to 1 decimal place
    }
  })

  // Get review count for a specific product
  const getProductReviewCount = computed(() => {
    return (productId) => getProductReviews.value(productId).length
  })

  // Get rating distribution for a specific product (1-5 stars)
  const getProductRatingDistribution = computed(() => {
    return (productId) => {
      const productReviews = getProductReviews.value(productId)
      const distribution = { 1: 0, 2: 0, 3: 0, 4: 0, 5: 0 }

      productReviews.forEach((review) => {
        if (review.rating >= 1 && review.rating <= 5) {
          distribution[review.rating]++
        }
      })

      return distribution
    }
  })

  // Get all reviews across all products
  const getAllReviews = computed(() => {
    const allReviews = []
    Object.values(reviews.value).forEach((productReviews) => {
      allReviews.push(...productReviews)
    })
    return allReviews
  })

  // Actions: Methods to modify the reviews state

  // Submit a new review for a product
  async function submitReview(productId, reviewData) {
    loading.value.submitting = true
    errors.value.submit = null

    try {
      // Validate review data
      if (!reviewData.rating || reviewData.rating < 1 || reviewData.rating > 5) {
        throw new Error('Rating must be between 1 and 5 stars')
      }

      if (!reviewData.comment || !reviewData.comment.trim()) {
        throw new Error('Review comment is required')
      }

      // Check if user is authenticated (has token)
      const token = localStorage.getItem('token')
      let newReview

      if (token) {
        // Authenticated user - submit to API
        const response = await reviewsApi.submitReview(productId, {
          rating: reviewData.rating,
          comment: reviewData.comment.trim(),
        })
        newReview = response.data
      } else {
        // Guest user - submit to guest endpoint
        const response = await reviewsApi.submitGuestReview(productId, {
          rating: reviewData.rating,
          comment: reviewData.comment.trim(),
          user: reviewData.user,
          email: reviewData.email,
        })
        newReview = response.data
      }

      // Add the review to local state
      if (!reviews.value[productId]) {
        reviews.value[productId] = []
      }
      reviews.value[productId].unshift(newReview)

      return newReview
    } catch (error) {
      errors.value.submit = error.message
      throw error
    } finally {
      loading.value.submitting = false
    }
  }

  // Fetch reviews for a specific product (from API in real implementation)
  async function fetchProductReviews(productId) {
    loading.value.fetching = true
    errors.value.fetch = null

    try {
      const response = await reviewsApi.getProductReviews(productId)
      reviews.value[productId] = response.data || []

      return reviews.value[productId]
    } catch (error) {
      errors.value.fetch = error.message
      throw error
    } finally {
      loading.value.fetching = false
    }
  }

  // Update a review (for editing functionality)
  function updateReview(productId, reviewId, updatedData) {
    const productReviews = reviews.value[productId]
    if (!productReviews) return false

    const reviewIndex = productReviews.findIndex((review) => review.id === reviewId)
    if (reviewIndex === -1) return false

    // Update only allowed fields
    const allowedFields = ['rating', 'comment']
    allowedFields.forEach((field) => {
      if (updatedData[field] !== undefined) {
        productReviews[reviewIndex][field] = updatedData[field]
      }
    })

    productReviews[reviewIndex].updatedAt = new Date().toISOString()

    return true
  }

  // Delete a review
  function deleteReview(productId, reviewId) {
    const productReviews = reviews.value[productId]
    if (!productReviews) return false

    const reviewIndex = productReviews.findIndex((review) => review.id === reviewId)
    if (reviewIndex === -1) return false

    productReviews.splice(reviewIndex, 1)
    return true
  }

  // Clear all reviews (useful for logout or reset)
  function clearAllReviews() {
    reviews.value = {}
    errors.value = { submit: null, fetch: null }
  }

  // Clear errors
  function clearErrors() {
    errors.value = { submit: null, fetch: null }
  }

  // Expose state, getters, and actions for use in components
  return {
    // State
    reviews,
    loading,
    errors,

    // Getters
    getProductReviews,
    getProductAverageRating,
    getProductReviewCount,
    getProductRatingDistribution,
    getAllReviews,

    // Actions
    submitReview,
    fetchProductReviews,
    updateReview,
    deleteReview,
    clearAllReviews,
    clearErrors,
  }
})
