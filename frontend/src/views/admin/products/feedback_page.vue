<template>
  <div class="feedback-page">
    <nav class="breadcrumb">
      <span class="breadcrumb-item active">Feedback Management</span>
    </nav>

    <FeedbackFilters
      v-model:customerSearch="customerSearch"
      v-model:ratingFilter="ratingFilter"
      v-model:categoryFilter="categoryFilter"
      v-model:brandFilter="brandFilter"
      v-model:dateFilter="dateFilter"
      @clearFilters="clearFilters"
    />

    <div class="main-content" :class="{ 'two-column': selectedProduct }">
      <div v-if="error" class="error-message">
        {{ error }}
      </div>

      <div v-if="loading" class="loading-message">Loading reviews...</div>

      <div v-else class="feedback-content-group">
        <div v-if="selectedProduct" class="product-section">
          <FeedbackProductCard :product="selectedProduct" />
        </div>

        <div class="feedback-section">
          <FeedbackList
            :feedbacks="paginatedFeedback"
            @view="viewFeedback"
            @update-review="updateFeedback"
            @delete="deleteFeedback"
            @toggle-select="toggleSelectFeedback"
          />
        </div>
      </div>

      <BulkActions
        :selectedCount="selectedFeedback.length"
        @mark-reviewed="bulkMarkReviewed"
        @delete-selected="bulkDelete"
      />

      <FeedbackPagination
        :currentPage="currentPage"
        :totalItems="totalItems"
        :itemsPerPage="ITEMS_PER_PAGE"
        @page-change="goToPage"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { reviewsApi } from '@/services/api'
import FeedbackFilters from '@/components/admin/feedback/FeedbackFilters.vue'
import FeedbackList from '@/components/admin/feedback/FeedbackList.vue'
import BulkActions from '@/components/admin/feedback/BulkActions.vue'
import FeedbackPagination from '@/components/admin/feedback/FeedbackPagination.vue'
import FeedbackProductCard from '@/components/admin/feedback/FeedbackProductCard.vue'

const ITEMS_PER_PAGE = 10
const currentPage = ref(1)
const router = useRouter()

// Filter reactive data
const ratingFilter = ref('')
const categoryFilter = ref('Mountain Bike')
const brandFilter = ref('Trek')
const dateFilter = ref('')
const customerSearch = ref('')

// API data
const reviews = ref([])
const loading = ref(false)
const error = ref(null)
const totalItems = ref(0)
const lastPage = ref(1)

// Fetch reviews from API
const fetchReviews = async (page = 1) => {
  loading.value = true
  error.value = null

  try {
    const params = {
      page,
      ...(ratingFilter.value && { rating: ratingFilter.value }),
      ...(categoryFilter.value && { category: categoryFilter.value }),
      ...(brandFilter.value && { brand: brandFilter.value }),
      ...(dateFilter.value && { date: dateFilter.value }),
      ...(customerSearch.value && { customer: customerSearch.value }),
    }

    const response = await reviewsApi.getAdminReviews(params)
    reviews.value = response.data.data
    totalItems.value = response.data.total
    lastPage.value = response.data.last_page
    currentPage.value = response.data.current_page
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to load reviews'
    console.error('Error fetching reviews:', err)
  } finally {
    loading.value = false
  }
}

// Computed properties
const paginatedFeedback = computed(() => {
  // Since API handles pagination, return the current page data
  return reviews.value.map((review) => ({
    id: review.id,
    productName: review.product?.name || 'Unknown Product',
    productImage: review.product?.image || '/images/default-product.jpg',
    productDescription: review.product?.description || 'No description available',
    productPrice: review.product?.pricing || 0,
    productRating: review.product?.rating || 0,
    category: review.product?.category?.name || 'Unknown Category',
    brand: review.product?.brand || 'Unknown Brand',
    customerName: review.customer_name || 'Anonymous',
    customerAvatar: review.user?.avatar || '/images/default-avatar.png',
    rating: review.rating,
    comment: review.comment,
    date: new Date(review.created_at).toISOString().split('T')[0],
    selected: false,
  }))
})

const selectedFeedback = computed(() => paginatedFeedback.value.filter((item) => item.selected))

const selectedProduct = computed(() => {
  if (reviews.value.length === 0) {
    return null
  }

  // Find the most common product in current reviews
  const productCounts = {}
  reviews.value.forEach((review) => {
    const productName = review.product?.name || 'Unknown Product'
    productCounts[productName] = (productCounts[productName] || 0) + 1
  })

  const mostCommonProductName = Object.keys(productCounts).reduce((a, b) =>
    productCounts[a] > productCounts[b] ? a : b,
  )

  const mostCommonReview = reviews.value.find((r) => r.product?.name === mostCommonProductName)

  if (mostCommonReview) {
    return {
      name: mostCommonReview.product.name,
      description: mostCommonReview.product.description || 'No description available',
      price: mostCommonReview.product.pricing || 0,
      rating: mostCommonReview.product.rating || 0,
      image: mostCommonReview.product.image || '/images/road_1.png',
      category: mostCommonReview.product.category?.name || 'Unknown',
      brand: mostCommonReview.product.brand || 'Unknown',
      color: mostCommonReview.product.color || null,
    }
  }

  return null
})

// Feedback methods
const toggleSelectFeedback = (feedbackId) => {
  const item = paginatedFeedback.value.find((f) => f.id === feedbackId)
  if (item) {
    item.selected = !item.selected
  }
}

const goToPage = (page) => {
  if (page >= 1 && page <= lastPage.value) {
    fetchReviews(page)
  }
}

const viewFeedback = (feedbackId) => {
  router.push(`/admin/feedback/view/${feedbackId}`)
}

const updateFeedback = async (reviewData) => {
  try {
    // Find the product ID for this review
    const review = reviews.value.find((r) => r.id === reviewData.id)
    if (!review || !review.product) {
      throw new Error('Review or product not found')
    }

    await reviewsApi.updateReview(review.product.id, reviewData.id, {
      rating: reviewData.rating,
      comment: reviewData.comment,
    })

    // Refresh current page to show updated data
    fetchReviews(currentPage.value)
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to update review'
    console.error('Error updating review:', err)
    // Re-throw to let the component handle the error
    throw err
  }
}

const deleteFeedback = async (feedbackId) => {
  if (confirm(`Delete review ${feedbackId}?`)) {
    try {
      await reviewsApi.deleteAdminReview(feedbackId)
      // Refresh current page
      fetchReviews(currentPage.value)
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete review'
      console.error('Error deleting review:', err)
    }
  }
}

const bulkMarkReviewed = () => {
  // For now, just update local state - in a real app, you'd call an API
  selectedFeedback.value.forEach((item) => {
    item.status = 'Reviewed'
  })
}

const bulkDelete = async () => {
  const count = selectedFeedback.value.length
  if (confirm(`Delete ${count} selected reviews?`)) {
    try {
      // Delete each selected review
      const deletePromises = selectedFeedback.value.map((review) =>
        reviewsApi.deleteAdminReview(review.id),
      )
      await Promise.all(deletePromises)
      // Refresh current page
      fetchReviews(currentPage.value)
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to delete reviews'
      console.error('Error deleting reviews:', err)
    }
  }
}

const clearFilters = () => {
  ratingFilter.value = ''
  categoryFilter.value = 'Mountain Bike'
  brandFilter.value = 'Trek'
  dateFilter.value = ''
  customerSearch.value = ''
  fetchReviews(1)
}

// Watchers
watch([ratingFilter, categoryFilter, brandFilter, dateFilter, customerSearch], () => {
  fetchReviews(1)
})

// Lifecycle
onMounted(() => {
  fetchReviews()
})
</script>

<style scoped>
.feedback-page {
  font-family: 'Poppins', sans-serif;
}

.main-content {
  display: flex;
  flex-direction: column;
  height: calc(90vh - 200px);
}

.feedback-content-group {
  display: flex;
  gap: 24px;
  flex: 1;
  overflow-y: scroll;
}

.feedback-section {
  flex: 1;
}

.main-content.two-column .product-section {
  flex: 0 0 400px;
  position: sticky;
  top: 0;
  height: fit-content;
}

.main-content.two-column .feedback-section {
  flex: 1;
}

.breadcrumb {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
  padding: 12px 16px;
  border-radius: 5px;
  font-size: 13px;
  color: #666;
  background-color: white;
  border: 1px solid #e9ecef;
  font-family: 'Poppins', sans-serif;
}

.breadcrumb-item {
  color: grey;
  text-decoration: none;
  cursor: pointer;
  transition: color 0.3s;
}

.breadcrumb-item.active {
  color: #ff9934;
  font-weight: 400;
  cursor: default;
}

.error-message {
  color: #dc3545;
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
  padding: 12px;
  border-radius: 5px;
  margin-bottom: 20px;
  text-align: center;
}

.loading-message {
  text-align: center;
  padding: 40px;
  color: #666;
  font-size: 16px;
}

@media (max-width: 1024px) {
  .feedback-content-group {
    flex-direction: column;
  }

  .main-content.two-column .product-section {
    flex: none;
    margin-bottom: 24px;
  }
}
</style>
