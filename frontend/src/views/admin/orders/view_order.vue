<template>
  <div class="admin-orders-container">
    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb">
      <router-link to="/admin/orders/list" class="breadcrumb-item">List Orders</router-link>
      <span class="breadcrumb-separator">></span>
      <span class="breadcrumb-item active">View Order</span>
      <span class="breadcrumb-separator">></span>
      <router-link :to="`/admin/orders/edit/${orderId}`" class="breadcrumb-item edit-link"
        >Edit</router-link
      >
    </nav>

    <div class="order-view-content">
      <!-- Order Header -->
      <div class="order-header">
        <div class="order-info">
          <h2><Icon icon="mdi:package-variant-closed" /> Order #{{ order?.order_number }}</h2>
          <div class="order-meta">
            <span class="order-date">Created {{ formatDate(order?.created_at) }}</span>
          </div>
        </div>
        <div class="order-actions">
          <router-link to="/admin/orders/list" class="btn btn-simple">
            <Icon icon="mdi:arrow-left" />
            Back to List
          </router-link>
          <router-link :to="`/admin/orders/edit/${orderId}`" class="btn btn-primary">
            <Icon icon="mdi:pencil" />
            Edit Order
          </router-link>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="isLoading" class="loading-section">
        <div class="loading-card">
          <Icon icon="mdi:loading" class="loading-icon" />
          <h3>Loading order details...</h3>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="error-section">
        <div class="error-card">
          <Icon icon="mdi:alert-circle" class="error-icon" />
          <h3>Error Loading Order</h3>
          <p>{{ error }}</p>
          <button @click="loadOrder" class="btn btn-primary">Try Again</button>
        </div>
      </div>

      <!-- Order Not Found -->
      <div v-else-if="!order" class="error-section">
        <div class="error-card">
          <Icon icon="mdi:alert-circle" class="error-icon" />
          <h3>Order Not Found</h3>
          <p>The order you're looking for doesn't exist or has been deleted.</p>
          <router-link to="/admin/orders/list" class="btn btn-primary">
            <Icon icon="mdi:arrow-left" />
            Back to Orders List
          </router-link>
        </div>
      </div>

      <!-- Order Details -->
      <template v-else>
        <!-- Customer Information Section -->
        <div class="order-details">
          <div class="content-section">
            <div class="section-header">
              <h2><Icon icon="mdi:account" /> Customer Information</h2>
            </div>
            <div class="section-content">
              <div class="info-list">
                <div class="info-row">
                  <span class="info-label">Customer Name:</span>
                  <span class="info-value">{{ order.customer_name }}</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Email:</span>
                  <span class="info-value">{{ order.customer_email || 'N/A' }}</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Phone:</span>
                  <span class="info-value">{{ order.customer_phone || 'N/A' }}</span>
                </div>
                <div class="info-row" v-if="order.tracking_number">
                  <span class="info-label">Tracking Number:</span>
                  <span class="info-value">{{ order.tracking_number }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Order Items Section -->
          <div class="content-section">
            <div class="section-header">
              <h2><Icon icon="mdi:cart" /> Order Items</h2>
            </div>
            <div class="section-content">
              <div class="order-group">
                <div class="order-header-info">
                  <div class="order-header-row">
                    <div class="order-left">
                      <div class="order-number">Order #{{ order.id }}</div>
                    </div>
                    <div class="order-date">{{ formatDate(order.created_at) }}</div>
                  </div>
                  <div class="item-flex-container">
                    <img
                      :src="order.items[0].image"
                      :alt="order.items[0].name"
                      class="item-thumb"
                    />
                    <div class="item-details">
                      <div class="item-info">
                        <div class="item-name">{{ order.items[0].name }}</div>
                        <div class="item-category-brand">
                          <span>{{ order.items[0].category || 'N/A' }}</span>
                          <span class="separator">•</span>
                          <span>{{ order.items[0].brand || 'N/A' }}</span>
                        </div>
                        <div class="item-price">
                          Price: ${{ order.items[0].price || 'N/A' }} | Quantity:
                          {{ order.items[0].quantity || 1 }}
                        </div>
                      </div>
                      <div class="order-statuses">
                        <span class="status-badge status-completed">Payment: Completed</span>
                        <span class="status-badge"
                          >Delivery Phone: {{ order.customer_phone || 'N/A' }}</span
                        >
                      </div>
                    </div>
                  </div>
                </div>

                <div class="order-summary">
                  <div class="summary-row">
                    <span>Subtotal</span>
                    <span>${{ parseFloat(order.subtotal || 0).toFixed(2) }}</span>
                  </div>
                  <div class="summary-row discount">
                    <span>Item Discount</span>
                    <span>-${{ itemDiscountAmount.toFixed(2) }}</span>
                  </div>
                  <div class="summary-row net-price">
                    <span>Net Price</span>
                    <span
                      >${{
                        (parseFloat(order.subtotal || 0) - itemDiscountAmount).toFixed(2)
                      }}</span
                    >
                  </div>
                  <div class="summary-row discount">
                    <span>Promo Discount</span>
                    <span>-${{ parseFloat(order.discount_amount || 0).toFixed(2) }}</span>
                  </div>
                  <div class="summary-row shipping">
                    <span>Shipping</span>
                    <span>${{ parseFloat(order.shipping_amount || 0).toFixed(2) }}</span>
                  </div>
                  <div class="summary-row total">
                    <span><strong>Total Amount</strong></span>
                    <span class="total-amount"
                      ><strong>${{ totalAmount.toFixed(2) }}</strong></span
                    >
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Icon } from '@iconify/vue'
import { ordersApi } from '@/services/api'

const props = defineProps({
  id: { type: [String, Number], required: true },
})

const order = ref(null)
const isLoading = ref(false)
const error = ref(null)

// Calculate total amount
const totalAmount = computed(() => {
  if (!order.value) {
    return 0
  } else {
    const subtotal = parseFloat(order.value.subtotal || 0)
    const shipping = parseFloat(order.value.shipping_amount || 0)
    const itemDiscount = itemDiscountAmount.value
    const promoDiscount = parseFloat(order.value.discount_amount || 0)
    return subtotal - itemDiscount + shipping - promoDiscount
  }
})

// Calculate item discount amount from order items
const itemDiscountAmount = computed(() =>
  (order.value?.items || []).reduce((total, item) => {
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
  }, 0),
)

// Get order ID from props
const orderId = computed(() => props.id)

// Load order data from API
const loadOrder = async () => {
  try {
    isLoading.value = true
    error.value = null

    const response = await ordersApi.adminGetOrder(orderId.value)
    if (response.data.success) {
      order.value = response.data.order
    } else {
      error.value = response.data.message || 'Failed to load order'
    }
  } catch (err) {
    console.error('Error loading order from API:', err)
    error.value = err.response?.data?.message || 'Failed to load order'
  } finally {
    isLoading.value = false
  }
}

// Format date for display
const formatDate = (dateString) => {
  if (!dateString) {
    return 'N/A'
  } else {
    return new Date(dateString).toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    })
  }
}

onMounted(() => loadOrder())
</script>

<style scoped>
.admin-orders-container {
  margin: 0 auto;
  font-family: 'Poppins', sans-serif;
  color: #333;
  background: #f8fafc;
  min-height: 100vh;
}

.order-view-content {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  margin: 0 auto;
  background: white;
}

.breadcrumb {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
  padding: 12px 16px;
  background: white;
  border-radius: 6px;
  font-size: 13px;
  color: #666;
  border: 1px solid #e9ecef;
  font-family: 'Poppins', sans-serif;
}

.breadcrumb-item {
  color: #666;
  text-decoration: none;
  cursor: pointer;
  transition: color 0.3s;
}

.breadcrumb-item:hover {
  color: #ff9934;
  text-decoration: underline;
}

.breadcrumb-item.active {
  color: #ff9934;
  /* font-weight: 500; */
  cursor: default;
}

.breadcrumb-separator {
  margin: 0 12px;
  color: #999;
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px;
  border-radius: 8px;
  border-bottom: 1px solid #e9ecef;
  background: #f8f9fa;
}

.order-info h2 {
  margin: 0 0 8px;
  font-size: 24px;
  font-weight: 600;
  color: #333;
}

.order-meta {
  display: flex;
  gap: 16px;
  align-items: center;
}

.order-date {
  color: #666;
  font-size: 14px;
  margin-top: 6px;
}

.order-status {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  text-transform: uppercase;
}

.order-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.loading-section,
.error-section {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
}

.loading-card,
.error-card {
  background: white;
  border-radius: 6px;
  padding: 48px;
  text-align: center;
  border: 1px solid #e2e8f0;
  max-width: 400px;
  width: 100%;
}

.loading-icon {
  font-size: 48px;
  color: #14c9c9;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

.error-icon {
  font-size: 48px;
  color: #dc3545;
  margin-bottom: 16px;
}

.loading-card h3,
.error-card h3 {
  margin: 0 0 12px 0;
  color: #333;
}

.error-card p {
  margin: 0 0 24px 0;
  color: #666;
}

.order-details {
  overflow-y: scroll;
  height: calc(100vh - 200px);
  max-height: 680px;
}

.item-image {
  width: 120px;
  height: 120px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.order-header-info {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 20px;
  background: #f8fafc;
  border-radius: 8px;
}

.order-number {
  font-weight: 600;
  color: #1e293b;
  font-size: 18px;
}

.order-date {
  color: #64748b;
  font-size: 14px;
}

.order-statuses {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.order-header-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.order-left {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.item-flex-container {
  display: flex;
  gap: 16px;
  align-items: flex-start;
}

.item-details {
  flex-direction: column;
  justify-content: space-between;
  height: 130px;
  flex: 1;
}

.item-thumb {
  width: 230px;
  height: auto;
  object-fit: cover;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
  flex-shrink: 0;
}

.item-info {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.item-name {
  font-weight: 600;
  color: #1e293b;
  font-size: 16px;
}

.item-category-brand {
  color: #64748b;
  font-size: 14px;
  display: flex;
  gap: 8px;
}

.item-price {
  font-size: 14px;
  color: #475569;
  margin-top: 4px;
}

.order-summary {
  background: #f8fafc;
  border-radius: 8px;
  padding: 20px;
}

.section-header {
  padding: 24px 32px;
  background: #f8f9fa;
  border-bottom: 1px solid #e2e8f0;
}

.section-header h2 {
  margin: 0;
  font-size: 20px;
  font-weight: 600;
  color: #1e293b;
  display: flex;
  align-items: center;
  gap: 10px;
}

.section-content {
  padding: 32px;
}

.order-group {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
}

.info-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  max-width: 600px;
}

.info-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f1f5f9;
}

.info-row:last-child {
  border-bottom: none;
}

.info-label {
  font-size: 14px;
  font-weight: 500;
  color: #64748b;
  min-width: 140px;
}

.info-value {
  font-size: 14px;
  font-weight: 500;
  color: #1e293b;
  text-align: right;
  display: flex;
  align-items: center;
  gap: 10px;
}

.status-select {
  padding: 6px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 14px;
  background: white;
  cursor: pointer;
  min-width: 120px;
}

.status-select:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

.loading-text {
  font-size: 12px;
  color: #6b7280;
  font-style: italic;
}

.items-grid {
  display: grid;
  gap: 16px;
}

.item-card {
  display: flex;
  gap: 16px;
  padding: 16px 20px;
  background: white;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
  align-items: center;
}

.item-image {
  width: 320px;
  height: auto;
  object-fit: cover;
  border-radius: 4px;
  flex-shrink: 0;
}

.item-details {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.item-name {
  font-weight: 600;
  color: #1e293b;
  font-size: 16px;
}

.item-category-brand {
  color: #64748b;
  font-size: 14px;
  display: flex;
  gap: 8px;
}

.status-badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  text-transform: uppercase;
  border: 1px solid #e2e8f0;
  background-color: #f8fafc;
}

.item-meta {
  display: flex;
  flex-direction: column;
  gap: 4px;
  margin-top: 8px;
}

.price-total-row {
  display: flex;
  align-items: center;
  gap: 8px;
}

.separator {
  color: grey;
}

.meta-item {
  font-size: 14px;
  color: #475569;
  font-weight: 500;
}

.meta-item.total {
  color: #10b981;
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

.summary-card {
  margin: 0 auto;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px solid #f1f5f9;
}

.summary-row:last-child {
  border-bottom: none;
}

.summary-row.total {
  border-top: 2px solid #e2e8f0;
  padding-top: 12px;
  margin-top: 8px;
  font-size: 16px;
}

.summary-row.discount {
  color: #dc3545;
}

.summary-row.shipping {
  border-bottom: none;
}

.total-amount {
  color: #10b981;
}

.status-completed,
.status-confirmed {
  background: rgba(16, 185, 129, 0.2);
  color: #059669;
  border: 1px solid rgba(16, 185, 129, 0.3);
}

.status-processing {
  background: rgba(59, 130, 246, 0.2);
  color: #2563eb;
  border: 1px solid rgba(59, 130, 246, 0.3);
}

.status-pending {
  background: rgba(255, 193, 7, 0.2);
  color: #d97706;
  border: 1px solid rgba(255, 193, 7, 0.3);
}

.status-cancelled {
  background: rgba(239, 68, 68, 0.2);
  color: #dc2626;
  border: 1px solid rgba(239, 68, 68, 0.3);
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border: 1px solid #dee2e6;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  text-decoration: none;
  font-family: 'Poppins', sans-serif;
  background: white;
  color: #333;
  transition: all 0.2s ease;
}

.btn:hover {
  background: #f8f9fa;
  transform: translateY(-1px);
}

.btn-simple {
  background: transparent;
  color: #333;
  border-color: #dee2e6;
}

.btn-simple:hover {
  background: transparent;
  color: #333;
  border-color: #dee2e6;
  transform: none;
}

@media (max-width: 768px) {
  .admin-orders-container {
    padding: 10px;
  }

  .section-header {
    padding: 20px 24px;
  }

  .section-content {
    padding: 24px;
  }

  .info-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
  }

  .info-label {
    min-width: auto;
    margin-bottom: 4px;
  }

  .info-value {
    margin-left: 0;
  }

  .order-header-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }

  .item-flex-container {
    flex-direction: column;
    gap: 12px;
  }

  .item-details {
    flex-direction: column;
    gap: 8px;
    align-items: flex-start;
  }

  .item-thumb {
    width: 60px;
    height: 60px;
  }

  .item-name {
    font-size: 14px;
  }

  .item-category-brand {
    font-size: 13px;
  }

  .item-price {
    font-size: 13px;
  }

  .order-summary {
    padding: 16px;
  }
}

@media (max-width: 480px) {
  .section-header h2 {
    flex-direction: column;
    gap: 8px;
    text-align: center;
  }

  .info-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
  }

  .info-label {
    min-width: auto;
    margin-bottom: 4px;
  }

  .info-value {
    margin-left: 0;
  }

  .order-header-info {
    padding: 12px;
  }

  .order-number {
    font-size: 16px;
  }

  .order-header-row {
    gap: 6px;
  }

  .item-flex-container {
    gap: 8px;
  }

  .item-details {
    gap: 6px;
  }

  .item-thumb {
    width: 50px;
    height: 50px;
  }

  .item-info {
    text-align: center;
  }

  .item-name {
    font-size: 14px;
  }

  .item-category-brand {
    justify-content: center;
  }

  .item-price {
    font-size: 12px;
  }

  .order-summary {
    padding: 12px;
  }

  .summary-row {
    font-size: 14px;
  }
}
</style>
