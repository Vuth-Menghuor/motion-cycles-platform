<template>
  <div class="info-section">
    <div class="info-content">
      <div class="basic-info">
        <div>
          <h1 class="bike-title">{{ bike.title }}</h1>

          <div class="brand-color-section">
            <span class="brand-name">{{ bike.brand }}</span>
            <span class="separator"> | </span>
            <span class="color-info">Color: {{ bike.color }}</span>
          </div>

          <div class="rating-section">
            <div class="stars">
              <span
                v-for="star in 5"
                :key="star"
                class="star"
                :class="{ filled: star <= Math.floor(bike.rating || 0) }"
              >
                ★
              </span>
            </div>
            <span class="rating-text">
              {{ bike.rating }} out of 5 ({{ formatNumber(bike.reviewCount) }} reviews)
            </span>
          </div>
        </div>

        <div class="price-section">
          <div class="price-details">
            <div class="current-price">${{ formatNumber(getDiscountedPrice(bike)) }}</div>
            <div v-if="hasDiscount(bike)" class="original-price">
              ${{ formatNumber(bike.price) }}
            </div>
          </div>
          <div v-if="hasDiscount(bike)" class="savings">
            You save: ${{ formatNumber(getSavings(bike)) }}
          </div>
        </div>
      </div>

      <div class="action-buttons">
        <button class="add-to-cart-btn" @click="emit('addToCart')">
          <Icon icon="ic:round-shopping-cart" class="cart-icon" />
          Add to Cart
        </button>
        <button class="buy-now-btn">
          <Icon icon="mdi:flash" />
          <span>Buy Now</span>
        </button>
      </div>
    </div>

    <div class="brand-info">
      <h3>About {{ bike.title }}</h3>
      <div class="brand-description">{{ getBrandDescription(bike.brand) }}</div>
    </div>
  </div>
</template>

<script setup>
import { Icon } from '@iconify/vue'

defineProps({
  bike: { type: Object, required: true },
  formatNumber: Function,
  getDiscountedPrice: Function,
  getSavings: Function,
  getBrandDescription: Function,
})

const emit = defineEmits(['addToCart'])

// Function to check if a bike has a discount
const hasDiscount = (bike) =>
  bike.discount &&
  Array.isArray(bike.discount) &&
  bike.discount.length > 0 &&
  bike.discount[0].value
</script>

<style scoped>
.info-section {
  display: flex;
  flex-direction: column;
}

.info-content {
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 18px;
}

.basic-info {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  flex-wrap: wrap;
  margin-top: 24px;
  gap: 24px;
}

.bike-title {
  font-size: 28px;
  font-weight: 600;
  color: #1e293b;
  margin: 0 0 12px 0;
  line-height: 1.2;
}

.rating-section {
  display: flex;
  align-items: center;
  gap: 12px;
  padding-bottom: 24px;
}

.stars {
  display: flex;
  gap: 2px;
}

.star {
  font-size: 20px;
  color: #e2e8f0;
}

.star.filled {
  color: #fbbf24;
}

.rating-text {
  font-size: 14px;
  color: #64748b;
}

.brand-color-section {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 16px;
  color: #64748b;
  font-weight: 400;
  margin-top: 8px;
  margin-bottom: 12px;
}

.brand-name {
  font-weight: 500;
}

.separator {
  color: #64748b;
}

.color-info {
  font-weight: 500;
}

.price-section {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  margin-bottom: 24px;
}

.price-details {
  display: flex;
  align-items: baseline;
  gap: 16px;
}

.current-price {
  font-size: 36px;
  font-weight: 700;
  color: #f53f3f;
  margin-bottom: 8px;
}

.original-price {
  font-size: 20px;
  color: #94a3b8;
  text-decoration: line-through;
  margin-bottom: 4px;
}

.savings {
  font-size: 16px;
  color: #16a34a;
  font-weight: 500;
}

.action-buttons {
  display: flex;
  gap: 16px;
}

.add-to-cart-btn,
.buy-now-btn {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 16px 24px;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
}

.add-to-cart-btn {
  background: #e0f7fa;
  color: #006064;
  border: 1px solid #14c9c9;
}

.add-to-cart-btn:hover {
  background: #b2ebf2;
}

.buy-now-btn {
  background: #3b82f6;
  color: white;
}

.buy-now-btn:hover {
  background: #2563eb;
}

.brand-info h3 {
  font-size: 20px;
  font-weight: 600;
  color: #1e293b;
  margin: 32px 0 16px 0;
}

.brand-description {
  line-height: 1.6;
  color: #475569;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 34px;
  margin-bottom: 14px;
}

@media (max-width: 768px) {
  .bike-title {
    font-size: 24px;
  }

  .action-buttons {
    flex-direction: column;
  }
}
</style>
