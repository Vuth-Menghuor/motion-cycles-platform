<template>
  <div class="bike-summary">
    <!-- Image -->
    <div class="bike-summary-image">
      <img :src="summaryImage" :alt="imageAlt" />
    </div>

    <!-- Info -->
    <div class="bike-summary-info">
      <div>
        <div>
          <h1 class="bike-summary-title">{{ bike.title }}</h1>
          <div class="bike-summary-meta">
            <span class="bike-summary-brand">{{ bike.subtitle }}</span>
            <span class="bike-summary-separator">|</span>
            <span>Color: {{ bike.color }}</span>
          </div>

          <!-- Rating -->
          <div class="bike-summary-rating">
            <div class="bike-summary-stars">
              <span
                v-for="star in 5"
                :key="star"
                class="bike-summary-star"
                :class="{ filled: star <= Math.floor(bike.rating) }"
              >
                <Icon icon="line-md:star-filled" />
              </span>
            </div>
            <span class="bike-summary-rating-text">
              {{ bike.rating }} out of 5 ({{ formatNumber(bike.reviewCount) }} reviews)
            </span>
          </div>
        </div>

        <!-- Price -->
        <div>
          <div class="bike-summary-price-details">
            <div class="bike-summary-current-price">
              ${{ formatNumber(getDiscountedPrice(bike)) }}
            </div>
            <div v-if="bike.discount" class="bike-summary-original-price">
              ${{ formatNumber(bike.price) }}
            </div>
          </div>
          <div v-if="bike.discount" class="bike-summary-savings">
            You save: ${{ formatNumber(getSavings(bike)) }}
          </div>
        </div>
      </div>

      <!-- Buttons -->
      <div class="bike-summary-actions">
        <button class="bike-summary-add-to-cart">
          <Icon icon="fa7-solid:cart-arrow-down" />
          <span>Add to Cart</span>
        </button>
        <button class="bike-summary-buy-now">
          <Icon icon="mdi:flash" />
          <span>Buy Now</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Icon } from '@iconify/vue'

const props = defineProps({
  bike: {
    type: Object,
    required: true,
  },
})

const selectedImage = ref(props.bike.image)
const summaryImage = computed(() => selectedImage.value)
const imageAlt = computed(() => props.bike.title)

const formatNumber = (number) => {
  return number.toLocaleString()
}

const getDiscountedPrice = (bike) => {
  if (!bike.discount) return bike.price
  if (bike.discount.type === 'percent') {
    return bike.price - (bike.price * bike.discount.value) / 100
  } else {
    return bike.price - bike.discount.value
  }
}

const getSavings = (bike) => {
  return bike.price - getDiscountedPrice(bike)
}
</script>

<style scoped>
.bike-summary-price-details {
  display: flex;
  align-items: baseline;
  gap: 10px;
}
.bike-summary {
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  width: 50%;
  overflow: hidden;
  background: #fff;
  margin-bottom: 38px;
}

/* Image */
.bike-summary-image img {
  width: 100%;
  height: auto;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
  object-fit: contain;
}

/* Info */
.bike-summary-info {
  padding: 16px;
}

.bike-summary-title {
  font-size: 20px;
  font-weight: 600;
}

.bike-summary-meta {
  font-size: 14px;
  color: #666;
}

.bike-summary-separator {
  margin: 0 6px;
  color: #aaa;
}

/* Rating */
.bike-summary-rating {
  display: flex;
  align-items: center;
  margin: 8px 0;
}

.bike-summary-stars {
  display: flex;
  margin-right: 8px;
}

.bike-summary-star {
  color: #d1d5db;
  font-size: 18px;
}

.bike-summary-star.filled {
  color: #facc15;
}

.bike-summary-rating-text {
  font-size: 13px;
  color: #444;
}

.bike-summary-original-price {
  font-size: 14px;
  color: #aaa;
  text-decoration: line-through;
}

.bike-summary-current-price {
  font-size: 20px;
  font-weight: bold;
  color: #f53f3f;
}

.bike-summary-savings {
  font-size: 14px;
  color: #16a34a;
}

/* Buttons */
.bike-summary-actions {
  display: flex;
  gap: 10px;
  margin-top: 16px;
}

.bike-summary-add-to-cart,
.bike-summary-buy-now {
  flex: 1;
  padding: 12px 16px;
  border: none;
  border-radius: 6px;
  color: white;
  font-weight: 500;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
}

.bike-summary-add-to-cart {
  background: #e0f7fa;
  color: #006064;
  border: 1px solid #14c9c9;
}

.bike-summary-buy-now {
  background: #3b82f6;
  color: white;
}
</style>
