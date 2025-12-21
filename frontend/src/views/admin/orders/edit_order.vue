<template>
  <div class="admin-orders-container">
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
      <router-link to="/admin/orders/list" class="breadcrumb-item">List Orders</router-link>
      <span class="breadcrumb-separator">&gt;</span>
      <router-link :to="`/admin/orders/view/${orderId}`" class="breadcrumb-item"
        >View Order</router-link
      >
      <span class="breadcrumb-separator">&gt;</span>
      <span class="breadcrumb-item active">Edit</span>
    </nav>

    <!-- Header -->
    <div class="order-header">
      <div class="order-info">
        <h2>
          <Icon icon="gridicons:product" /> Edit Order #{{ order?.order_number || 'Loading...' }}
        </h2>
        <div class="order-meta">
          <span class="order-date">Created {{ formatDate(order?.created_at) }}</span>
        </div>
      </div>
      <div class="section-header-actions">
        <router-link :to="`/admin/orders/view/${orderId}`" class="btn btn-simple">
          <Icon icon="gridicons:arrow-left" />
          Back to View
        </router-link>
        <button
          @click="updateOrderStatus"
          :disabled="updatingStatus || !hasStatusChanged"
          class="btn btn-primary"
        >
          <Icon icon="gridicons:refresh" />
          <span v-if="updatingStatus">Updating...</span>
          <span v-else>Update Status</span>
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="!order" class="loading-section">
      <div class="loading-card">
        <Icon icon="gridicons:loading" class="loading-icon" />
        <h3>Loading order details...</h3>
      </div>
    </div>

    <!-- Order Not Found -->
    <div v-else-if="!order.id" class="error-section">
      <div class="error-card">
        <Icon icon="gridicons:notice-outline" class="error-icon" />
        <h3>Order Not Found</h3>
        <p>The order you're looking for doesn't exist or has been deleted.</p>
        <router-link to="/admin/orders/list" class="btn btn-primary">
          <Icon icon="gridicons:arrow-left" />
          Back to Orders List
        </router-link>
      </div>
    </div>

    <!-- Edit Form -->
    <template v-else>
      <div class="order-view-content">
        <div class="order-details">
          <!-- Update Order Status Card -->
          <div class="content-section">
            <div class="section-header">
              <h2><Icon icon="gridicons:clipboard" /> Update Order Status</h2>
            </div>
            <div class="section-content">
              <div class="info-list">
                <div class="info-row">
                  <span class="info-label">Order Status:</span>
                  <span class="info-value">
                    <select
                      v-model="formData.order_status"
                      :disabled="updatingStatus"
                      class="status-select"
                    >
                      <option value="pending">Order Placed</option>
                      <option value="processing">Processing</option>
                      <option value="shipped">Shipped</option>
                      <option value="delivered">Delivered</option>
                    </select>
                  </span>
                </div>
                <div class="info-row">
                  <span class="info-label">Status Description:</span>
                  <span class="info-value status-description">
                    <div class="status-info">
                      <div class="status-title">{{ getStatusTitle(formData.order_status) }}</div>
                      <div class="status-text">
                        {{ getStatusDescription(formData.order_status) }}
                      </div>
                      <div v-if="formData.order_status === 'pending'" class="status-date">
                        {{ formatDate(order?.created_at) }}
                      </div>
                    </div>
                  </span>
                </div>
                <div class="info-row">
                  <span class="info-label">Payment Status:</span>
                  <span class="info-value">
                    <span class="status-badge status-completed">Completed</span>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Order Progress Card -->
          <div class="content-section">
            <div class="section-header">
              <h2><Icon icon="gridicons:time" /> Order Progress</h2>
            </div>
            <div class="section-content">
              <div class="progress-container">
                <div
                  class="progress-step"
                  :class="{
                    completed: getStatusIndex(order.order_status) >= 0,
                    active: order.order_status === 'pending',
                  }"
                >
                  <div class="progress-icon">
                    <Icon icon="gridicons:product" />
                  </div>
                  <div class="progress-label">ORDER PLACED</div>
                  <div class="progress-date">{{ formatDate(order.created_at) }}</div>
                </div>

                <div
                  class="progress-step"
                  :class="{
                    completed: getStatusIndex(order.order_status) >= 1,
                    active: order.order_status === 'processing',
                  }"
                >
                  <div class="progress-icon">
                    <Icon icon="gridicons:cog" />
                  </div>
                  <div class="progress-label">PROCESSING</div>
                  <div
                    class="progress-subtitle"
                    :class="{ pending: getStatusIndex(order.order_status) < 1 }"
                  >
                    {{ getStatusIndex(order.order_status) >= 1 ? 'Completed' : 'In Progress' }}
                  </div>
                </div>

                <div
                  class="progress-step"
                  :class="{
                    completed: getStatusIndex(order.order_status) >= 2,
                    active: order.order_status === 'shipped',
                  }"
                >
                  <div class="progress-icon">
                    <Icon icon="gridicons:shipping" />
                  </div>
                  <div class="progress-label">SHIPPED</div>
                  <div
                    class="progress-subtitle"
                    :class="{ pending: getStatusIndex(order.order_status) < 2 }"
                  >
                    {{ getStatusIndex(order.order_status) >= 2 ? 'Completed' : 'Pending' }}
                  </div>
                </div>

                <div
                  class="progress-step"
                  :class="{
                    completed: getStatusIndex(order.order_status) >= 3,
                    active: order.order_status === 'delivered',
                  }"
                >
                  <div class="progress-icon">
                    <Icon icon="gridicons:checkmark-circle" />
                  </div>
                  <div class="progress-label">DELIVERED</div>
                  <div
                    class="progress-subtitle"
                    :class="{ pending: getStatusIndex(order.order_status) < 3 }"
                  >
                    {{ getStatusIndex(order.order_status) >= 3 ? 'Completed' : 'Pending' }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import { Icon } from '@iconify/vue'
import { useOrdersStore } from '@/stores/orders'
import { ordersApi } from '@/services/api'

const route = useRoute()
const ordersStore = useOrdersStore()

const orderId = computed(() => route.params.id)
const order = ref(null)
const updatingStatus = ref(false)

const formData = ref({
  order_status: '',
})

// Status order for timeline
const statusOrder = ['pending', 'processing', 'shipped', 'delivered']

// Load order data
const loadOrder = async () => {
  try {
    const orderData = await ordersStore.fetchOrderById(orderId.value)
    order.value = orderData
  } catch (error) {
    console.error('Error loading order from backend:', error)
    order.value = null
  }
}

// Check if status has changed
const hasStatusChanged = computed(() => {
  return formData.value.order_status !== order.value?.order_status
})

// Update order status
const updateOrderStatus = async () => {
  if (!order.value || updatingStatus.value || !hasStatusChanged.value) return

  updatingStatus.value = true
  try {
    const updateData = {
      order_status: formData.value.order_status,
    }

    const response = await ordersApi.updateOrderStatus(order.value.id, updateData)

    if (response.data.success) {
      // Fetch updated order data from backend
      await loadOrder()
      alert(`Order status updated to ${formData.value.order_status}`)
    } else {
      alert('Failed to update order status: ' + (response.data.message || 'Unknown error'))
    }
  } catch (error) {
    console.error('Error updating order status:', error)
    alert('Failed to update order status')
  } finally {
    updatingStatus.value = false
  }
}

// Get status index for timeline
const getStatusIndex = (status) => {
  return statusOrder.indexOf(status)
}

// Get status title for display
const getStatusTitle = (status) => {
  const titles = {
    pending: 'Order Placed',
    processing: 'Processing',
    shipped: 'Shipped',
    delivered: 'Delivered',
  }
  return titles[status] || 'Unknown'
}

// Get status description for display
const getStatusDescription = (status) => {
  const descriptions = {
    pending: 'Your order has been received',
    processing: 'Your order is being prepared for shipment',
    shipped: 'Your order has been shipped',
    delivered: 'Your order has been delivered successfully',
  }
  return descriptions[status] || 'Status description not available'
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

onMounted(() => {
  loadOrder()
})

// Watch for order changes to update form
watch(order, (newOrder) => {
  if (newOrder) {
    formData.value.order_status = newOrder.order_status
  }
})
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
  /* border-radius: 8px; */
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
  border: 1px solid #e9ecef;
  border-bottom: none;
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

.order-card {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 20px;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 20px;
}

.order-image {
  flex-shrink: 0;
}

.item-image {
  width: 320px;
  height: auto;
  object-fit: cover;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
}

.order-header-info {
  flex: 1;
}

.order-number {
  font-weight: 600;
  color: #1e293b;
  font-size: 16px;
  margin-bottom: 4px;
}

.order-date {
  color: #64748b;
  font-size: 14px;
  margin-bottom: 8px;
}

.order-statuses {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.order-summary {
  flex: 1;
  max-width: 300px;
  background: #f8fafc;
  border-radius: 8px;
  padding: 16px;
}

.section-header {
  padding: 24px 32px;
  background: #f8f9fa;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.section-header-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.section-header-actions {
  display: flex;
  align-items: center;
  gap: 12px;
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
  color: #333;
  cursor: pointer;
  min-width: 120px;
  font-weight: 500;
}

.status-select:focus {
  outline: none;
}

.form-input,
.form-textarea {
  padding: 8px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
  background: white;
  color: #333;
  transition: border-color 0.2s ease;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #059669;
  box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
}

.form-textarea {
  resize: vertical;
  min-height: 80px;
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

.progress-container {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  margin-top: 20px;
}

.progress-step {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  position: relative;
}

.progress-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 12px;
  position: relative;
  background-color: #f5f5f5;
  color: #bdbdbd;
  transition: all 0.3s ease;
  border: 2px solid #e2e8f0;
}

.progress-step.completed .progress-icon {
  background-color: #e3f2fd;
  color: #2196f3;
  border-color: #2196f3;
}

.progress-step.active .progress-icon {
  background-color: #e3f2fd;
  color: #2196f3;
  border-color: #2196f3;
  box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.2);
}

.progress-icon svg {
  width: 28px;
  height: 28px;
}

.progress-label {
  font-size: 11px;
  font-weight: 600;
  color: #666;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 4px;
}

.progress-date {
  font-size: 11px;
  color: #999;
}

.progress-subtitle {
  font-size: 11px;
  color: #2196f3;
  font-weight: 500;
}

.progress-subtitle.pending {
  color: #999;
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

.status-description {
  text-align: left;
  max-width: 300px;
}

.status-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.status-title {
  font-weight: 600;
  color: #1e293b;
  font-size: 14px;
}

.status-text {
  color: #64748b;
  font-size: 13px;
  line-height: 1.4;
}

.status-date {
  color: #059669;
  font-size: 12px;
  font-weight: 500;
}

@media (max-width: 768px) {
  .admin-orders-container {
    padding: 10px;
  }

  .order-header {
    flex-direction: column;
    gap: 16px;
    align-items: flex-start;
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

  .order-card {
    flex-direction: column;
    align-items: flex-start;
  }

  .order-details {
    width: 100%;
    justify-content: space-between;
  }

  .progress-container {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .order-header {
    flex-direction: column;
    gap: 8px;
    align-items: flex-start;
  }

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

  .item-card {
    flex-direction: column;
    text-align: center;
    gap: 8px;
    padding: 12px 16px;
  }

  .item-image {
    width: 50px;
    height: 50px;
  }

  .item-details {
    text-align: center;
  }

  .item-meta {
    justify-content: flex-start;
    align-items: flex-start;
    flex-wrap: nowrap;
    gap: 4px;
  }

  .price-total-row {
    flex-wrap: wrap;
    gap: 8px;
  }

  .meta-item {
    font-size: 13px;
  }

  .progress-container {
    grid-template-columns: 1fr;
  }
}
</style>
