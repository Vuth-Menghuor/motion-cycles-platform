<template>
  <div class="bike-summary">
    <div class="bike-summary-image">
      <img :src="bike.image" :alt="bike.title" />
    </div>

    <div class="bike-summary-info">
      <div>
        <div>
          <h1 class="bike-summary-title">{{ bike.title }}</h1>

          <div class="bike-summary-rating">
            <div class="bike-summary-stars">
              <span
                v-for="star in 5"
                :key="star"
                class="bike-summary-star"
                :class="{ filled: star <= Math.floor(bike.rating || 0) }"
              >
                ★
              </span>
            </div>
            <span class="bike-summary-rating-text">
              {{ bike.rating }} out of 5 ({{ formatNumber(bike.reviewCount) }} reviews)
            </span>
          </div>

          <div class="bike-summary-brand-color">
            <span class="bike-summary-brand">{{ bike.brand }}</span>
            <span class="bike-summary-separator"> | </span>
            <span class="bike-summary-color">Color: {{ bike.color }}</span>
          </div>
        </div>

        <div>
          <div class="bike-summary-price-details">
            <div class="bike-summary-current-price">
              ${{ formatNumber(getDiscountedPrice(bike)) }}
            </div>
            <div
              v-if="
                bike.discount &&
                Array.isArray(bike.discount) &&
                bike.discount.length > 0 &&
                bike.discount[0].value
              "
              class="bike-summary-original-price"
            >
              ${{ formatNumber(bike.price) }}
            </div>
          </div>
          <div
            v-if="
              bike.discount &&
              Array.isArray(bike.discount) &&
              bike.discount.length > 0 &&
              bike.discount[0].value
            "
            class="bike-summary-savings"
          >
            You save: ${{ formatNumber(getSavings(bike)) }}
          </div>
        </div>
      </div>

      <div class="bike-summary-actions">
        <button class="bike-summary-add-to-cart" @click="$emit('addToCart', bike)">
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
import { Icon } from '@iconify/vue'

// Define emits for parent component communication
defineEmits(['addToCart'])

// Define props for the component
defineProps({
  bike: {
    type: Object,
    required: true,
  },
})

// Function to format numbers with commas
const formatNumber = (number) => number.toLocaleString()

// Function to calculate the discounted price
const getDiscountedPrice = (bike) => {
  if (
    !bike.discount ||
    !Array.isArray(bike.discount) ||
    bike.discount.length === 0 ||
    !bike.discount[0].value
  ) {
    return bike.price // No discount, return original price
  }
  const discount = bike.discount[0]
  if (discount.type === 'percent') {
    return bike.price - (bike.price * discount.value) / 100 // Percentage discount
  } else {
    return bike.price - discount.value // Fixed amount discount
  }
}

// Function to calculate savings amount
const getSavings = (bike) => {
  if (!bike.price) return 0
  const originalPrice = bike.price
  const discountedPrice = getDiscountedPrice(bike)
  return Math.max(0, originalPrice - discountedPrice)
}
</script>

<style scoped>
.bike-summary {
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  width: 50%;
  overflow: hidden;
  background: #fff;
  margin-bottom: 38px;
}

.bike-summary-image img {
  width: 100%;
  height: auto;
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
  object-fit: contain;
}

.bike-summary-info {
  padding: 12px 16px 20px 16px;
}

.bike-summary-title {
  font-size: 20px;
  font-weight: 600;
  margin: 4px 0;
}

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

.bike-summary-brand-color {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: #64748b;
  font-weight: 400;
  margin: 8px 0;
}

.bike-summary-brand {
  font-weight: 500;
}

.bike-summary-separator {
  color: #64748b;
}

.bike-summary-color {
  font-weight: 500;
}

.bike-summary-price-details {
  display: flex;
  align-items: baseline;
  gap: 10px;
}

.bike-summary-current-price {
  font-size: 20px;
  font-weight: bold;
  color: #f53f3f;
}

.bike-summary-original-price {
  font-size: 14px;
  color: #aaa;
  text-decoration: line-through;
}

.bike-summary-savings {
  font-size: 14px;
  color: #16a34a;
}

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
}
</style>
