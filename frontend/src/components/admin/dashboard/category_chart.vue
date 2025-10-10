<template>
  <!-- Category Distribution Donut Chart -->
  <div class="category-chart">
    <div class="chart-header">
      <h3>Product Category Distribution</h3>
    </div>
    <div class="chart-container">
      <canvas ref="donutCanvas"></canvas>
    </div>
    <div class="chart-legend">
      <div v-for="(item, index) in categories" :key="index" class="legend-item">
        <span class="legend-color" :style="{ backgroundColor: item.color }"></span>
        <span class="legend-label">{{ item.name }} - {{ item.percentage }}%</span>
      </div>
    </div>
  </div>
</template>

<script>
import { Chart as ChartJS, ArcElement, Tooltip, Legend, DoughnutController } from 'chart.js'

ChartJS.register(ArcElement, Tooltip, Legend, DoughnutController)

export default {
  name: 'CategoryChart',
  data() {
    return {
      chart: null,
      categories: [
        { name: 'Mountain Bike', percentage: 65, color: '#42A5F5' },
        { name: 'Road Bike', percentage: 35, color: '#EF5350' },
      ],
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
      const ctx = this.$refs.donutCanvas.getContext('2d')

      const data = {
        labels: this.categories.map((cat) => cat.name),
        datasets: [
          {
            data: this.categories.map((cat) => cat.percentage),
            backgroundColor: this.categories.map((cat) => cat.color),
            borderColor: this.categories.map((cat) => cat.color),
            borderWidth: 2,
          },
        ],
      }

      this.chart = new ChartJS(ctx, {
        type: 'doughnut',
        data: data,
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '60%',
          hover: {
            mode: 'nearest',
            intersect: true,
          },
          plugins: {
            legend: { display: false },
            tooltip: {
              enabled: true,
              callbacks: {
                label: function (context) {
                  return `${context.label}: ${context.parsed}%`
                },
              },
            },
          },
          animation: {
            animateScale: true,
            animateRotate: true,
            duration: 800,
          },
        },
      })
    },
  },
}
</script>

<style scoped>
.category-chart {
  background: #ffffff;
  border-radius: 12px;
  padding: 18px;
  box-shadow: 0 6px 18px rgba(2, 6, 23, 0.06);
  display: flex;
  flex-direction: column;
  font-family: 'Poppins', sans-serif;
}

.chart-header {
  margin: 8px 0 28px;
}

.chart-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 400;
  color: black;
}

.chart-container {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 12px;
  height: 230px;
  width: 100%;
}

canvas {
  max-width: 100%;
  max-height: 100%;
}

.chart-legend {
  display: flex;
  justify-content: space-around;
  gap: 10px;
  position: relative;
  top: 24px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
}

.legend-color {
  width: 16px;
  height: 16px;
  border-radius: 4px;
}

.legend-label {
  color: #475569;
}
</style>
