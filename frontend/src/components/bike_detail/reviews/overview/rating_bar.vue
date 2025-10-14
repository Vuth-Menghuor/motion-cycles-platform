<template>
  <div class="rating-overview">
    <div class="rating-content">
      <div>
        <Rating_overview
          :averageRating="averageRating"
          :totalRating="totalRatings"
          :totalReviews="totalReviews"
        />
      </div>
      <div class="rating-distribution">
        <Rating_stars
          v-for="(count, rating) in ratingDistribution"
          :key="rating"
          :rating="Number(rating)"
          :count="count"
          :total="totalRatings"
        />
      </div>

      <div class="overall-badge">
        <div class="circular-progress">
          <svg viewBox="0 0 36 36" class="circular-chart">
            <path
              class="circle-bg"
              d="M18 2.0845
                a 15.9155 15.9155 0 0 1 0 31.831
                a 15.9155 15.9155 0 0 1 0 -31.831"
            />
            <path
              class="circle"
              :stroke-dasharray="`${progressPercentage}, 100`"
              d="M18 2.0845
                a 15.9155 15.9155 0 0 1 0 31.831
                a 15.9155 15.9155 0 0 1 0 -31.831"
            />
          </svg>
          <div class="progress-text">
            <div class="badge-score">{{ averageRating }}</div>
          </div>
        </div>
        <div class="badge-label">Overall</div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import Rating_overview from './rating_overview.vue'
import Rating_stars from './rating_stars.vue'

// Define props for the component
const props = defineProps({
  reviews: {
    type: Array,
    default: () => [],
  },
})

// Total number of reviews
const totalReviews = computed(() => props.reviews.length)

// Count of each rating (1-5 stars)
const ratings = computed(() => {
  const result = { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 }
  props.reviews.forEach((review) => (result[review.rating] = (result[review.rating] || 0) + 1))
  return result
})

// Total number of ratings (same as totalReviews in this case)
const totalRatings = computed(() =>
  Object.values(ratings.value).reduce((sum, count) => sum + count, 0),
)

// Average rating calculated from all reviews
const averageRating = computed(() => {
  if (totalRatings.value === 0) return 0
  let totalScore = 0
  for (const [rating, count] of Object.entries(ratings.value)) {
    totalScore += Number(rating) * count
  }
  const average = totalScore / totalRatings.value
  return parseFloat(average.toFixed(1))
})

// Rating distribution sorted by rating descending
const ratingDistribution = computed(() => {
  const sortedEntries = Object.entries(ratings.value).sort((a, b) => b[0] - a[0]) // Sort by rating descending
  return Object.fromEntries(sortedEntries)
})

// Progress percentage for the circular chart (0-100)
const progressPercentage = computed(() => (averageRating.value / 5) * 100)
</script>

<style scoped>
.rating-overview {
  display: flex;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 34px;
  margin-bottom: 14px;
  background: white;
  border-radius: 8px;
}

.rating-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-right: 50px;
  width: 100%;
}

.rating-distribution {
  display: flex;
  flex-direction: column;
  padding: 24px 120px 24px 24px;
  gap: 10px;
  flex: 1;
}

.overall-badge {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.circular-progress {
  position: relative;
  width: 100px;
  height: 100px;
}

.circular-chart {
  display: block;
  margin: 0 auto;
  width: 100%;
  height: 100%;
}

.circle-bg {
  fill: none;
  stroke: #e5e7eb;
  stroke-width: 3;
}

.circle {
  fill: none;
  stroke: #22c55e;
  stroke-width: 3;
  stroke-linecap: round;
  animation: progress 1s ease-in-out forwards;
}

@keyframes progress {
  0% {
    stroke-dasharray: 0 100;
  }
}

.progress-text {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  text-align: center;
}

.badge-score {
  font-size: 24px;
  font-weight: bold;
  color: #111827;
  line-height: 1;
}

.badge-label {
  font-size: 14px;
  font-weight: 500;
  color: #6b7280;
  text-align: center;
}

@media (max-width: 640px) {
  .rating-overview {
    padding: 24px;
  }

  .rating-content {
    flex-direction: column;
    gap: 32px;
  }

  .circular-progress {
    width: 60px;
    height: 60px;
  }

  .badge-score {
    font-size: 20px;
  }
}
</style>
