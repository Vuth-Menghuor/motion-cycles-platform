<template>
  <div class="rating-summary">
    <h2 class="rating-score">{{ averageRating }}</h2>
    <div class="rating-content">
      <div class="total-section">
        <span class="total rating">{{ totalRating }}</span> Ratings &
      </div>
      <div class="total-section">
        <span class="total review">{{ totalReviews }}</span> Reviews
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  averageRating: {
    type: Number,
    required: true,
  },
  totalRating: {
    type: Number,
    required: true,
  },
  totalReviews: {
    type: Number,
    required: true,
  },
})
</script>

<style scoped>
.rating-summary {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 24px 24px 24px 0;
  border-right: 1px solid #e5e7eb;
  font-family: 'Poppins', sans-serif;
}
.rating-score {
  font-size: 48px;
  font-weight: 600;
  color: #111827;
  margin: 0;
}
.rating-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  margin-top: 8px;
}
.total-section {
  font-size: 14px;
  color: #6b7280;
}
</style>
