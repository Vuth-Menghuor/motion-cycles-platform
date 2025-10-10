<template>
  <div class="review-card">
    <div class="review-card-header">
      <h3 class="review-card-user">
        {{ review.user }}
      </h3>
      <span class="review-card-date">{{ review.date || 'Just now' }} </span>
    </div>

    <div class="review-card-stars">
      <span
        v-for="i in 5"
        :key="i"
        class="review-card-star"
        :class="{ active: i <= review.rating }"
      >
        <Icon icon="ic:round-star" />
      </span>
    </div>

    <p class="review-card-content">
      {{ review.comment || 'No comment provided.' }}
    </p>
  </div>
</template>

<script setup>
import { Icon } from '@iconify/vue'

defineProps({
  review: {
    type: Object,
    required: true,
  },
})
</script>

<style scoped>
.review-card {
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 16px;
  font-family: 'Poppins', sans-serif;
  background-color: #ffffff;
}

.review-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
}

.review-card-user {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #111827;
}

.review-card-date {
  font-size: 12px;
  color: #6b7280;
}

.review-card-stars {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
}

.review-card-star {
  font-size: 16px;
  color: #d1d5db;
}

.review-card-star.active {
  color: #fbbf24;
}

.review-card-content {
  font-size: 14px;
  line-height: 1.5;
  color: #374151;
  margin: 0;
}
</style>
