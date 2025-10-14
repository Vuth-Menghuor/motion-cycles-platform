<template>
  <div class="review-card">
    <div class="review-card-header">
      <div class="review-card-user-info">
        <div class="review-card-avatar">
          {{ getInitials(review.user.name) }}
        </div>
        <h3 class="review-card-user">
          {{ review.user.name }}
        </h3>
      </div>
      <span class="review-card-date">{{ formatDate(review.created_at) }} </span>
    </div>
    <div class="review-card-stars">
      <span
        v-for="i in 5"
        :key="i"
        class="review-card-star"
        :class="{ active: i <= (review.rating || 0) }"
      >
        ★
      </span>
    </div>

    <p class="review-card-content">
      {{ review.comment || 'No comment provided.' }}
    </p>
  </div>
</template>

<script setup>
defineProps({
  review: {
    type: Object,
    required: true,
  },
})

// Function to get initials from name
const getInitials = (name) => {
  if (!name) return '?'
  return name
    .split(' ')
    .map((n) => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
}

// Function to format date
const formatDate = (dateString) => {
  if (!dateString) return 'Just now'
  const date = new Date(dateString)
  return date.toLocaleDateString()
}
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

.review-card-user-info {
  display: flex;
  align-items: center;
  gap: 8px;
}

.review-card-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background-color: #3b82f6;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 600;
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
