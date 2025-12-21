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
  <!-- Loading State -->
  <div v-if="isLoading" class="loading-state">
    <div class="loading-spinner-large"></div>
    <h2>Loading your order details...</h2>
    <p>Please wait while we prepare your invoice.</p>
  </div>

  <!-- Error State -->
  <div v-else-if="hasError" class="error-state">
    <div class="error-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"></circle>
        <line x1="15" y1="9" x2="9" y2="15"></line>
        <line x1="9" y1="9" x2="15" y2="15"></line>
      </svg>
    </div>
    <h2>Order Not Found</h2>
    <p>We couldn't find your order details. Please try again or contact support.</p>
    <button @click="router.push('/')" class="btn-continue">Return to Home</button>
  </div>

  <!-- Success State -->
  <div v-else class="payment-success">
    <div class="success-icon">
      <svg viewBox="0 0 24 24" class="checkmark">
        <path fill="none" stroke="white" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
    </div>

    <h2 class="success-title">Payment successful!</h2>
    <p class="success-subtitle">
      The ordered confirmation has been sent to
      <strong>{{ formData?.buyerName || recipient }}</strong>
    </p>

    <div class="order-card">
      <!-- Invoice Header -->
      <div class="invoice-header">
        <h2 class="invoice-title">Purchase Summary</h2>
        <div class="invoice-info">
          <p><strong>Bill To:</strong> {{ formData?.buyerName || recipient }}</p>
          <p><strong>Email:</strong> {{ userEmail || 'N/A' }}</p>
          <p><strong>Phone:</strong> {{ formData?.buyerPhone || 'N/A' }}</p>
          <p><strong>Date:</strong> {{ transactionDate }}</p>
          <p><strong>Order #:</strong> {{ orderData?.invoiceNumber || generateShortOrderId() }}</p>
        </div>
      </div>
      <div v-for="item in cartItems" :key="item.id" class="order-item">
        <img :src="item.product.image" :alt="item.product.name" class="order-item-image" />
        <div class="order-item-details">
          <p class="order-item-name">{{ item.product.name }}</p>
          <div class="item-category-brand">
            <span class="badge">{{ getCategoryName(item.product) }}</span>
            <span class="badge">{{ item.product.brand }}</span>
            <span class="badge">Color: {{ item.product.color }}</span>
          </div>
          <p class="order-item-quantity">Quantity: {{ item.quantity }}</p>
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
        <div class="summary-row">
          <span>Subtotal</span>
          <span>${{ totalMRP.toFixed(2) }}</span>
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
        <button class="btn-download" @click="downloadInvoice">Download Invoice</button>
        <button class="btn-continue" @click="continueShopping">Continue Shopping</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import Navigation_header from '@/components/navigation_header.vue'
import jsPDF from 'jspdf'

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

// Dynamic order data from localStorage
const orderData = ref(null)
const cartItems = ref([])
const formData = ref({})
const actualPromoCode = ref('')
const actualPaymentMethod = ref('Bakong KHQR')
const storedSummaryBreakdown = ref(null)
const isLoading = ref(true)
const hasError = ref(false)

// Get user email from localStorage
const userEmail = computed(() => {
  try {
    const user = JSON.parse(localStorage.getItem('user'))
    return user?.email || null
  } catch {
    return null
  }
})

// Load order data on component mount
onMounted(() => {
  // Try to get order data from localStorage (check both possible keys for backward compatibility)
  let storedOrderData =
    localStorage.getItem('lastOrderData') || localStorage.getItem('orderDetails')

  if (storedOrderData) {
    try {
      const parsedData = JSON.parse(storedOrderData)

      orderData.value = parsedData
      cartItems.value = parsedData.cartItems || []
      formData.value = parsedData.formData || {}
      actualPromoCode.value = parsedData.promoCode || ''
      actualPaymentMethod.value = parsedData.paymentMethod || 'Bakong KHQR'
      storedSummaryBreakdown.value = parsedData.summaryBreakdown || null
      isLoading.value = false

      // Save order to admin orders list immediately after successful load
      saveOrderToAdminList()

      // Clean up old data after successful load
      localStorage.removeItem('orderDetails') // Remove old key if it exists
    } catch (error) {
      console.error('❌ Error parsing order data:', error)
      hasError.value = true
      isLoading.value = false
    }
  } else {
    console.warn('⚠️ No order data found in localStorage')
    hasError.value = true
    isLoading.value = false
  }
})

// Function to generate a short order ID
const generateShortOrderId = () => {
  // Use last 6 digits of timestamp for shorter ID
  const timestamp = Date.now().toString()
  const shortId = timestamp.slice(-6)
  return `ORD-${shortId}`
}

// Constants
const CORRECT_PROMO = 'BOOKRIDE50'
const PROMO_DISCOUNT = 5.0
const SHIPPING_AMOUNT = 2.0

// Function to get category name for display
const getCategoryName = (product) => {
  if (!product) {
    return 'Unknown'
  }

  // If category is an object (from API relationship), get the name
  if (product.category && typeof product.category === 'object' && product.category.name) {
    // Map category names to display names
    const categoryDisplayMap = {
      Mountain: 'Mountain Bike',
      Road: 'Road Bike',
    }
    return categoryDisplayMap[product.category.name] || product.category.name
  }

  // If category is a string, use it directly
  if (product.category && typeof product.category === 'string') {
    const categoryDisplayMap = {
      mountain: 'Mountain Bike',
      road: 'Road Bike',
    }
    return categoryDisplayMap[product.category] || product.category
  }

  return 'Unknown'
}

// Helper functions for discount calculation (same as cart_item_card)
const hasDiscount = (item) => {
  return (
    item.product.discount &&
    Array.isArray(item.product.discount) &&
    item.product.discount.length > 0 &&
    (item.product.discount[0].type === 'percent' || item.product.discount[0].type === 'fixed')
  )
}

const getDiscountedPrice = (item) => {
  const pricing = parseFloat(item.product.pricing) || 0

  if (!hasDiscount(item)) {
    return pricing
  }

  const discount = item.product.discount[0] // Take the first discount
  if (discount.type === 'percent') {
    return pricing - (pricing * discount.value) / 100
  } else if (discount.type === 'fixed') {
    return pricing - discount.value
  }

  return pricing
}

// Computed properties - use stored breakdown if available, otherwise calculate
const totalMRP = computed(() => {
  // Use stored breakdown data if available (from checkout_summary)
  if (storedSummaryBreakdown.value?.totalMRP) {
    return storedSummaryBreakdown.value.totalMRP
  }

  // Fallback calculation
  return cartItems.value.reduce((total, item) => {
    return total + item.product.pricing * item.quantity
  }, 0)
})

const itemDiscountAmount = computed(() => {
  return cartItems.value.reduce((total, item) => {
    if (!hasDiscount(item)) return total

    const originalPrice = item.product.pricing
    const discountedPrice = getDiscountedPrice(item)
    const itemDiscount = (originalPrice - discountedPrice) * item.quantity

    return total + itemDiscount
  }, 0)
})

const promoDiscountAmount = computed(() => {
  // Use stored breakdown if available
  if (storedSummaryBreakdown.value?.promoDiscount !== undefined) {
    return storedSummaryBreakdown.value.promoDiscount
  }

  // Fallback calculation
  if (actualPromoCode.value.trim().toUpperCase() === CORRECT_PROMO) {
    return PROMO_DISCOUNT
  } else {
    return 0
  }
})

const discountAmount = computed(() => {
  // Always calculate total discount (item discounts + promo discount)
  return itemDiscountAmount.value + promoDiscountAmount.value
})

const netPrice = computed(() => {
  // Use stored breakdown if available
  if (storedSummaryBreakdown.value?.netPrice !== undefined) {
    return storedSummaryBreakdown.value.netPrice
  }

  // Fallback calculation
  const calculatedNet = totalMRP.value - discountAmount.value
  if (calculatedNet > 0) {
    return calculatedNet
  } else {
    return 0
  }
})

const totalPrice = computed(() => {
  // Use stored breakdown if available
  if (storedSummaryBreakdown.value?.finalTotal !== undefined) {
    return storedSummaryBreakdown.value.finalTotal
  }

  // Fallback calculation
  return netPrice.value + SHIPPING_AMOUNT
})

const shippingCharge = computed(() => {
  // Use stored breakdown if available
  if (storedSummaryBreakdown.value?.shippingAmount !== undefined) {
    return storedSummaryBreakdown.value.shippingAmount
  }

  // Fallback value
  return SHIPPING_AMOUNT
})

const transactionDate = computed(() => {
  if (orderData.value?.timestamp) {
    const date = new Date(orderData.value.timestamp)
    const options = {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    }
    return date.toLocaleDateString('en-US', options)
  } else {
    const date = new Date()
    const options = {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
    }
    return date.toLocaleDateString('en-US', options)
  }
})

const paymentMethod = computed(() => actualPaymentMethod.value)

const downloadInvoice = () => {
  const doc = new jsPDF()

  // Colors
  const primaryColor = [66, 143, 192] // #428fc0
  const textColor = [51, 51, 51] // #333
  const lightGray = [102, 102, 102] // #666

  // Set up fonts
  doc.setFont('helvetica', 'bold')

  // Company Header
  doc.setFontSize(24)
  doc.setTextColor(...primaryColor)
  doc.text('MOTION CYCLE', 20, 30)

  doc.setFontSize(10)
  doc.setTextColor(...lightGray)
  doc.setFont('helvetica', 'normal')
  doc.text('Phnom Penh, Cambodia', 20, 38)
  doc.text('+855 12 345 678 | info@motioncycle.com', 20, 44)

  // Invoice Title
  doc.setFontSize(18)
  doc.setTextColor(...textColor)
  doc.setFont('helvetica', 'bold')
  doc.text('INVOICE', 150, 30)

  // Invoice Details
  const invoiceNumber = orderData.value?.invoiceNumber || `INV-${Date.now()}`
  doc.setFontSize(10)
  doc.setFont('helvetica', 'normal')
  doc.text(`Invoice #: ${invoiceNumber}`, 150, 40)
  doc.text(`Date: ${transactionDate.value}`, 150, 46)
  doc.text(`Payment Method: ${paymentMethod.value}`, 150, 52)

  // Customer Information
  doc.setFont('helvetica', 'bold')
  doc.text('Bill To:', 20, 65)
  doc.setFont('helvetica', 'normal')
  doc.text(formData.value?.buyerName || props.recipient, 20, 72)
  doc.text(`Email: ${userEmail.value || 'N/A'}`, 20, 78)
  doc.text(`Phone: ${formData.value?.buyerPhone || 'N/A'}`, 20, 84)

  // Items Table Header
  let yPosition = 105
  doc.setFillColor(...primaryColor)
  doc.rect(20, yPosition - 5, 170, 8, 'F')

  doc.setTextColor(255, 255, 255)
  doc.setFont('helvetica', 'bold')
  doc.setFontSize(9)
  doc.text('Item', 25, yPosition)
  doc.text('Qty', 120, yPosition)
  doc.text('Unit Price', 140, yPosition)
  doc.text('Total', 165, yPosition)

  // Items
  yPosition += 10
  doc.setTextColor(...textColor)
  doc.setFont('helvetica', 'normal')

  cartItems.value.forEach((item) => {
    const itemName = item.product.name
    const quantity = item.quantity
    const unitPrice = getDiscountedPrice(item)
    const total = unitPrice * quantity

    // Item name (truncate if too long)
    const truncatedName = itemName.length > 30 ? itemName.substring(0, 27) + '...' : itemName
    doc.text(truncatedName, 25, yPosition)

    // Brand and color - removed as per user request
    // if (item.product.brand || item.product.color) {
    //   const brandColor = `${item.product.brand || ''}${item.product.color ? ` | ${item.product.color}` : ''}`
    //   doc.setFontSize(8)
    //   doc.setTextColor(...lightGray)
    //   doc.text(brandColor, 25, yPosition + 4)
    //   doc.setFontSize(9)
    //   doc.setTextColor(...textColor)
    // }

    // Quantity, Unit Price, Total
    doc.text(quantity.toString(), 125, yPosition)
    doc.text(`$${unitPrice.toFixed(2)}`, 145, yPosition)
    doc.text(`$${total.toFixed(2)}`, 170, yPosition)

    yPosition += 12

    // Check if we need a new page
    if (yPosition > 250) {
      doc.addPage()
      yPosition = 30
    }
  })

  // Summary Section
  yPosition += 10
  doc.setDrawColor(200, 200, 200)
  doc.line(20, yPosition, 190, yPosition)
  yPosition += 10

  // Summary items
  const summaryItems = [
    { label: 'Subtotal:', value: `$${totalMRP.value.toFixed(2)}` },
    {
      label: 'Item Discounts:',
      value: `-$${itemDiscountAmount.value.toFixed(2)}`,
      color: [114, 193, 94],
    },
    {
      label: 'Promo Discount:',
      value: `-$${promoDiscountAmount.value.toFixed(2)}`,
      color: [114, 193, 94],
    },
    { label: 'Net Price:', value: `$${netPrice.value.toFixed(2)}` },
    { label: 'Shipping:', value: `$${shippingCharge.value.toFixed(2)}` },
  ]

  summaryItems.forEach((item) => {
    doc.setFont('helvetica', 'normal')
    doc.setTextColor(...textColor)
    doc.text(item.label, 120, yPosition)

    if (item.color) {
      doc.setTextColor(...item.color)
    }
    doc.text(item.value, 170, yPosition)
    yPosition += 6
  })

  // Total
  yPosition += 5
  doc.setDrawColor(200, 200, 200)
  doc.line(120, yPosition, 190, yPosition)
  yPosition += 8

  doc.setFont('helvetica', 'bold')
  doc.setFontSize(12)
  doc.setTextColor(...primaryColor)
  doc.text('TOTAL:', 120, yPosition)
  doc.text(`$${totalPrice.value.toFixed(2)}`, 170, yPosition)

  // Footer
  yPosition += 20
  doc.setFontSize(8)
  doc.setTextColor(...lightGray)
  doc.setFont('helvetica', 'normal')
  doc.text('Thank you for your business!', 20, yPosition)
  doc.text('For any questions, please contact us at info@motioncycle.com', 20, yPosition + 5)

  // Save the PDF
  doc.save(`invoice_${invoiceNumber}.pdf`)
}

const continueShopping = () => {
  // Clear all order data from localStorage
  localStorage.removeItem('lastOrderData')
  localStorage.removeItem('orderDetails') // Clean up any old data
  localStorage.removeItem('checkoutPromoCode') // Clear saved promo code
  // Navigate to home page
  router.push('/home')
}

// Save completed order to admin orders storage
const saveOrderToAdminList = () => {
  try {
    // Get existing admin orders or initialize empty array
    const existingOrders = JSON.parse(localStorage.getItem('adminOrders') || '[]')

    // Create admin order format
    const adminOrder = {
      id: Date.now(), // Use timestamp as ID
      order_number: orderData.value?.invoiceNumber || generateShortOrderId(),
      customer_name: formData.value?.buyerName || 'Unknown Customer',
      customer_email: userEmail.value || formData.value?.buyerEmail || 'N/A',
      customer_phone: formData.value?.buyerPhone || 'N/A',
      payment_method: actualPaymentMethod.value || 'bakong',
      payment_status: 'completed', // Payment was successful since user reached summary page
      subtotal: totalMRP.value, // Save subtotal (total of all items before discount)
      shipping_amount: shippingCharge.value, // Add shipping amount
      discount_amount: discountAmount.value, // Add discount amount
      total_amount: totalPrice.value, // Keep total for backward compatibility
      created_at: orderData.value?.timestamp || new Date().toISOString(),
      order_status: 'completed',
      items: cartItems.value.map((item) => {
        // Helper function to get category name
        const getCategoryValue = (product) => {
          // If category is an object (from API relationship), get the name
          if (product.category && typeof product.category === 'object' && product.category.name) {
            return product.category.name
          }
          // If category is a string, use it directly
          if (product.category && typeof product.category === 'string') {
            return product.category
          }
          return 'Uncategorized'
        }

        return {
          id: item.id,
          name: item.product.name,
          image: item.product.image, // Save product image URL
          category: getCategoryValue(item.product), // Save as string
          brand: item.product.brand || 'N/A',
          color: item.product.color || 'N/A', // Save product color
          original_price: item.product.pricing, // Save original price
          quantity: item.quantity,
          price: getDiscountedPrice(item), // Save discounted price
        }
      }),
      selected: false,
    }

    // Add to existing orders
    existingOrders.push(adminOrder)

    // Save back to localStorage
    localStorage.setItem('adminOrders', JSON.stringify(existingOrders))

    console.log('✅ Order saved to admin list:', adminOrder)
  } catch (error) {
    console.error('❌ Error saving order to admin list:', error)
  }
}
</script>

<style scoped>
.payment-success {
  text-align: center;
  padding: 2rem;
  font-family: 'Poppins', sans-serif;
  margin-top: 150px;
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

.success-title {
  font-size: 1.8rem;
  margin-bottom: 0.5rem;
  font-weight: 600;
  color: #333;
  font-family: 'Poppins', sans-serif;
}

.success-subtitle {
  color: #808080;
  margin-bottom: 2rem;
}

.order-card {
  max-width: 1000px;
  margin: auto;
  border: 1px solid #eee;
  border-radius: 8px;
  padding: 2rem;
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
  width: auto;
  height: 120px;
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
  color: #333;
  margin: 0;
  font-size: 16px;
}

.order-item-brand {
  color: #666;
  font-size: 14px;
  margin: 0;
}

.order-item-category {
  color: #666;
  font-size: 14px;
  margin: 0;
}

.order-item-color {
  color: #666;
  font-size: 14px;
  margin: 0;
}

.order-item-quantity {
  color: #666;
  font-size: 12px;
  margin: 0;
  padding: 24px 0 8px 0;
}

.price-total-row {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 8px;
}

.order-item-price {
  color: #666;
  font-size: 14px;
  font-weight: 500;
}

.order-item-total {
  color: #10b981;
  font-size: 14px;
  font-weight: 600;
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
  margin-bottom: 1.5rem;
  font-size: 14px;
}

.summary-row.total {
  font-weight: 600;
  font-size: 1.2rem;
  color: #333;
}

.discount {
  color: #72c15e;
  font-weight: 500;
}

hr {
  margin: 1rem 0;
  border: none;
  border-top: 1px solid #eee;
}

.order-actions {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 1.5rem;
  justify-content: center;
  flex-wrap: wrap;
}

.btn-download,
.btn-continue {
  padding: 1rem;
  border-radius: 6px;
  border: none;
  font-family: 'Poppins', sans-serif;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 14px;
}

.separator {
  color: grey;
}

.brand-color-row {
  display: flex;
  align-items: center;
  margin-top: 4px;
  gap: 10px;
}

.btn-download {
  background-color: #428fc0;
  color: white;
}

.invoice-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;
  border-bottom: 1px solid #eee;
  padding-bottom: 1rem;
}

.invoice-title {
  font-size: 1.6rem;
  font-weight: 600;
  color: #333;
}

.invoice-info {
  text-align: right;
  font-size: 14px;
  color: #555;
}

.invoice-info p {
  margin: 2px 0;
}

.btn-download:hover {
  background-color: #357abd;
}

.btn-continue {
  background-color: #14c9c9;
  color: white;
}

.btn-continue:hover {
  background-color: rgb(11, 184, 184);
}

/* Loading State */
.loading-state {
  text-align: center;
  padding: 4rem 2rem;
  font-family: 'Poppins', sans-serif;
  margin-top: 150px;
}

.loading-spinner-large {
  display: inline-block;
  width: 60px;
  height: 60px;
  border: 4px solid rgba(20, 201, 201, 0.2);
  border-radius: 50%;
  border-top-color: #14c9c9;
  animation: spin 1s ease-in-out infinite;
  margin-bottom: 2rem;
}

.loading-state h2 {
  font-size: 1.5rem;
  margin-bottom: 1rem;
  font-weight: 500;
  color: #333;
}

.loading-state p {
  color: #666;
  margin-bottom: 2rem;
}

/* Error State */
.error-state {
  text-align: center;
  padding: 4rem 2rem;
  font-family: 'Poppins', sans-serif;
  margin-top: 150px;
}

.error-icon {
  width: 4rem;
  height: 4rem;
  margin: 0 auto 2rem;
  color: #dc3545;
  background-color: rgba(220, 53, 69, 0.1);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.error-icon svg {
  width: 2rem;
  height: 2rem;
}

.error-state h2 {
  font-size: 1.5rem;
  margin-bottom: 1rem;
  font-weight: 500;
  color: #dc3545;
}

.error-state p {
  color: #666;
  margin-bottom: 2rem;
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
  border: 1px solid #ddd;
  border-radius: 90px;
  background-color: #f0f0f0;
  color: #333;
  font-size: 12px;
  font-weight: 500;
  margin-top: 8px;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
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

  .price-total-row {
    justify-content: center;
  }
}
</style>
