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

  <div class="order-tracking-page">
    <div class="tracking-container">
      <!-- Back Button -->
      <button @click="goBack" class="back-btn">
        <Icon icon="mdi:arrow-left" />
        Back to Orders
      </button>

      <div v-if="loading" class="loading-state">
        <p>Loading order details...</p>
      </div>

      <div v-else-if="error" class="error-state">
        <p>{{ error }}</p>
        <button @click="fetchOrderDetails" class="retry-btn">Retry</button>
      </div>

      <div v-else-if="order" class="order-details">
        <!-- Order Header -->
        <div class="order-header">
          <div class="order-title">
            <h1>Order #{{ order.id }}</h1>
            <p class="order-date">Placed on {{ formatDate(order.created_at) }}</p>
          </div>
          <div class="order-status-badge">
            <span :class="`status-${order.order_status?.toLowerCase()}`">{{
              order.order_status || 'Pending'
            }}</span>
          </div>
        </div>

        <!-- Order Tracking Progress -->
        <div class="tracking-section">
          <h2>Order Tracking</h2>
          <div class="tracking-timeline">
            <div
              v-for="(step, index) in trackingSteps"
              :key="step.id"
              class="tracking-step"
              :class="{ 'step-active': step.active, 'step-completed': step.completed }"
            >
              <div class="step-icon">
                <Icon :icon="step.icon" />
              </div>
              <div class="step-content">
                <h3>{{ step.title }}</h3>
                <p>{{ step.description }}</p>
                <span class="step-date" v-if="step.date">{{ formatDate(step.date) }}</span>
              </div>
              <div v-if="index < trackingSteps.length - 1" class="step-connector"></div>
            </div>
          </div>
        </div>

        <!-- Order Items -->
        <div class="order-items-section">
          <h2>Order Items</h2>
          <div class="order-items">
            <div v-for="item in order.items || []" :key="item.id" class="order-item">
              <div class="item-image">
                <img :src="item.image" :alt="item.name" />
              </div>
              <div class="item-details">
                <h3>{{ item.name }}</h3>
                <p class="item-category-brand">
                  <span v-if="item.category" class="badge">{{ item.category }}</span>
                  <span v-if="item.brand" class="badge">{{ item.brand }}</span>
                  <span v-if="item.color" class="badge">{{ item.color }}</span>
                </p>
                <div class="item-meta">
                  <span class="item-quantity">Qty: {{ item.quantity }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Order Summary -->
        <div class="order-summary-section">
          <h2>Order Summary</h2>
          <div class="summary-details">
            <div class="summary-row">
              <span>Subtotal:</span>
              <span>${{ formatCurrency(order.subtotal) }}</span>
            </div>
            <div class="summary-row discount" v-if="calculateItemDiscount(order) > 0">
              <span>Item Discount:</span>
              <span>-${{ formatCurrency(calculateItemDiscount(order)) }}</span>
            </div>
            <div
              class="summary-row"
              v-if="
                calculateItemDiscount(order) > 0 ||
                (order.discount_amount && order.discount_amount > 0)
              "
            >
              <span>Net Price:</span>
              <span
                >${{
                  formatCurrency(
                    order.subtotal - calculateItemDiscount(order) - (order.discount_amount || 0),
                  )
                }}</span
              >
            </div>
            <div
              class="summary-row discount"
              v-if="order.discount_amount && order.discount_amount > 0"
            >
              <span>Promo Discount:</span>
              <span>-${{ formatCurrency(order.discount_amount) }}</span>
            </div>
            <div class="summary-row">
              <span>Shipping:</span>
              <span>${{ formatCurrency(order.shipping_amount) }}</span>
            </div>
            <div class="summary-row">
              <span>Tax:</span>
              <span>${{ formatCurrency(0) }}</span>
            </div>
            <div class="summary-row summary-total">
              <span>Total:</span>
              <span>${{ formatCurrency(calculateTotal(order)) }}</span>
            </div>
          </div>
        </div>

        <!-- Shipping Information -->
        <div class="shipping-section" v-if="order.shipping_address">
          <div class="section-header">
            <div class="section-icon">
              <Icon icon="mdi:truck-delivery" />
            </div>
            <h2>Shipping Information</h2>
          </div>
          <div class="shipping-details">
            <div class="info-card shipping-address-card">
              <div class="card-content">
                <div class="address-inline">
                  <span class="address-label">Delivery Address</span>
                  <span class="address-value"
                    >{{ order.shipping_address.street_address }}, {{ order.shipping_address.city }},
                    {{ order.shipping_address.state }} {{ order.shipping_address.postal_code }},
                    {{ order.shipping_address.country }}</span
                  >
                </div>
              </div>
            </div>
            <div class="info-card shipping-method-card" v-if="order.shipping_method">
              <div class="card-header">
                <Icon icon="mdi:package-variant-closed" class="card-icon" />
                <h3>Shipping Method</h3>
              </div>
              <div class="card-content">
                <p class="method-name">{{ order.shipping_method }}</p>
                <div class="tracking-info" v-if="order.tracking_number">
                  <span class="tracking-label">Tracking Number:</span>
                  <span class="tracking-number">{{ order.tracking_number }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
          <button @click="goBack" class="btn-secondary">Back to Orders</button>
          <button v-if="canReorder" @click="reorderItems" class="btn-primary">Reorder Items</button>
          <button v-if="canCancel" @click="cancelOrder" class="btn-danger">Cancel Order</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Icon } from '@iconify/vue'
import { useOrdersStore } from '@/stores/orders'
import Navigation_header from '@/components/navigation_header.vue'

const route = useRoute()
const router = useRouter()
const ordersStore = useOrdersStore()

const order = ref(null)
const loading = ref(true)
const error = ref(null)

const orderId = route.params.id

// Tracking steps based on order status
const trackingSteps = computed(() => {
  const status = order.value?.order_status?.toLowerCase() || 'pending'

  const steps = [
    {
      id: 'ordered',
      title: 'Order Placed',
      description: 'Your order has been received',
      icon: 'mdi:check-circle',
      active: status === 'pending' || status === 'order_placed',
      completed: ['processing', 'shipped', 'delivered'].includes(status),
      date: order.value?.created_at,
    },
    {
      id: 'processing',
      title: 'Processing',
      description: 'Your order is being prepared for shipment',
      icon: 'mdi:cog',
      active: status === 'processing',
      completed: ['shipped', 'delivered'].includes(status),
      date: order.value?.processing_at || order.value?.payment_completed_at,
    },
    {
      id: 'shipped',
      title: 'Shipped',
      description: 'Your order has been shipped',
      icon: 'mdi:truck',
      active: status === 'shipped',
      completed: status === 'delivered',
      date: order.value?.shipped_at,
    },
    {
      id: 'delivered',
      title: 'Delivered',
      description: 'Your order has been delivered successfully',
      icon: 'mdi:package-variant-closed',
      active: status === 'delivered',
      completed: status === 'delivered',
      date: order.value?.delivered_at,
    },
  ]
  return steps
})

// Computed properties for action buttons
const canReorder = computed(() => {
  const status = order.value?.order_status?.toLowerCase()
  return status === 'delivered' || status === 'cancelled'
})

const canCancel = computed(() => {
  const status = order.value?.order_status?.toLowerCase()
  return status === 'pending' || status === 'processing'
})

const fetchOrderDetails = async () => {
  try {
    loading.value = true
    error.value = null
    const orderData = await ordersStore.fetchOrderById(orderId)
    order.value = orderData
  } catch (err) {
    error.value = 'Failed to load order details. Please try again.'
    console.error('Error fetching order details:', err)
  } finally {
    loading.value = false
  }
}

const goBack = () => {
  router.push('/orders')
}

const reorderItems = () => {
  // Add items back to cart and go to cart
  if (order.value?.items) {
    // This would need to be implemented in the cart store
    alert('Reorder functionality will be implemented soon!')
  }
}

const cancelOrder = () => {
  if (confirm('Are you sure you want to cancel this order?')) {
    // This would need to be implemented
    alert('Order cancellation will be implemented soon!')
  }
}

const formatCurrency = (amount) => {
  if (amount == null || amount === '') return '0.00'
  const numAmount = typeof amount === 'string' ? parseFloat(amount) : amount
  return isNaN(numAmount) ? '0.00' : numAmount.toFixed(2)
}

// Calculate item discount amount for an order
const calculateItemDiscount = (order) => {
  return (order.items || []).reduce((total, item) => {
    const discount = item.discount?.[0]
    if (!discount) return total

    const pricing = parseFloat(item.price) || 0
    let discountValue = 0

    if (discount.type === 'percent') {
      discountValue = pricing * (discount.value / 100) * item.quantity
    } else if (discount.type === 'fixed') {
      discountValue = discount.value * item.quantity
    }
    return total + discountValue
  }, 0)
}

// Calculate total amount for an order
const calculateTotal = (order) => {
  const subtotal = parseFloat(order.subtotal || 0)
  const shipping = parseFloat(order.shipping_amount || 0)
  const itemDiscount = calculateItemDiscount(order)
  const promoDiscount = parseFloat(order.discount_amount || 0)
  return subtotal - itemDiscount + shipping - promoDiscount
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

onMounted(() => {
  fetchOrderDetails()
})
</script>

<style scoped>
.order-tracking-page {
  min-height: 100vh;
  background: #f8fafc;
  padding: 150px 20px 40px;
  font-family: 'Poppins', sans-serif;
}

.tracking-container {
  max-width: 1000px;
  margin: 0 auto;
}

.back-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 12px 20px;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  font-size: 0.9rem;
  cursor: pointer;
  margin-bottom: 2rem;
  transition: all 0.2s ease;
}

.back-btn:hover {
  background: #f9fafb;
  border-color: #d1d5db;
}

.loading-state,
.error-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 12px;
}

.loading-state p,
.error-state p {
  font-size: 1.1rem;
  color: #6b7280;
  margin-bottom: 1rem;
}

.retry-btn {
  padding: 12px 24px;
  background: #14c9c9;
  color: white;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s ease;
}

.retry-btn:hover {
  background: #0fa5a5;
}

.order-details {
  background: white;
  border-radius: 12px;
  padding: 2rem;
}

/* Order Header */
.order-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 3rem;
  padding-bottom: 2rem;
  border-bottom: 1px solid #e5e7eb;
}

.order-title h1 {
  margin: 0 0 0.5rem 0;
  font-size: 2rem;
  color: #1f2937;
}

.order-date {
  margin: 0;
  color: #6b7280;
  font-size: 1rem;
}

.order-status-badge {
  text-align: right;
}

.status-pending {
  background: #fef3c7;
  color: #d97706;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 500;
}

.status-processing {
  background: #dbeafe;
  color: #2563eb;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 500;
}

.status-shipped {
  background: #fef3c7;
  color: #d97706;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 500;
}

.status-delivered {
  background: #d1fae5;
  color: #065f46;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 500;
}

.status-cancelled {
  background: #fee2e2;
  color: #dc2626;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 0.9rem;
  font-weight: 500;
}

/* Tracking Section */
.tracking-section {
  margin-bottom: 3rem;
}

.tracking-section h2 {
  font-size: 1.5rem;
  color: #1f2937;
  margin-bottom: 2rem;
}

.tracking-timeline {
  position: relative;
}

.tracking-step {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  margin-bottom: 2rem;
  position: relative;
}

.tracking-step:last-child {
  margin-bottom: 0;
}

.step-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #e5e7eb;
  color: #9ca3af;
  flex-shrink: 0;
  transition: all 0.3s ease;
}

.step-active .step-icon {
  background: #10b981;
  color: white;
}

.step-completed .step-icon {
  background: #10b981;
  color: white;
}

.step-content {
  flex: 1;
  padding-bottom: 1rem;
}

.step-content h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.1rem;
  color: #1f2937;
}

.step-content p {
  margin: 0 0 0.5rem 0;
  color: #6b7280;
}

.step-date {
  font-size: 0.8rem;
  color: #9ca3af;
}

.step-connector {
  position: absolute;
  left: 20px;
  top: 40px;
  width: 2px;
  height: calc(100% - 20px);
  background: #e5e7eb;
}

.step-active .step-connector {
  background: #10b981;
}

.step-completed .step-connector {
  background: #10b981;
}

/* Order Items Section */
.order-items-section,
.order-summary-section,
.shipping-section {
  margin-bottom: 3rem;
}

.order-items-section h2,
.order-summary-section h2,
.shipping-section h2 {
  font-size: 1.5rem;
  color: #1f2937;
  margin-bottom: 1.5rem;
}

.order-items {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.order-item {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  padding: 1rem;
  background: #f9fafb;
  border-radius: 8px;
  border: 1px solid #e5e7eb;
}

.item-image {
  width: 230px;
  height: auto;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
}

.item-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.item-details {
  flex: 1;
}

.item-details h3 {
  margin: 0 0 0.5rem 0;
  font-size: 1.1rem;
  color: #1f2937;
}

.item-category-brand {
  color: #64748b;
  font-size: 14px;
  display: flex;
  gap: 8px;
  margin: 12px 0;
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
}

.item-description {
  margin: 0 0 0.5rem 0;
  color: #6b7280;
  font-size: 0.9rem;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  line-clamp: 2;
  overflow: hidden;
}

.item-meta {
  display: flex;
  gap: 1rem;
  font-size: 0.9rem;
}

.item-quantity {
  color: #6b7280;
}

/* Order Summary */
.summary-details {
  background: #f9fafb;
  border-radius: 8px;
  padding: 1.5rem;
  border: 1px solid #e5e7eb;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.75rem;
  font-size: 1rem;
}

.summary-row:last-child {
  margin-bottom: 0;
}

.summary-total {
  border-top: 1px solid #e5e7eb;
  padding-top: 1rem;
  margin-top: 1rem;
  font-size: 1.2rem;
  font-weight: 600;
  color: #1f2937;
}

/* Shipping Section */
.shipping-section {
  margin-bottom: 3rem;
  background: white;
  border-radius: 8px;
  padding: 2rem;
  border: 1px solid #e5e7eb;
}

.section-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #e5e7eb;
}

.section-icon {
  width: 32px;
  height: 32px;
  background: #f3f4f6;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6b7280;
}

.section-icon svg {
  width: 18px;
  height: 18px;
}

.shipping-section h2 {
  font-size: 1.25rem;
  color: #374151;
  margin: 0;
  font-weight: 600;
}

.shipping-details {
  display: grid;
  gap: 1.5rem;
}

.info-card {
  background: #f9fafb;
  border-radius: 6px;
  padding: 1.5rem;
  border: 1px solid #e5e7eb;
}

.card-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.card-icon {
  width: 20px;
  height: 20px;
  color: #6b7280;
  flex-shrink: 0;
}

.info-card h3 {
  font-size: 1rem;
  color: #374151;
  margin: 0;
  font-weight: 600;
}

.card-content {
  color: #6b7280;
}

.address-inline {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
  width: 100%;
}

.address-label {
  font-size: 0.9rem;
  font-weight: 600;
  color: #374151;
  flex-shrink: 0;
}

.address-value {
  font-size: 0.9rem;
  color: #6b7280;
  text-align: right;
  flex: 1;
  min-width: 0;
}

.method-name {
  font-size: 0.95rem;
  font-weight: 500;
  color: #374151;
  margin: 0 0 0.75rem 0;
}

.tracking-info {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  margin-top: 1rem;
}

.tracking-label {
  font-size: 0.75rem;
  color: #9ca3af;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.tracking-number {
  font-size: 0.9rem;
  font-weight: 600;
  color: #374151;
  font-family: monospace;
  background: #f3f4f6;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  display: inline-block;
}

/* Action Buttons */
.action-buttons {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
  padding-top: 2rem;
  border-top: 1px solid #e5e7eb;
}

.btn-secondary,
.btn-primary,
.btn-danger {
  padding: 12px 24px;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-secondary {
  background: white;
  border: 1px solid #d1d5db;
  color: #6b7280;
}

.btn-secondary:hover {
  background: #f9fafb;
}

.btn-primary {
  background: #14c9c9;
  border: 1px solid #14c9c9;
  color: white;
}

.btn-primary:hover {
  background: #0fa5a5;
}

.btn-danger {
  background: #ef4444;
  border: 1px solid #ef4444;
  color: white;
}

.btn-danger:hover {
  background: #dc2626;
}

/* Responsive Design */
@media (max-width: 768px) {
  .shipping-section,
  .payment-section {
    padding: 2rem 1.5rem;
  }

  .order-header {
    flex-direction: column;
    gap: 1rem;
  }

  .order-status-badge {
    text-align: left;
  }

  .tracking-step {
    gap: 0.75rem;
  }

  .step-icon {
    width: 35px;
    height: 35px;
  }

  .order-item {
    flex-direction: column;
    text-align: center;
  }

  .item-meta {
    justify-content: center;
  }

  .shipping-section {
    padding: 1.5rem;
  }

  .shipping-details {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .section-header {
    padding-bottom: 0.75rem;
  }

  .info-card {
    padding: 1.25rem;
  }

  .section-icon {
    width: 28px;
    height: 28px;
  }

  .section-icon svg {
    width: 16px;
    height: 16px;
  }

  .shipping-section h2 {
    font-size: 1.1rem;
  }

  .address-inline {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }

  .address-value {
    text-align: left;
  }

  .action-buttons {
    flex-direction: column;
  }

  .btn-secondary,
  .btn-primary,
  .btn-danger {
    width: 100%;
  }
}
</style>
