<template>
  <!-- Stock Status Bar Chart with Filters -->
  <div class="stock-status-chart">
    <div class="chart-header">
      <h3>Stock Status Overview</h3>
      <div class="chart-filters">
        <!-- Period Filter -->
        <select v-model="selectedPeriod" class="filter-select" @change="updateChart">
          <option value="current">Current Stock</option>
          <option value="weekly">Weekly Trend</option>
          <option value="monthly">Monthly Trend</option>
        </select>
        <!-- Category Filter -->
        <select v-model="selectedCategory" class="filter-select" @change="updateChart">
          <option value="all">All Categories</option>
          <option value="mountain">Mountain Bike</option>
          <option value="road">Road Bike</option>
        </select>
      </div>
    </div>
    <div class="chart-container">
      <canvas ref="statusCanvas"></canvas>
    </div>
  </div>
</template>

<script>
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  BarController,
  Title,
  Tooltip,
  Legend,
} from 'chart.js'

ChartJS.register(CategoryScale, LinearScale, BarElement, BarController, Title, Tooltip, Legend)

export default {
  name: 'StockStatusChart',
  data() {
    return {
      selectedPeriod: 'current',
      selectedType: 'count',
      selectedCategory: 'all',
      chart: null,
      // Sample data for different periods and categories
      chartData: {
        current: {
          all: {
            labels: ['FULL', 'Normal', 'LOW'],
            count: [40, 30, 30],
          },
          mountain: {
            labels: ['FULL', 'Normal', 'LOW'],
            count: [25, 20, 15],
          },
          road: {
            labels: ['FULL', 'Normal', 'LOW'],
            count: [15, 10, 15],
          },
        },
        weekly: {
          all: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            full: [20, 25, 22, 28],
            normal: [10, 12, 11, 12],
            low: [5, 5, 5, 5],
          },
          mountain: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            full: [12, 15, 13, 17],
            normal: [6, 7, 6, 7],
            low: [2, 3, 3, 4],
          },
          road: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            full: [8, 10, 9, 11],
            normal: [4, 5, 5, 5],
            low: [3, 2, 2, 1],
          },
        },
        monthly: {
          all: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            full: [22, 25, 20, 30, 24, 28],
            normal: [10, 12, 10, 12, 11, 12],
            low: [6, 5, 5, 6, 6, 6],
          },
          mountain: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            full: [13, 15, 12, 18, 14, 17],
            normal: [6, 7, 6, 8, 7, 8],
            low: [3, 3, 2, 4, 3, 3],
          },
          road: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            full: [9, 10, 8, 12, 10, 11],
            normal: [4, 5, 4, 4, 4, 4],
            low: [3, 2, 3, 2, 3, 3],
          },
        },
      },
    }
  },
  mounted() {
    this.initChart()
  },
  beforeUnmount() {
    if (this.chart) {
      this.chart.destroy()
    }
  },
  methods: {
    initChart() {
      this.updateChart()
    },
    updateChart() {
      const ctx = this.$refs.statusCanvas.getContext('2d')
      const currentData = this.chartData[this.selectedPeriod][this.selectedCategory]

      let data
      if (this.selectedPeriod === 'current') {
        data = {
          labels: currentData.labels,
          datasets: [
            {
              label: this.getDataLabel(),
              data: currentData.count,
              backgroundColor: this.getBarColors(),
              borderColor: this.getBarColors(),
              borderWidth: 1,
              barPercentage: 0.2,
            },
          ],
        }
      } else {
        data = {
          labels: currentData.labels,
          datasets: [
            {
              label: 'FULL',
              data: currentData.full,
              backgroundColor: '#42A5F5',
              borderColor: '#42A5F5',
              borderWidth: 1,
              barPercentage: 0.6,
            },
            {
              label: 'Normal',
              data: currentData.normal,
              backgroundColor: '#FFA726',
              borderColor: '#FFA726',
              borderWidth: 1,
              barPercentage: 0.6,
            },
            {
              label: 'LOW',
              data: currentData.low,
              backgroundColor: '#EF5350',
              borderColor: '#EF5350',
              borderWidth: 1,
              barPercentage: 0.6,
            },
          ],
        }
      }

      if (this.chart) {
        this.chart.destroy()
      }

      this.chart = new ChartJS(ctx, {
        type: 'bar',
        data: data,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: this.selectedPeriod !== 'current',
            },
            tooltip: {
              callbacks: {
                label: (context) => {
                  const value = context.parsed.y
                  return `${context.dataset.label}: ${value} items`
                },
              },
            },
          },
          scales: {
            x: {
              categoryPercentage: 0.4,
              barPercentage: 0.4,
            },
            y: {
              beginAtZero: true,
              ticks: {
                callback: (value) => {
                  return value
                },
              },
            },
          },
          animation: {
            duration: 800,
          },
          clip: false,
        },
      })
    },
    getDataLabel() {
      return 'Product Count'
    },
    getBarColors() {
      return ['#42A5F5', '#FFA726', '#EF5350']
    },
  },
}
</script>

<style scoped>
.stock-status-chart {
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
  margin-bottom: 20px;
}

.chart-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 400;
  color: black;
}

.chart-filters {
  display: flex;
  gap: 12px;
  align-items: center;
}

.filter-select {
  padding: 10px 14px;
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
}

.chart-container {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 12px;
  height: 300px;
  width: 100%;
}

canvas {
  max-width: 500%;
  max-height: 100%;
}
</style>
