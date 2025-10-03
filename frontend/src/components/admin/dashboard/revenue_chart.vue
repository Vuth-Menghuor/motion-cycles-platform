<template>
  <div class="revenue-chart">
    <div class="chart-header">
      <h3>Revenue & Profit Trend</h3>
      <div class="chart-filters">
        <select v-model="selectedPeriod" class="filter-select">
          <option value="daily">Daily Revenue</option>
          <option value="weekly">Weekly Revenue</option>
          <option value="monthly">Monthly Revenue</option>
        </select>
        <select v-model="selectedType" class="filter-select">
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
export default {
  name: 'RevenueChart',
  data() {
    return {
      selectedPeriod: 'daily',
      selectedType: 'revenue',
      chart: null,
    }
  },
  mounted() {
    this.initChart()
  },
  beforeUnmount() {
    if (this.chart) {
      this.chart = null
    }
  },
  methods: {
    initChart() {
      const canvas = this.$refs.chartCanvas
      const ctx = canvas.getContext('2d')

      // Set canvas size
      canvas.width = canvas.offsetWidth
      canvas.height = 300

      // Sample data
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
      const data = [65, 70, 68, 80, 75, 68, 90, 85, 78, 88, 72, 82]

      this.drawChart(ctx, canvas.width, canvas.height, months, data)
    },
    drawChart(ctx, width, height, labels, data) {
      const padding = 40
      const chartWidth = width - padding * 2
      const chartHeight = height - padding * 2

      // Clear canvas
      ctx.clearRect(0, 0, width, height)

      // Draw grid lines
      ctx.strokeStyle = '#e0e0e0'
      ctx.lineWidth = 1

      for (let i = 0; i <= 5; i++) {
        const y = padding + (chartHeight / 5) * i
        ctx.beginPath()
        ctx.moveTo(padding, y)
        ctx.lineTo(width - padding, y)
        ctx.stroke()

        // Y-axis labels
        ctx.fillStyle = '#666'
        ctx.font = '12px Arial'
        ctx.textAlign = 'right'
        ctx.fillText(`${100 - i * 20}%`, padding - 10, y + 4)
      }

      // Draw line chart
      ctx.strokeStyle = '#42A5F5'
      ctx.lineWidth = 3
      ctx.beginPath()

      const step = chartWidth / (data.length - 1)

      data.forEach((value, index) => {
        const x = padding + step * index
        const y = padding + chartHeight - (value / 100) * chartHeight

        if (index === 0) {
          ctx.moveTo(x, y)
        } else {
          ctx.lineTo(x, y)
        }

        // Draw data points
        ctx.fillStyle = '#42A5F5'
        ctx.beginPath()
        ctx.arc(x, y, 4, 0, Math.PI * 2)
        ctx.fill()
      })

      ctx.stroke()

      // X-axis labels
      ctx.fillStyle = '#666'
      ctx.font = '11px Arial'
      ctx.textAlign = 'center'
      labels.forEach((label, index) => {
        const x = padding + step * index
        ctx.fillText(label, x, height - 10)
      })
    },
  },
}
</script>

<style scoped>
.revenue-chart {
  background: white;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.chart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
}

.chart-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #333;
}

.chart-filters {
  display: flex;
  gap: 10px;
}

.filter-select {
  padding: 8px 12px;
  border: 1px solid #e0e0e0;
  border-radius: 6px;
  background: white;
  cursor: pointer;
  font-size: 14px;
  outline: none;
}

.filter-select:hover {
  border-color: #42a5f5;
}

.chart-container {
  position: relative;
  width: 100%;
  height: 300px;
}

canvas {
  width: 100%;
  height: 100%;
}
</style>
