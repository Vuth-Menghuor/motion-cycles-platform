<template>
  <div class="reviews-page">
    <h2 class="review-title">Rating & Reviews</h2>
    <Rating_bar :reviews="reviews" />

    <h2 class="review-title">Customer Feedback</h2>
    <Reviews_list :reviews="reviews" class="review-list" />

    <h2 class="review-title">Submit Your Review</h2>
    <Review_form @submit-review="handleNewReview" />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import Review_form from './form/review_form.vue'
import Reviews_list from './list/reviews_list.vue'
import Rating_bar from './overview/rating_bar.vue'

// Make reviews reactive
const reviews = ref([
  {
    user: 'v1',
    rating: 2,
    comment: 'This is a great product!',
    date: '2023-10-01',
  },
  {
    user: 'v2',
    rating: 2,
    comment: 'Not bad, could be better.',
    date: '2023-10-02',
  },
  {
    user: 'v3',
    rating: 3,
    comment: 'Average experience.',
    date: '2023-10-03',
  },
  {
    user: 'v4',
    rating: 5,
    comment: 'Exceeded my expectations!',
    date: '2023-10-04',
  },
  {
    user: 'v5',
    rating: 3,
    comment: "It's okay for the price.",
    date: '2023-10-05',
  },
  {
    user: 'v6',
    rating: 2,
    comment: 'Could be improved in some areas.',
    date: '2023-10-06',
  },
  {
    user: 'v7',
    rating: 4,
    comment: 'Good value for money.',
    date: '2023-10-07',
  },
  {
    user: 'v8',
    rating: 5,
    comment: 'Absolutely love it!',
    date: '2023-10-08',
  },
])

// Handle new review submission
const handleNewReview = (reviewData) => {
  // Add new review to the beginning of the array (most recent first)
  reviews.value.unshift(reviewData)

  console.log('New review added:', reviewData)
  console.log('Total reviews:', reviews.value.length)
}
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
