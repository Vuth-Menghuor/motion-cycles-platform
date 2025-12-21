<template>
  <div class="analytics-page">
    <MetricsCards
      :total-revenue="analyticsData.totalRevenue"
      :net-profit="analyticsData.netProfit"
      :total-orders="analyticsData.totalOrders"
      :active-customers="analyticsData.activeCustomers"
      :total-expenses="analyticsData.totalExpenses"
    />

    <div class="analytics-content">
      <ComprehensiveAnalyticsTable />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import MetricsCards from '@/components/admin/analytics/MetricsCards.vue'
import ComprehensiveAnalyticsTable from '@/components/admin/analytics/ComprehensiveAnalyticsTable.vue'
import { ordersApi } from '@/services/api'

// Orders data
const orders = ref([])
const isLoading = ref(false)

// Load orders from API
const loadOrders = async () => {
  try {
    isLoading.value = true
    const response = await ordersApi.adminListOrders()
    if (response.data.success) {
      orders.value = response.data.orders.data || []
    } else {
      orders.value = []
    }
  } catch (error) {
    console.error('Error loading orders:', error)
    orders.value = []
  } finally {
    isLoading.value = false
  }
}

// Calculate analytics data from orders
const analyticsData = computed(() => {
  let totalRevenue = 0

  orders.value.forEach((order) => {
    const subtotal = parseFloat(order.subtotal || 0)
    const shipping = parseFloat(order.shipping_amount || 0)
    const promoDiscount = parseFloat(order.discount_amount || 0)
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

    const correctTotal = subtotal - itemDiscount + shipping - promoDiscount
    totalRevenue += correctTotal
  })

  const expense = totalRevenue * 0.75 // Assume 75% expense
  const netProfit = totalRevenue - expense
  const totalOrders = orders.value.length
  const activeCustomers = new Set(orders.value.map((order) => order.customer_email)).size

  return {
    totalRevenue: Math.round(totalRevenue),
    netProfit: Math.round(netProfit),
    totalOrders,
    activeCustomers,
    totalExpenses: Math.round(expense),
  }
})

onMounted(() => {
  loadOrders()
})
</script>

<style scoped>
.analytics-page {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  font-family: 'Poppins', sans-serif;
  padding: 20px;
}

.analytics-content {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
  margin-top: 20px;
}

@media (max-width: 768px) {
  .analytics-page {
    padding: 16px;
  }
}
</style>
