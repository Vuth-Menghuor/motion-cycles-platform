<template>
  <div class="admin-orders-container">
    <div class="orders-header">
      <h1>Order Management</h1>
      <div class="filters">
        <select v-model="selectedPaymentStatus" @change="loadOrders">
          <option value="">All Payment Status</option>
          <option value="pending">Pending</option>
          <option value="processing">Processing</option>
          <option value="completed">Completed</option>
          <option value="failed">Failed</option>
        </select>
        <select v-model="selectedOrderStatus" @change="loadOrders">
          <option value="">All Order Status</option>
          <option value="pending">Pending</option>
          <option value="confirmed">Confirmed</option>
          <option value="processing">Processing</option>
          <option value="shipped">Shipped</option>
          <option value="delivered">Delivered</option>
          <option value="cancelled">Cancelled</option>
        </select>
        <button @click="loadOrders" class="btn btn-primary">Refresh</button>
      </div>
    </div>

    <div v-if="isLoading" class="loading-state">
      <div class="loading-spinner"></div>
      <p>Loading orders...</p>
    </div>

    <div v-else-if="hasError" class="error-state">
      <p>Error loading orders: {{ errorMessage }}</p>
      <button @click="loadOrders" class="btn btn-secondary">Try Again</button>
    </div>

    <div v-else class="orders-content">
      <div v-if="orders.length === 0" class="no-orders">
        <p>No orders found.</p>
      </div>

      <div v-else class="orders-table">
        <table>
          <thead>
            <tr>
              <th>Order #</th>
              <th>Customer</th>
              <th>Amount</th>
              <th>Payment Status</th>
              <th>Order Status</th>
              <th>Payment Method</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="order in orders" :key="order.id" class="order-row">
              <td>
                <strong>{{ order.order_number }}</strong>
                <small class="invoice-number">{{ order.invoice_number }}</small>
              </td>
              <td>
                <div class="customer-info">
                  <div>{{ order.customer_name }}</div>
                  <small>{{ order.customer_phone }}</small>
                </div>
              </td>
              <td>
                <strong>${{ order.total_amount }}</strong>
                <small>{{ order.currency }}</small>
              </td>
              <td>
                <span :class="`status status-${order.payment_status}`">
                  {{ formatStatus(order.payment_status) }}
                </span>
              </td>
              <td>
                <span :class="`status status-${order.order_status}`">
                  {{ formatStatus(order.order_status) }}
                </span>
              </td>
              <td>{{ order.payment_method }}</td>
              <td>{{ formatDate(order.created_at) }}</td>
              <td>
                <div class="actions">
                  <button @click="viewOrder(order)" class="btn btn-sm btn-secondary">View</button>
                  <button
                    v-if="order.payment_status === 'processing'"
                    @click="checkPayment(order)"
                    class="btn btn-sm btn-primary"
                  >
                    Check Payment
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="pagination">
        <button
          @click="changePage(pagination.current_page - 1)"
          :disabled="pagination.current_page <= 1"
          class="btn btn-secondary"
        >
          Previous
        </button>
        <span class="page-info">
          Page {{ pagination.current_page }} of {{ pagination.last_page }}
        </span>
        <button
          @click="changePage(pagination.current_page + 1)"
          :disabled="pagination.current_page >= pagination.last_page"
          class="btn btn-secondary"
        >
          Next
        </button>
      </div>
    </div>

    <!-- Order Detail Modal -->
    <div v-if="selectedOrder" class="modal-overlay" @click="closeModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Order Details</h3>
          <button @click="closeModal" class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
          <div class="order-info">
            <div class="info-section">
              <h4>Order Information</h4>
              <p><strong>Order Number:</strong> {{ selectedOrder.order_number }}</p>
              <p><strong>Invoice Number:</strong> {{ selectedOrder.invoice_number }}</p>
              <p><strong>Date:</strong> {{ formatDate(selectedOrder.created_at) }}</p>
              <p>
                <strong>Payment Status:</strong>
                <span :class="`status status-${selectedOrder.payment_status}`">
                  {{ formatStatus(selectedOrder.payment_status) }}
                </span>
              </p>
              <p>
                <strong>Order Status:</strong>
                <span :class="`status status-${selectedOrder.order_status}`">
                  {{ formatStatus(selectedOrder.order_status) }}
                </span>
              </p>
            </div>

            <div class="info-section">
              <h4>Customer Information</h4>
              <p><strong>Name:</strong> {{ selectedOrder.customer_name }}</p>
              <p><strong>Phone:</strong> {{ selectedOrder.customer_phone }}</p>
              <p><strong>Email:</strong> {{ selectedOrder.customer_email || 'N/A' }}</p>
            </div>

            <div class="info-section">
              <h4>Order Items</h4>
              <div v-for="item in selectedOrder.items" :key="item.id" class="order-item">
                <img :src="item.image" :alt="item.name" class="item-image" />
                <div class="item-details">
                  <p>
                    <strong>{{ item.name }}</strong>
                  </p>
                  <p>{{ item.subtitle }} | Color: {{ item.color }}</p>
                  <p>
                    Qty: {{ item.quantity }} × ${{ item.price }} = ${{
                      (item.quantity * item.price).toFixed(2)
                    }}
                  </p>
                </div>
              </div>
            </div>

            <div class="info-section">
              <h4>Order Summary</h4>
              <div class="summary-row">
                <span>Subtotal:</span>
                <span>${{ selectedOrder.subtotal }}</span>
              </div>
              <div v-if="selectedOrder.discount_amount > 0" class="summary-row">
                <span>Discount:</span>
                <span>-${{ selectedOrder.discount_amount }}</span>
              </div>
              <div class="summary-row">
                <span>Shipping:</span>
                <span>${{ selectedOrder.shipping_amount }}</span>
              </div>
              <div class="summary-row total">
                <span><strong>Total:</strong></span>
                <span
                  ><strong>${{ selectedOrder.total_amount }}</strong></span
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { listOrders, checkOrderPaymentStatus } from '@/services/orders'

// State
const orders = ref([])
const isLoading = ref(false)
const hasError = ref(false)
const errorMessage = ref('')
const selectedPaymentStatus = ref('')
const selectedOrderStatus = ref('')
const selectedOrder = ref(null)
const pagination = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
})

// Methods
const loadOrders = async (page = 1) => {
  isLoading.value = true
  hasError.value = false
  errorMessage.value = ''

  try {
    const filters = {
      page: page,
      per_page: pagination.value.per_page,
    }

    if (selectedPaymentStatus.value) {
      filters.payment_status = selectedPaymentStatus.value
    }

    if (selectedOrderStatus.value) {
      filters.order_status = selectedOrderStatus.value
    }

    const result = await listOrders(filters)

    if (result.success) {
      orders.value = result.orders.data
      pagination.value = {
        current_page: result.orders.current_page,
        last_page: result.orders.last_page,
        per_page: result.orders.per_page,
        total: result.orders.total,
      }
    } else {
      throw new Error(result.message || 'Failed to load orders')
    }
  } catch (error) {
    console.error('Error loading orders:', error)
    hasError.value = true
    errorMessage.value = error.message || 'Failed to load orders'
  } finally {
    isLoading.value = false
  }
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    loadOrders(page)
  }
}

const formatStatus = (status) => {
  return status.charAt(0).toUpperCase() + status.slice(1)
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const viewOrder = (order) => {
  selectedOrder.value = order
}

const closeModal = () => {
  selectedOrder.value = null
}

const checkPayment = async (order) => {
  try {
    const result = await checkOrderPaymentStatus(order.id)
    if (result.success) {
      if (result.payment_status === 'completed') {
        alert('Payment completed! Refreshing orders...')
        loadOrders()
      } else {
        alert('Payment still pending')
      }
    }
  } catch (error) {
    console.error('Error checking payment:', error)
    alert('Error checking payment status')
  }
}

// Lifecycle
onMounted(() => {
  loadOrders()
})
</script>

<style scoped>
.admin-orders-container {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
  font-family: 'Poppins', sans-serif;
}

.orders-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
  flex-wrap: wrap;
  gap: 1rem;
}

.orders-header h1 {
  font-size: 2rem;
  font-weight: 600;
  color: #333;
  margin: 0;
}

.filters {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.filters select {
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 0.9rem;
}

.btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-primary {
  background: #14c9c9;
  color: white;
}

.btn-primary:hover {
  background: #0fa5a5;
}

.btn-secondary {
  background: #f8f9fa;
  color: #333;
  border: 1px solid #dee2e6;
}

.btn-secondary:hover {
  background: #e9ecef;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.8rem;
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.loading-state,
.error-state,
.no-orders {
  text-align: center;
  padding: 3rem;
  color: #666;
}

.loading-spinner {
  border: 3px solid #f3f3f3;
  border-top: 3px solid #14c9c9;
  border-radius: 50%;
  width: 30px;
  height: 30px;
  animation: spin 1s linear infinite;
  margin: 0 auto 1rem;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}

.orders-table {
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.orders-table table {
  width: 100%;
  border-collapse: collapse;
}

.orders-table th {
  background: #f8f9fa;
  padding: 1rem;
  text-align: left;
  font-weight: 600;
  color: #333;
  border-bottom: 1px solid #dee2e6;
}

.orders-table td {
  padding: 1rem;
  border-bottom: 1px solid #f1f1f1;
  vertical-align: top;
}

.order-row:hover {
  background: #f8f9fa;
}

.invoice-number {
  display: block;
  color: #666;
  font-size: 0.8rem;
}

.customer-info div {
  font-weight: 500;
}

.customer-info small {
  color: #666;
  font-size: 0.8rem;
}

.status {
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: 500;
  text-transform: uppercase;
}

.status-pending {
  background: #fff3cd;
  color: #856404;
}

.status-processing {
  background: #cce7ff;
  color: #0066cc;
}

.status-completed,
.status-confirmed {
  background: #d4edda;
  color: #155724;
}

.status-failed,
.status-cancelled {
  background: #f8d7da;
  color: #721c24;
}

.actions {
  display: flex;
  gap: 0.5rem;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
  margin-top: 2rem;
}

.page-info {
  color: #666;
  font-size: 0.9rem;
}

/* Modal Styles */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 8px;
  max-width: 800px;
  max-height: 90vh;
  width: 90%;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem;
  border-bottom: 1px solid #eee;
}

.modal-header h3 {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
}

.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #666;
}

.close-btn:hover {
  color: #333;
}

.modal-body {
  padding: 1.5rem;
}

.info-section {
  margin-bottom: 2rem;
}

.info-section h4 {
  margin: 0 0 1rem;
  font-size: 1.1rem;
  font-weight: 600;
  color: #333;
  border-bottom: 1px solid #eee;
  padding-bottom: 0.5rem;
}

.info-section p {
  margin: 0.5rem 0;
  font-size: 0.9rem;
}

.order-item {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
}

.item-image {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 4px;
}

.item-details p {
  margin: 0.25rem 0;
  font-size: 0.8rem;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 0.5rem;
  font-size: 0.9rem;
}

.summary-row.total {
  border-top: 1px solid #eee;
  padding-top: 0.5rem;
  margin-top: 0.5rem;
  font-size: 1rem;
}

@media (max-width: 768px) {
  .orders-header {
    flex-direction: column;
    align-items: stretch;
  }

  .filters {
    flex-wrap: wrap;
  }

  .orders-table {
    overflow-x: auto;
  }

  .orders-table table {
    min-width: 800px;
  }

  .modal-content {
    width: 95%;
    max-height: 95vh;
  }

  .modal-header,
  .modal-body {
    padding: 1rem;
  }
}
</style>
