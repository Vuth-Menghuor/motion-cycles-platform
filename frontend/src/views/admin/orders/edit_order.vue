<template>
  <div class="admin-orders-container">
    <nav class="breadcrumb">
      <router-link to="/admin/orders/list" class="breadcrumb-item">List Orders</router-link>
      <span class="breadcrumb-separator">></span>
      <router-link :to="`/admin/orders/view/${order?.id}`" class="breadcrumb-item"
        >View Order</router-link
      >
      <span class="breadcrumb-separator">></span>
      <span class="breadcrumb-item active">Edit Order</span>
    </nav>

    <div class="order-edit-content">
      <div class="order-header">
        <div class="order-info">
          <h2>Edit Order #{{ order?.order_number }}</h2>
          <div class="order-meta">
            <span class="order-date">Created {{ formatDate(order?.created_at) }}</span>
            <span :class="`order-status status-${formData.order_status}`">
              {{ formatStatus(formData.order_status) }}
            </span>
          </div>
        </div>
        <div class="order-actions">
          <router-link :to="`/admin/orders/view/${order?.id}`" class="btn btn-secondary">
            <Icon icon="mdi:arrow-left" />
            Back to View
          </router-link>
          <button @click="saveOrder" class="btn btn-primary" :disabled="isSaving">
            <Icon icon="mdi:content-save" />
            {{ isSaving ? 'Updating...' : 'Update Status' }}
          </button>
        </div>
      </div>

      <div class="form-section">
        <div class="section-header">
          <h2><Icon icon="mdi:clipboard-check" /> Update Order Status</h2>
        </div>
        <div class="section-content">
          <div class="status-form">
            <div class="form-group">
              <label for="order-status">Order Status</label>
              <select id="order-status" v-model="formData.order_status" class="status-select">
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="confirmed">Confirmed</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>

            <div class="form-group">
              <label for="payment-status">Payment Status</label>
              <select id="payment-status" v-model="formData.payment_status" class="status-select">
                <option value="pending">Pending</option>
                <option value="processing">Processing</option>
                <option value="completed">Completed</option>
                <option value="failed">Failed</option>
                <option value="refunded">Refunded</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="timeline-section">
        <div class="section-header">
          <h2><Icon icon="mdi:timeline-clock" /> Order Progress</h2>
        </div>
        <div class="section-content">
          <div class="timeline-card">
            <div class="timeline-steps">
              <div
                class="timeline-step"
                :class="{
                  active: ['pending', 'processing', 'confirmed', 'completed'].includes(
                    formData.order_status,
                  ),
                  completed: formData.order_status === 'completed',
                }"
              >
                <div class="step-icon">
                  <Icon icon="mdi:cart-outline" />
                </div>
                <span class="step-label">Order Placed</span>
                <span class="step-date">{{ formatDate(order?.created_at) }}</span>
              </div>
              <div
                class="timeline-step"
                :class="{
                  active: ['processing', 'confirmed', 'completed'].includes(formData.order_status),
                  completed: ['confirmed', 'completed'].includes(formData.order_status),
                }"
              >
                <div class="step-icon">
                  <Icon icon="mdi:cog-outline" />
                </div>
                <span class="step-label">Processing</span>
                <span class="step-date" v-if="formData.order_status !== 'pending'"
                  >In Progress</span
                >
              </div>
              <div
                class="timeline-step"
                :class="{
                  active: ['confirmed', 'completed'].includes(formData.order_status),
                  completed: formData.order_status === 'completed',
                }"
              >
                <div class="step-icon">
                  <Icon icon="mdi:check-circle-outline" />
                </div>
                <span class="step-label">Confirmed</span>
                <span
                  class="step-date"
                  v-if="['confirmed', 'completed'].includes(formData.order_status)"
                  >Ready</span
                >
              </div>
              <div
                class="timeline-step"
                :class="{ completed: formData.order_status === 'completed' }"
              >
                <div class="step-icon">
                  <Icon icon="mdi:truck-delivery-outline" />
                </div>
                <span class="step-label">Delivered</span>
                <span class="step-date" v-if="formData.order_status === 'completed'"
                  >Completed</span
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
import { ref, reactive, onMounted } from 'vue'
import { Icon } from '@iconify/vue'

const order = ref(null)
const isSaving = ref(false)
const formData = reactive({
  order_status: 'pending',
  payment_status: 'pending',
})

const mockOrder = {
  id: 1,
  order_number: 'ORD-001',
  customer_name: 'John Doe',
  payment_method: 'bakong',
  payment_status: 'completed',
  order_status: 'completed',
  created_at: '2024-01-15T10:30:00Z',
  items: [{ id: 1, name: 'Mountain Bike Pro', category: 'mountain' }],
}

const statusMap = {
  pending: 'Pending',
  processing: 'Processing',
  confirmed: 'Confirmed',
  completed: 'Completed',
  cancelled: 'Cancelled',
  failed: 'Failed',
  refunded: 'Refunded',
}

const formatStatus = (status) => statusMap[status] || status

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

const loadOrder = () => {
  order.value = mockOrder
  formData.order_status = mockOrder.order_status
  formData.payment_status = mockOrder.payment_status
}

const saveOrder = async () => {
  isSaving.value = true
  try {
    console.log('Updating:', formData)
    await new Promise((resolve) => setTimeout(resolve, 1000))
    alert('Order status updated successfully!')
  } catch (error) {
    console.error('Error:', error)
    alert('Error updating order status')
  } finally {
    isSaving.value = false
  }
}

onMounted(loadOrder)
</script>

<style scoped>
.admin-orders-container {
  margin: 0 auto;
  font-family: 'Poppins', sans-serif;
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

.order-edit-content {
  background: white;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
  margin: 0 auto;
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px;
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
  background: rgba(16, 185, 129, 0.2);
  color: #059669;
  border: 1px solid rgba(16, 185, 129, 0.3);
}

.order-actions {
  display: flex;
  gap: 12px;
  align-items: center;
}

.form-section {
  margin: 24px;
  background: white;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
}

.form-section:last-child {
  margin-bottom: 32px;
}

.section-header {
  padding: 20px 24px;
  background: #f8f9fa;
  border-bottom: 1px solid #e2e8f0;
}

.section-header h2 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #1e293b;
  display: flex;
  align-items: center;
  gap: 8px;
}

.section-content {
  padding: 24px;
}

.status-form {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-size: 14px;
  font-weight: 500;
  color: #374151;
}

.status-select {
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  background: white;
  cursor: pointer;
  font-family: 'Poppins', sans-serif;
  transition: border-color 0.2s;
}

.status-select:focus {
  outline: none;
  border-color: #14c9c9;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 10px 16px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  text-decoration: none;
  font-family: 'Poppins', sans-serif;
}

.btn-primary {
  background: #14c9c9;
  color: white;
}

.btn-primary:disabled {
  background: #ccc;
  cursor: not-allowed;
}

.btn-secondary {
  background: #f8f9fa;
  color: #333;
  border: 1px solid #dee2e6;
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

.status-refunded {
  background: #6b7280;
  color: white;
}

.timeline-section {
  border-bottom: 1px solid #f1f5f9;
}

.timeline-section:last-child {
  border-bottom: none;
}

.timeline-card {
  background: white;
  border-radius: 8px;
  padding: 24px;
  margin-bottom: 24px;
  border: 1px solid #e2e8f0;
}

.timeline-steps {
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: relative;
  padding: 0 20px;
}

.timeline-steps::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 60px;
  right: 60px;
  height: 1px;
  background: #d1d5db;
  z-index: 1;
}

.timeline-step {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
  z-index: 2;
  opacity: 0.6;
  transition: opacity 0.3s ease;
}

.timeline-step.active {
  opacity: 1;
}

.timeline-step.completed {
  opacity: 1;
}

.timeline-step.completed .step-icon {
  background: #d1fae5;
  border-color: #10b981;
  color: #10b981;
}

.timeline-step.active .step-icon {
  background: #dbeafe;
  border-color: #3b82f6;
  color: #3b82f6;
}

.step-icon {
  width: 40px;
  height: 40px;
  border-radius: 6px;
  background: #f3f4f6;
  border: 1px solid #e5e7eb;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 30px;
  transition: all 0.3s ease;
  color: #9ca3af;
}

.step-label {
  font-size: 12px;
  font-weight: 500;
  color: #374151;
  text-align: center;
  margin-bottom: 4px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.step-date {
  font-size: 10px;
  color: #6b7280;
  text-align: center;
  font-weight: 400;
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
    padding: 16px 20px;
  }

  .section-header h2 {
    font-size: 16px;
  }

  .section-content {
    padding: 20px;
  }

  .status-form {
    grid-template-columns: 1fr;
    gap: 16px;
  }

  .timeline-steps {
    flex-direction: column;
    gap: 24px;
    padding: 0;
  }

  .timeline-steps::before {
    left: 20px;
    right: 20px;
    top: 20px;
    width: 1px;
    height: calc(100% - 40px);
    background: #d1d5db;
  }

  .timeline-step {
    flex-direction: row;
    width: 100%;
    justify-content: flex-start;
    padding-left: 20px;
  }

  .step-icon {
    margin-bottom: 0;
    margin-right: 16px;
    flex-shrink: 0;
  }

  .step-label,
  .step-date {
    text-align: left;
    margin-bottom: 0;
  }

  .step-label {
    margin-bottom: 2px;
  }
}
</style>
