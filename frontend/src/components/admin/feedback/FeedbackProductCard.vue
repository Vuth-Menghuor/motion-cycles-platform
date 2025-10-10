<template>
  <div class="product-card">
    <div class="product-image-container">
      <img v-if="product.image" :src="product.image" :alt="product.name" class="product-image" />
      <div v-else class="product-image-placeholder">
        <span>{{ product.name }}</span>
      </div>
    </div>
    <div class="product-info">
      <h3 class="product-name">{{ product.name }}</h3>
      <p class="product-description">{{ product.description }}</p>
      <div class="product-details">
        <span class="product-price">${{ product.price }}</span>
        <div class="product-rating">
          <div class="stars">
            <span
              v-for="star in 5"
              :key="star"
              class="star"
              :class="{ filled: star <= Math.floor(product.rating) }"
            >
              ★
            </span>
          </div>
          <span class="rating-text">({{ product.rating }})</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  product: {
    type: Object,
    required: true,
  },
})
</script>

<style scoped>
.product-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  max-width: 400px;
  border: 1px solid #e2e8f0;
}

.product-image-container {
  height: 200px;
  overflow: hidden;
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.product-image-placeholder {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 16px;
  font-weight: 600;
  text-align: center;
  padding: 20px;
}

.product-info {
  padding: 20px;
}

.product-name {
  font-size: 20px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 8px 0;
}

.product-description {
  font-size: 14px;
  color: #6b7280;
  line-height: 1.5;
  margin: 0 0 16px 0;
}

.product-details {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.product-price {
  font-size: 18px;
  font-weight: 600;
  color: #059669;
}

.product-rating {
  display: flex;
  align-items: center;
  gap: 8px;
}

.stars {
  display: flex;
}

.star {
  color: #d1d5db;
  font-size: 16px;
}

.star.filled {
  color: #fbbf24;
}

.rating-text {
  font-size: 14px;
  color: #6b7280;
}
</style>
