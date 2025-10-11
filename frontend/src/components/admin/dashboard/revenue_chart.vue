<template>
  <div class="revenue-chart">
    <div class="chart-header">
      <h3>Revenue & Profit Trend</h3>
      <div class="chart-filters">
        <select v-model="selectedPeriod" class="filter-select" @change="updateChart">
          <option value="daily">Daily Revenue</option>
          <option value="weekly">Weekly Revenue</option>
          <option value="monthly">Monthly Revenue</option>
        </select>
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
  Filler,
} from 'chart.js'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  LineController,
  Title,
  Tooltip,
  Legend,
  Filler,
)

export default {
  name: 'RevenueChart',
  // Data properties for the component
  data() {
    return {
      selectedPeriod: 'daily',
      selectedType: 'revenue',
      chart: null,
      chartData: {
        daily: {
          labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
          revenue: [1200, 1900, 3000, 5000, 2000, 3000, 4000],
          profit: [200, 300, 500, 800, 300, 400, 600],
        },
        weekly: {
          labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
          revenue: [15000, 22000, 18000, 25000],
          profit: [2500, 3500, 2800, 4000],
        },
        monthly: {
          labels: [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec',
          ],
          revenue: [
            65000, 70000, 68000, 80000, 75000, 68000, 90000, 85000, 78000, 88000, 72000, 82000,
          ],
          profit: [
            10000, 12000, 11000, 15000, 13000, 12000, 18000, 16000, 14000, 17000, 13000, 15000,
          ],
        },
      },
    }
  },
  // Lifecycle hook to initialize the chart when component is mounted
  mounted() {
    this.initChart()
  },
  // Lifecycle hook to destroy the chart before unmounting
  beforeUnmount() {
    if (this.chart) {
      this.chart.destroy()
    }
  },
  methods: {
    // Method to initialize the chart
    initChart() {
      const ctx = this.$refs.chartCanvas.getContext('2d')
      this.createChart(ctx)
    },

    // Method to create or update the chart
    createChart(ctx) {
      if (this.chart) {
        this.chart.destroy()
      }

      const data = this.chartData[this.selectedPeriod]
      const datasets = []

      // Determine which datasets to add based on selected type
      if (this.selectedType === 'revenue') {
        datasets.push({
          label: 'Revenue ($)',
          data: data.revenue,
          borderColor: '#42A5F5',
          backgroundColor: 'rgba(66, 165, 245, 0.1)',
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#42A5F5',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 6,
          pointHoverRadius: 8,
        })
      } else if (this.selectedType === 'profit') {
        datasets.push({
          label: 'Profit ($)',
          data: data.profit,
          borderColor: '#66BB6A',
          backgroundColor: 'rgba(102, 187, 106, 0.1)',
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#66BB6A',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 6,
          pointHoverRadius: 8,
        })
      } else if (this.selectedType === 'both') {
        datasets.push({
          label: 'Revenue ($)',
          data: data.revenue,
          borderColor: '#42A5F5',
          backgroundColor: 'rgba(66, 165, 245, 0.1)',
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#42A5F5',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 6,
          pointHoverRadius: 8,
        })
        datasets.push({
          label: 'Profit ($)',
          data: data.profit,
          borderColor: '#66BB6A',
          backgroundColor: 'rgba(102, 187, 106, 0.1)',
          fill: true,
          tension: 0.4,
          pointBackgroundColor: '#66BB6A',
          pointBorderColor: '#fff',
          pointBorderWidth: 2,
          pointRadius: 6,
          pointHoverRadius: 8,
        })
      }

      // Determine if legend should be shown
      let showLegend
      if (this.selectedType === 'both') {
        showLegend = true
      } else {
        showLegend = false
      }

      this.chart = new ChartJS(ctx, {
        type: 'line',
        data: {
          labels: data.labels,
          datasets: datasets,
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          interaction: {
            intersect: false,
            mode: 'index',
          },
          plugins: {
            legend: {
              display: showLegend,
              position: 'top',
              labels: {
                usePointStyle: true,
                padding: 20,
                font: {
                  size: 12,
                  family: 'Poppins, sans-serif',
                },
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
              titleFont: {
                size: 12,
                family: 'Poppins, sans-serif',
              },
              bodyFont: {
                size: 12,
                family: 'Poppins, sans-serif',
              },
              callbacks: {
                label: function (context) {
                  return `${context.dataset.label}: $${context.parsed.y.toLocaleString()}`
                },
              },
            },
          },
          scales: {
            x: {
              grid: {
                display: false,
              },
              ticks: {
                font: {
                  size: 12,
                  family: 'Poppins, sans-serif',
                },
                color: '#666',
              },
            },
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)',
              },
              ticks: {
                callback: function (value) {
                  return '$' + value.toLocaleString()
                },
                font: {
                  size: 12,
                  family: 'Poppins, sans-serif',
                },
                color: '#666',
              },
            },
          },
          elements: {
            point: {
              hoverBorderWidth: 3,
            },
          },
          animation: {
            duration: 1000,
            easing: 'easeInOutQuart',
          },
        },
      })
    },

    // Method to update the chart when filters change
    updateChart() {
      if (this.chart) {
        this.createChart(this.chart.ctx)
      }
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
