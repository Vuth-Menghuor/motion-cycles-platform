<template>
  <div class="admin-customers-container">
    <nav class="breadcrumb">
      <router-link to="/admin/customers/list" class="breadcrumb-item">List Customers</router-link>
      <span class="breadcrumb-separator">></span>
      <span class="breadcrumb-item active">View Customer</span>
    </nav>

    <div class="customer-view-content">
      <div class="customer-header">
        <div class="customer-info">
          <h2><Icon icon="mdi:account" /> {{ customer?.name }}</h2>
          <div class="customer-meta">
            <span class="customer-id">ID: {{ customer?.customer_id }}</span>
            <span class="registration-date"
              >Registered {{ formatDate(customer?.registration_date) }}</span
            >
          </div>
        </div>
        <div class="customer-actions">
          <!-- Edit functionality removed -->
        </div>
      </div>

      <div v-if="isLoading" class="loading-section">
        <div class="loading-card">
          <Icon icon="mdi:loading" class="loading-icon" />
          <h3>Loading customer details...</h3>
        </div>
      </div>

      <div v-else-if="errorMessage" class="error-section">
        <div class="error-card">
          <Icon icon="mdi:alert-circle" class="error-icon" />
          <h3>
            {{
              errorMessage === 'Customer not found'
                ? 'Customer Not Found'
                : 'Error Loading Customer'
            }}
          </h3>
          <p>
            {{
              errorMessage === 'Customer not found'
                ? "The customer you're looking for doesn't exist or has been deleted."
                : errorMessage
            }}
          </p>
          <router-link to="/admin/customers/list" class="btn btn-primary">
            <Icon icon="mdi:arrow-left" />
            Back to Customers List
          </router-link>
        </div>
      </div>

      <template v-else-if="!errorMessage && !isLoading && customer">
        <div class="customer-view-inner">
          <div class="content-section">
            <div class="section-header">
              <h2><Icon icon="mdi:account-details" /> Customer Information</h2>
            </div>
            <div class="section-content">
              <div class="info-list">
                <div class="info-row">
                  <span class="info-label">Full Name:</span>
                  <span class="info-value">{{ customer.name }}</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Email Address:</span>
                  <span class="info-value">{{ customer.email }}</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Registration Date:</span>
                  <span class="info-value">{{ formatDate(customer.registration_date) }}</span>
                </div>
                <div class="info-row">
                  <span class="info-label">Total Orders:</span>
                  <span class="info-value">{{ customer.total_orders }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="content-section">
            <div class="section-header">
              <h2><Icon icon="mdi:history" /> Order History</h2>
            </div>
            <div class="section-content">
              <div v-if="customer.orders && customer.orders.length > 0">
                <div class="orders-list">
                  <div v-for="order in customer.orders" :key="order.id" class="order-group">
                    <div class="order-header-info">
                      <div class="order-header-row">
                        <div class="order-left">
                          <div class="order-number">Order #{{ order.order_number }}</div>
                        </div>
                        <div class="order-date">{{ formatDate(order.created_at) }}</div>
                      </div>
                      <div class="item-flex-container">
                        <img
                          v-if="order.items && order.items.length > 0"
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
                              Quantity: {{ order.items[0].quantity || 1 }}
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
                        <span>-${{ calculateItemDiscount(order).toFixed(2) }}</span>
                      </div>
                      <div class="summary-row">
                        <span>Net Price</span>
                        <span
                          >${{
                            (
                              parseFloat(order.subtotal || 0) - calculateItemDiscount(order)
                            ).toFixed(2)
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
                          ><strong>${{ calculateTotal(order).toFixed(2) }}</strong></span
                        >
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div v-else class="no-orders">
                <Icon icon="mdi:package-variant-closed" class="no-orders-icon" />
                <h4>No orders yet</h4>
                <p>This customer hasn't placed any orders.</p>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Icon } from '@iconify/vue'
import { usersApi } from '@/services/api'

const props = defineProps({
  id: { type: [String, Number], required: true },
})

const customer = ref(null)
const isLoading = ref(false)
const errorMessage = ref('')

// Load customer data from API
const loadCustomer = async () => {
  try {
    isLoading.value = true
    errorMessage.value = ''

    const response = await usersApi.getUser(props.id)

    if (response.data.success) {
      customer.value = response.data.data
    } else {
      errorMessage.value = response.data.message || 'Failed to load customer'
    }
  } catch (error) {
    console.error('Error loading customer:', error)
    if (error.response?.status === 404) {
      errorMessage.value = 'Customer not found'
    } else {
      errorMessage.value = error.response?.data?.message || 'Failed to load customer'
    }
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

onMounted(() => loadCustomer())
</script>

<style scoped>
.admin-customers-container {
  margin: 0 auto;
  font-family: 'Poppins', sans-serif;
  color: #333;
  background: #f8fafc;
}

.customer-view-content {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
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

.customer-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px;
  border-bottom: 1px solid #e9ecef;
  background: #f8f9fa;
}

.customer-info h2 {
  margin: 0 0 8px 0;
  font-size: 24px;
  font-weight: 600;
  color: #333;
  display: flex;
  align-items: center;
  gap: 10px;
}

.customer-meta {
  display: flex;
  gap: 16px;
  align-items: center;
}

.customer-id {
  color: #666;
  font-size: 14px;
  font-family: 'Monaco', 'Menlo', monospace;
  font-weight: 500;
}

.registration-date {
  color: #666;
  font-size: 14px;
}

.customer-status {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  text-transform: uppercase;
}

.customer-actions {
  display: flex;
  gap: 12px;
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

.content-section {
  background: white;
  border-bottom: 1px solid #e2e8f0;
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

.orders-grid {
  display: grid;
  gap: 16px;
}

.order-card {
  padding: 20px;
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 16px;
}

.order-header {
  display: flex;
  align-items: center;
  gap: 16px;
  flex: 1;
}

.order-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.order-number {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #1e293b;
}

.order-date {
  font-size: 12px;
  color: #64748b;
}

.order-status {
  display: flex;
  align-items: center;
}

.order-details {
  display: flex;
  align-items: center;
  gap: 24px;
}

.items-count {
  font-size: 14px;
  color: #64748b;
}

.total-amount {
  font-size: 16px;
  font-weight: 600;
  color: #10b981;
}

.order-actions {
  display: flex;
  gap: 8px;
}

.items-grid {
  display: grid;
  gap: 24px;
}

.order-section {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  overflow: hidden;
}

.order-header-section {
  background: #f8f9fa;
  padding: 16px 20px;
  border-bottom: 1px solid #e2e8f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
}

.order-header-section h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: #1e293b;
}

.order-status-badges {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.order-items {
  padding: 8px;
}

.item-card {
  display: flex;
  gap: 16px;
  background: white;
  border-radius: 6px;
  align-items: center;
  margin-bottom: 12px;
}

.item-card:last-child {
  margin-bottom: 0;
}

.item-image {
  width: 240px;
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
  font-size: 18px;
}

.item-category-brand {
  color: #64748b;
  font-size: 14px;
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
  color: #64748b;
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

.no-orders {
  text-align: center;
  padding: 48px;
  color: #666;
}

.no-orders-icon {
  font-size: 48px;
  color: #cbd5e0;
  margin-bottom: 16px;
}

.no-orders h4 {
  margin: 0 0 8px 0;
  color: #333;
}

.no-orders p {
  margin: 0;
}

.status-active {
  background: rgba(16, 185, 129, 0.2);
  color: #059669;
  border: 1px solid rgba(16, 185, 129, 0.3);
}

.status-inactive {
  background: rgba(239, 68, 68, 0.2);
  color: #dc2626;
  border: 1px solid rgba(239, 68, 68, 0.3);
}

.status-completed {
  background: rgba(16, 185, 129, 0.2);
  color: #059669;
}

.status-processing {
  background: rgba(59, 130, 246, 0.2);
  color: #2563eb;
}

.status-pending {
  background: rgba(255, 193, 7, 0.2);
  color: #d97706;
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

.customer-view-inner {
  overflow-y: scroll;
  max-height: calc(100vh - 200px);
  border-top: 1px solid #e2e8f0;
  height: 680px;
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

.btn-small {
  padding: 8px 16px;
  font-size: 12px;
}

.btn-primary {
  background: #14c9c9;
  color: white;
  border-color: #14c9c9;
}

.btn-primary:hover {
  background: #0fa5a5;
  border-color: #0fa5a5;
}

.badge {
  display: inline-block;
  padding: 4px 8px;
  margin: 2px;
  border: 1px solid #ddd;
  border-radius: 4px;
  background-color: #f0f0f0;
  color: #333;
  font-size: 12px;
  font-weight: 500;
}

.orders-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.order-group {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
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

.order-summary {
  background: #f8fafc;
  border-radius: 8px;
  padding: 20px;
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

.separator {
  color: grey;
}

@media (max-width: 768px) {
  .admin-customers-container {
    padding: 10px;
  }

  .customer-header {
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

  .info-list {
    max-width: 100%;
  }

  .info-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
  }

  .info-label {
    min-width: auto;
  }

  .info-value {
    text-align: left;
  }

  .order-card {
    flex-direction: column;
    align-items: flex-start;
  }

  .order-details {
    width: 100%;
    justify-content: space-between;
  }
}

@media (max-width: 480px) {
  .customer-header {
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
  }

  .info-value {
    text-align: left;
  }

  .order-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
}
</style>
