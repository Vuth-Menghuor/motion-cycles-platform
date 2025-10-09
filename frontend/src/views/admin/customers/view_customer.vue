<template>
  <div class="admin-customers-container">
    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb">
      <router-link to="/admin/customers/list" class="breadcrumb-item">List Customers</router-link>
      <span class="breadcrumb-separator">></span>
      <span class="breadcrumb-item active">View Customer</span>
    </nav>

    <div class="customer-view-content">
      <!-- Customer Header -->
      <div class="customer-header">
        <div class="customer-info">
          <h2><Icon icon="mdi:account" /> {{ customer?.name }}</h2>
          <div class="customer-meta">
            <span class="customer-id">ID: {{ customer?.customer_id }}</span>
            <span class="registration-date"
              >Registered {{ formatDate(customer?.registration_date) }}</span
            >
            <span :class="`customer-status status-${customer?.status}`">
              Status: {{ formatStatus(customer?.status) }}
            </span>
          </div>
        </div>
        <div class="customer-actions">
          <router-link :to="`/admin/customers/edit/${customer?.id}`" class="btn btn-simple">
            <Icon icon="mdi:pencil" />
            Edit Customer
          </router-link>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="!customer" class="loading-section">
        <div class="loading-card">
          <Icon icon="mdi:loading" class="loading-icon" />
          <h3>Loading customer details...</h3>
        </div>
      </div>

      <!-- Customer Not Found -->
      <div v-else-if="!customer.id" class="error-section">
        <div class="error-card">
          <Icon icon="mdi:alert-circle" class="error-icon" />
          <h3>Customer Not Found</h3>
          <p>The customer you're looking for doesn't exist or has been deleted.</p>
          <router-link to="/admin/customers/list" class="btn btn-primary">
            <Icon icon="mdi:arrow-left" />
            Back to Customers List
          </router-link>
        </div>
      </div>

      <!-- Customer Details -->
      <template v-else>
        <!-- Customer Information Section -->
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
                <span class="info-label">Phone Number:</span>
                <span class="info-value">{{ customer.phone || 'Not provided' }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Registration Date:</span>
                <span class="info-value">{{ formatDate(customer.registration_date) }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Total Orders:</span>
                <span class="info-value">{{ customer.total_orders }}</span>
              </div>
              <div class="info-row">
                <span class="info-label">Account Status:</span>
                <span :class="`status-badge status-${customer.status}`" class="info-value">
                  {{ formatStatus(customer.status) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Orders Section -->
        <div class="content-section">
          <div class="section-header">
            <h2><Icon icon="mdi:history" /> Recent Orders</h2>
          </div>
          <div class="section-content">
            <div
              v-if="customer.recent_orders && customer.recent_orders.length > 0"
              class="orders-grid"
            >
              <div v-for="order in customer.recent_orders" :key="order.id" class="order-card">
                <div class="order-header">
                  <div class="order-info">
                    <h4 class="order-number">{{ order.order_number }}</h4>
                    <span class="order-date">{{ formatDate(order.created_at) }}</span>
                  </div>
                  <div class="order-status">
                    <span :class="`status-badge status-${order.status}`">{{
                      formatStatus(order.status)
                    }}</span>
                  </div>
                </div>
                <div class="order-details">
                  <div class="order-items">
                    <span class="items-count">{{ order.items_count }} item(s)</span>
                  </div>
                  <div class="order-total">
                    <span class="total-amount">${{ order.total_amount }}</span>
                  </div>
                </div>
                <div class="order-actions">
                  <router-link :to="`/admin/orders/view/${order.id}`" class="btn btn-small">
                    <Icon icon="mdi:eye" />
                    View Order
                  </router-link>
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
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Icon } from '@iconify/vue'

// ===== PROPS =====
const props = defineProps({
  id: {
    type: [String, Number],
    required: true,
  },
})

// ===== REACTIVE DATA =====
const customer = ref(null)

// ===== MOCK DATA =====
// TODO: Replace with actual API calls in production
const mockCustomers = [
  {
    id: 1,
    customer_id: 'CUST-001',
    name: 'John Doe',
    email: 'john.doe@example.com',
    phone: '+1-555-0123',
    registration_date: '2024-01-15T10:30:00Z',
    total_orders: 5,
    total_spent: 1499.95,
    average_rating: 4.8,
    status: 'active',
    recent_orders: [
      {
        id: 1,
        order_number: 'ORD-001',
        created_at: '2024-01-15T10:30:00Z',
        status: 'completed',
        items_count: 1,
        total_amount: 299.99,
      },
      {
        id: 2,
        order_number: 'ORD-002',
        created_at: '2024-01-14T14:20:00Z',
        status: 'processing',
        items_count: 1,
        total_amount: 399.99,
      },
    ],
  },
  {
    id: 2,
    customer_id: 'CUST-002',
    name: 'Jane Smith',
    email: 'jane.smith@example.com',
    phone: '+1-555-0124',
    registration_date: '2024-01-14T14:20:00Z',
    total_orders: 3,
    total_spent: 899.97,
    average_rating: 4.5,
    status: 'active',
    recent_orders: [
      {
        id: 3,
        order_number: 'ORD-003',
        created_at: '2024-01-13T09:15:00Z',
        status: 'pending',
        items_count: 1,
        total_amount: 299.99,
      },
    ],
  },
]

// ===== METHODS =====
/**
 * Load customer data by ID
 */
const loadCustomer = () => {
  const customerId = parseInt(props.id)
  customer.value = mockCustomers.find((c) => c.id === customerId) || null

  if (!customer.value) {
    console.error('Customer not found')
  }
}

/**
 * Format status for display
 */
const formatStatus = (status) => {
  if (!status) return 'Unknown'
  return status.charAt(0).toUpperCase() + status.slice(1)
}

/**
 * Format date for display
 */
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

// ===== LIFECYCLE =====
onMounted(() => {
  loadCustomer()
})
</script>

<style scoped>
/* ===== LAYOUT ===== */
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

/* ===== BREADCRUMB ===== */
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

/* ===== HEADER ===== */
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

/* ===== LOADING & ERROR STATES ===== */
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

/* ===== CONTENT SECTIONS ===== */
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

/* ===== CUSTOMER INFORMATION ===== */
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
}

/* ===== RECENT ORDERS ===== */
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

/* ===== STATUS COLORS ===== */
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

/* ===== BUTTONS ===== */
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

/* ===== ANIMATIONS ===== */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* ===== RESPONSIVE ===== */
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
