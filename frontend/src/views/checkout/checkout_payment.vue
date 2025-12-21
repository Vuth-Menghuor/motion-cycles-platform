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

  <div class="checkout-payment-container">
    <div class="checkout-content">
      <div class="payment-section">
        <Indecator_process :currentStep="3" />
        <div class="payment-header">
          <h2>Choose Payment Method</h2>
        </div>
        <div class="payment-list">
          <Payment_card
            v-for="payment in payments"
            :key="payment.id"
            :payment="payment"
            :is-selected="selectedPaymentId === payment.id"
            @select="selectPayment"
          />
        </div>

        <div class="checkout-actions">
          <button @click="returnToAddress" class="btn btn-secondary">Return to address</button>
          <button @click="continueToPayment" class="btn btn-primary" :disabled="!selectedPaymentId">
            Continue with shipping
          </button>
        </div>
      </div>

      <Checkout_summary
        :cart-items="cartItems"
        :promo-code="promoCode"
        @update:promo-code="updatePromoCode"
        class="checkout-summary-sticky"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useCartStore } from '@/stores/cart'

import Navigation_header from '@/components/navigation_header.vue'
import Indecator_process from '@/components/checkout/cart/indecator_process.vue'
import Checkout_summary from '@/components/checkout/cart/checkout_summary.vue'
import Payment_card from '@/components/checkout/payment/payment_card.vue'

import image1 from '@/assets/images/payment_method/bakong_khqr.png'
import image2 from '@/assets/images/payment_method/credit_card.png'

const router = useRouter()
const route = useRoute()
const cartStore = useCartStore()
const cartItems = computed(() => cartStore.items)

const promoCode = ref('')
const selectedPaymentId = ref(1)

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

const payments = ref([
  {
    id: 1,
    name: 'Bakong KHQR',
    image: image1,
  },
  {
    id: 2,
    name: 'Credit Card/Debit Card',
    image: image2,
  },
])

// Load promo code from localStorage on mount
onMounted(() => {
  const savedPromoCode = localStorage.getItem('checkoutPromoCode')
  if (savedPromoCode) {
    promoCode.value = savedPromoCode
  }
})

const updatePromoCode = (newCode) => {
  promoCode.value = newCode
}

const returnToAddress = () => {
  navigationAndScroll('/checkout/address')
}

const continueToPayment = () => {
  if (selectedPaymentId.value) {
    // Save selected payment method to localStorage
    const selectedPayment = payments.value.find((p) => p.id === selectedPaymentId.value)
    if (selectedPayment) {
      localStorage.setItem('selectedPaymentMethod', JSON.stringify(selectedPayment))
    }
    navigationAndScroll('/checkout/purchase')
  }
}

const navigationAndScroll = (path) => {
  router.push(path).then(() => {
    setTimeout(() => {
      window.scrollTo(0, 0)
    }, 100)
  })
}

const selectPayment = (payment) => {
  selectedPaymentId.value = payment.id
}
</script>

<style scoped>
.checkout-payment-container {
  max-width: 1350px;
  margin: 0 auto;
  padding: 20px;
  margin-top: 150px;
  font-family: 'Poppins', sans-serif;
}

.payment-header {
  display: flex;
  justify-content: flex-start;
  margin-bottom: 24px;
}

.payment-header h2 {
  font-size: 18px;
  font-weight: 500;
  color: #2d2d2d;
  margin: 0;
}

.checkout-content {
  display: grid;
  grid-template-columns: 1fr 390px;
  gap: 30px;
  margin-bottom: 60px;
}

.payment-section {
  max-width: 1000px;
  border-right: 1px solid #d9d9d9;
  padding-right: 50px;
}

.payment-list {
  margin-bottom: 40px;
}

.checkout-actions {
  display: flex;
  gap: 16px;
}

.btn {
  padding: 14px 32px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  border: none;
  font-family: 'Poppins', sans-serif;
}

.btn-secondary {
  background: white;
  color: #2d2d2d;
  border: 1px solid #d1d1d1;
}

.btn-secondary:hover {
  background: #f5f5f5;
}

.btn-primary {
  background: #e8fffb;
  color: #2d2d2d;
  border: 1px solid #14c9c9;
}

.btn-primary:hover {
  background: #b5f3e7;
}

.btn-primary:disabled {
  background: #f5f5f5;
  color: #a0a0a0;
  cursor: not-allowed;
}

.checkout-summary-sticky {
  position: sticky;
  top: 10rem;
  align-self: flex-start;
  height: fit-content;
}

/* Responsive */
@media (max-width: 768px) {
  .checkout-content {
    grid-template-columns: 1fr;
  }

  .payment-section {
    border-right: none;
    padding-right: 0;
  }

  .checkout-actions {
    flex-direction: column;
  }
}
</style>
