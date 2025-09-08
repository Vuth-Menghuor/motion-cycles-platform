<template>
  <div class="rating-overview">
    <div class="rating-content">
      <div>
        <!-- summary -->
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

      <!-- Overall Badge -->
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
import Rating_stars from './rating_stars.vue'
import Rating_overview from './rating_overview.vue'

// const props = defineProps({
//   ratings: {
//     type: Object,
//     default: () => ({
//       5: 102,
//       4: 45,
//       3: 40,
//       2: 34,
//       1: 12,
//     }),
//   },
// })

const props = defineProps({
  reviews: {
    type: Array,
    default: () => [
      {
        user: 'v1',
        rating: 1,
      },
      {
        user: 'v2',
        rating: 2,
      },
      {
        user: 'v3',
        rating: 3,
      },
      {
        user: 'v4',
        rating: 5,
      },
      {
        user: 'v5',
        rating: 3,
      },
      {
        user: 'v6',
        rating: 2,
      },
      {
        user: 'v7',
        rating: 1,
      },
      {
        user: 'v8',
        rating: 1,
      },
    ],
  },
})

const totalReviews = computed(() => props.reviews.length)

const ratings = computed(() => {
  const result = { 5: 0, 4: 0, 3: 0, 2: 0, 1: 0 }
  props.reviews.forEach((review) => {
    result[review.rating] = (result[review.rating] || 0) + 1
  })
  return result
})

const totalRatings = computed(() =>
  Object.values(ratings.value).reduce((sum, count) => sum + count, 0),
)

const averageRating = computed(() => {
  const totalScore = Object.entries(ratings.value).reduce(
    (sum, [rating, count]) => sum + Number(rating) * count,
    0,
  )
  return (totalScore / totalRatings.value).toFixed(1)
})

const ratingDistribution = computed(() =>
  Object.fromEntries(Object.entries(ratings.value).sort(([a], [b]) => b - a)),
)

const progressPercentage = computed(() => (averageRating.value / 5) * 100)
</script>

<style scoped>
.rating-overview {
  display: flex;
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
