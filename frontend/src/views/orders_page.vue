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

  <div class="orders-page">
    <div class="orders-container">
      <h1 class="page-title">My Orders</h1>

      <div v-if="loading" class="loading-state">
        <p>Loading your orders...</p>
      </div>

      <div v-else-if="error" class="error-state">
        <p>{{ error }}</p>
        <button @click="fetchOrders" class="retry-btn">Retry</button>
      </div>

      <div v-else-if="orders.length === 0" class="empty-state">
        <p>You haven't placed any orders yet.</p>
        <router-link to="/home" class="shop-btn">Start Shopping</router-link>
      </div>

      <div v-else class="orders-list">
        <div
          v-for="order in orders"
          :key="order.id"
          class="order-card"
          @click="viewOrderDetails(order.id)"
        >
          <div class="order-header">
            <div class="order-info">
              <h3>Order #{{ order.id }}</h3>
              <p class="order-date">{{ formatDate(order.created_at) }}</p>
            </div>
            <div class="order-status">
              <span :class="`status-${order.order_status?.toLowerCase()}`">{{
                order.order_status || 'Pending'
              }}</span>
            </div>
          </div>

          <div class="order-items">
            <div v-for="item in order.items?.slice(0, 2)" :key="item.id" class="order-item-preview">
              <img :src="item.image" :alt="item.name" class="item-image" />
              <div class="item-info">
                <p class="item-name">{{ item.name }}</p>
                <p class="item-category-brand">
                  <span v-if="item.category" class="badge">{{ item.category }}</span>
                  <span v-if="item.brand" class="badge">{{ item.brand }}</span>
                  <span v-if="item.color" class="badge">{{ item.color }}</span>
                </p>
                <p class="item-quantity">Quantity: {{ item.quantity }}</p>
              </div>
            </div>
            <div v-if="order.items?.length > 2" class="more-items">
              +{{ order.items.length - 2 }} more items
            </div>
          </div>

          <div class="order-footer">
            <div class="order-total">
              <span class="total-label">Total:</span>
              <span class="total-amount">${{ calculateOrderTotal(order) }}</span>
            </div>
            <button class="view-details-btn">View Details</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useOrdersStore } from '@/stores/orders'
import { storeToRefs } from 'pinia'
import Navigation_header from '@/components/navigation_header.vue'

const router = useRouter()
const ordersStore = useOrdersStore()
const { orders, loading, error } = storeToRefs(ordersStore)

const fetchOrders = async () => {
  try {
    await ordersStore.fetchUserOrders()
  } catch (err) {
    console.error('Error fetching orders:', err)
  }
}

const viewOrderDetails = (orderId) => {
  router.push(`/orders/${orderId}`)
}

// Calculate total amount for an order (accounting for item discounts and promo discounts)
const calculateOrderTotal = (order) => {
  const subtotal = parseFloat(order.subtotal || 0)
  const shipping = parseFloat(order.shipping_amount || 0)
  const promoDiscount = parseFloat(order.discount_amount || 0)

  // Calculate item discount
  const itemDiscount = (order.items || []).reduce((total, item) => {
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

  return subtotal - itemDiscount + shipping - promoDiscount
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

onMounted(() => {
  // Clear any stale orders and fetch fresh data
  ordersStore.clearOrders()
  fetchOrders()
})
</script>

<style scoped>
.orders-page {
  min-height: 100vh;
  background: #f8fafc;
  padding: 150px 20px 40px;
  font-family: 'Poppins', sans-serif;
}

.orders-container {
  max-width: 1200px;
  margin: 0 auto;
}

.page-title {
  font-size: 2rem;
  font-weight: 600;
  color: #1f2937;
  margin-bottom: 2rem;
  text-align: center;
}

.loading-state,
.error-state,
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.loading-state p,
.error-state p,
.empty-state p {
  font-size: 1.1rem;
  color: #6b7280;
  margin-bottom: 1rem;
}

.retry-btn,
.shop-btn {
  display: inline-block;
  padding: 12px 24px;
  background: #14c9c9;
  color: white;
  text-decoration: none;
  border-radius: 8px;
  font-weight: 500;
  transition: background 0.2s ease;
}

.retry-btn:hover,
.shop-btn:hover {
  background: #0fa5a5;
}

.orders-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.order-card {
  background: #f8fafc;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 1.5rem;
  cursor: pointer;
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.order-info h3 {
  margin: 0 0 0.25rem 0;
  font-size: 1.1rem;
  color: #1f2937;
}

.order-date {
  margin: 0;
  font-size: 0.9rem;
  color: #6b7280;
}

.order-status {
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

.order-items {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
}

.order-item-preview {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  background: #f8fafc;
  padding: 0.75rem;
  border-radius: 8px;
  flex: 1;
  min-width: 200px;
}

.item-image {
  width: 230px;
  height: auto;
  object-fit: cover;
  border-radius: 6px;
}

.item-info {
  flex: 1;
}

.item-name {
  margin: 0 0 0.25rem 0;
  font-size: 0.9rem;
  font-weight: 500;
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

.item-quantity {
  margin: 0;
  font-size: 0.8rem;
  color: #6b7280;
}

.more-items {
  display: flex;
  align-items: center;
  justify-content: center;
  background: #e5e7eb;
  padding: 0.75rem;
  border-radius: 8px;
  font-size: 0.9rem;
  color: #6b7280;
  font-weight: 500;
}

.order-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 1rem;
  border-top: 1px solid #e5e7eb;
}

.order-total {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.total-label {
  font-size: 0.9rem;
  color: #6b7280;
}

.total-amount {
  font-size: 1.2rem;
  font-weight: 600;
  color: #1f2937;
}

.view-details-btn {
  padding: 8px 16px;
  background: #14c9c9;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s ease;
}

.view-details-btn:hover {
  background: #0fa5a5;
}

@media (max-width: 768px) {
  .orders-page {
    padding: 120px 15px 40px;
  }

  .order-header {
    flex-direction: column;
    gap: 0.5rem;
  }

  .order-status {
    text-align: left;
  }

  .order-items {
    flex-direction: column;
  }

  .order-item-preview {
    min-width: auto;
  }

  .order-footer {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }

  .view-details-btn {
    align-self: flex-end;
    width: fit-content;
  }
}
</style>
