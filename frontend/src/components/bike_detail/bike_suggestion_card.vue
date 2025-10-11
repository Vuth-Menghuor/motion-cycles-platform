<template>
  <div class="suggestions-container">
    <div class="suggestions-header">
      <h2>You might also like</h2>
      <p>Similar bikes that other customers have viewed</p>
    </div>

    <div class="bikes-scroll-container">
      <div class="bikes-grid">
        <div v-for="bike in suggestionBikes" :key="bike.id" class="product-card">
          <div class="card-header">
            <div
              class="sale-badge"
              :style="{ background: bike.badge.gradient }"
              v-if="bike.badge.text"
            >
              <Icon :icon="bike.badge.icon" class="sale-icon" />
              <span>{{ bike.badge.text }}</span>
            </div>
            <button
              class="favorite-btn"
              :class="{ favorited: isFavorited(bike.id) }"
              @click="toggleFavorite(bike.id)"
            >
              <Icon
                :icon="isFavorited(bike.id) ? 'solar:heart-bold' : 'solar:heart-linear'"
                class="fav-icon"
              />
            </button>
          </div>

          <div class="product-image-container">
            <div v-if="bike.discount" class="promotion-price">
              <span>{{ getDiscountLabel(bike) }}</span>
            </div>
            <img :src="bike.image" alt="bikes-image-transparent" class="product-image" />
          </div>

          <div class="price-info">
            <label class="product-price discounted">
              ${{ formatNumber(getDiscountedPrice(bike)) }}
            </label>
            <span v-if="bike.discount" class="original-price">${{ formatNumber(bike.price) }}</span>
          </div>

          <div class="product-info">
            <label class="product-title">{{ bike.title }}</label>
            <div class="subtitle-color">
              <span class="product-subtitle">{{ bike.subtitle }}</span>
              <span class="separator">|</span>
              <span class="product-color">Color {{ bike.color }}</span>
            </div>
          </div>

          <div class="rating-section">
            <div class="stars">
              <span
                v-for="star in stars"
                :key="star"
                class="star"
                :class="{ filled: star <= Math.floor(bike.rating) }"
              >
                <Icon icon="line-md:star-filled" />
              </span>
            </div>
            <span class="rating-text">
              ({{ bike.rating }}) {{ formatNumber(bike.reviewCount) }}
            </span>
          </div>

          <div class="card-footer">
            <button class="view-detail-btn" @click="viewBikeDetails(bike.id)">
              <span class="detail">View Details</span>
            </button>
            <button class="add-to-cart-btn" @click="addToCart(bike)">
              <Icon icon="fa-solid:cart-plus" />
              <span>Add To Cart</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Icon } from '@iconify/vue'
import { ref, computed } from 'vue'

// Define props for the component
const props = defineProps({
  currentBikeId: { type: Number, default: null },
  maxSuggestions: { type: Number, default: 6 },
})

// Define emits for parent component communication
const emit = defineEmits(['add-to-cart'])

// Static data for all bikes
const allBikes = ref([
  // Bike data objects...
])

// Computed for star array
const stars = computed(() => Array.from({ length: 5 }, (_, i) => i + 1))

// Computed for suggestion bikes
const suggestionBikes = computed(() => {
  const filtered = allBikes.value.filter((bike) => bike.id !== props.currentBikeId)
  return filtered.sort(() => 0.5 - Math.random()).slice(0, props.maxSuggestions)
})

// Reactive data for favorites
const favorites = ref(new Set())

// Function to format numbers
const formatNumber = (number) => number.toLocaleString()

// Function to toggle favorite
const toggleFavorite = (bikeId) => {
  if (favorites.value.has(bikeId)) {
    favorites.value.delete(bikeId)
  } else {
    favorites.value.add(bikeId)
  }
  favorites.value = new Set(favorites.value)
}

// Function to check if favorited
const isFavorited = (bikeId) => favorites.value.has(bikeId)

// Function to get discount label
const getDiscountLabel = (bike) => {
  if (!bike.discount) {
    return null
  }
  if (bike.discount.type === 'percent') {
    return `${bike.discount.value}% OFF`
  } else {
    return `-${bike.discount.value.toLocaleString()} OFF`
  }
}

// Function to calculate discounted price
const getDiscountedPrice = (bike) => {
  if (!bike.discount) {
    return bike.price
  }
  if (bike.discount.type === 'percent') {
    return bike.price - (bike.price * bike.discount.value) / 100
  } else {
    return bike.price - bike.discount.value
  }
}

// Function to add to cart
const addToCart = (bike) => {
  emit('add-to-cart', bike)
  console.log('Added to cart:', bike.title)
}

// Function to view bike details
const viewBikeDetails = (bikeId) => {
  window.location.href = `/bike/${bikeId}`
}
</script>

<style scoped>
.suggestions-container {
  width: 100%;
  font-family: 'Poppins', sans-serif;
  padding: 40px 0;
}

.suggestions-header {
  text-align: start;
  margin-bottom: 40px;
}

.suggestions-header h2 {
  margin: 0 0 14px 0;
  font-size: 28px;
  font-weight: 600;
  color: #1f2937;
}

.suggestions-header p {
  margin: 0;
  font-size: 16px;
  color: #6b7280;
}

.bikes-scroll-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 0;
  overflow: hidden;
  position: relative;
}

.bikes-scroll-container::after {
  content: '';
  position: absolute;
  top: 0;
  right: 0;
  width: 100px;
  height: 100%;
  background: linear-gradient(to right, transparent, white);
  pointer-events: none;
  z-index: 10;
}

.bikes-grid {
  display: flex;
  gap: 24px;
  overflow-x: auto;
  overflow-y: hidden;
  scroll-behavior: smooth;
  padding-bottom: 10px;
  scrollbar-width: thin;
  scrollbar-color: #cbd5e0 transparent;
}

.bikes-grid::-webkit-scrollbar {
  height: 6px;
}

.bikes-grid::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.bikes-grid::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 10px;
}

.bikes-grid::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}

.product-card {
  background-color: white;
  border: 1px solid #dee2e6;
  border-radius: 10px;
  overflow: hidden;
  position: relative;
  transition: all 0.3s ease;
  flex: 0 0 400px;
  height: fit-content;
}

.card-header {
  position: absolute;
  top: 16px;
  left: 20px;
  right: 16px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  z-index: 5;
}

.sale-badge {
  position: relative;
  left: -26px;
  color: white;
  padding: 6px 12px 6px 14px;
  display: flex;
  align-items: center;
  clip-path: polygon(0 0, 95% 0, 100% 20%, 100% 80%, 95% 100%, 0 100%, 0% 80%, 0% 20%);
  gap: 4px;
}

.sale-badge span {
  font-size: 12px;
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
}

.sale-icon {
  font-size: 22px;
}

.favorite-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #dee2e6;
  cursor: pointer;
  padding: 6px;
  border-radius: 50%;
  color: grey;
  background-color: white;
  transition: all 0.3s ease;
}

.favorite-btn.favorited {
  background-color: #ffebef;
  border-color: #ff4d6d;
  color: #ff4d6d;
}

.favorite-btn.favorited .fav-icon {
  color: #ff4d6d;
}

.fav-icon {
  font-size: 20px;
}

.product-image-container {
  position: relative;
  width: 100%;
  height: 200px;
  overflow: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

.product-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  z-index: 2;
}

.promotion-price {
  position: absolute;
  bottom: 0px;
  right: -1px;
  color: white;
  background-color: orange;
  font-family: 'Poppins', sans-serif;
  padding: 6px 12px 6px 14px;
  display: flex;
  align-items: center;
  clip-path: polygon(5% 0, 100% 0, 100% 20%, 100% 80%, 100% 99%, 5% 100%, 0 85%, 0 15%);
  gap: 4px;
  z-index: 10;
}

.promotion-price span {
  font-family: 'Poppins', sans-serif;
  font-size: 12px;
  font-weight: 600;
}

.price-info {
  padding: 12px 20px 0 20px;
}

.product-price.discounted {
  font-size: 18px;
  font-weight: 600;
  color: red;
}

.original-price {
  font-size: 14px;
  color: #999;
  text-decoration: line-through;
  margin-left: 8px;
}

.product-info {
  padding: 10px 20px 0 20px;
}

.product-title {
  font-size: 16px;
  font-weight: 600;
  color: #333;
  margin: 0 0 4px 0;
  display: block;
  line-height: 1.4;
}

.subtitle-color {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
  color: grey;
  font-weight: 400;
}

.separator {
  color: grey;
}

.product-color {
  font-weight: 500;
}

.rating-section {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
}

.stars {
  display: flex;
  gap: 2px;
}

.star {
  color: #d1d5db;
  font-size: 14px;
}

.star.filled {
  color: #fbbf24;
}

.rating-text {
  font-size: 12px;
  color: #6b7280;
  font-family: 'Poppins', sans-serif;
}

.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  gap: 12px;
}

.view-detail-btn {
  display: flex;
  align-items: center;
  border: 1px solid #b8b8b8;
  color: black;
  background-color: white;
  border-radius: 6px;
  transition: all 0.2s ease;
  flex: 1;
  justify-content: center;
  cursor: pointer;
  font-family: 'Poppins', sans-serif;
}

.view-detail-btn:hover {
  background-color: #f8f9fa;
  border-color: #6c757d;
  transform: translateY(-1px);
}

.detail {
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  font-weight: 500;
  padding: 10px 20px;
}

.add-to-cart-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  font-family: 'Poppins', sans-serif;
  border: 1px solid #14c9c9;
  background: #e8fffb;
  color: #07828b;
  padding: 10px 20px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.2s ease;
  flex: 1;
  justify-content: center;
}
</style>
