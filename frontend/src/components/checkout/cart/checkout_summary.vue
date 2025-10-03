<template>
  <div class="price-summary">
    <h3>Price Summary</h3>

    <div class="stand-delivery">
      <svg
        width="16"
        height="16"
        viewBox="0 0 16 16"
        fill="none"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          fill-rule="evenodd"
          clip-rule="evenodd"
          d="M10.664 2.0212C9.37472 1.44673 7.93428 1.30442 6.55751 1.61548C5.18073 1.92654 3.94139 2.67432 3.02433 3.74729C2.10726 4.82025 1.56161 6.16092 1.46873 7.56933C1.37586 8.97775 1.74076 10.3785 2.50899 11.5626C3.27723 12.7466 4.40765 13.6507 5.73165 14.1398C7.05566 14.629 8.50232 14.677 9.85587 14.2768C11.2094 13.8766 12.3973 13.0496 13.2425 11.9191C14.0876 10.7886 14.5446 9.41522 14.5455 8.00375V7.33507C14.5455 6.93341 14.8711 6.6078 15.2727 6.6078C15.6744 6.6078 16 6.93341 16 7.33507V8.00416C15.999 9.7293 15.4404 11.4083 14.4075 12.79C13.3745 14.1718 11.9226 15.1826 10.2683 15.6717C8.61394 16.1608 6.84581 16.1021 5.22757 15.5042C3.60934 14.9064 2.22772 13.8015 1.28877 12.3542C0.349813 10.907 -0.096167 9.19503 0.0173414 7.47363C0.13085 5.75223 0.797765 4.11364 1.91862 2.80224C3.03948 1.49083 4.55423 0.576885 6.23695 0.196696C7.91967 -0.183493 9.68021 -0.00955144 11.256 0.692579C11.6229 0.856055 11.7878 1.286 11.6243 1.65289C11.4608 2.01978 11.0309 2.18468 10.664 2.0212Z"
          fill="#3491FA"
        />
        <path
          fill-rule="evenodd"
          clip-rule="evenodd"
          d="M15.7671 1.67699C16.0513 1.96087 16.0515 2.42135 15.7676 2.70551L8.4949 9.98551C8.35853 10.122 8.17351 10.1987 7.98056 10.1988C7.78761 10.1988 7.60255 10.1222 7.46612 9.98576L5.2843 7.80395C5.00028 7.51993 5.00028 7.05944 5.2843 6.77543C5.56832 6.49141 6.0288 6.49141 6.31282 6.77543L7.98012 8.44273L14.7386 1.6775C15.0225 1.39334 15.4829 1.39311 15.7671 1.67699Z"
          fill="#3491FA"
        />
      </svg>
      <span>Standard delivery by {{ deliveryDate }}</span>
    </div>

    <div class="promo-section">
      <label for="promo">Discounted Code</label>
      <div class="promo-input">
        <Icon icon="mdi:coupon" class="input-icon left-icon" />
        <input
          type="text"
          id="promo"
          :value="promoCode"
          @input="$emit('update:promoCode', $event.target.value)"
          placeholder="Enter your promo code"
        />
        <Icon
          :icon="promoIcon"
          :class="['input-icon right-icon', { 'valid-icon': isPromoValid }]"
        />
      </div>
    </div>

    <div class="summary-calculations">
      <div class="summary-row MRP">
        <span>MRP</span>
        <span>${{ (totalMRP || 0).toFixed(2) }}</span>
      </div>
      <div v-if="isPromoValid" class="summary-row discount">
        <span>Discount</span>
        <span class="discount-price">-${{ (discountAmount || 0).toFixed(2) }}</span>
      </div>
      <div class="summary-row net-price">
        <span>Net Price</span>
        <span>${{ (netPrice || 0).toFixed(2) }}</span>
      </div>
      <div class="summary-row shipping">
        <span>Shipping Charges</span>
        <span>${{ (shippingAmount || 0).toFixed(2) }}</span>
      </div>
      <div class="summary-row total">
        <span>Total Amount</span>
        <span>${{ (finalTotal || 0).toFixed(2) }}</span>
      </div>
      <button v-if="showCheckoutBtn" @click="handleCheckout" class="checkout-btn">
        Processed to Checkout
      </button>
    </div>
    <div class="checkout-features">
      <div class="feature">Secure Payment</div>
      <div class="feature">Fast Delivery</div>
      <div class="feature">24/7 Support</div>
    </div>
  </div>
</template>

<script setup>
import router from '@/router'
import { Icon } from '@iconify/vue'
import { defineProps, defineEmits, computed, watch, onMounted } from 'vue'
import { useRoute } from 'vue-router'

const props = defineProps({
  cartItems: {
    type: Array,
    required: true,
  },
  promoCode: {
    type: String,
    required: true,
  },
})

const route = useRoute()
const emit = defineEmits([
  'update:promo-code',
  'proceedToCheckout',
  'update:finalTotal',
  'update:summaryBreakdown',
])

// Constants
const CORRECT_PROMO = 'BOOKRIDE50'
const PROMO_DISCOUNT = 5.0
const SHIPPING_AMOUNT = 2.0

// Delivery date
const deliveryDate = computed(() => {
  const date = new Date()
  date.setDate(date.getDate() + 3)
  const options = { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' }
  return date.toLocaleDateString('en-US', options)
})

// Promo code validation
const isPromoValid = computed(() => {
  return props.promoCode.trim().toUpperCase() === CORRECT_PROMO
})

// Promo icon
const promoIcon = computed(() => {
  return isPromoValid.value
    ? 'fluent:checkbox-checked-20-filled'
    : 'fluent:checkbox-checked-20-regular'
})

// Discount amount
const discountAmount = computed(() => {
  return isPromoValid.value ? PROMO_DISCOUNT : 0
})

// Total MRP
const totalMRP = computed(() => {
  return (props.cartItems || []).reduce((total, item) => {
    const price = item.originalPrice || item.price
    return total + price * item.quantity
  }, 0)
})

// Net price
const netPrice = computed(() => {
  const calculatedNet = totalMRP.value - discountAmount.value
  return calculatedNet > 0 ? calculatedNet : 0
})

// Shipping amount
const shippingAmount = computed(() => SHIPPING_AMOUNT)

// Final total (net price + shipping)
const finalTotal = computed(() => netPrice.value + SHIPPING_AMOUNT)

// Hide checkout button on certain routes
const hiddenRoutes = ['/checkout/address', '/checkout/payment', '/checkout/purchase']
const showCheckoutBtn = computed(() => !hiddenRoutes.includes(route.path))

const navigationAndScroll = (path) => {
  router.push(path).then(() => {
    setTimeout(() => {
      window.scrollTo(0, 0)
    }, 100)
  })
}

const handleCheckout = () => {
  emit('proceedToCheckout') // emit the event
  navigationAndScroll('/checkout/address')
}

// Complete summary breakdown for invoice
const summaryBreakdown = computed(() => {
  return {
    totalMRP: totalMRP.value,
    discountAmount: discountAmount.value,
    netPrice: netPrice.value,
    shippingAmount: SHIPPING_AMOUNT,
    finalTotal: finalTotal.value,
    promoCode: props.promoCode,
    promoDiscount: isPromoValid.value ? PROMO_DISCOUNT : 0,
  }
})

// Emit final total and breakdown whenever they change
watch(finalTotal, (newVal) => {
  emit('update:finalTotal', newVal)
  emit('update:summaryBreakdown', summaryBreakdown.value)
})

// Emit initial values when component mounts
onMounted(() => {
  emit('update:finalTotal', finalTotal.value)
  emit('update:summaryBreakdown', summaryBreakdown.value)
})
</script>

<style scoped>
.discount-price {
  color: #72c15e;
}
.net-price {
  margin-top: 15px;
  padding-top: 15px;
  border-top: 1px solid #ddd;
}
.net-price span {
  color: black;
  font-size: 14px;
}
.summary-calculations {
  padding: 10px 16px 14px 16px;
  background-color: #f9fafb;
  border-radius: 8px;
}
.stand-delivery {
  background-color: #e8f7ff;
  display: flex;
  align-items: center;
  font-size: 12px;
  padding: 6px 10px;
  border-radius: 6px;
  gap: 10px;
  color: #23272f;
}

.price-summary {
  height: fit-content;
}

.price-summary h3 {
  margin: 0 0 20px 0;
  color: #333;
  font-size: 18px;
  font-weight: 600;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
  font-size: 14px;
  color: #464646;
}

.summary-row.shipping {
  margin-bottom: 15px;
  padding-bottom: 15px;
  color: #07828b;
  border-bottom: 1px solid #ddd;
}

.summary-row.total {
  font-weight: 600;
  font-size: 14px;
  color: #333;
  margin: 20px 0;
}

.promo-section {
  margin: 20px 0;
}

.promo-section label {
  display: block;
  margin-bottom: 8px;
  font-size: 14px;
  color: #23272f;
}

.promo-input {
  position: relative;
  display: flex;
  align-items: center;
}

.promo-input input {
  width: 100%;
  padding: 10px 40px; /* Add padding for the icons */
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 14px;
}

.promo-input input:focus {
  border: 1px solid #ddd;
  outline: none;
}

.input-icon {
  position: absolute;
  font-size: 18px;
  color: #999;
  transition:
    color 0.3s ease,
    transform 0.3s ease;
}

.left-icon {
  left: 10px;
}

.right-icon {
  right: 10px;
}

.valid-icon {
  color: #3491fa;
  transform: scale(1.1);
}

.checkout-btn {
  font-family: 'Poppins', sans-serif;
  width: 100%;
  background: #e8fffb;
  color: black;
  border: none;
  padding: 15px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 15px;
  border: 1px solid #14c9c9;
}

.checkout-features {
  display: flex;
  justify-content: space-around;
  gap: 8px;
  margin-top: 8px;
}

.feature {
  font-size: 12px;
  color: #666;
  display: flex;
  align-items: center;
  gap: 8px;
}
</style>
