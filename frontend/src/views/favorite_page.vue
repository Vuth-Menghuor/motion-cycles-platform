<template>
  <Navigation_header
    :disableAnimation="true"
    :colors="{
      headerBg: 'white',
      boxShadowHeader: '0 4px 20px rgba(0, 0, 0, 0.1)',
      menuIcon: 'black',
      logoName: 'black',
      searchBorder: 'rgba(0, 0, 0, 0.2)',
      searchBg: 'white',
      cartIcon: 'black',
      userIcon: 'black',
      userBorderBtn: 'rgba(0, 0, 0, 0.2)',
      userBgBtn: 'white',
      brandName: 'black',
      brandBorder: '#e5e5e5',
      brandBg: 'white',
    }"
  />

  <div class="favorites-page">
    <h2>Your Favorites</h2>
    <div class="favorites-saved">
      <span class="length-saved">{{ favoritesStore.favoriteCount }}</span>
      <span>Bike saved </span>
    </div>

    <div v-if="favoritesStore.favorites.length > 0" class="favorites-container">
      <div v-for="favorite in favoritesStore.favorites" :key="favorite.id" class="favorite-card">
        <div class="info-section">
          <img :src="favorite.product.image" alt="bike image" class="bike-image" />

          <div class="bike-info">
            <div class="bike-content-name">
              <h3>{{ favorite.product.name }}</h3>
              <div class="item-category-brand">
                <span class="badge">{{ favorite.product.brand }}</span>
                <span class="badge">Color: {{ favorite.product.color }}</span>
              </div>
              <div class="rating-section">
                <div class="stars">
                  <span
                    v-for="star in stars"
                    :key="star"
                    class="star"
                    :class="{ filled: star <= Math.floor(favorite.product.rating || 0) }"
                  >
                    <Icon icon="line-md:star-filled" />
                  </span>
                </div>
                <span class="rating-text">
                  ({{ favorite.product.rating || 0 }})
                  {{ formatNumber(favorite.product.review_count || 0) }}
                </span>
              </div>
            </div>

            <div class="price-info">
              <span class="price">
                ${{ getDiscountedPrice(favorite.product).toLocaleString() }}
              </span>
              <span v-if="favorite.product.discount" class="original-price">
                ${{ favorite.product.pricing.toLocaleString() }}
              </span>
            </div>

            <div v-if="favorite.product.discount" class="promotion-price">
              <span>{{ getDiscountLabel(favorite.product) }}</span>
            </div>
          </div>

          <div class="card-actions">
            <button class="btn view-btn" @click="viewBikeDetails(favorite.product.id)">
              View Details
            </button>
            <button class="btn remove-btn" @click="removeFromFavorites(favorite.product.id)">
              Remove
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="no-favorites">
      <img src="@/assets/images/favorites/empty_saved.png" alt="empty-saved" class="empty-saved" />
      <h3>No favorites yet.</h3>
      <p>
        Start browsing and save your favorite bikes to see<br />
        them here.
      </p>
    </div>
  </div>
</template>

<script setup>
import Navigation_header from '@/components/navigation_header.vue'
import { useFavoritesStore } from '@/stores/favorites'
import { useRouter } from 'vue-router'
import { Icon } from '@iconify/vue'
import { computed, onMounted } from 'vue'

const favoritesStore = useFavoritesStore()
const router = useRouter()
const stars = computed(() => Array.from({ length: 5 }, (_, i) => i + 1))

// Fetch favorites when component mounts
onMounted(async () => {
  const token = localStorage.getItem('token')
  if (!token) {
    router.push('/authentication/sign_in')
    return
  }

  try {
    await favoritesStore.fetchFavorites()
  } catch (error) {
    console.error('Failed to fetch favorites:', error)
  }
})

function formatNumber(number) {
  // Format number with commas if it exists
  if (number) {
    return number.toLocaleString()
  }
  return 0
}

const viewBikeDetails = (bikeId) => {
  router.push(`/bike/${bikeId}`).then(() => window.scrollTo(0, 0))
}

const removeFromFavorites = async (productId) => {
  try {
    await favoritesStore.removeFromFavorites(productId)
  } catch (error) {
    console.error('Failed to remove from favorites:', error)
  }
}

const getDiscountLabel = (product) => {
  // Return null if no discount
  if (!product.discount || !Array.isArray(product.discount) || product.discount.length === 0) {
    return null
  }

  const discount = product.discount[0]
  // Return label based on discount type
  if (discount.type === 'percent') {
    return `${discount.value}% OFF`
  } else {
    return `-${discount.value.toLocaleString()} OFF`
  }
}

const getDiscountedPrice = (product) => {
  // Return original price if no discount
  if (!product.discount || !Array.isArray(product.discount) || product.discount.length === 0) {
    return product.pricing
  }

  const discount = product.discount[0]
  // Calculate discounted price based on type
  if (discount.type === 'percent') {
    return product.pricing - (product.pricing * discount.value) / 100
  } else {
    return product.pricing - discount.value
  }
}
</script>

<style scoped>
.no-favorites h3 {
  color: black;
  font-weight: 600;
  margin: 0;
}
.no-favorites p {
  color: #555;
  font-size: 14px;
}
.no-favorites {
  text-align: center;
  font-size: 1.1rem;
  margin-top: 40px;
  display: flex;
  align-items: center;
  flex-direction: column;
  justify-content: center;
  background: #f8fafc;
  height: 40vh;
  border-radius: 10px;
  font-family: 'Poppins', sans-serif;
  border: 2px solid #e5e7eb;
}
.rating-section {
  display: flex;
  align-items: center;
  gap: 4px;
  margin-top: 6px;
}

.star {
  color: #d1d5db; /* gray for empty */
  font-size: 16px;
}

.star.filled {
  color: #fbbf24; /* gold for filled */
}

.rating-text {
  font-size: 12px;
  color: #6b7280;
  margin-left: 4px;
}

.price-info {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 8px;
}

.original-price {
  color: #999;
  text-decoration: line-through;
  font-size: 14px;
}

.favorite-card {
  position: relative; /* <-- added */
  display: flex;
  flex-direction: column;
  border: 1px solid #e5e5e5;
  border-radius: 12px;
  padding: 1rem;
  background-color: #fff;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}

.promotion-price {
  position: absolute;
  top: 1.5rem;
  right: 0rem;
  color: white;
  background-color: orange;
  font-family: 'Poppins', sans-serif;
  padding: 6px 12px 6px 14px;
  display: flex;
  align-items: center;
  clip-path: polygon(5% 0, 100% 0, 100% 20%, 100% 80%, 100% 99%, 5% 100%, 0 85%, 0 15%);
  gap: 4px;
  z-index: 10;
  font-size: 12px;
  font-weight: 600;
}

.bike-content-name {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.info-section {
  display: flex;
  gap: 24px;
  flex: 1;
}

.bike-image {
  height: auto;
  width: 30%;
  border-radius: 10px;
  object-fit: contain;
}

.bike-info {
  font-family: 'Poppins', sans-serif;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: 12px;
  padding: 14px 0;
  flex: 1;
}

.bike-info h3,
.bike-info p {
  margin: 0;
}

.subtitle {
  color: grey;
}

.bike-info .price {
  font-weight: bold;
  color: red;
  font-size: 1.5rem;
}

.card-actions {
  display: flex;
  align-items: flex-end;
  gap: 10px;
  padding: 14px 0;
}

.btn {
  padding: 10px 34px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-family: 'Poppins', sans-serif;
  font-size: 0.9rem;
  transition: 0.2s;
}
.view-btn {
  border: 2px solid #e5e7eb;
  background-color: white;
  color: #374151;
}

.remove-btn {
  background-color: #dc2626;
  color: white;
}

.remove-btn:hover {
  background-color: #b91c1c;
}
.favorites-container {
  display: flex;
  flex-direction: column;
  gap: 18px;
  margin-top: 24px;
}
.empty-saved {
  height: auto;
  width: 100px;
  margin-bottom: 40px;
}

.favorites-saved {
  font-family: 'Poppins', sans-serif;
  display: flex;
  align-items: center;
  font-size: 16px;
  padding-bottom: 24px;
  border-bottom: 2px solid #e5e7eb;
  color: grey;
  gap: 10px;
}

.length-saved {
  padding: 2px 12px;
  border-radius: 14px;
  border: 2px solid #3491fa;
  color: #3491fa;
  font-weight: 500;
}

.favorites-page {
  margin-top: 140px;
  padding: 20px;
  margin-left: auto;
  margin-right: auto;
  max-width: 1400px;
}

.favorites-page h2 {
  font-family: 'Poppins', sans-serif;
  font-size: 1.6rem;
  font-weight: 600;
  margin-bottom: 25px;
  color: #333;
}
/* New styles for subtitle and color */
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

.item-category-brand {
  color: #64748b;
  font-size: 14px;
  display: flex;
  gap: 8px;
}

.badge {
  display: inline-block;
  padding: 4px 12px;
  margin: 2px;
  border: 1px solid #ddd;
  border-radius: 90px;
  background-color: #f0f0f0;
  color: #333;
  font-size: 12px;
  font-weight: 500;
  margin: 8px 0;
}
</style>
