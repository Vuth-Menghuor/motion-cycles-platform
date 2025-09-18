<script setup>
import { Icon } from '@iconify/vue'
import { ref, onMounted, onUnmounted, computed, reactive } from 'vue' // Import reactive
import bike_filter from './bike_filter.vue'
import bike1 from '@/assets/images/product_card/mount_1.png'
import bike2 from '@/assets/images/product_card/mount_2/mount_2.png'
import bike3 from '@/assets/images/product_card/mount_3.png'
import bike4 from '@/assets/images/product_card/road_1.png'
import bike5 from '@/assets/images/product_card/road_2.png'
import bike6 from '@/assets/images/product_card/road_3.png'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { useFavoritesStore } from '@/stores/favorites'

// router instance
const router = useRouter()

const props = defineProps({
  brand: {
    type: String,
    required: false,
    default: '',
  },
})

const cartStore = useCartStore() // Use the store

// Emit event for parent to listen to
const emit = defineEmits(['add-to-cart'])

const stars = computed(() => Array.from({ length: 5 }, (_, i) => i + 1))

function formatNumber(number) {
  return number.toLocaleString()
}

const bikes = ref([
  {
    id: 1,
    title: 'Bianchi T-Tronik C Type - Sunrace (2023)',
    subtitle: 'Bianchi',
    price: 24400,
    color: 'Pink',
    badge: {
      text: 'Hot',
      icon: 'mdi:hot',
      gradient: 'linear-gradient(135deg, rgb(255, 107, 107), rgb(255, 82, 82))',
    },
    discount: {
      type: 'percent',
      value: 10,
    },
    rating: 4.8,
    reviewCount: 3221,
    specs: [
      {
        text: 'The 2023 Bianchi T-Tronik C-Type Sunrace is a refined Class 1 electric city bike designed for urban commuting and leisure rides. It features a Shimano E6100 250W mid-drive motor delivering 60Nm of torque, paired with a 417Wh Phylion battery offering up to 95 km of range.',
      },
    ],
    image: bike1,
  },
  {
    id: 2,
    title: 'Trek Slash 9.8 XT Carbon',
    subtitle: 'Trek',
    price: 44299,
    color: 'Orange',
    badge: {
      text: 'New',
      icon: 'material-symbols-light:new-releases',
      gradient: 'linear-gradient(135deg, #3491FA, #3491FA)',
    },
    discount: {
      type: 'fixed',
      value: 399,
    },
    rating: 2.4,
    reviewCount: 3221,
    specs: [
      {
        text: 'The Trek Slash 9.8 XT is a high-performance enduro mountain bike designed for aggressive trail riding and technical descents. It features a lightweight OCLV Mountain Carbon frame with 170mm of front and rear travel, utilizing a high-pivot suspension system with an idler pulley to enhance rear-wheel traction and control over rough terrain .',
      },
    ],
    image: bike2,
  },
  {
    id: 3,
    title: 'Santa Cruz Hightower CC X01',
    subtitle: 'Specialized',
    price: 29999,
    color: 'Grey',
    badge: {},
    discount: null,
    rating: 5,
    reviewCount: 3221,
    specs: [
      {
        text: "The Santa Cruz Hightower CC X01 is a versatile trail bike designed to excel on both climbs and descents. Featuring 150mm front and 140mm rear travel, it incorporates Santa Cruz's lower-link VPP suspension design, providing a nearly linear leverage curve for efficient pedaling and excellent bump absorption.",
      },
    ],
    image: bike3,
  },
  {
    id: 4,
    title: 'Giant TCR Advanced Pro 1',
    subtitle: 'Giant',
    price: 3799,
    color: 'Black',
    badge: {},
    discount: {
      type: 'percent',
      value: 30,
    },
    rating: 4.8,
    reviewCount: 1120,
    specs: [
      {
        text: 'Carbon fiber frame, lightweight and aerodynamic. Perfect for racing and long-distance rides on smooth roads.',
      },
    ],
    image: bike4,
  },
  {
    id: 5,
    title: 'Specialized Allez Sprint Comp',
    subtitle: 'Specialized',
    price: 3299,
    color: 'Blue',
    badge: {},
    discount: {
      type: 'fixed',
      value: 97,
    },
    rating: 4.7,
    reviewCount: 890,
    specs: [
      {
        text: 'High-modulus carbon fork, lightweight alloy frame. Optimized for sprinting and fast-paced group rides.',
      },
    ],
    image: bike5,
  },
  {
    id: 6,
    title: 'Cannondale Synapse Carbon Disc Ultegra',
    subtitle: 'Cannondale',
    price: 4199,
    color: 'Red',
    badge: {
      text: 'New',
      icon: 'material-symbols-light:new-releases',
      gradient: 'linear-gradient(135deg, #3491FA, #3491FA)',
    },
    discount: null,
    rating: 3.5,
    reviewCount: 650,
    specs: [
      {
        text: 'Comfort-oriented carbon frame with disc brakes. Designed for long endurance rides with smooth handling and stability.',
      },
    ],
    image: bike6,
  },
])

const showToast = ref(false)
const toastMessage = ref('')
// Use a reactive object to store item quantities by ID
const itemQuantities = reactive({})

const showToastMessage = (message) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
    toastMessage.value = ''
  }, 2000)
}

// Function to handle the "Add to Cart" click
const handleAddToCart = (bikeId) => {
  const bike = bikes.value.find((b) => b.id === bikeId)
  if (bike) {
    // Increment the quantity for this specific item (local state)
    itemQuantities[bikeId] = (itemQuantities[bikeId] || 0) + 1
    const quantityString = 'x' + itemQuantities[bikeId].toString().padStart(2, '0')
    const message = `${bike.title} added to cart! ${quantityString} 🛒`
    showToastMessage(message)

    // Call the action from the Pinia store to update the global count
    // cartStore.incrementCount()
    cartStore.addItem(bike)
  }
}

const selectedPriceRange = ref('')
const selectedColors = ref([])
const selectedBrands = ref([])
const selectedDiscountStatuses = ref([])
const showFilters = ref(false)
const bike_filters = ref(null)
const hasDiscount = (bike) => {
  return bike.discount && (bike.discount.type === 'percent' || bike.discount.type === 'fixed')
}
const filteredBikes = computed(() => {
  return bikes.value.filter((bike) => {
    if (props.brand && bike.subtitle.toLowerCase() !== props.brand.toLowerCase()) {
      return false
    }
    const discountedPrice = getDiscountedPrice(bike)
    if (selectedPriceRange.value && bike_filters.value) {
      const range = bike_filters.value.priceRanges.find((r) => r.label === selectedPriceRange.value)
      if (range && (discountedPrice < range.min || discountedPrice > range.max)) {
        return false
      }
    }
    if (selectedDiscountStatuses.value.length > 0) {
      const bikeHasDiscount = hasDiscount(bike)
      const shouldShowDiscounted = selectedDiscountStatuses.value.includes('discounted')
      const shouldShowRegular = selectedDiscountStatuses.value.includes('regular')
      if (bikeHasDiscount && !shouldShowDiscounted) {
        return false
      }
      if (!bikeHasDiscount && !shouldShowRegular) {
        return false
      }
    }
    if (selectedColors.value.length > 0 && !selectedColors.value.includes(bike.color)) {
      return false
    }
    if (selectedBrands.value.length > 0 && !selectedBrands.value.includes(bike.subtitle)) {
      return false
    }
    return true
  })
})
const favorites = ref(new Set())
const visibleBikes = ref(new Set())

const favoritesStore = useFavoritesStore()

const toggleFavorite = (bike) => {
  favoritesStore.toggleFavorite(bike)
}

const isFavorited = (bikeId) => {
  return favoritesStore.isFavorited(bikeId)
}

const getDiscountLabel = (bike) => {
  if (!bike.discount) return null
  return bike.discount.type === 'percent'
    ? `${bike.discount.value}% OFF`
    : `-${bike.discount.value.toLocaleString()} OFF`
}
const getDiscountedPrice = (bike) => {
  if (!bike.discount) return bike.price
  if (bike.discount.type === 'percent') {
    return bike.price - (bike.price * bike.discount.value) / 100
  } else {
    return bike.price - bike.discount.value
  }
}
const viewBikeDetails = (bikeId) => {
  router.push(`/bike/${bikeId}`).then(() => window.scrollTo(0, 0))
}
const handleClearFilters = () => {
  selectedPriceRange.value = ''
  selectedColors.value = []
  selectedBrands.value = []
  selectedDiscountStatuses.value = []
}
let observer
onMounted(() => {
  observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        const id = Number(entry.target.dataset.id)
        if (entry.isIntersecting) {
          visibleBikes.value.add(id)
        } else {
          visibleBikes.value.delete(id)
        }
        visibleBikes.value = new Set(visibleBikes.value)
      })
    },
    { threshold: 0.5 },
  )
  const cards = document.querySelectorAll('.product-card')
  cards.forEach((card) => observer.observe(card))
})
onUnmounted(() => {
  if (observer) observer.disconnect()
})
</script>

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

        <div class="bikes-container">
          <div v-for="bike in filteredBikes" :key="bike.id" class="product-card" :data-id="bike.id">
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
                @click="toggleFavorite(bike)"
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
              <label class="product-price discounted"
                >${{ formatNumber(getDiscountedPrice(bike)) }}</label
              >
              <span v-if="bike.discount" class="original-price"
                >${{ formatNumber(bike.price) }}</span
              >
            </div>

            <div class="product-info">
              <label class="product-title">{{ bike.title }}<br /></label>
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

            <div class="product-specs">
              <div v-for="spec in bike.specs" :key="spec.label" class="spec-item">
                <div class="spec-content">
                  <span class="spec-text">{{ spec.text }}</span>
                </div>
              </div>
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

        <div v-if="filteredBikes.length === 0" class="no-results">
          <Icon icon="mdi:magnify-close" class="no-results-icon" />
          <h3>No bikes found</h3>
          <p>Try adjusting your filters to see more results.</p>
          <button class="clear-filters-btn" @click="handleClearFilters">Clear All Filters</button>
        </div>
      </div>
    </div>

    <Transition name="fade">
      <div v-if="showToast" class="toast-popup">
        <p>{{ toastMessage }}</p>
      </div>
    </Transition>
  </div>
</template>

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

.product-price.discounted {
  font-size: 20px;
  font-weight: 600;
  color: red;
}
.original-price {
  font-size: 14px;
  color: #999;
  text-decoration: line-through;
  margin-left: 8px;
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

.price-info {
  flex: 1;
  font-family: 'Poppins', sans-serif;
  padding: 12px 24px 0 24px;
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
  color: #333;
}

.product-title {
  font-size: 16px;
  font-weight: 600;
  color: #333;
  margin: 0 0 4px 0;
}

.product-subtitle {
  font-size: 14px;
  color: grey;
  font-weight: 400;
  margin: 0;
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
