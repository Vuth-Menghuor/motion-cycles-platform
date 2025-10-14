<template>
  <div class="main-container">
    <div class="filter-toggle-container">
      <button class="filter-toggle-btn" @click="showFilters = !showFilters">
        <Icon icon="mdi:filter-variant" class="toggle-icon" />
        <span>Filters</span>
        <Icon :icon="showFilters ? 'mdi:chevron-up' : 'mdi:chevron-down'" class="toggle-icon" />
      </button>
    </div>

    <div class="content-wrapper">
      <bike_filter
        ref="bike_filters"
        :bikes="bikes"
        :show-filters="showFilters"
        v-model:selected-price-range="selectedPriceRange"
        v-model:selected-colors="selectedColors"
        v-model:selected-brands="selectedBrands"
        v-model:selected-discount-statuses="selectedDiscountStatuses"
        @clear-filters="handleClearFilters"
      />

      <div class="products-section">
        <div class="products-header">
          <h2>
            {{ props.brand ? props.brand + ' Bikes' : 'All Bikes' }} ({{ filteredBikes.length }}
            results)
          </h2>
        </div>

        <div v-if="loading" class="loading-state">
          <p>Loading bikes...</p>
        </div>
        <div v-else-if="error" class="error-state">
          <p>{{ error }}</p>
        </div>
        <div v-else>
          <div class="bikes-container">
            <div
              v-for="bike in filteredBikes"
              :key="bike.id"
              class="product-card"
              :data-id="bike.id"
            >
              <div class="card-header">
                <div
                  class="sale-badge"
                  :style="{ background: bike.badge?.gradient }"
                  v-if="bike.badge && bike.badge.text"
                >
                  <Icon :icon="bike.badge.icon" class="sale-icon" />
                  <span>{{ bike.badge.text }}</span>
                </div>
                <button
                  class="favorite-btn"
                  :class="{ favorited: isFavorited(bike.id) }"
                  @click="toggleFavorite(bike)"
                >
                  <Icon
                    :icon="isFavorited(bike.id) ? 'solar:heart-bold' : 'solar:heart-linear'"
                    class="fav-icon"
                  />
                </button>
              </div>

              <div class="product-image-container">
                <div class="promotion-price" v-if="hasDiscount(bike)">
                  <span>{{ getDiscountLabel(bike) }}</span>
                </div>
                <img :src="bike.image" alt="bike-image" class="product-image" />
              </div>

              <div class="price-info">
                <div class="price-container">
                  <label class="product-price">${{ formatNumber(getDiscountedPrice(bike)) }}</label>
                  <div v-if="hasDiscount(bike)" class="discount-info">
                    <span class="original-price">${{ formatNumber(bike.price) }}</span>
                    <span class="savings">You save: ${{ formatNumber(getSavings(bike)) }}</span>
                  </div>
                </div>
              </div>

              <div class="product-info">
                <label class="product-title">{{ bike.title }}</label>
                <div v-if="bike.highlight" class="product-highlight">
                  <span>{{ bike.highlight }}</span>
                </div>
                <div class="subtitle-color">
                  <span class="product-brand-info">{{ bike.brand }}</span>
                  <span class="separator"> | </span>
                  <span class="product-color">Color: {{ bike.color }}</span>
                </div>
              </div>

              <div class="brand-description">
                {{ getBrandDescription(bike.brand, bike.description) }}
              </div>

              <div class="rating-section">
                <div class="stars">
                  <span
                    v-for="star in stars"
                    :key="star"
                    class="star"
                    :class="{ filled: star <= Math.floor(bike.rating || 0) }"
                  >
                    ★
                  </span>
                </div>
                <span class="rating-text"
                  >({{ bike.rating }}) {{ formatNumber(bike.reviewCount) }}</span
                >
              </div>

              <div class="card-footer">
                <button class="view-detail-btn" @click="viewBikeDetails(bike.id)">
                  <span class="detail">View Details</span>
                </button>
                <button class="quick-buy-btn" @click="handleAddToCart(bike.id)">
                  <Icon icon="fa7-solid:cart-arrow-down" />
                  <span>Add To Cart</span>
                </button>
              </div>
            </div>
          </div>

          <div class="no-results" v-if="filteredBikes.length === 0">
            <Icon icon="mdi:magnify-close" class="no-results-icon" />
            <h3>No bikes found</h3>
            <p>Try adjusting your filters to see more results.</p>
            <button class="clear-filters-btn" @click="handleClearFilters">Clear All Filters</button>
          </div>
        </div>
      </div>
    </div>

    <Transition name="fade">
      <div class="toast-popup" v-if="showToast">
        <p>{{ toastMessage }}</p>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { Icon } from '@iconify/vue'
import { ref, computed } from 'vue'
import bike_filter from './bike_filter.vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useFavoritesStore } from '@/stores/favorites'
import { productsApi } from '@/services/api'

const router = useRouter()
const cartStore = useCartStore()
const favoritesStore = useFavoritesStore()

const props = defineProps({
  brand: { type: String, default: '' },
})

const stars = computed(() => Array.from({ length: 5 }, (_, i) => i + 1))

const formatNumber = (number) => number.toLocaleString()

// Computed property to get bikes with updated ratings from reviews store
const bikesWithLiveRatings = computed(() => {
  return bikes.value
})

// Reactive state
const bikes = ref([])
const loading = ref(true)
const error = ref(null)
const showToast = ref(false)
const toastMessage = ref('')
const itemQuantities = ref({})

// Filter state
const selectedPriceRange = ref('')
const selectedColors = ref([])
const selectedBrands = ref([])
const selectedDiscountStatuses = ref([])
const showFilters = ref(false)
const bike_filters = ref(null)

// Fetch products
const fetchProducts = async () => {
  try {
    loading.value = true
    const response = await productsApi.getProducts()
    bikes.value = response.data
    // Handle long data URLs
    bikes.value.forEach((bike) => {
      if (
        bike.image &&
        typeof bike.image === 'string' &&
        bike.image.startsWith('data:') &&
        bike.image.length > 100000
      ) {
        bike.image = ''
      }
    })
  } catch (err) {
    error.value = 'Failed to load products'
    console.error('Error fetching products:', err)
  } finally {
    loading.value = false
  }
}

// Toast notification
const showToastMessage = (message) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
    toastMessage.value = ''
  }, 2000)
}

// Cart functionality
const handleAddToCart = (bikeId) => {
  const bike = bikesWithLiveRatings.value.find((b) => b.id === bikeId)
  if (bike) {
    itemQuantities.value[bikeId] = (itemQuantities.value[bikeId] || 0) + 1
    const quantityString = 'x' + itemQuantities.value[bikeId].toString().padStart(2, '0')
    const message = `${bike.title} added to cart! ${quantityString} 🛒`
    showToastMessage(message)
    cartStore.addItem(bike)
  }
}

// Filtering
const filteredBikes = computed(() => {
  return bikesWithLiveRatings.value.filter((bike) => {
    // Filter by brand if specified in props
    if (props.brand) {
      if (bike.brand?.toLowerCase() !== props.brand.toLowerCase()) {
        return false
      }
    }

    // Filter by price range
    if (selectedPriceRange.value && bike_filters.value) {
      const discountedPrice = getDiscountedPrice(bike)
      const range = bike_filters.value.priceRanges.find((r) => r.label === selectedPriceRange.value)
      if (range) {
        if (discountedPrice < range.min || discountedPrice > range.max) {
          return false
        }
      }
    }

    // Filter by discount status
    if (selectedDiscountStatuses.value.length > 0) {
      const bikeHasDiscount = hasDiscount(bike)
      const shouldShowDiscounted = selectedDiscountStatuses.value.includes('discounted')
      const shouldShowRegular = selectedDiscountStatuses.value.includes('regular')

      if (bikeHasDiscount) {
        if (!shouldShowDiscounted) {
          return false
        }
      } else {
        if (!shouldShowRegular) {
          return false
        }
      }
    }

    // Filter by selected colors
    if (selectedColors.value.length > 0) {
      if (!selectedColors.value.includes(bike.color)) {
        return false
      }
    }

    // Filter by selected brands
    if (selectedBrands.value.length > 0) {
      if (!selectedBrands.value.includes(bike.brand)) {
        return false
      }
    }

    return true
  })
})

// Favorites
const toggleFavorite = (bike) => favoritesStore.toggleFavorite(bike)
const isFavorited = (bikeId) => favoritesStore.isFavorited(bikeId)

// Discount utilities
const hasDiscount = (bike) => {
  if (!bike.discount) {
    return false
  }
  if (!Array.isArray(bike.discount)) {
    return false
  }
  if (bike.discount.length === 0) {
    return false
  }
  if (!bike.discount[0].value) {
    return false
  }
  return true
}

const getDiscountLabel = (bike) => {
  if (!hasDiscount(bike)) {
    return ''
  }
  const discount = bike.discount[0]
  if (discount.type === 'percent') {
    return `${discount.value}% OFF`
  } else {
    return `$${discount.value.toLocaleString()} OFF`
  }
}

const getDiscountedPrice = (bike) => {
  if (!hasDiscount(bike)) {
    return bike.price
  }
  const discount = bike.discount[0]
  if (discount.type === 'percent') {
    return bike.price - (bike.price * discount.value) / 100
  } else {
    return bike.price - discount.value
  }
}

const getSavings = (bike) => {
  if (!hasDiscount(bike)) {
    return 0
  }
  const discount = bike.discount[0]
  if (discount.type === 'percent') {
    return (bike.price * discount.value) / 100
  } else {
    return discount.value
  }
}

// Brand description
const getBrandDescription = (brand, productDescription) => {
  if (productDescription) return productDescription

  const descriptions = {
    Bianchi:
      'Founded in 1885, Bianchi is one of the oldest bicycle manufacturers in the world. Known for their distinctive celeste green color and Italian craftsmanship, Bianchi has been at the forefront of cycling innovation for over a century.',
    Trek: 'Trek Bicycle Corporation is an American bicycle and cycling product manufacturer and distributor. Trek is known for high-quality road, mountain, and electric bikes, with a commitment to advancing bicycle technology and sustainability.',
    Specialized:
      'Specialized is an American company that designs, manufactures, and markets bicycles, bicycle components, and related products. Founded in 1974, Specialized is known for innovation and performance-oriented cycling equipment.',
    Giant:
      "Giant Manufacturing Co. is a Taiwanese bicycle manufacturer. Founded in 1972, Giant is the world's largest bicycle manufacturer and is known for producing high-quality bikes at competitive prices.",
    Cannondale:
      'Cannondale Bicycle Corporation is an American division of Dutch conglomerate Pon Holdings. Founded in 1971, Cannondale is known for innovative designs and high-performance bicycles.',
    Cervélo:
      'Cervélo is a Canadian bicycle manufacturer that designs and manufactures high-performance road, triathlon, and track bicycles.',
    Shimano:
      'Shimano Inc. is a Japanese multinational manufacturer of cycling components, fishing tackle, and rowing equipment.',
  }
  return descriptions[brand] || 'A leading bicycle manufacturer known for quality and innovation.'
}

// Navigation
const viewBikeDetails = (bikeId) => router.push(`/bike/${bikeId}`).then(() => window.scrollTo(0, 0))

const handleClearFilters = () => {
  selectedPriceRange.value = ''
  selectedColors.value = []
  selectedBrands.value = []
  selectedDiscountStatuses.value = []
}

// Initialize
fetchProducts()
</script>

<style scoped>
.toast-popup {
  position: fixed;
  bottom: 2rem;
  left: 50%;
  transform: translateX(-50%);
  background-color: rgba(0, 0, 0, 0.8);
  color: white;
  padding: 1rem 2rem;
  border-radius: 8px;
  z-index: 1000;
  text-align: center;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  transition:
    opacity 0.3s ease-in-out,
    transform 0.3s ease-in-out;
}
.fade-enter-active,
.fade-leave-active {
  transition:
    opacity 0.3s ease,
    transform 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(20px) translateX(-50%);
}
.main-container {
  width: 100%;
  font-family: 'Poppins', sans-serif;
}

.filter-toggle-container {
  display: none;
  padding: 16px 20px;
  border-bottom: 1px solid #e5e5e5;
}

.filter-toggle-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  background: white;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  padding: 12px 16px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.filter-toggle-btn:hover {
  background: #f9fafb;
  border-color: #9ca3af;
}

.toggle-icon {
  margin-left: auto;
}

.content-wrapper {
  display: flex;
  gap: 24px;
  max-width: 2000px;
  margin: 0 auto;
  padding: 20px;
}
.clear-filters-btn {
  background: none;
  border: none;
  color: #6366f1;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 6px;
  transition: background-color 0.2s ease;
}

.clear-filters-btn:hover {
  background: #f0f0ff;
}

.products-section {
  flex: 1;
  min-width: 0;
}
.products-header {
  margin-bottom: 24px;
}
.loading-state,
.error-state {
  text-align: center;
  padding: 50px;
  font-size: 18px;
}

.loading-state {
  color: #666;
}

.error-state {
  color: #e74c3c;
}
.no-results {
  text-align: center;
  padding: 64px 32px;
  color: #6b7280;
}
.no-results-icon {
  font-size: 64px;
  margin-bottom: 16px;
  opacity: 0.5;
}
.no-results h3 {
  margin: 0 0 8px 0;
  font-size: 20px;
  font-weight: 600;
  color: #374151;
}
.no-results p {
  margin: 0 0 24px 0;
  font-size: 16px;
}
.products-header h2 {
  margin: 0;
  font-size: 24px;
  font-weight: 600;
  color: #1f2937;
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

.product-price {
  font-size: 20px;
  font-weight: 600;
  color: #333;
}

.original-price {
  font-size: 14px;
  color: #999;
  text-decoration: line-through;
}
.rating-section {
  display: flex;
  align-items: start;
  gap: 6px;
  padding: 10px 24px 10px 24px;
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
  font-size: 12px;
  color: #6b7280;
  font-family: 'Poppins', sans-serif;
}

.product-specs {
  display: flex;
  font-family: 'Poppins', sans-serif;
  padding: 10px 20px;
  gap: 16px;
  position: relative;
  width: 400px;
  overflow: hidden;
  text-overflow: ellipsis;
  z-index: 3;
}

.spec-item {
  flex: 1;
  display: block;
  min-width: 0;
}

.spec-text {
  line-height: 1.6;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  line-clamp: 3;
  overflow: hidden;
  font-size: 12px;
  color: grey;
  font-weight: 500;
}

.bikes-container {
  display: flex;
  gap: 24px;
  flex-wrap: wrap;
  justify-content: start;
}
.sale-icon {
  font-size: 22px;
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

.sale-badge span {
  font-size: 12px;
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
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

.product-card {
  max-width: 460px;
  background-color: white;
  border: 1px solid #dee2e6;
  border-radius: 10px;
  object-fit: contain;
  overflow: hidden;
  position: relative;
}

.card-header {
  position: absolute;
  top: 16px;
  left: 20px;
  right: 16px;
  display: flex;
  justify-content: space-between; /* push badge left, heart right */
  align-items: center;
  z-index: 5;
}

.product-info {
  flex: 1;
  font-family: 'Poppins', sans-serif;
  line-height: 1.6;
  padding: 10px 24px 0 24px;
}

.product-highlight {
  margin: 8px 0;
  padding: 8px 12px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 6px;
  color: white;
  font-size: 14px;
  font-weight: 500;
  text-align: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.product-highlight span {
  font-style: italic;
}

.brand-description {
  padding: 8px 24px 0 24px;
  line-height: 1.6;
  color: #475569;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  line-clamp: 3;
  overflow: hidden;
  text-overflow: ellipsis;
}

.price-info {
  flex: 1;
  font-family: 'Poppins', sans-serif;
  padding: 12px 24px 0 24px;
}

.price-container {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.discount-info {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.savings {
  font-size: 14px;
  color: #16a34a;
  font-weight: 500;
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

.product-price {
  font-size: 20px;
  font-weight: 600;
  color: #e74c3c;
}

.product-title {
  font-size: 16px;
  font-weight: 600;
  color: #333;
  margin: 0 0 4px 0;
}

.fav-icon {
  font-size: 20px;
}

.product-image {
  width: 100%; /* always take full width of card */
  height: 100%; /* stretch to container height */
  max-height: unset; /* remove fixed height limit */
  object-fit: cover; /* fill area while keeping ratio (may crop) */
  z-index: 2;
  position: relative;
  opacity: 1; /* always visible */
  transform: none; /* no translation */
  transition: none; /* remove animation */
}

.product-image-container {
  position: relative;
  width: 100%;
  height: 250px; /* set a fixed height for consistency */
  overflow: hidden; /* crop overflow */
  display: flex;
  align-items: center;
  justify-content: center;
}

.product-image-container img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: 0.3s;
}

.product-image-container:hover img {
  transform: scale(1.05);
}

.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  gap: 16px;
}

/* Updated styles for the View Details router-link */
.view-detail-btn {
  display: flex;
  align-items: center;
  z-index: 4;
  border: 1px solid #b8b8b8;
  color: black;
  background-color: white;
  text-decoration: none;
  border-radius: 6px;
  transition: all 0.2s ease;
}

.view-detail-btn:hover {
  background-color: #f8f9fa;
  border-color: #6c757d;
  transform: translateY(-1px);
}

.detail {
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  font-weight: 500;
  height: 40px;
  cursor: pointer;
  width: 180px;
}

.quick-buy-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  font-family: 'Poppins', sans-serif;
  border: 1px solid #14c9c9;
  background: #e8fffb;
  color: #07828b;
  padding: 10px 50px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  border-radius: 6px;
  transition: all 0.2s ease;
  flex-shrink: 0;
}

.quick-buy-btn:hover {
  background: #b2ebf2;
  transform: translateY(-1px);
}

/* Responsive Design */
@media (max-width: 768px) {
  .filter-toggle-container {
    display: block;
  }

  .card-footer {
    flex-direction: column;
    gap: 12px;
  }

  .view-detail-btn,
  .quick-buy-btn {
    width: 100%;
    justify-content: center;
  }
}
</style>
