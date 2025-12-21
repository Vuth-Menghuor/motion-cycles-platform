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
  <div class="bike-detail-container">
    <div class="bike-detail">
      <div v-if="loading" class="loading-state">
        <p>Loading bike details...</p>
      </div>
      <div v-else-if="error" class="error-state">
        <p>{{ error }}</p>
      </div>
      <div v-else-if="bike">
        <Bread_crumb :brand="bike.brand" :product="bike.title" />
        <div class="detail-content">
          <Bike_image_gallery
            :image="bike.image"
            :title="bike.title"
            :additional-images="bike.additionalImages || []"
          />
          <Bike_info
            :bike="bike"
            :formatNumber="formatNumber"
            :getDiscountedPrice="getDiscountedPrice"
            :getSavings="getSavings"
            :getBrandDescription="(brand) => getBrandDescription(brand, bike.description)"
            @addToCart="handleAddToCart(bike)"
            @buyNow="handleBuyNow(bike)"
          />
          <div class="spec-card-wrapper">
            <div class="specifications-section">
              <Bike_specifications :specs="bike.specs" />
              <Reviews_page :product-id="bike.id" @rating-updated="updateBikeRating" />
            </div>
            <Bike_card_sticky
              :bike="bike"
              :image="bike.image"
              class="sticky-card"
              @addToCart="handleAddToCart(bike)"
              @buyNow="handleBuyNow(bike)"
            />
          </div>
          <div class="recommend-section">
            <Bike_suggestion_card
              :products="bikes"
              :current-bike-id="bike.id"
              @add-to-cart="handleAddToCart"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
  <Transition name="fade">
    <div v-if="showToast" class="toast-popup">
      <p>{{ toastMessage }}</p>
    </div>
  </Transition>
</template>

<script setup>
import { ref, watch, reactive, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Navigation_header from '@/components/navigation_header.vue'
import Bread_crumb from '@/components/bread_crumb.vue'
import Bike_image_gallery from '@/components/bike_detail/bike_image_gallery.vue'
import Bike_info from '@/components/bike_detail/bike_info.vue'
import Bike_specifications from '@/components/bike_detail/bike_specifications.vue'
import Reviews_page from '@/components/bike_detail/reviews/reviews_page.vue'
import Bike_card_sticky from '@/components/bike_detail/bike_card_sticky.vue'
import { useCartStore } from '@/stores/cart'
import Bike_suggestion_card from '@/components/bike_detail/bike_suggestion_card.vue'
import { productsApi } from '@/services/api'

const route = useRoute()
const router = useRouter()

const cartStore = useCartStore()

// Reactive variables for products and current bike
const bikes = ref([])
const bike = ref(null)
const loading = ref(true)
const error = ref(null)

// Fetch all products from API
const fetchProducts = async () => {
  try {
    const response = await productsApi.getProducts()
    bikes.value = response.data.data || []
  } catch (err) {
    error.value = 'Failed to load products'
    console.error('Error fetching products:', err)
  }
}

// Fetch single product by ID
const fetchProduct = async (id) => {
  try {
    loading.value = true
    const response = await productsApi.getProduct(id)
    bike.value = response.data
    // Ensure additionalImages is an array
    if (bike.value.additionalImages && typeof bike.value.additionalImages === 'string') {
      try {
        bike.value.additionalImages = JSON.parse(bike.value.additionalImages)
      } catch {
        bike.value.additionalImages = []
      }
    }
    // Handle long data URLs that browsers can't display
    if (
      bike.value.image &&
      typeof bike.value.image === 'string' &&
      bike.value.image.startsWith('data:') &&
      bike.value.image.length > 100000
    ) {
      bike.value.image = ''
    }
    if (Array.isArray(bike.value.additionalImages)) {
      bike.value.additionalImages = bike.value.additionalImages.map((img) => {
        if (
          img.url &&
          typeof img.url === 'string' &&
          img.url.startsWith('data:') &&
          img.url.length > 100000
        ) {
          return { ...img, url: '' }
        }
        return img
      })
    }
  } catch (err) {
    error.value = 'Failed to load product'
    console.error('Error fetching product:', err)
  } finally {
    loading.value = false
  }
}

// Toast state (New)
const showToast = ref(false)
const toastMessage = ref('')
const itemQuantities = reactive({})

// Toast function (New)
const showToastMessage = (message) => {
  toastMessage.value = message
  showToast.value = true
  setTimeout(() => {
    showToast.value = false
    toastMessage.value = ''
  }, 2000)
}

// Handle Add to Cart logic (New and Corrected)
const handleAddToCart = (product) => {
  if (product) {
    const productId = product.id
    // Update local state for the toast message
    itemQuantities[productId] = (itemQuantities[productId] || 0) + 1
    const quantityString = 'x' + itemQuantities[productId].toString().padStart(2, '0')
    const message = `${product.title} added to cart! ${quantityString} 🛒`
    showToastMessage(message)

    // Call the correct Pinia store action
    cartStore.addToCart(productId, 1)
  }
}

// Handle Buy Now logic
const handleBuyNow = async (product) => {
  // First add to cart
  await handleAddToCart(product)

  // Then navigate to checkout
  router.push('/checkout/cart')
}

// Update bike rating when user submits a review
const updateBikeRating = (ratingData) => {
  if (bike.value) {
    bike.value.rating = parseFloat(ratingData.rating.toFixed(1))
    bike.value.reviewCount = ratingData.reviewCount
  }
}

// Watch for route changes and fetch the product
watch(
  () => route.params.id,
  async (newId) => {
    if (newId) {
      await fetchProduct(parseInt(newId))
    }
  },
  { immediate: true },
)

// Fetch products on mount
onMounted(async () => {
  await fetchProducts()
})

// Utility functions
const formatNumber = (number) => {
  return number.toLocaleString()
}

const getDiscountedPrice = (bike) => {
  // Check if bike has no discount
  if (
    !bike ||
    !bike.discount ||
    !Array.isArray(bike.discount) ||
    bike.discount.length === 0 ||
    !bike.discount[0].value
  ) {
    return bike ? bike.price : 0
  }

  const discount = bike.discount[0]
  // Apply percentage discount
  if (discount.type === 'percent') {
    return bike.price - (bike.price * discount.value) / 100
  } else {
    // Apply fixed discount
    return bike.price - discount.value
  }
}

const getSavings = (bike) => {
  if (!bike) return 0
  return bike.price - getDiscountedPrice(bike)
}

const getBrandDescription = (brand, productDescription) => {
  // If we have a product description, use it
  if (productDescription) {
    return productDescription
  }

  // Fallback to brand-specific descriptions
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

.recommend-title {
  font-family: 'Poppins', sans-serif;
  font-size: 24px;
  font-weight: 600;
  color: #111827;
}
.recommend-section {
  display: flex;
  flex-direction: column;
  font-family: 'Poppins', sans-serif;
  border-top: 1px solid #e2e8f0;
}
.specifications-section {
  margin-bottom: 14px;
  width: 100%;
}
.sticky-card {
  position: sticky;
  top: 10rem;
  align-self: flex-start;
  height: fit-content;
}

.spec-card-wrapper {
  display: flex;
  width: 100%;
  gap: 40px;
}

.bike-detail-container {
  min-height: 100vh;
  font-family: 'Poppins', sans-serif;
  margin-top: 14vh;
}

.bike-detail {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
}

.detail-content {
  display: flex;
  flex-direction: column;
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

@media (max-width: 768px) {
  .detail-content {
    gap: 32px;
  }

  .bike-title {
    font-size: 24px;
  }

  .features-grid {
    grid-template-columns: 1fr;
  }

  .action-buttons {
    flex-direction: column;
  }
}
</style>
