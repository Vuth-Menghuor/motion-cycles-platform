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

  <div class="checkout-purchase-container">
    <!-- Main Checkout Content - Always Visible -->
    <div class="checkout-content">
      <div class="purchase-section">
        <Indecator_process :currentStep="4" />

        <div class="purchase-header">
          <h2>Review & Complete Purchase</h2>
          <div v-if="paymentStatus === 'processing'" class="status-badge processing">
            Processing Payment...
          </div>
          <div v-if="paymentStatus === 'failed'" class="status-badge failed">Payment Failed</div>
          <div v-if="paymentStatus === 'success'" class="status-badge success">
            Payment Successful!
          </div>
        </div>

        <div class="purchase-list">
          <Purschase_section @update:formData="updateFormData" />
        </div>

        <div class="checkout-actions" v-if="!khqrData">
          <button @click="returnToPayment" class="btn btn-secondary">Return to Payment</button>
          <button
            @click="handlePurchase"
            :disabled="
              !formData.buyerName || !formData.buyerPhone || !formData.payment || isProcessing
            "
            class="btn btn-primary"
          >
            <span v-if="isProcessing">Processing...</span>
            <span v-else>Complete Purchase - ${{ purchaseAmount.toFixed(2) }}</span>
          </button>
        </div>

        <!-- KHQR Payment Modal -->
        <div v-if="khqrData && paymentStatus === 'processing'" class="bakong-payment-modal">
          <div class="bakong-payment-content">
            <div class="payment-header">
              <h3>Complete Payment</h3>
              <button @click="cancelPayment" class="close-btn">×</button>
            </div>

            <div class="qr-section">
              <div class="khqr-card">
                <div class="khqr-header">
                  <img
                    src="@/assets/images/payment_method/bakong_khqr_white.png"
                    alt="KHQR Logo"
                    class="khqr-logo"
                  />
                </div>
                <div class="khqr-merchant">
                  <div class="merchant-name">MOTION CYCLE</div>
                  <div class="amount-display">${{ totalAmount.toFixed(2) }}</div>
                </div>
                <div class="qr-code-container">
                  <img
                    :src="khqrQrImageUrl"
                    alt="KHQR Code"
                    class="qr-code"
                    @error="(e) => (e.target.style.display = 'none')"
                  />
                </div>
              </div>
              <p class="qr-instruction">Scan with your banking app</p>
            </div>

            <div class="payment-status" v-if="khqrData?.tracking_enabled">
              <div v-if="pollingAttempts === 0" class="status-waiting">
                <div class="status-icon">📱</div>
                <p>Waiting for payment...</p>
              </div>
              <div v-else class="status-checking">
                <div class="spinner-small"></div>
                <p>Detecting payment ({{ pollingAttempts }}/{{ maxPollingAttempts }})</p>
              </div>
            </div>

            <div class="payment-actions">
              <button
                @click="confirmPayment"
                class="btn btn-primary"
                v-if="showManualConfirmation || !khqrData?.tracking_enabled"
              >
                I've Completed Payment
              </button>
            </div>
          </div>
        </div>
      </div>

      <Checkout_summary
        :cart-items="cartItems"
        :promo-code="promoCode"
        @update:promo-code="updatePromoCode"
        @update:finalTotal="updateTotalAmount"
        @update:summaryBreakdown="updateSummaryBreakdown"
        class="checkout-summary-sticky"
      />
    </div>

    <!-- Success Overlay - Appears on top of everything -->
    <div v-if="paymentStatus === 'success'" class="payment-success">
      <div class="success-content">
        <div class="success-icon">
          <svg viewBox="0 0 24 24" class="checkmark">
            <path fill="none" stroke="white" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>
        <h3>Payment Successful!</h3>
        <p>Your order has been confirmed</p>
        <div class="loading-dots">
          <span>Redirecting to order summary</span>
          <div class="dots">
            <span></span>
            <span></span>
            <span></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
/**
 * Checkout Purchase Page - Final Step
 *
 * This is the final checkout step where customers:
 * 1. Review their order details
 * 2. Complete payment using Bakong KHQR
 * 3. Get real-time payment confirmation
 *
 * Features:
 * - Bakong KHQR integration with real banking apps
 * - Automatic payment detection (no manual confirmation needed)
 * - Real-time payment status updates
 * - Order processing and invoice generation
 */

import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { storeToRefs } from 'pinia'

// Bakong Payment API Services
import {
  generateKHQRIndividualWithImage, // Generate QR codes for payments
  checkPaymentStatus, // Check if payment completed
} from '@/services/khqr'

// UI Components
import Navigation_header from '@/components/navigation_header.vue'
import Indecator_process from '@/components/checkout/cart/indecator_process.vue'
import Checkout_summary from '@/components/checkout/cart/checkout_summary.vue'
import Purschase_section from '@/components/checkout/payment/purschase_section.vue'

// ============================================
// STORE & ROUTER SETUP
// ============================================
const router = useRouter()
const cartStore = useCartStore()
const { cartItems } = storeToRefs(cartStore)

// ============================================
// FORM DATA & PURCHASE INFO
// ============================================
const promoCode = ref('BOOKRIDE50') // Promo code for discounts
const formData = ref({
  buyerName: '', // Customer name
  buyerPhone: '', // Customer phone
  payment: null, // Selected payment method
})

const purchaseAmount = ref(0) // Total amount to pay
const isProcessing = ref(false) // Loading state during payment
const paymentStatus = ref('pending') // pending | processing | success | failed

// ============================================
// BAKONG KHQR PAYMENT DATA
// ============================================
const khqrData = ref(null) // QR code data from API
const khqrQrImageUrl = ref('') // Generated QR image URL for display

// ============================================
// AUTOMATIC PAYMENT DETECTION
// ============================================
const pollingAttempts = ref(0) // Current check attempt
const maxPollingAttempts = 20 // Max attempts (10 minutes at 30-second intervals)
const pollingInterval = ref(null) // Timer for periodic checks
const showManualConfirmation = ref(false) // Show manual button if auto-detection fails

// QR code image generator helper
const generateQRImageUrl = (qrString) => {
  const encodedText = encodeURIComponent(qrString)
  return `https://api.qrserver.com/v1/create-qr-code/?size=400x400&data=${encodedText}`
}

// Total amount from cart (reactive)
const totalAmount = computed(() => {
  // Calculate total from cart items (using already discounted prices)
  const cartTotal = cartItems.value.reduce((total, item) => {
    return total + item.price * item.quantity
  }, 0)

  // Check if promo code is valid
  let discount = 0
  if (promoCode.value.trim().toUpperCase() === 'BOOKRIDE50') {
    discount = 5.0
  } else {
    discount = 0
  }

  // Ensure net price is not negative
  const netPrice = Math.max(cartTotal - discount, 0)

  // Add shipping cost
  const shipping = 2.0
  return netPrice + shipping
})

// Keep purchaseAmount in sync
watch(
  totalAmount,
  (newVal) => {
    purchaseAmount.value = newVal
  },
  { immediate: true },
)

// Update handlers
const updateFormData = (data) => {
  formData.value = data
}
const updatePromoCode = (newCode) => {
  promoCode.value = newCode
}

const navigationAndScroll = (path) => {
  router.push(path).then(() => {
    setTimeout(() => {
      window.scrollTo(0, 0)
    }, 100)
  })
}

// Return to payment page
const returnToPayment = () => {
  navigationAndScroll('/checkout/payment')
}

// ============================================
// MAIN PURCHASE HANDLER
// ============================================
/**
 * Handle the final purchase submission
 * 1. Validates customer information
 * 2. Generates KHQR payment code
 * 3. Starts automatic payment detection
 */
const handlePurchase = async () => {
  // Step 1: Validate required fields
  if (!formData.value.buyerName || !formData.value.buyerPhone) {
    alert('Please fill in all required fields')
    return
  }
  if (!formData.value.payment) {
    alert('Please select a payment method')
    return
  }

  // Step 2: Process Bakong KHQR Payment
  if (formData.value.payment.name === 'Bakong KHQR') {
    isProcessing.value = true
    paymentStatus.value = 'processing'

    try {
      // Prepare payment request data
      const requestData = {
        bakong_account: 'vuth_menghuor@aclb', // Merchant's Bakong account
        account_name: 'MOTION CYCLE', // Display name for customer
        amount: totalAmount.value, // Total amount to pay
        currency: 'USD', // Payment currency (USD/KHR)
        track_payment: true, // Enable real-time payment detection
        include_image: false, // Generate QR image URL separately for better control
      }

      // Call API to generate KHQR
      const khqrResponse = await generateKHQRIndividualWithImage(requestData)

      // Check if API response is successful
      if (khqrResponse && khqrResponse.success) {
        // Get the QR string from the response
        const qrString = khqrResponse.data?.qr_string

        if (qrString) {
          khqrData.value = {
            ...khqrResponse.data,
            tracking_enabled: !!khqrResponse.data.md5,
          }
          // Generate QR code image from the QR string
          khqrQrImageUrl.value = generateQRImageUrl(qrString)

          // Start automatic payment detection if MD5 is available
          if (khqrResponse.data.md5) {
            startPaymentPolling(khqrResponse.data.md5)
          } else {
            showManualConfirmation.value = true
          }
        } else {
          throw new Error('QR string not found in API response')
        }
      } else {
        const errorMsg = khqrResponse?.message || khqrResponse?.error || 'Failed to generate KHQR'
        throw new Error(errorMsg)
      }
    } catch (error) {
      let errorMessage = 'Unknown error occurred'

      // Handle different types of errors
      if (error.response?.data?.error) {
        errorMessage = error.response.data.error
      } else if (error.response?.data?.message) {
        errorMessage = error.response.data.message
      } else if (error.message) {
        errorMessage = error.message
      } else {
        // Keep default message
      }

      // Special handling for common API errors
      if (errorMessage.includes('403') || errorMessage.includes('CloudFront')) {
        errorMessage =
          'API Configuration Error: Please check if Bakong API is properly configured for production mode.'
      } else if (errorMessage.includes('timeout')) {
        errorMessage = 'Connection timeout: Please check your internet connection and try again.'
      } else if (errorMessage.includes('Network Error')) {
        errorMessage = 'Network error: Cannot connect to payment service. Please try again.'
      } else {
        // Keep the error message as is
      }

      alert(
        `❌ Failed to generate payment QR code:\n\n${errorMessage}\n\nPlease try again or contact support if the problem persists.`,
      )
      paymentStatus.value = 'failed'
    } finally {
      isProcessing.value = false
    }
  } else {
    // Handle other payment methods here if needed
    alert('Payment method not yet implemented')
  }
}

// Confirm payment manually (for demo purposes)
const confirmPayment = () => {
  paymentStatus.value = 'success'

  // Store order data for invoice
  localStorage.setItem(
    'lastOrderData',
    JSON.stringify({
      cartItems: cartItems.value,
      formData: formData.value,
      totalAmount: totalAmount.value,
      promoCode: promoCode.value,
      paymentMethod: 'Bakong KHQR',
      timestamp: new Date().toISOString(),
      paymentType: 'manual_confirmation',
      summaryBreakdown: summaryBreakdown.value, // Include detailed breakdown
    }),
  )

  // Clear cart
  cartStore.clearCart()

  // Navigate to summary
  setTimeout(() => {
    // Keep the success modal visible during navigation
    router.push('/checkout/purchase/summary')
  }, 5000)
}

// Payment detection polling
const startPaymentPolling = (md5) => {
  // Check if MD5 hash is available for tracking
  if (!md5) {
    console.warn('No MD5 hash available for payment tracking')
    showManualConfirmation.value = true
    return
  }

  pollingAttempts.value = 0 // Start at 0 - will increment only when we actually check
  showManualConfirmation.value = false

  const checkPayment = async () => {
    try {
      // Only increment attempts when we actually start checking for payments
      pollingAttempts.value++

      const result = await checkPaymentStatus(md5)

      // Check if payment is completed
      if (result.success && result.payment_status === 'completed') {
        stopPaymentPolling()
        paymentStatus.value = 'success'

        // Store complete order data and navigate to summary
        localStorage.setItem(
          'lastOrderData',
          JSON.stringify({
            cartItems: cartItems.value,
            formData: formData.value,
            totalAmount: totalAmount.value,
            promoCode: promoCode.value,
            paymentMethod: 'Bakong KHQR',
            timestamp: new Date().toISOString(),
            paymentType: 'automatic_detection',
            summaryBreakdown: summaryBreakdown.value, // Include detailed breakdown
          }),
        )

        // Clear cart after successful payment
        cartStore.clearCart()

        // Navigate to success page after 5 second delay
        setTimeout(() => {
          // Keep the success modal visible during navigation
          router.push('/checkout/purchase/summary')
        }, 5000)
        return
      }

      // Continue polling if not completed and within limits
      if (pollingAttempts.value < maxPollingAttempts) {
        pollingInterval.value = setTimeout(checkPayment, 30000) // Check every 30 seconds
      } else {
        showManualConfirmation.value = true
      }
    } catch (error) {
      console.error('💥 Payment polling error:', error)
      showManualConfirmation.value = true
    }
  }

  // Start the first check after giving user time to scan QR (30 seconds)

  pollingInterval.value = setTimeout(checkPayment, 30000)
}

const stopPaymentPolling = () => {
  if (pollingInterval.value) {
    clearTimeout(pollingInterval.value)
    pollingInterval.value = null
  }
}

// Cancel payment
const cancelPayment = () => {
  stopPaymentPolling()
  khqrData.value = null
  khqrQrImageUrl.value = ''
  paymentStatus.value = 'pending'
  pollingAttempts.value = 0
}

// Store summary breakdown for invoice
const summaryBreakdown = ref(null)

// Update purchase amount from summary
const updateTotalAmount = (newTotal) => {
  purchaseAmount.value = newTotal
}

// Update summary breakdown for invoice
const updateSummaryBreakdown = (breakdown) => {
  summaryBreakdown.value = breakdown
}
</script>

<style scoped>
.bakong-payment-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  backdrop-filter: blur(3px);
}

.bakong-payment-content {
  background: white;
  border-radius: 20px;
  padding: 32px;
  max-width: 380px;
  width: 90%;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  text-align: center;
  position: relative;
  animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
  from {
    transform: translateY(-20px);
    opacity: 0;
  }
  to {
    transform: translateY(0);
    opacity: 1;
  }
}

.checkout-purchase-container {
  max-width: 1350px;
  margin: 0 auto;
  padding: 20px;
  margin-top: 150px;
  font-family: 'Poppins', sans-serif;
}

.purchase-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.purchase-header h2 {
  font-size: 18px;
  font-weight: 500;
  color: #2d2d2d;
  margin: 0;
}

.status-badge {
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
}

.status-badge.processing {
  background: #fff3cd;
  color: #856404;
}

.status-badge.failed {
  background: #ffeaea;
  color: #dc3545;
}

.status-badge.success {
  background: #d1fae5;
  color: #065f46;
}

.checkout-content {
  display: grid;
  grid-template-columns: 1fr 390px;
  gap: 30px;
  margin-bottom: 60px;
}

.purchase-section {
  max-width: 1000px;
  border-right: 1px solid #d9d9d9;
  padding-right: 50px;
}

.purchase-list {
  margin-bottom: 40px;
}

.checkout-actions {
  display: flex;
  gap: 16px;
  margin-bottom: 30px;
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
  background: #14c9c9;
  color: white;
  border: 1px solid #14c9c9;
}

.btn-primary:hover {
  background: #0fa5a5;
}

.btn-primary:disabled {
  background: #f5f5f5;
  color: #a0a0a0;
  border: 1px solid #d1d1d1;
  cursor: not-allowed;
  opacity: 0.7;
}

.payment-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
}

.payment-header h3 {
  margin: 0;
  font-size: 20px;
  font-weight: 600;
  color: #1f2937;
}

.close-btn {
  background: none;
  border: none;
  font-size: 28px;
  color: #9ca3af;
  cursor: pointer;
  padding: 0;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
  transition: all 0.2s ease;
}

.close-btn:hover {
  background: #f3f4f6;
  color: #374151;
}

.qr-section {
  margin-bottom: 24px;
}

.khqr-card {
  background: white;
  border-radius: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  overflow: hidden;
  margin-bottom: 16px;
  border: 1px solid #e5e7eb;
}

.khqr-header {
  background: #f90000;
  padding: 12px 20px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.khqr-logo {
  height: 24px;
}

.khqr-merchant {
  background: #f9fafb;
  padding: 16px 20px;
  text-align: left;
  border-bottom: 2px dashed #e5e7eb;
}

.merchant-name {
  font-size: 16px;
  font-weight: 500;
  color: #111827;
  margin-bottom: 4px;
}

.amount-display {
  font-size: 24px;
  font-weight: bold;
  color: #111827;
  padding: 4px 0;
}

.qr-code-container {
  padding: 40px 20px;
  background: white;
  text-align: center;
  min-height: 280px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.qr-code {
  width: 300px;
  height: 300px;
  display: block;
  margin: 0 auto;
}

.qr-instruction {
  font-size: 14px;
  color: #64748b;
  margin: 0;
  text-align: center;
}

.payment-status {
  margin-bottom: 24px;
}

.status-waiting,
.status-checking {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 16px;
  background: #f1f5f9;
  border-radius: 12px;
}

.status-icon {
  font-size: 24px;
}

.status-waiting p,
.status-checking p {
  margin: 0;
  font-size: 14px;
  color: #475569;
  font-weight: 500;
}

.spinner-small {
  width: 20px;
  height: 20px;
  border: 2px solid #e2e8f0;
  border-top: 2px solid #14c9c9;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

.payment-actions {
  display: flex;
  gap: 12px;
}

.payment-actions .btn {
  flex: 1;
}

.payment-success {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.8);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  backdrop-filter: blur(2px);
  transition: opacity 0.3s ease-out;
}

.success-icon {
  background-color: #4caf50;
  border-radius: 50%;
  display: inline-flex;
  padding: 1rem;
  margin-bottom: 1rem;
}

.checkmark {
  width: 4rem;
  height: 4rem;
}

.success-content {
  background: white;
  border-radius: 6px;
  padding: 48px 40px;
  text-align: center;
  max-width: 420px;
  width: 90%;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  animation: successSlideIn 0.6s ease-out;
}

@keyframes successSlideIn {
  from {
    transform: scale(0.8) translateY(-20px);
    opacity: 0;
  }
  to {
    transform: scale(1) translateY(0);
    opacity: 1;
  }
}

.success-icon {
  margin-bottom: 24px;
  animation: checkmarkBounce 0.8s ease-out 0.2s both;
}

@keyframes checkmarkBounce {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
  }
}

.success-content h3 {
  color: #065f46;
  margin-bottom: 8px;
  font-size: 28px;
  font-weight: 700;
}

.success-content p {
  color: #6b7280;
  font-size: 16px;
  margin-bottom: 24px;
}

.loading-dots {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-size: 14px;
  color: #9ca3af;
}

.dots {
  display: flex;
  gap: 4px;
}

.dots span {
  width: 6px;
  height: 6px;
  background: #d1d5db;
  border-radius: 50%;
  animation: loadingDots 1.4s ease-in-out infinite both;
}

.dots span:nth-child(1) {
  animation-delay: -0.32s;
}
.dots span:nth-child(2) {
  animation-delay: -0.16s;
}
.dots span:nth-child(3) {
  animation-delay: 0s;
}

@keyframes loadingDots {
  0%,
  80%,
  100% {
    transform: scale(0.8);
    opacity: 0.5;
  }
  40% {
    transform: scale(1);
    opacity: 1;
  }
}

.checkout-summary-sticky {
  position: sticky;
  top: 10rem;
  align-self: flex-start;
  height: fit-content;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

/* Responsive */
@media (max-width: 768px) {
  .checkout-content {
    grid-template-columns: 1fr;
  }

  .purchase-section {
    border-right: none;
    padding-right: 0;
  }

  .checkout-actions,
  .payment-actions {
    flex-direction: column;
  }

  .purchase-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }

  .bakong-payment-content {
    padding: 30px 20px;
  }

  .qr-code {
    max-width: 300px;
  }
}
</style>
