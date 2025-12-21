<template>
  <div class="dashboard">
    <div class="stats-grid">
      <Stat_card
        title="Total Products"
        :value="stats.totalProducts"
        color="blue"
        icon="mdi:package-variant"
        :actions="['add', 'edit', 'view']"
        @action-click="handleStatAction"
      />
      <Stat_card
        title="Total Revenue"
        :value="stats.totalRevenue"
        prefix="$"
        color="green"
        icon="streamline-ultimate:money-bag-dollar-bold"
        :actions="['view']"
        @action-click="handleRevenueView"
      />
      <Stat_card
        title="Total Customer"
        :value="stats.totalCustomers"
        color="red"
        icon="ic:round-groups"
        :actions="['view']"
        @action-click="handleStatAction"
      />
    </div>

    <div class="chart-row">
      <Revenue_chart
        :analytics-data="chartAnalyticsData"
        :selected-view="selectedView"
        @date-changed="handleChartDateChange"
      />
      <Category_chart :categories="categoryData" />
    </div>

    <div class="stock-section">
      <Stock_alert_table :stock-data="stockAlerts" />
    </div>
  </div>
</template>

<script setup>
// Import dashboard components
import Category_chart from '@/components/admin/dashboard/category_chart.vue'
import Revenue_chart from '@/components/admin/dashboard/revenue_chart.vue'
import Stat_card from '@/components/admin/dashboard/stat_card.vue'
import Stock_alert_table from '@/components/admin/dashboard/stock_alert_table.vue'
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { dashboardApi, ordersApi } from '@/services/api.js'

// Reactive data
const stats = ref({
  totalProducts: 0,
  totalRevenue: 0,
  totalCustomers: 0,
})
const stockAlerts = ref([])
const categoryData = ref([])
const orders = ref([])
const selectedView = ref('daily')
const selectedChartDate = ref(new Date().toLocaleDateString('en-CA')) // Add this
const router = useRouter()

// Helper functions for date calculations
const getStartOfWeek = (date) => {
  const dayOfWeek = date.getDay() // 0 = Sunday, 1 = Monday, etc.
  let mondayOffset

  if (dayOfWeek === 0) {
    // If Sunday, go back 6 days to get to Monday
    mondayOffset = -6
  } else {
    // Otherwise, calculate offset to Monday
    mondayOffset = 1 - dayOfWeek
  }

  const startOfWeek = new Date(date)
  startOfWeek.setDate(date.getDate() + mondayOffset)
  return startOfWeek
}

const filterOrdersByDate = (ordersList, targetDate) => {
  return ordersList.filter((order) => {
    const orderDate = new Date(order.created_at)
    const orderLocalDate = new Date(
      orderDate.getFullYear(),
      orderDate.getMonth(),
      orderDate.getDate(),
    )
    const targetLocalDate = new Date(
      targetDate.getFullYear(),
      targetDate.getMonth(),
      targetDate.getDate(),
    )

    return orderLocalDate.getTime() === targetLocalDate.getTime()
  })
}

const filterOrdersByMonth = (ordersList, month, year) => {
  return ordersList.filter((order) => {
    const orderDate = new Date(order.created_at)
    return orderDate.getMonth() === month && orderDate.getFullYear() === year
  })
}

const calculateMetrics = (orders) => {
  let totalRevenue = 0

  orders.forEach((order) => {
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

  const ordersCount = orders.length
  const customers = new Set(orders.map((order) => order.customer_email)).size
  const expense = totalRevenue * 0.75 // Assume 75% expense
  const netProfit = totalRevenue - expense
  const margin = totalRevenue > 0 ? (netProfit / totalRevenue) * 100 : 0
  const aov = ordersCount > 0 ? totalRevenue / ordersCount : 0

  return {
    revenue: Math.round(totalRevenue),
    ordersCount,
    customers,
    expense: Math.round(expense),
    netProfit: Math.round(netProfit),
    margin: parseFloat(margin.toFixed(1)),
    aov: Math.round(aov),
  }
}

const processDailyData = (ordersList, startDate, days) => {
  const data = []
  const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']

  for (let i = 0; i < days; i++) {
    const currentDate = new Date(startDate)
    currentDate.setDate(startDate.getDate() + i)
    const dateString = currentDate.toLocaleDateString('en-CA')

    // Get orders for this specific date
    const dayOrders = filterOrdersByDate(ordersList, currentDate)

    // Calculate metrics for this day
    const metrics = calculateMetrics(dayOrders)

    data.push({
      id: i + 1,
      day: daysOfWeek[currentDate.getDay()],
      date: dateString,
      revenue: metrics.revenue,
      expense: metrics.expense,
      netProfit: metrics.netProfit,
      margin: metrics.margin,
      orders: metrics.ordersCount,
      customers: metrics.customers,
      aov: metrics.aov,
      growth: 0, // Will calculate later
      status: 'Good', // Default status
    })
  }

  return data
}

const processMonthlyData = (ordersList, year) => {
  const data = []
  const months = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December',
  ]

  months.forEach((monthName, index) => {
    // Get orders for this month
    const monthOrders = filterOrdersByMonth(ordersList, index, year)

    // Calculate metrics for this month
    const metrics = calculateMetrics(monthOrders)

    data.push({
      id: index + 1,
      period: monthName,
      revenue: metrics.revenue,
      expense: metrics.expense,
      netProfit: metrics.netProfit,
      margin: metrics.margin,
      orders: metrics.ordersCount,
      growth: 0, // Will calculate later
      customers: metrics.customers,
      aov: metrics.aov,
    })
  })

  return data
}

// Computed property for chart data
const chartAnalyticsData = computed(() => {
  if (orders.value.length === 0) {
    return []
  }

  const selectedDate = new Date(selectedChartDate.value)
  const startOfWeek = getStartOfWeek(selectedDate)

  if (selectedView.value === 'daily') {
    return processDailyData(orders.value, startOfWeek, 7)
  } else {
    const year = selectedDate.getFullYear()
    return processMonthlyData(orders.value, year)
  }
})

// Load dashboard data
const loadDashboardData = async () => {
  try {
    // Load orders data for revenue and customer calculations
    const ordersResponse = await ordersApi.adminListOrders()
    let totalRevenue = 0
    let totalCustomers = 0

    if (ordersResponse.data.success) {
      orders.value = ordersResponse.data.orders.data || []

      // Calculate correct total revenue
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

      totalCustomers = new Set(orders.value.map((order) => order.customer_email)).size
    }

    // Load stats
    const statsResponse = await dashboardApi.getStats()
    if (statsResponse.data.success) {
      stats.value = {
        totalProducts: statsResponse.data.data.total_products,
        totalRevenue: Math.round(totalRevenue),
        totalCustomers: totalCustomers,
      }
    }

    // Load stock alerts and map to expected format
    const stockResponse = await dashboardApi.getStockAlerts({ threshold: 10 })
    if (stockResponse.data.success) {
      stockAlerts.value = stockResponse.data.data.map((item) => ({
        brand: item.brand || 'Unknown',
        category: item.category,
        currentStock: item.current_stock,
        minStock: item.min_stock,
        status: item.status,
        stockAlert: item.stock_alert,
        lastUpdated: item.last_updated,
      }))
    }

    // Load category distribution
    const categoryResponse = await dashboardApi.getCategoryDistribution()
    if (categoryResponse.data.success) {
      categoryData.value = categoryResponse.data.data
    }
  } catch (error) {
    console.error('Failed to load dashboard data:', error)
    // Keep default values on error
  }
}

// Load data on component mount
onMounted(() => {
  loadDashboardData()
})

// Handle stat card action clicks
const handleStatAction = (eventData) => {
  const { action, title } = eventData
  switch (title) {
    case 'Total Customer':
      if (action === 'view') {
        router.push('/admin/customers/list')
      }
      break
    case 'Total Products':
      switch (action) {
        case 'add':
          router.push({ name: 'AddProduct' })
          break
        case 'edit':
        case 'view':
          router.push({ name: 'ManageProduct' })
          break
      }
      break
    default:
      console.warn('Unknown stat card title:', title)
  }
}

// Handle revenue view action
const handleRevenueView = () => {
  router.push('/admin/analytics')
}

// Handle chart date change
const handleChartDateChange = (newDate) => {
  selectedChartDate.value = newDate
}
</script>

<style scoped>
.dashboard {
  overflow-y: scroll;
  max-height: 88vh;
  margin: 0 auto;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 16px;
  margin-bottom: 25px;
}

.chart-row {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 20px;
  margin-bottom: 25px;
}

.stock-section {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
  margin-bottom: 25px;
}
</style>
