<template>
  <div class="admin-orders-container">
    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb">
      <router-link to="/admin/orders/list" class="breadcrumb-item">List Orders</router-link>
      <span class="breadcrumb-separator">></span>
      <span class="breadcrumb-item active">View Order</span>
    </nav>

    <div class="order-view-content">
      <!-- Order Header -->
      <div class="order-header">
        <div class="order-info">
          <h2><Icon icon="mdi:package-variant-closed" /> Order #{{ order?.order_number }}</h2>
          <div class="order-meta">
            <span class="order-date">Created {{ formatDate(order?.created_at) }}</span>
            <span :class="`order-status status-${order?.order_status}`">
              Order Status: {{ formatStatus(order?.order_status) }}
            </span>
            <span :class="`order-status status-${order?.payment_status}`">
              Payment Status: {{ formatStatus(order?.payment_status) }}
            </span>
          </div>
        </div>
        <div class="order-actions">
          <router-link :to="`/admin/orders/edit/${order?.id}`" class="btn btn-simple">
            <Icon icon="mdi:pencil" />
            Edit Order
          </router-link>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="!order" class="loading-section">
        <div class="loading-card">
          <Icon icon="mdi:loading" class="loading-icon" />
          <h3>Loading order details...</h3>
        </div>
      </div>

      <!-- Order Not Found -->
      <div v-else-if="!order.id" class="error-section">
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
              </div>
            </div>
          </div>

          <!-- Order Items Section -->
          <div class="content-section">
            <div class="section-header">
              <h2><Icon icon="mdi:cart" /> Order Items</h2>
            </div>
            <div class="section-content">
              <div class="items-grid">
                <div v-for="item in order.items" :key="item.id" class="item-card">
                  <img :src="item.image" :alt="item.name" class="item-image" />
                  <div class="item-details">
                    <div class="item-name">{{ item.name }}</div>
                    <div class="item-category">{{ getCategoryName(item.category) }}</div>
                    <div class="item-meta">
                      <span class="meta-item">Qty: {{ item.quantity }}</span>
                      <span class="meta-item">Price: ${{ item.price }}</span>
                      <span class="meta-item total"
                        >Total: ${{ (item.quantity * item.price).toFixed(2) }}</span
                      >
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Order Summary Section -->
          <div class="content-section">
            <div class="section-header">
              <h2><Icon icon="mdi:receipt" /> Order Summary</h2>
            </div>
            <div class="section-content">
              <div class="summary-card">
                <div class="summary-row">
                  <span>Subtotal</span>
                  <span>${{ order.subtotal || order.total_amount }}</span>
                </div>
                <div v-if="order.discount_amount > 0" class="summary-row discount">
                  <span>Discount</span>
                  <span>-${{ order.discount_amount }}</span>
                </div>
                <div class="summary-row shipping">
                  <span>Shipping</span>
                  <span>${{ order.shipping_amount || 0 }}</span>
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
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { Icon } from '@iconify/vue'

const props = defineProps({
  id: { type: [String, Number], required: true },
})

const order = ref(null)

const totalAmount = computed(() => {
  if (!order.value) return 0
  return (
    (order.value.subtotal || 0) +
    (order.value.shipping_amount || 0) -
    (order.value.discount_amount || 0)
  )
})

const mockOrders = [
  {
    id: 1,
    order_number: 'ORD-001',
    customer_name: 'John Doe',
    customer_email: 'john.doe@example.com',
    customer_phone: '+1-555-0123',
    payment_method: 'bakong',
    payment_status: 'completed',
    order_status: 'completed',
    subtotal: 299.99,
    discount_amount: 0,
    shipping_amount: 10.0,
    total_amount: 309.99,
    created_at: '2024-01-15T10:30:00Z',
    items: [
      {
        id: 1,
        name: 'Mountain Bike Pro',
        subtitle: 'Full suspension mountain bike',
        category: 'mountain',
        color: 'Black',
        quantity: 1,
        price: 299.99,
        image: '/placeholder-bike.jpg',
      },
    ],
  },
]

const loadOrder = () => {
  const orderId = parseInt(props.id)
  order.value = mockOrders.find((o) => o.id === orderId) || null
}

const formatStatus = (status) =>
  status ? status.charAt(0).toUpperCase() + status.slice(1) : 'Unknown'

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const getCategoryName = (category) => {
  if (!category) return 'Unknown'
  const categories = { mountain: 'Mountain', road: 'Road' }
  return categories[category] || category.charAt(0).toUpperCase() + category.slice(1)
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
  font-weight: 500;
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

.info-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  max-width: 600px;
}

.info-row {
  display: flex;
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
  min-width: 120px;
  flex-shrink: 0;
}

.info-value {
  font-size: 16px;
  font-weight: 500;
  color: #1e293b;
  margin-left: 16px;
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
  width: 80px;
  height: 80px;
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

.item-category {
  color: #64748b;
  font-size: 13px;
}

.item-meta {
  display: flex;
  gap: 16px;
  margin-top: 8px;
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

.summary-card {
  margin: 0 auto;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 0;
  border-bottom: 1px solid #f1f5f9;
}

.summary-row:last-child {
  border-bottom: none;
}

.summary-row.total {
  border-top: 2px solid #e2e8f0;
  padding-top: 20px;
  margin-top: 12px;
  font-size: 18px;
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

.status-pending {
  background: rgba(255, 193, 7, 0.2);
  color: #d97706;
  border: 1px solid rgba(255, 193, 7, 0.3);
}

.status-processing {
  background: rgba(59, 130, 246, 0.2);
  color: #2563eb;
  border: 1px solid rgba(59, 130, 246, 0.3);
}

.status-completed,
.status-confirmed {
  background: rgba(16, 185, 129, 0.2);
  color: #059669;
  border: 1px solid rgba(16, 185, 129, 0.3);
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

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
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

  .item-card {
    flex-direction: column;
    text-align: center;
    gap: 12px;
  }

  .item-image {
    width: 60px;
    height: 60px;
  }

  .item-details {
    text-align: center;
  }

  .item-meta {
    justify-content: center;
    flex-wrap: wrap;
    gap: 12px;
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
    justify-content: center;
    flex-wrap: wrap;
    gap: 8px;
  }

  .meta-item {
    font-size: 13px;
  }
}
</style>
