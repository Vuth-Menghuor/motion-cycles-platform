<template>
  <div class="reviews-page">
    <h2 class="review-title">Rating & Reviews</h2>
    <Rating_bar :reviews="productReviews" />

    <h2 class="review-title">Customer Feedback</h2>
    <Reviews_list :reviews="productReviews" class="review-list" />

    <h2 class="review-title">Submit Your Review</h2>
    <Review_form @submit-review="handleNewReview" />
  </div>
</template>

<script setup>
import { computed, onMounted, watch } from 'vue'
import { useReviewsStore } from '@/stores/reviews'
import Rating_bar from './overview/rating_bar.vue'
import Reviews_list from './list/reviews_list.vue'
import Review_form from './form/review_form.vue'

// Define props to receive product ID
const props = defineProps({
  productId: {
    type: [String, Number],
    required: true,
  },
})

// Define emits for parent component communication
const emit = defineEmits(['rating-updated'])

// Initialize Pinia store
const reviewsStore = useReviewsStore()

// Get reviews for current product
const productReviews = computed(() => reviewsStore.getProductReviews(props.productId))

// Watch for changes in reviews and emit rating updates
watch(
  () => productReviews.value,
  (newReviews) => {
    if (newReviews.length > 0) {
      const totalRating = newReviews.reduce((sum, review) => sum + review.rating, 0)
      const averageRating = totalRating / newReviews.length
      emit('rating-updated', {
        rating: Math.round(averageRating * 10) / 10,
        reviewCount: newReviews.length,
      })
    } else {
      emit('rating-updated', { rating: 0, reviewCount: 0 })
    }
  },
  { immediate: true, deep: true },
)

// Handle new review submission
const handleNewReview = async (reviewData) => {
  try {
    await reviewsStore.submitReview(props.productId, reviewData)
    // The watch above will automatically emit the rating update
  } catch (error) {
    console.error('Failed to submit review:', error)
    // You could emit an error event here if needed
    alert('Failed to submit review. Please make sure you are logged in.')
  }
}

// Fetch reviews when component mounts
onMounted(async () => {
  try {
    await reviewsStore.fetchProductReviews(props.productId)
  } catch (error) {
    console.error('Failed to fetch reviews:', error)
  }
})
</script>

<style scoped>
.reviews-page {
  max-width: 100%;
}

.review-list {
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 34px;
  margin-bottom: 14px;
}

.review-title {
  font-family: 'Poppins', sans-serif;
  font-size: 24px;
  font-weight: 600;
  color: #111827;
  margin: 24px 0 8px 0;
}
</style>
