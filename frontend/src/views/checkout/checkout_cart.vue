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
  <div class="cart-container">
    <div class="cart-content">
      <div class="cart-items-section">
        <Indecator_process />
        <Cart_item_card
          v-for="item in safeCartItems"
          :key="item.id"
          :item="item"
          @increase-quantity="increaseQuantity"
          @decrease-quantity="decreaseQuantity"
          @remove-item="removeItem"
        />
        <Bike_suggestion_card :products="allBikes" @add-to-cart="addToCart" />
      </div>
      <Checkout_summary
        :cart-items="safeCartItems"
        :promo-code="promoCode"
        @update:promo-code="updatePromoCode"
        @proceed-to-checkout="proceedToCheckout"
        class="checkout-summary-sticky"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import Navigation_header from '@/components/navigation_header.vue'
import Indecator_process from '@/components/checkout/cart/indecator_process.vue'
import Cart_item_card from '@/components/checkout/cart/cart_item_card.vue'
import Bike_suggestion_card from '@/components/bike_detail/bike_suggestion_card.vue'
import Checkout_summary from '@/components/checkout/cart/checkout_summary.vue'
import { productsApi } from '@/services/api'

const router = useRouter()
const route = useRoute()
const promoCode = ref('')
const cartStore = useCartStore()
const allBikes = ref([])

// Add safety check for cartItems
const safeCartItems = computed(() => cartStore.items || [])

// Watch for route changes to clear promo code when leaving checkout
watch(
  () => route.path,
  (newPath) => {
    const checkoutRoutes = [
      '/checkout/cart',
      '/checkout/address',
      '/checkout/payment',
      '/checkout/purchase',
    ]
    if (!checkoutRoutes.includes(newPath)) {
      promoCode.value = ''
      localStorage.removeItem('checkoutPromoCode')
    }
  },
)

const increaseQuantity = async (item) => {
  const newQuantity = item.quantity + 1
  await cartStore.updateCartItem(item.id, newQuantity)
}

const decreaseQuantity = async (item) => {
  if (item.quantity > 1) {
    const newQuantity = item.quantity - 1
    await cartStore.updateCartItem(item.id, newQuantity)
  }
}

const removeItem = async (itemId) => {
  await cartStore.removeFromCart(itemId)
}

const addToCart = async (bike) => {
  try {
    await cartStore.addToCart(bike.id, 1)
  } catch (error) {
    console.error('Error adding to cart:', error)
  }
}

const updatePromoCode = (newCode) => {
  promoCode.value = newCode
}

const proceedToCheckout = () => {
  router.push('/checkout/address')
}

// Fetch products for suggestions
const fetchProducts = async () => {
  try {
    const response = await productsApi.getProducts()
    allBikes.value = response.data.data || []
  } catch (err) {
    console.error('Error fetching products:', err)
  }
}

onMounted(() => {
  fetchProducts()
  cartStore.fetchCart()

  // Load saved promo code
  const savedPromoCode = localStorage.getItem('checkoutPromoCode')
  if (savedPromoCode) {
    promoCode.value = savedPromoCode
  }
})
</script>

<style scoped>
.cart-container {
  max-width: 1350px;
  margin: 0 auto;
  padding: 20px;
  margin-top: 150px;
  font-family: 'Poppins', sans-serif;
}

.cart-content {
  display: grid;
  grid-template-columns: 1fr 390px;
  gap: 30px;
}

.cart-items-section {
  display: flex;
  flex-direction: column;
  max-width: 879px;
  border-right: 1px solid #d9d9d9;
  padding-right: 50px;
}
.checkout-summary-sticky {
  position: sticky;
  top: 10rem;
  align-self: flex-start;
  height: fit-content;
}
@media (max-width: 768px) {
  .cart-content {
    grid-template-columns: 1fr;
  }
}
</style>
