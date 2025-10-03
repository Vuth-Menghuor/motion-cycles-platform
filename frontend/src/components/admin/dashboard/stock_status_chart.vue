<template>
  <div class="stock-status-chart">
    <div class="chart-header">
      <h3>Stock Alert Status</h3>
    </div>
    <div class="chart-container">
      <canvas ref="statusCanvas"></canvas>
    </div>
    <div class="chart-legend">
      <div v-for="(item, index) in statusData" :key="index" class="legend-item">
        <span class="legend-color" :style="{ backgroundColor: item.color }"></span>
        <span class="legend-label">{{ item.name }} {{ item.percentage }}%</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'StockStatusChart',
  data() {
    return {
      statusData: [
        { name: 'FULL', percentage: 40, color: '#42A5F5' },
        { name: 'Normal', percentage: 30, color: '#FFA726' },
        { name: 'LOW', percentage: 30, color: '#EF5350' },
      ],
    }
  },
  mounted() {
    this.drawDonutChart()
  },
  methods: {
    drawDonutChart() {
      const canvas = this.$refs.statusCanvas
      const ctx = canvas.getContext('2d')

      // Set canvas size
      canvas.width = 240
      canvas.height = 240

      const centerX = canvas.width / 2
      const centerY = canvas.height / 2
      const radius = 80
      const innerRadius = 50

      let currentAngle = -Math.PI / 2

      this.statusData.forEach((status) => {
        const sliceAngle = (status.percentage / 100) * 2 * Math.PI

        // Draw outer arc
        ctx.beginPath()
        ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + sliceAngle)
        ctx.arc(centerX, centerY, innerRadius, currentAngle + sliceAngle, currentAngle, true)
        ctx.closePath()
        ctx.fillStyle = status.color
        ctx.fill()

        currentAngle += sliceAngle
      })

      // Draw center circle
      ctx.beginPath()
      ctx.arc(centerX, centerY, innerRadius, 0, 2 * Math.PI)
      ctx.fillStyle = 'white'
      ctx.fill()
    },
  },
}
</script>

<style scoped>
.stock-status-chart {
  background: white;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
}

.chart-header {
  margin-bottom: 25px;
}

.chart-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #333;
}

.chart-container {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 25px;
}

canvas {
  max-width: 100%;
}

.chart-legend {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.legend-item {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 14px;
}

.legend-color {
  width: 16px;
  height: 16px;
  border-radius: 3px;
}

.legend-label {
  color: #666;
}
</style>
