<template>
  <div class="payment-success">
    <div class="success-icon">
      <svg viewBox="0 0 24 24" class="checkmark">
        <path fill="none" stroke="white" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
    </div>

    <h2 class="success-title">Payment successful!</h2>
    <p class="success-subtitle">
      The ordered confirmation has been sent to <strong>{{ recipient }}</strong>
    </p>

    <div class="order-card">
      <div v-for="item in cartItems" :key="item.id" class="order-item">
        <img :src="item.image" :alt="item.name || item.title" class="order-item-image" />
        <div class="order-item-details">
          <p class="order-item-name">{{ item.name || item.title }}</p>
          <p class="order-item-brand">{{ item.subtitle }}</p>
          <p class="order-item-color">Color: {{ item.color }}</p>
          <p class="order-item-quantity">Quantity: {{ item.quantity }}</p>
          <div class="order-item-price-container">
            <span v-if="hasDiscount(item)" class="original-price"
              >${{ formatPrice(item.price) }}</span
            >
            <span class="current-price">${{ formatPrice(getDiscountedPrice(item)) }}</span>
          </div>
        </div>
      </div>

      <div class="order-summary">
        <div class="summary-row">
          <span>Transaction Date</span>
          <span>{{ transactionDate }}</span>
        </div>
        <div class="summary-row">
          <span>Payment Method</span>
          <span>{{ paymentMethod }}</span>
        </div>
        <div class="summary-row" v-if="discountAmount > 0">
          <span>Discount</span>
          <span class="discount">-${{ discountAmount.toFixed(2) }}</span>
        </div>
        <div class="summary-row">
          <span>Net Price</span>
          <span>${{ netPrice.toFixed(2) }}</span>
        </div>
        <div class="summary-row">
          <span>Shipping Charge</span>
          <span>${{ shippingCharge.toFixed(2) }}</span>
        </div>
        <hr />
        <div class="summary-row total">
          <span>Total Price</span>
          <span>${{ totalPrice.toFixed(2) }}</span>
        </div>
      </div>

      <div class="order-actions">
        <button class="btn-download" @click="downloadTransaction">Download Transaction</button>
        <button class="btn-continue" @click="continueShopping">Continue Shopping</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { storeToRefs } from 'pinia'

const props = defineProps({
  recipient: {
    type: String,
    default: 'MOTIONCYCLE',
  },
  promoCode: {
    type: String,
    default: '',
  },
})

const router = useRouter()
const cartStore = useCartStore()
const { cartItems } = storeToRefs(cartStore)

// Constants
const CORRECT_PROMO = 'BOOKRIDE50'
const PROMO_DISCOUNT = 1250.0
const SHIPPING_AMOUNT = 5.0

// Helper functions for discount calculation (same as cart_item_card)
const hasDiscount = (item) => {
  return item.discount && (item.discount.type === 'percent' || item.discount.type === 'fixed')
}

const getDiscountedPrice = (item) => {
  if (!hasDiscount(item)) {
    return item.price
  }

  if (item.discount.type === 'percent') {
    return item.price - (item.price * item.discount.value) / 100
  } else if (item.discount.type === 'fixed') {
    return item.price - item.discount.value
  }

  return item.price
}

const formatPrice = (price) => {
  return price.toLocaleString()
}

// Computed properties for dynamic calculations
const totalMRP = computed(() => {
  return cartItems.value.reduce((total, item) => {
    // Use original price for items with discount, discounted price for items without
    const priceToUse = hasDiscount(item) ? item.price : getDiscountedPrice(item)
    return total + priceToUse * item.quantity
  }, 0)
})

const itemDiscountAmount = computed(() => {
  return cartItems.value.reduce((total, item) => {
    if (!hasDiscount(item)) return total

    const originalPrice = item.price
    const discountedPrice = getDiscountedPrice(item)
    const itemDiscount = (originalPrice - discountedPrice) * item.quantity

    return total + itemDiscount
  }, 0)
})

const promoDiscountAmount = computed(() => {
  return props.promoCode.trim().toUpperCase() === CORRECT_PROMO ? PROMO_DISCOUNT : 0
})

const discountAmount = computed(() => {
  return itemDiscountAmount.value + promoDiscountAmount.value
})

const netPrice = computed(() => {
  const calculatedNet = totalMRP.value - discountAmount.value
  return calculatedNet > 0 ? calculatedNet : 0
})

const totalPrice = computed(() => netPrice.value + SHIPPING_AMOUNT)

const shippingCharge = computed(() => SHIPPING_AMOUNT)

const transactionDate = computed(() => {
  const date = new Date()
  const options = {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  }
  return date.toLocaleDateString('en-US', options)
})

const paymentMethod = 'Bakong KHQR'

const downloadTransaction = () => {
  // Generate transaction receipt data
  const transactionData = {
    date: transactionDate.value,
    items: cartItems.value.map((item) => ({
      name: item.name || item.title,
      brand: item.subtitle,
      color: item.color,
      quantity: item.quantity,
      originalPrice: hasDiscount(item) ? item.price : null,
      finalPrice: getDiscountedPrice(item),
      discount: item.discount || null,
    })),
    summary: {
      subtotal: totalMRP.value,
      itemDiscounts: itemDiscountAmount.value,
      promoDiscount: promoDiscountAmount.value,
      totalDiscount: discountAmount.value,
      netPrice: netPrice.value,
      shipping: shippingCharge.value,
      total: totalPrice.value,
    },
    paymentMethod: paymentMethod,
    recipient: props.recipient,
  }

  // Create and download JSON file
  const dataStr = JSON.stringify(transactionData, null, 2)
  const dataBlob = new Blob([dataStr], { type: 'application/json' })
  const url = URL.createObjectURL(dataBlob)
  const link = document.createElement('a')
  link.href = url
  link.download = `transaction_${Date.now()}.json`
  link.click()
  URL.revokeObjectURL(url)
}

const continueShopping = () => {
  // Clear the cart after successful purchase
  cartStore.clearCart()
  // Navigate to home page
  router.push('/')
}
</script>

<style scoped>
.payment-success {
  text-align: center;
  padding: 2rem;
  font-family: 'Poppins', sans-serif;
}

.success-icon {
  background-color: #4caf50;
  border-radius: 50%;
  display: inline-flex;
  padding: 1rem;
  margin-bottom: 1rem;
}

.checkmark {
  width: 2rem;
  height: 2rem;
}

.success-title {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
  color: #333;
}

.success-subtitle {
  color: #555;
  margin-bottom: 2rem;
}

.order-card {
  max-width: 600px;
  margin: auto;
  border: 1px solid #eee;
  border-radius: 8px;
  padding: 1.5rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.order-item {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #f0f0f0;
}

.order-item:last-of-type {
  border-bottom: none;
  margin-bottom: 1.5rem;
}

.order-item-image {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 8px;
  flex-shrink: 0;
}

.order-item-details {
  text-align: left;
  flex: 1;
}

.order-item-name {
  font-weight: 600;
  margin-bottom: 0.25rem;
  color: #333;
  font-size: 14px;
}

.order-item-brand {
  color: #666;
  font-size: 12px;
  margin-bottom: 0.25rem;
}

.order-item-color {
  color: #666;
  font-size: 12px;
  margin-bottom: 0.25rem;
}

.order-item-quantity {
  color: #666;
  font-size: 12px;
  margin-bottom: 0.5rem;
}

.order-item-price-container {
  display: flex;
  gap: 8px;
  align-items: center;
}

.original-price {
  text-decoration: line-through;
  color: #999;
  font-size: 12px;
}

.current-price {
  font-weight: 600;
  color: #e74c3c;
  font-size: 14px;
}

.order-summary {
  margin-top: 1rem;
  text-align: left;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
  font-size: 14px;
}

.summary-row.total {
  font-weight: bold;
  font-size: 16px;
  color: #333;
}

.discount {
  color: #4caf50;
  font-weight: 500;
}

hr {
  margin: 1rem 0;
  border: none;
  border-top: 1px solid #eee;
}

.order-actions {
  display: flex;
  gap: 1rem;
  margin-top: 1.5rem;
  justify-content: center;
  flex-wrap: wrap;
}

.btn-download,
.btn-continue {
  padding: 0.75rem 1.5rem;
  border-radius: 6px;
  border: none;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 14px;
}

.btn-download {
  background-color: #4a90e2;
  color: white;
}

.btn-download:hover {
  background-color: #357abd;
}

.btn-continue {
  background-color: #1abc9c;
  color: white;
}

.btn-continue:hover {
  background-color: #16a085;
}

@media (max-width: 768px) {
  .order-actions {
    flex-direction: column;
  }

  .order-item {
    flex-direction: column;
    text-align: center;
  }

  .order-item-details {
    text-align: center;
  }
}
</style>
