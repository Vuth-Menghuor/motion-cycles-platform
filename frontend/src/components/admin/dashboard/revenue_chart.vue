<template>
  <div class="revenue-chart">
    <div class="chart-header">
      <h3>Revenue & Profit Trend</h3>
      <div class="chart-filters">
        <span class="date-indicator">Week of {{ weekRange }}</span>
        <!-- Date Picker Button -->
        <div class="date-picker-container">
          <button @click="toggleCalendar" class="date-picker-button">
            <Icon icon="mdi:calendar" class="date-icon" width="16" height="16" />
            <span class="selected-date-text">{{ formatDate(selectedDate) }}</span>
            <span class="dropdown-arrow" :class="{ rotated: showCalendar }">▼</span>
          </button>

          <!-- Calendar Overlay -->
          <div v-if="showCalendar" class="calendar-overlay">
            <div class="calendar-dropdown" @click.stop>
              <div class="calendar-header">
                <button @click="previousMonth" class="calendar-nav-btn">‹</button>
                <span class="calendar-month-year"> {{ currentMonthName }} {{ currentYear }} </span>
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
        <select v-model="selectedType" class="filter-select" @change="updateChart">
          <option value="revenue">Revenue</option>
          <option value="profit">Profit</option>
          <option value="both">Both</option>
        </select>
      </div>
    </div>
    <div class="chart-container">
      <canvas ref="chartCanvas"></canvas>
    </div>
  </div>
</template>

<script>
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  LineController,
  Title,
  Tooltip,
  Legend,
} from 'chart.js'
import { Icon } from '@iconify/vue'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  LineController,
  Title,
  Tooltip,
  Legend,
)

export default {
  name: 'RevenueChart',
  components: { Icon },
  props: {
    analyticsData: { type: Array, default: () => [] },
    selectedView: { type: String, default: 'daily' },
  },
  data() {
    return {
      selectedType: 'revenue',
      chart: null,
      selectedDate: new Date().toLocaleDateString('en-CA'),
      currentCalendarDate: new Date(),
      showCalendar: false,
      monthNames: [
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
      ],
      dayNames: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    }
  },
  computed: {
    chartData() {
      return this.processChartData()
    },
    currentMonthName() {
      return this.monthNames[this.currentCalendarDate.getMonth()]
    },
    currentYear() {
      return this.currentCalendarDate.getFullYear()
    },
    calendarDates() {
      const year = this.currentCalendarDate.getFullYear()
      const month = this.currentCalendarDate.getMonth()
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
        const dateString = currentDate.toLocaleDateString('en-CA')
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
    },
    weekRange() {
      const selectedDateObj = new Date(this.selectedDate)
      const startOfWeek = this.getStartOfWeek(selectedDateObj)
      const endOfWeek = new Date(startOfWeek)
      endOfWeek.setDate(startOfWeek.getDate() + 6)
      return `${this.formatDate(startOfWeek.toLocaleDateString('en-CA'))} - ${this.formatDate(endOfWeek.toLocaleDateString('en-CA'))}`
    },
  },
  mounted() {
    this.$nextTick(() => this.initChart())
  },
  beforeUnmount() {
    if (this.chart) {
      this.chart.destroy()
      this.chart = null
    }
  },
  watch: {
    analyticsData: {
      handler() {
        this.updateChart()
      },
      deep: true,
    },
    selectedDate(newDate) {
      const date = new Date(newDate)
      this.currentCalendarDate = new Date(date.getFullYear(), date.getMonth(), 1)
      this.updateChart()
      this.$emit('date-changed', newDate)
    },
  },
  methods: {
    processChartData() {
      if (!this.analyticsData || this.analyticsData.length === 0) {
        return { labels: [], revenue: [], profit: [] }
      }

      const selectedDateObj = new Date(this.selectedDate)
      const startOfWeek = this.getStartOfWeek(selectedDateObj)
      const weekData = []

      for (let i = 0; i < 7; i++) {
        const currentDate = new Date(startOfWeek)
        currentDate.setDate(startOfWeek.getDate() + i)
        const dateString = currentDate.toLocaleDateString('en-CA')
        const dayData = this.analyticsData.find((item) => item.date === dateString)

        weekData.push(
          dayData || {
            day: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][
              currentDate.getDay()
            ],
            date: dateString,
            revenue: 0,
            netProfit: 0,
          },
        )
      }

      return {
        labels: weekData.map((item) => item.day.substring(0, 3)),
        revenue: weekData.map((item) => item.revenue || 0),
        profit: weekData.map((item) => item.netProfit || 0),
      }
    },

    initChart() {
      if (!this.$refs.chartCanvas || this.chart) return
      const ctx = this.$refs.chartCanvas.getContext('2d')
      if (!ctx) return
      this.createChart(ctx)
    },

    createChart(ctx) {
      // Ensure any existing chart is properly destroyed
      if (this.chart) {
        this.chart.destroy()
        this.chart = null
      }

      const data = this.chartData
      if (!data.labels || data.labels.length === 0) return

      // Additional check to ensure canvas is available
      if (!ctx || !ctx.canvas) return

      const datasets = []
      const baseConfig = {
        fill: false,
        tension: 0,
        pointBorderWidth: 2,
        pointRadius: 4,
        pointHoverRadius: 6,
      }

      if (this.selectedType === 'revenue') {
        datasets.push({
          ...baseConfig,
          label: 'Revenue ($)',
          data: data.revenue,
          borderColor: '#42A5F5',
          backgroundColor: '#42A5F5',
          pointBackgroundColor: '#42A5F5',
          pointBorderColor: '#fff',
        })
      } else if (this.selectedType === 'profit') {
        datasets.push({
          ...baseConfig,
          label: 'Profit ($)',
          data: data.profit,
          borderColor: '#66BB6A',
          backgroundColor: '#66BB6A',
          pointBackgroundColor: '#66BB6A',
          pointBorderColor: '#fff',
        })
      } else if (this.selectedType === 'both') {
        datasets.push(
          {
            ...baseConfig,
            label: 'Revenue ($)',
            data: data.revenue,
            borderColor: '#42A5F5',
            backgroundColor: '#42A5F5',
            pointBackgroundColor: '#42A5F5',
            pointBorderColor: '#fff',
          },
          {
            ...baseConfig,
            label: 'Profit ($)',
            data: data.profit,
            borderColor: '#66BB6A',
            backgroundColor: '#66BB6A',
            pointBackgroundColor: '#66BB6A',
            pointBorderColor: '#fff',
          },
        )
      }

      try {
        this.chart = new ChartJS(ctx, {
          type: 'line',
          data: { labels: data.labels, datasets },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: false,
            interaction: { intersect: false, mode: 'index' },
            plugins: {
              legend: {
                display: this.selectedType === 'both',
                position: 'top',
                labels: {
                  usePointStyle: true,
                  padding: 20,
                  font: { size: 12, family: 'Poppins, sans-serif' },
                },
              },
              tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: 'rgba(255, 255, 255, 0.1)',
                borderWidth: 1,
                cornerRadius: 8,
                displayColors: true,
                titleFont: { size: 12, family: 'Poppins, sans-serif' },
                bodyFont: { size: 12, family: 'Poppins, sans-serif' },
                callbacks: {
                  label: (context) =>
                    `${context.dataset.label}: $${(context.parsed.y || 0).toLocaleString()}`,
                },
              },
            },
            scales: {
              x: {
                grid: { display: false },
                ticks: { font: { size: 12, family: 'Poppins, sans-serif' }, color: '#666' },
              },
              y: {
                beginAtZero: true,
                grid: { color: 'rgba(0, 0, 0, 0.05)' },
                ticks: {
                  callback: (value) => '$' + (value || 0).toLocaleString(),
                  font: { size: 12, family: 'Poppins, sans-serif' },
                  color: '#666',
                },
              },
            },
            elements: { point: { hoverBorderWidth: 2 } },
          },
        })
      } catch (error) {
        console.error('Error creating chart:', error)
      }
    },

    updateChart() {
      if (!this.$refs.chartCanvas) return

      // Destroy existing chart
      if (this.chart) {
        this.chart.destroy()
        this.chart = null
      }

      // Use nextTick to ensure DOM is updated before recreating chart
      this.$nextTick(() => {
        this.initChart()
      })
    },

    isSelectedDate(dateString) {
      return this.selectedDate === dateString
    },
    isToday(dateString) {
      return new Date().toLocaleDateString('en-CA') === dateString
    },
    selectDate(dateString) {
      this.selectedDate = dateString
      this.showCalendar = false
    },
    toggleCalendar() {
      this.showCalendar = !this.showCalendar
    },
    previousMonth() {
      this.currentCalendarDate = new Date(
        this.currentCalendarDate.getFullYear(),
        this.currentCalendarDate.getMonth() - 1,
        1,
      )
    },
    nextMonth() {
      this.currentCalendarDate = new Date(
        this.currentCalendarDate.getFullYear(),
        this.currentCalendarDate.getMonth() + 1,
        1,
      )
    },
    formatDate(dateString) {
      return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
      })
    },
    getStartOfWeek(date) {
      const dayOfWeek = date.getDay()
      const mondayOffset = dayOfWeek === 0 ? -6 : 1 - dayOfWeek
      const startOfWeek = new Date(date)
      startOfWeek.setDate(date.getDate() + mondayOffset)
      return startOfWeek
    },
  },
}
</script>

<style scoped>
.revenue-chart {
  background: #ffffff;
  border-radius: 12px;
  padding: 18px;
  box-shadow: 0 6px 18px rgba(2, 6, 23, 0.06);
  display: flex;
  flex-direction: column;
  font-family: 'Poppins', sans-serif;
}

.chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  gap: 12px;
}

.chart-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 400;
  color: #0f172a;
}

.chart-filters {
  display: flex;
  gap: 8px;
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
  background: transparent;
  z-index: 1000;
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

.filter-select {
  padding: 10px 0px 10px 10px;
  border: 1px solid #e2e8f0;
  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
  cursor: pointer;
  font-size: 12px;
  font-weight: 400;
  outline: none;
  font-family: 'Poppins', sans-serif !important;
  color: #374151;
  transition: all 0.2s ease;
  min-width: 140px;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  margin: 0 auto;
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

.chart-container {
  position: relative;
  width: 100%;
  height: 320px;
}

canvas {
  width: auto;
  height: 100%;
}
</style>
