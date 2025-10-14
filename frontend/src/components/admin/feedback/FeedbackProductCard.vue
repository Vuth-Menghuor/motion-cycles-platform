<template>
  <div class="product-card">
    <div class="product-image-container">
      <img :src="product.image" :alt="product.name" class="product-image" />
    </div>
    <div class="product-info">
      <div class="product-header">
        <h3 class="product-name">{{ product.name }}</h3>
        <div class="product-meta">
          <span class="product-brand">{{ product.brand }}</span>
        </div>
      </div>
      <p class="product-description">{{ product.description }}</p>
      <div class="product-details">
        <div class="product-price-rating">
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

.product-info {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.product-header {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.product-name {
  font-size: 20px;
  font-weight: 600;
  color: #1f2937;
  margin: 0;
}

.product-meta {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.product-brand,
.product-category {
  background-color: #f3f4f6;
  padding: 4px 8px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
  color: #374151;
  border: 1px solid #d1d5db;
}

.product-description {
  font-size: 14px;
  color: #6b7280;
  line-height: 1.5;
  margin: 0;
  max-width: 100%;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}

.product-details {
  margin-top: auto;
}

.product-price-rating {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
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
