<template>
  <div class="analytics-table-container">
    <div class="table-header">
      <h3 class="table-title">Revenue & Profit Analytics</h3>
      <div class="filters-wrapper">
        <div class="table-filters">
          <select v-model="selectedView" @change="updateData" class="view-select">
            <option value="daily">Weekly Tracking</option>
            <option value="monthly">Monthly Summary</option>
          </select>

          <!-- Date Picker Button -->
          <div class="date-picker-container">
            <button @click="toggleCalendar" class="date-picker-button">
              <Icon icon="mdi:calendar" class="date-icon" width="16" height="16" />
              <span class="selected-date-text">
                {{
                  selectedView === 'daily'
                    ? formatDate(selectedDate)
                    : `${currentMonthName} ${currentYear}`
                }}
              </span>
              <span class="dropdown-arrow" :class="{ rotated: showCalendar }">▼</span>
            </button>

            <!-- Calendar Overlay -->
            <div v-if="showCalendar" class="calendar-overlay">
              <div class="calendar-dropdown" @click.stop>
                <div class="calendar-header">
                  <button @click="previousMonth" class="calendar-nav-btn">‹</button>
                  <span class="calendar-month-year">
                    {{ currentMonthName }} {{ currentYear }}
                  </span>
                  <button @click="nextMonth" class="calendar-nav-btn">›</button>
                </div>

                <div class="calendar-grid">
                  <div class="calendar-day-header" v-for="day in dayNames" :key="day">
                    {{ day }}
                  </div>

                  <div
                    v-for="date in calendarDates"
                    :key="date.dateString"
                    class="calendar-day"
                    :class="{
                      selected: isSelectedDate(date.dateString),
                      today: isToday(date.dateString),
                      'current-month': date.isCurrentMonth,
                      disabled: !date.isSelectable,
                    }"
                    @click="date.isSelectable ? selectDate(date.dateString) : null"
                  >
                    {{ date.day }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="table-wrapper">
      <table class="analytics-table">
        <thead>
          <tr>
            <th class="text-left" v-if="selectedView === 'daily'">Day</th>
            <th class="text-left" v-if="selectedView === 'daily'">Date</th>
            <th class="text-left" v-if="selectedView === 'monthly'">Period</th>
            <th class="text-left">Revenue</th>
            <th class="text-left">Expense</th>
            <th class="text-left">Net Profit</th>
            <th class="text-left">Orders</th>
            <th class="text-left" v-if="selectedView === 'daily'">Customers</th>
            <th class="text-left" v-if="selectedView === 'daily'">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="item in analyticsData"
            :key="item.id"
            class="table-row"
            :class="{ 'selected-date-row': isSelectedDateRow(item.date) }"
          >
            <td class="day-cell" v-if="selectedView === 'daily'">{{ item.day }}</td>
            <td class="date-cell" v-if="selectedView === 'daily'">{{ formatDate(item.date) }}</td>
            <td class="period-cell" v-if="selectedView === 'monthly'">{{ item.period }}</td>
            <td class="revenue-cell">${{ formatNumber(item.revenue) }}</td>
            <td class="expense-cell">${{ formatNumber(item.expense) }}</td>
            <td class="net-profit-cell" :class="{ negative: item.netProfit < 0 }">
              ${{ formatNumber(Math.abs(item.netProfit)) }}
            </td>
            <td class="orders-cell">{{ item.orders.toLocaleString() }}</td>
            <td class="customers-cell" v-if="selectedView === 'daily'">
              {{ item.customers.toLocaleString() }}
            </td>
            <td class="status-cell" v-if="selectedView === 'daily'">
              <span class="status-badge" :class="getStatusClass(item.status)">
                {{ item.status }}
              </span>
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr class="summary-row">
            <td class="summary-label" :colspan="getColspan()">
              <strong>{{ selectedView === 'daily' ? 'Weekly Summary' : 'Yearly Summary' }}</strong>
            </td>
            <td class="summary-revenue revenue-cell">
              <strong>${{ formatNumber(totalRevenue) }}</strong>
            </td>
            <td class="summary-expense expense-cell">
              <strong>${{ formatNumber(totalExpense) }}</strong>
            </td>
            <td
              class="summary-net-profit net-profit-cell"
              :class="{ negative: totalNetProfit < 0 }"
            >
              <strong>${{ formatNumber(Math.abs(totalNetProfit)) }}</strong>
            </td>
            <td class="summary-orders orders-cell">
              <strong>{{ totalOrders.toLocaleString() }}</strong>
            </td>
            <td class="summary-customers customers-cell" v-if="selectedView === 'daily'">
              <strong>{{ totalCustomers.toLocaleString() }}</strong>
            </td>
            <td class="summary-status status-cell" v-if="selectedView === 'daily'">
              <span class="status-badge excellent">Excellent</span>
            </td>
          </tr>
        </tfoot>
      </table>

      <!-- Week Pagination Controls (only for daily view) -->
      <div v-if="selectedView === 'daily'" class="week-info">
        <span class="date-indicator"> Week of {{ weekRange }} </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { Icon } from '@iconify/vue'
import { ordersApi } from '@/services/api'

// Reactive data
const selectedView = ref('daily')
const selectedDate = ref(new Date().toLocaleDateString('en-CA')) // Use local date in YYYY-MM-DD format
const currentCalendarDate = ref(new Date())
const showCalendar = ref(false)
const orders = ref([])
const isLoading = ref(false)

// Watch for date changes
watch(selectedDate, (newDate) => {
  const date = new Date(newDate)
  currentCalendarDate.value = new Date(date.getFullYear(), date.getMonth(), 1)
})

// Constants
const monthNames = [
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
const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']

// Computed properties for calendar
const currentMonthName = computed(() => monthNames[currentCalendarDate.value.getMonth()])
const currentYear = computed(() => currentCalendarDate.value.getFullYear())

const calendarDates = computed(() => {
  const year = currentCalendarDate.value.getFullYear()
  const month = currentCalendarDate.value.getMonth()
  const firstDay = new Date(year, month, 1)
  const startDate = new Date(firstDay)
  startDate.setDate(startDate.getDate() - firstDay.getDay())

  const today = new Date()
  const dataStartDate = new Date('2025-10-01')
  const todayNormalized = new Date(today.getFullYear(), today.getMonth(), today.getDate())
  const startDateLimit = new Date(
    dataStartDate.getFullYear(),
    dataStartDate.getMonth(),
    dataStartDate.getDate(),
  )

  const dates = []
  for (let i = 0; i < 42; i++) {
    const currentDate = new Date(startDate)
    currentDate.setDate(startDate.getDate() + i)
    const dateString = currentDate.toLocaleDateString('en-CA') // Use local date format
    const checkDate = new Date(
      currentDate.getFullYear(),
      currentDate.getMonth(),
      currentDate.getDate(),
    )
    const isSelectable = checkDate >= startDateLimit && checkDate <= todayNormalized

    dates.push({
      dateString,
      day: currentDate.getDate(),
      isCurrentMonth: currentDate.getMonth() === month,
      isSelectable,
    })
  }
  return dates
})

// Calendar functions
const isSelectedDate = (dateString) => selectedDate.value === dateString
const isToday = (dateString) => new Date().toLocaleDateString('en-CA') === dateString

const selectDate = (dateString) => {
  selectedDate.value = dateString
  showCalendar.value = false
  updateData()
}

const toggleCalendar = () => (showCalendar.value = !showCalendar.value)

const previousMonth = () => {
  currentCalendarDate.value = new Date(
    currentCalendarDate.value.getFullYear(),
    currentCalendarDate.value.getMonth() - 1,
    1,
  )
}

const nextMonth = () => {
  currentCalendarDate.value = new Date(
    currentCalendarDate.value.getFullYear(),
    currentCalendarDate.value.getMonth() + 1,
    1,
  )
}

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

const isDateInFuture = (dateString) => {
  const selectedDate = new Date(dateString)
  const today = new Date()
  const currentDate = new Date(today.getFullYear(), today.getMonth(), today.getDate())
  const checkDate = new Date(
    selectedDate.getFullYear(),
    selectedDate.getMonth(),
    selectedDate.getDate(),
  )

  return checkDate > currentDate
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

const determineStatus = (margin) => {
  if (margin >= 25) {
    return 'Excellent'
  } else if (margin >= 15) {
    return 'Good'
  } else if (margin >= 5) {
    return 'Fair'
  } else {
    return 'Poor'
  }
}

const calculateGrowth = (data) => {
  for (let i = 1; i < data.length; i++) {
    const prevRevenue = data[i - 1].revenue
    const currentRevenue = data[i].revenue

    if (prevRevenue > 0) {
      const growth = ((currentRevenue - prevRevenue) / prevRevenue) * 100
      data[i].growth = Math.round(growth)
    } else {
      data[i].growth = 0
    }
  }
  return data
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
    const status = determineStatus(metrics.margin)

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
      status: status,
    })
  }

  // Calculate growth rates
  return calculateGrowth(data)
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

  // Calculate growth rates
  return calculateGrowth(data)
}

const processOrdersData = (ordersList, startDate, days = 7, view = 'daily') => {
  if (view === 'daily') {
    return processDailyData(ordersList, startDate, days)
  } else {
    // Monthly view
    const year = startDate.getFullYear()
    return processMonthlyData(ordersList, year)
  }
}

const generateDataForDate = (dateString, view) => {
  // Check if selected date is in the future
  if (isDateInFuture(dateString)) {
    return []
  }

  const selectedDateObj = new Date(dateString)

  if (view === 'daily') {
    // For daily view, show the entire week containing the selected date
    const startOfWeek = getStartOfWeek(selectedDateObj)
    return processOrdersData(orders.value, startOfWeek, 7, 'daily')
  } else {
    // For monthly view, find the specific month data
    const year = selectedDateObj.getFullYear()
    const month = selectedDateObj.getMonth()
    const monthlyEntry = monthlyData.value.find((item) => {
      const itemDate = new Date(`${item.period} 1, ${year}`)
      return itemDate.getMonth() === month && itemDate.getFullYear() === year
    })
    return monthlyEntry ? [monthlyEntry] : []
  }
}

const monthlyData = computed(() => {
  const year = new Date(selectedDate.value).getFullYear()
  return processOrdersData(orders.value, new Date(year, 0, 1), 12, 'monthly')
})

const analyticsData = ref([])

// Helper functions for summary calculations
const calculateTotal = (data, field) => {
  return data.reduce((sum, item) => sum + (item[field] || 0), 0)
}

// Summary computed properties
const totalRevenue = computed(() => calculateTotal(analyticsData.value, 'revenue'))
const totalExpense = computed(() => calculateTotal(analyticsData.value, 'expense'))
const totalNetProfit = computed(() => calculateTotal(analyticsData.value, 'netProfit'))
const totalOrders = computed(() => calculateTotal(analyticsData.value, 'orders'))
const totalCustomers = computed(() => calculateTotal(analyticsData.value, 'customers'))

// Computed property for week range display
const weekRange = computed(() => {
  if (selectedView.value !== 'daily') {
    return ''
  }

  if (analyticsData.value.length === 0) {
    return ''
  }

  const firstDay = analyticsData.value[0]
  const lastDay = analyticsData.value[analyticsData.value.length - 1]
  return `${formatDate(firstDay.date)} - ${formatDate(lastDay.date)}`
})

// Utility functions
const getColspan = () => {
  if (selectedView.value === 'daily') {
    return 2
  } else {
    return 1
  }
}

const isSelectedDateRow = (itemDate) => {
  if (selectedView.value !== 'daily') {
    return false
  }
  return itemDate === selectedDate.value
}

const updateData = async () => {
  if (orders.value.length === 0) {
    await loadOrders()
  }
  analyticsData.value = generateDataForDate(selectedDate.value, selectedView.value)
}

onMounted(() => updateData())

const formatNumber = (num) => num.toLocaleString()
const formatDate = (dateString) =>
  new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  })

const getStatusClass = (status) => {
  const lowerStatus = status?.toLowerCase()

  if (lowerStatus === 'excellent') {
    return 'excellent'
  } else if (lowerStatus === 'good') {
    return 'good'
  } else if (lowerStatus === 'fair') {
    return 'fair'
  } else if (lowerStatus === 'poor') {
    return 'poor'
  } else {
    return 'neutral'
  }
}
</script>

<style scoped>
.analytics-table-container {
  background: white;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  padding: 24px;
  margin-top: 24px;
  font-family: 'Poppins', sans-serif;
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.table-title {
  font-size: 18px;
  font-weight: 600;
  color: #111827;
  margin: 0;
}

.filters-wrapper {
  position: relative;
  z-index: 20;
}

.table-filters {
  display: flex;
  gap: 12px;
  align-items: flex-start;
  margin-bottom: 20px;
  position: relative;
  z-index: 10;
}

.date-picker-container {
  position: relative;
  display: inline-block;
}

.date-picker-button {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  background: white;
  color: #374151;
  font-size: 14px;
  cursor: pointer;
  min-width: 180px;
  font-family: 'Poppins', sans-serif;
  transition: all 0.2s ease;
}

.date-picker-button:hover {
  border-color: #9ca3af;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.date-picker-button:focus {
  outline: none;
  border-color: #14c9c9;
  box-shadow: 0 0 0 3px rgba(20, 201, 201, 0.1);
}

.date-icon {
  font-size: 16px;
}

.selected-date-text {
  flex: 1;
  text-align: left;
  font-weight: 500;
}

.dropdown-arrow {
  font-size: 12px;
  transition: transform 0.2s ease;
  color: #6b7280;
}

.dropdown-arrow.rotated {
  transform: rotate(180deg);
}

.calendar-overlay {
  position: absolute;
  top: 100%;
  left: -140px;
  right: 0;
  bottom: auto;
  background: transparent;
  z-index: 1000;
  display: block;
  margin-top: 4px;
}

.calendar-dropdown {
  background: white;
  border-radius: 8px;
  box-shadow:
    0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06);
  padding: 16px;
  min-width: 280px;
  max-width: 320px;
  font-family: 'Poppins', sans-serif;
  border: 1px solid #e5e7eb;
}

.calendar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.calendar-month-year {
  font-size: 16px;
  font-weight: 600;
  color: #111827;
}

.calendar-nav-btn {
  background: none;
  border: none;
  font-size: 18px;
  color: #6b7280;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 4px;
  transition: background-color 0.2s ease;
}

.calendar-nav-btn:hover {
  background: #f3f4f6;
  color: #374151;
}

.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 2px;
}

.calendar-day-header {
  padding: 8px 4px;
  text-align: center;
  font-size: 12px;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.calendar-day {
  padding: 8px 4px;
  text-align: center;
  font-size: 14px;
  color: #374151;
  cursor: pointer;
  border-radius: 4px;
  transition: all 0.2s ease;
  min-height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.calendar-day:hover {
  background: #f3f4f6;
}

.calendar-day.selected {
  background: #14c9c9;
  color: white;
  font-weight: 600;
}

.calendar-day.today {
  background: #dbeafe;
  color: #1e40af;
  font-weight: 600;
}

.calendar-day.disabled {
  color: #d1d5db;
  cursor: not-allowed;
}

.calendar-day.disabled:hover {
  background: transparent;
}

.week-info {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 20px;
  margin-top: 20px;
  padding: 16px 16px;
  border-top: 1px solid #e5e7eb;
}

.date-indicator {
  font-size: 14px;
  font-weight: 600;
  color: #374151;
  background: #f3f4f6;
  padding: 8px 16px;
  border-radius: 6px;
  min-width: 200px;
  text-align: center;
  font-family: 'Poppins', sans-serif;
}

.view-select {
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  background: white;
  color: #374151;
  font-size: 14px;
  cursor: pointer;
  min-width: 120px;
  font-family: 'Poppins', sans-serif;
  position: relative;
  z-index: 5;
}

.view-select:focus {
  outline: none;
  border-color: #14c9c9;
  box-shadow: 0 0 0 3px rgba(20, 201, 201, 0.1);
}

.table-wrapper {
  overflow-x: auto;
  border-radius: 8px;
  background: white;
  border: 1px solid #e5e7eb;
  position: relative;
  z-index: 1;
}

.analytics-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 14px;
  background: white;
}

.analytics-table th {
  padding: 12px 16px;
  text-align: left;
  font-size: 12px;
  font-weight: 600;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  border-bottom: 1px solid #e5e7eb;
  white-space: nowrap;
  background: #f8fafc;
}

.analytics-table td {
  padding: 16px;
  border-bottom: 1px solid #e5e7eb;
  vertical-align: middle;
  transition: background-color 0.2s ease;
}

.table-row:hover {
  background: #f7fafc;
}

.table-row.selected-date-row {
  background: #dbeafe !important;
  box-shadow: 0 2px 4px rgba(20, 201, 201, 0.1);
}

.table-row.selected-date-row:hover {
  background: #dbeafe !important;
}

.revenue-cell,
.expense-cell,
.net-profit-cell,
.orders-cell,
.customers-cell {
  text-align: left;
  min-width: 100px;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
}

.expense-cell {
  color: #dc2626;
}

.net-profit-cell.negative {
  color: #dc2626;
}

.status-cell {
  text-align: left;
  min-width: 100px;
}

.status-badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-family: 'Poppins', sans-serif;
}

.status-badge.excellent {
  background: #dcfce7;
  color: #166534;
}

.status-badge.good {
  background: #dbeafe;
  color: #1e40af;
}

.status-badge.fair {
  background: #fef3c7;
  color: #92400e;
}

.status-badge.poor {
  background: #fee2e2;
  color: #dc2626;
}

.status-badge.neutral {
  background: #f3f4f6;
  color: #6b7280;
}

/* Summary row styling */
.summary-row {
  background: #f8fafc;
  border-top: 2px solid #e5e7eb;
}

.summary-row .summary-label {
  font-size: 16px;
  text-align: left;
  padding: 20px 16px;
  border-bottom: 1px solid #e5e7eb;
  vertical-align: middle;
  font-weight: 600;
  color: #111827;
}

.summary-row .revenue-cell,
.summary-row .expense-cell,
.summary-row .net-profit-cell,
.summary-row .orders-cell,
.summary-row .customers-cell,
.summary-row .status-cell {
  font-weight: 700;
  color: #111827;
  padding: 20px 16px;
  border-bottom: 1px solid #e5e7eb;
  vertical-align: middle;
  text-align: left;
  font-family: 'Poppins', sans-serif;
}

.summary-row .margin-cell {
  text-align: left;
}

.summary-row .status-cell {
  text-align: left;
}

.summary-row .expense-cell {
  color: #dc2626;
}

.summary-row .net-profit-cell.negative {
  color: #dc2626;
}

.summary-row .revenue-cell,
.summary-row .expense-cell,
.summary-row .net-profit-cell,
.summary-row .orders-cell,
.summary-row .customers-cell {
  min-width: 100px;
}

.summary-row .status-cell {
  min-width: 100px;
}

.summary-revenue,
.summary-orders,
.summary-customers {
  color: #059669;
}

.summary-expense {
  color: #dc2626;
}

.summary-net-profit.negative {
  color: #dc2626;
}

@media (max-width: 768px) {
  .analytics-table-container {
    padding: 16px;
  }

  .table-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .table-filters {
    width: 100%;
    justify-content: space-between;
    flex-wrap: wrap;
  }

  .calendar-dropdown {
    min-width: 250px;
    max-width: 280px;
  }

  .date-picker-button {
    min-width: 160px;
  }

  .view-select {
    order: 1;
    min-width: 140px;
  }

  .analytics-table th,
  .analytics-table td {
    padding: 12px 8px;
    font-size: 12px;
  }

  .summary-row .summary-label,
  .summary-row .revenue-cell,
  .summary-row .expense-cell,
  .summary-row .net-profit-cell,
  .summary-row .orders-cell,
  .summary-row .customers-cell,
  .summary-row .status-cell {
    padding: 16px;
  }
}
</style>
