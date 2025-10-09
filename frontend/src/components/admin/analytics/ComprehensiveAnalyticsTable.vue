<template>
  <div class="analytics-table-container">
    <div class="table-header">
      <h3 class="table-title">Revenue & Profit Analytics</h3>
      <div class="filters-wrapper">
        <div class="table-filters">
          <select v-model="selectedView" @change="updateData" class="view-select">
            <option value="daily">Daily Tracking</option>
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
            <th class="text-left">Margin %</th>
            <th class="text-left">Orders</th>
            <th class="text-left" v-if="selectedView === 'daily'">Customers</th>
            <th class="text-left" v-if="selectedView === 'daily'">AOV</th>
            <th class="text-left">Growth %</th>
            <th class="text-left" v-if="selectedView === 'daily'">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in analyticsData" :key="item.id" class="table-row">
            <td class="day-cell" v-if="selectedView === 'daily'">{{ item.day }}</td>
            <td class="date-cell" v-if="selectedView === 'daily'">{{ formatDate(item.date) }}</td>
            <td class="period-cell" v-if="selectedView === 'monthly'">{{ item.period }}</td>
            <td class="revenue-cell">${{ formatNumber(item.revenue) }}</td>
            <td class="expense-cell">${{ formatNumber(item.expense) }}</td>
            <td class="net-profit-cell" :class="{ negative: item.netProfit < 0 }">
              ${{ formatNumber(Math.abs(item.netProfit)) }}
            </td>
            <td class="margin-cell" :class="{ negative: item.margin < 0 }">
              {{ item.margin.toFixed(1) }}%
            </td>
            <td class="orders-cell">{{ item.orders.toLocaleString() }}</td>
            <td class="customers-cell" v-if="selectedView === 'daily'">
              {{ item.customers.toLocaleString() }}
            </td>
            <td class="aov-cell" v-if="selectedView === 'daily'">${{ formatNumber(item.aov) }}</td>
            <td class="growth-cell">
              <div class="growth-indicator" :class="getGrowthClass(item.growth)">
                <span class="growth-icon">{{ getGrowthIcon(item.growth) }}</span>
                <span class="growth-value">{{ Math.abs(item.growth) }}%</span>
              </div>
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
            <td class="summary-margin margin-cell" :class="{ negative: averageMargin < 0 }">
              <strong>{{ averageMargin.toFixed(1) }}%</strong>
            </td>
            <td class="summary-orders orders-cell">
              <strong>{{ totalOrders.toLocaleString() }}</strong>
            </td>
            <td class="summary-customers customers-cell" v-if="selectedView === 'daily'">
              <strong>{{ totalCustomers.toLocaleString() }}</strong>
            </td>
            <td class="summary-aov aov-cell" v-if="selectedView === 'daily'">
              <strong>${{ formatNumber(averageOrderValue) }}</strong>
            </td>
            <td class="summary-growth growth-cell">
              <div class="growth-indicator" :class="getGrowthClass(overallGrowth)">
                <span class="growth-icon">{{ getGrowthIcon(overallGrowth) }}</span>
                <span class="growth-value">{{ Math.abs(overallGrowth) }}%</span>
              </div>
            </td>
            <td class="summary-status status-cell" v-if="selectedView === 'daily'">
              <span class="status-badge excellent">Excellent</span>
            </td>
          </tr>
        </tfoot>
      </table>

      <!-- Week Pagination Controls (only for daily view) -->
      <div v-if="selectedView === 'daily'" class="week-info">
        <span class="week-indicator">
          Showing data for the week of {{ formatDate(selectedDate) }}
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Icon } from '@iconify/vue'

const selectedView = ref('daily')
const selectedDate = ref(new Date().toISOString().split('T')[0]) // Default to today
const currentCalendarDate = ref(new Date()) // For calendar navigation
const showCalendar = ref(false) // Controls calendar dropdown visibility

// Sync calendar with selected date
watch(selectedDate, (newDate) => {
  const date = new Date(newDate)
  currentCalendarDate.value = new Date(date.getFullYear(), date.getMonth(), 1)
})

// Calendar constants
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

// Calendar computed properties
const currentMonthName = computed(() => monthNames[currentCalendarDate.value.getMonth()])
const currentYear = computed(() => currentCalendarDate.value.getFullYear())

const calendarDates = computed(() => {
  const year = currentCalendarDate.value.getFullYear()
  const month = currentCalendarDate.value.getMonth()

  const firstDay = new Date(year, month, 1)
  const startDate = new Date(firstDay)
  startDate.setDate(startDate.getDate() - firstDay.getDay())

  const dates = []
  const currentDate = new Date(startDate)
  const today = new Date()
  const dataStartDate = new Date('2025-10-01') // October 1, 2025

  // Reset time to start of day for accurate comparison
  const todayNormalized = new Date(today.getFullYear(), today.getMonth(), today.getDate())
  const startDateLimit = new Date(
    dataStartDate.getFullYear(),
    dataStartDate.getMonth(),
    dataStartDate.getDate(),
  )

  for (let i = 0; i < 42; i++) {
    // 6 weeks * 7 days
    const dateString = currentDate.toISOString().split('T')[0]
    const checkDate = new Date(
      currentDate.getFullYear(),
      currentDate.getMonth(),
      currentDate.getDate(),
    )

    // A date is selectable if it's between Oct 1, 2025 and today (inclusive)
    const isSelectable = checkDate >= startDateLimit && checkDate <= todayNormalized

    dates.push({
      dateString,
      day: currentDate.getDate(),
      isCurrentMonth: currentDate.getMonth() === month,
      isSelectable,
    })
    currentDate.setDate(currentDate.getDate() + 1)
  }

  return dates
})

// Helper methods for calendar
const isSelectedDate = (dateString) => selectedDate.value === dateString
const isToday = (dateString) => {
  const today = new Date().toISOString().split('T')[0]
  return dateString === today
}

const selectDate = (dateString) => {
  selectedDate.value = dateString
  showCalendar.value = false // Close calendar after selection
  updateData()
}

const toggleCalendar = () => {
  showCalendar.value = !showCalendar.value
}

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

// Data generation functions
const generateMockData = (startDate, days = 7) => {
  const data = []
  const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
  const start = new Date(startDate)

  for (let i = 0; i < days; i++) {
    const currentDate = new Date(start)
    currentDate.setDate(start.getDate() + i)

    const baseRevenue = 3000 + Math.random() * 4000
    const baseExpense = baseRevenue * 0.75

    data.push({
      id: i + 1,
      day: daysOfWeek[currentDate.getDay()],
      date: currentDate.toISOString().split('T')[0],
      revenue: Math.round(baseRevenue),
      expense: Math.round(baseExpense),
      netProfit: Math.round(baseRevenue - baseExpense),
      margin: 25.0,
      orders: Math.round(20 + Math.random() * 15),
      customers: Math.round(18 + Math.random() * 10),
      aov: Math.round(baseRevenue / (20 + Math.random() * 15)),
      growth: Math.round((Math.random() - 0.5) * 40),
      status: ['Excellent', 'Good', 'Fair'][Math.floor(Math.random() * 3)],
    })
  }
  return data
}

// Generate data based on selected date
const generateDataForDate = (dateString, view) => {
  const selectedDateObj = new Date(dateString)
  const today = new Date()
  const currentDate = new Date(today.getFullYear(), today.getMonth(), today.getDate())

  // Reset time to start of day for accurate comparison
  const selectedDate = new Date(
    selectedDateObj.getFullYear(),
    selectedDateObj.getMonth(),
    selectedDateObj.getDate(),
  )

  // Only show data for dates up to today
  if (selectedDate > currentDate) {
    return []
  }

  if (view === 'daily') {
    // For daily view, show the week containing the selected date (Monday to Sunday)
    const startOfWeek = new Date(selectedDateObj)
    const dayOfWeek = startOfWeek.getDay()
    // Adjust to start from Monday: if Sunday (0), go back 6 days; otherwise go back (dayOfWeek - 1) days
    const offset = dayOfWeek === 0 ? 6 : dayOfWeek - 1
    startOfWeek.setDate(selectedDateObj.getDate() - offset)

    return generateMockData(startOfWeek, 7)
  } else {
    // For monthly view, show monthly summary for the selected month
    const year = selectedDateObj.getFullYear()
    const month = selectedDateObj.getMonth()

    // Find the monthly data for this month/year
    const monthlyEntry = monthlyData.value.find((item) => {
      const itemDate = new Date(`${item.period} 1, ${year}`)
      return itemDate.getMonth() === month && itemDate.getFullYear() === year
    })

    return monthlyEntry ? [monthlyEntry] : []
  }
}

// Reactive monthly data based on selected year
const monthlyData = computed(() => {
  const year = new Date(selectedDate.value).getFullYear()
  const today = new Date()
  const currentYear = today.getFullYear()
  const currentMonth = today.getMonth()

  const allMonthlyData = [
    {
      id: 1,
      period: 'January',
      revenue: 88000 + (year - 2024) * 5000,
      expense: 66000 + (year - 2024) * 3000,
      netProfit: 22000 + (year - 2024) * 2000,
      margin: 25.0,
      orders: 703 + (year - 2024) * 50,
      growth: 12.5,
    },
    {
      id: 2,
      period: 'February',
      revenue: 75600 + (year - 2024) * 4000,
      expense: 56700 + (year - 2024) * 2500,
      netProfit: 18900 + (year - 2024) * 1500,
      margin: 25.0,
      orders: 604 + (year - 2024) * 40,
      growth: 8.3,
    },
    {
      id: 3,
      period: 'March',
      revenue: 69200 + (year - 2024) * 3500,
      expense: 51900 + (year - 2024) * 2200,
      netProfit: 17300 + (year - 2024) * 1300,
      margin: 25.0,
      orders: 553 + (year - 2024) * 35,
      growth: -2.1,
    },
    {
      id: 4,
      period: 'April',
      revenue: 63400 + (year - 2024) * 3000,
      expense: 47550 + (year - 2024) * 2000,
      netProfit: 15850 + (year - 2024) * 1000,
      margin: 25.0,
      orders: 506 + (year - 2024) * 30,
      growth: 6.7,
    },
    {
      id: 5,
      period: 'May',
      revenue: 58000 + (year - 2024) * 2800,
      expense: 43500 + (year - 2024) * 1800,
      netProfit: 14500 + (year - 2024) * 1000,
      margin: 25.0,
      orders: 465 + (year - 2024) * 28,
      growth: 4.3,
    },
    {
      id: 6,
      period: 'June',
      revenue: 62000 + (year - 2024) * 3200,
      expense: 46500 + (year - 2024) * 2000,
      netProfit: 15500 + (year - 2024) * 1200,
      margin: 25.0,
      orders: 496 + (year - 2024) * 32,
      growth: 7.1,
    },
    {
      id: 7,
      period: 'July',
      revenue: 67000 + (year - 2024) * 3500,
      expense: 50250 + (year - 2024) * 2200,
      netProfit: 16750 + (year - 2024) * 1300,
      margin: 25.0,
      orders: 536 + (year - 2024) * 35,
      growth: 8.2,
    },
    {
      id: 8,
      period: 'August',
      revenue: 72000 + (year - 2024) * 3800,
      expense: 54000 + (year - 2024) * 2400,
      netProfit: 18000 + (year - 2024) * 1400,
      margin: 25.0,
      orders: 576 + (year - 2024) * 38,
      growth: 7.5,
    },
    {
      id: 9,
      period: 'September',
      revenue: 76000 + (year - 2024) * 4000,
      expense: 57000 + (year - 2024) * 2500,
      netProfit: 19000 + (year - 2024) * 1500,
      margin: 25.0,
      orders: 608 + (year - 2024) * 40,
      growth: 5.6,
    },
    {
      id: 10,
      period: 'October',
      revenue: 80000 + (year - 2024) * 4200,
      expense: 60000 + (year - 2024) * 2600,
      netProfit: 20000 + (year - 2024) * 1600,
      margin: 25.0,
      orders: 640 + (year - 2024) * 42,
      growth: 5.3,
    },
    {
      id: 11,
      period: 'November',
      revenue: 83000 + (year - 2024) * 4400,
      expense: 62250 + (year - 2024) * 2700,
      netProfit: 20750 + (year - 2024) * 1700,
      margin: 25.0,
      orders: 664 + (year - 2024) * 44,
      growth: 3.8,
    },
    {
      id: 12,
      period: 'December',
      revenue: 88000 + (year - 2024) * 4600,
      expense: 66000 + (year - 2024) * 2800,
      netProfit: 22000 + (year - 2024) * 1800,
      margin: 25.0,
      orders: 704 + (year - 2024) * 46,
      growth: 6.0,
    },
  ]

  // Only return data for months that are not in the future
  return allMonthlyData.filter((item, index) => {
    if (year < currentYear) {
      // Past years: show all months
      return true
    } else if (year === currentYear) {
      // Current year: only show months up to current month
      return index <= currentMonth
    } else {
      // Future years: show no data
      return false
    }
  })
})

const analyticsData = ref([])

const totalRevenue = computed(() => {
  return analyticsData.value.reduce((sum, item) => sum + item.revenue, 0)
})

const totalExpense = computed(() => {
  return analyticsData.value.reduce((sum, item) => sum + item.expense, 0)
})

const totalNetProfit = computed(() => {
  return analyticsData.value.reduce((sum, item) => sum + item.netProfit, 0)
})

const totalOrders = computed(() => {
  return analyticsData.value.reduce((sum, item) => sum + item.orders, 0)
})

const totalCustomers = computed(() => {
  return analyticsData.value.reduce((sum, item) => sum + (item.customers || 0), 0)
})

const averageOrderValue = computed(() => {
  return totalOrders.value > 0 ? Math.round(totalRevenue.value / totalOrders.value) : 0
})

const averageMargin = computed(() => {
  const margins = analyticsData.value.map((item) => item.margin)
  return margins.length > 0 ? margins.reduce((sum, margin) => sum + margin, 0) / margins.length : 0
})

const overallGrowth = computed(() => {
  const growths = analyticsData.value.map((item) => item.growth)
  return growths.length > 0 ? growths.reduce((sum, growth) => sum + growth, 0) / growths.length : 0
})

const getColspan = () => {
  return selectedView.value === 'daily' ? 2 : 1
}

const updateData = () => {
  analyticsData.value = generateDataForDate(selectedDate.value, selectedView.value)
}

// Initialize data
updateData()

const formatNumber = (num) => {
  return num.toLocaleString()
}

const formatDate = (dateString) => {
  const date = new Date(dateString)
  return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
}

const getGrowthClass = (growth) => {
  if (growth > 0) return 'positive'
  if (growth < 0) return 'negative'
  return 'neutral'
}

const getGrowthIcon = (growth) => {
  if (growth > 0) return '↗'
  if (growth < 0) return '↘'
  return '→'
}

const getStatusClass = (status) => {
  switch (status?.toLowerCase()) {
    case 'excellent':
      return 'excellent'
    case 'good':
      return 'good'
    case 'fair':
      return 'fair'
    case 'poor':
      return 'poor'
    default:
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

.week-indicator {
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

.revenue-cell,
.expense-cell,
.net-profit-cell,
.orders-cell,
.customers-cell,
.aov-cell {
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

.margin-cell {
  text-align: left;
  min-width: 80px;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
}

.margin-cell.negative {
  color: #dc2626;
}

.growth-cell {
  text-align: left;
  min-width: 80px;
}

.growth-indicator {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
}

.growth-indicator.positive {
  background: #dcfce7;
  color: #166534;
}

.growth-indicator.negative {
  background: #fee2e2;
  color: #dc2626;
}

.growth-indicator.neutral {
  background: #f3f4f6;
  color: #6b7280;
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
.summary-row .aov-cell,
.summary-row .margin-cell,
.summary-row .growth-cell,
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

.summary-row .growth-cell,
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
.summary-row .customers-cell,
.summary-row .aov-cell {
  min-width: 100px;
}

.summary-row .margin-cell {
  min-width: 80px;
}

.summary-row .growth-cell {
  min-width: 80px;
}

.summary-row .status-cell {
  min-width: 100px;
}

.summary-revenue,
.summary-orders,
.summary-customers,
.summary-aov {
  color: #059669;
}

.summary-expense {
  color: #dc2626;
}

.summary-net-profit.negative {
  color: #dc2626;
}

.summary-margin.negative {
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
  .summary-row .aov-cell,
  .summary-row .margin-cell,
  .summary-row .growth-cell,
  .summary-row .status-cell {
    padding: 16px;
  }
}
</style>
