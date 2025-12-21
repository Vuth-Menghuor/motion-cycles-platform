<template>
  <div class="metrics-grid">
    <div class="metric-card">
      <div class="card-content">
        <div class="metric-icon">
          <Icon icon="solar:hand-money-linear" class="icon" />
        </div>
        <div class="metric-info">
          <div class="insight-value">${{ formatNumber(totalRevenue) }}</div>
          <div class="insight-label">Total Revenue</div>
        </div>
      </div>
    </div>

    <div class="metric-card">
      <div class="card-content">
        <div class="metric-icon">
          <Icon icon="lucide:trending-up" class="icon" />
        </div>
        <div class="metric-info">
          <div class="insight-value" :class="{ negative: netProfit < 0 }">
            ${{ formatNumber(Math.abs(netProfit)) }}
          </div>
          <div class="insight-label">Net Profit</div>
        </div>
      </div>
    </div>

    <div class="metric-card">
      <div class="card-content">
        <div class="metric-icon">
          <Icon icon="lucide:shopping-cart" class="icon" />
        </div>
        <div class="metric-info">
          <div class="insight-value">{{ totalOrders.toLocaleString() }}</div>
          <div class="insight-label">Total Orders</div>
        </div>
      </div>
    </div>

    <div class="metric-card">
      <div class="card-content">
        <div class="metric-icon">
          <Icon icon="lucide:users" class="icon" />
        </div>
        <div class="metric-info">
          <div class="insight-value">{{ activeCustomers.toLocaleString() }}</div>
          <div class="insight-label">Total Customers</div>
        </div>
      </div>
    </div>

    <div class="metric-card">
      <div class="card-content">
        <div class="metric-icon">
          <Icon icon="lucide:credit-card" class="icon" />
        </div>
        <div class="metric-info">
          <div class="insight-value">${{ formatNumber(totalExpenses) }}</div>
          <div class="insight-label">Total Expenses</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Icon } from '@iconify/vue'

defineProps({
  totalRevenue: { type: Number, default: 0 },
  netProfit: { type: Number, default: 0 },
  totalOrders: { type: Number, default: 0 },
  activeCustomers: { type: Number, default: 0 },
  totalExpenses: { type: Number, default: 0 },
})

const formatNumber = (num) => {
  if (num >= 1000000) {
    return (num / 1000000).toFixed(1) + 'M'
  } else if (num >= 1000) {
    return (num / 1000).toFixed(1) + 'K'
  } else {
    return num.toLocaleString()
  }
}
</script>

<style scoped>
.metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  margin-bottom: 32px;
  font-family: 'Poppins', sans-serif;
}

.metric-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e5e7eb;
  overflow: hidden;
  transition: box-shadow 0.2s ease;
}

.card-content {
  padding: 24px;
  display: flex;
  align-items: center;
  gap: 16px;
}

.metric-icon {
  width: 48px;
  height: 48px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  background: #f3f4f6;
}

.metric-icon:nth-child(1) {
  background: #dcfce7;
}

.metric-icon:nth-child(2) {
  background: #dbeafe;
}

.metric-icon:nth-child(3) {
  background: #fef3c7;
}

.metric-icon:nth-child(4) {
  background: #f3e8ff;
}

.metric-icon:nth-child(5) {
  background: #fee2e2;
}

.icon {
  width: 24px;
  height: 24px;
  color: #374151;
}

.metric-info {
  flex: 1;
}

.insight-value {
  font-size: 28px;
  font-weight: 600;
  color: #111827;
  margin: 0 0 4px 0;
  line-height: 1.2;
  letter-spacing: -0.02em;
}

.insight-value.negative {
  color: #dc2626;
}

.insight-label {
  font-size: 14px;
  font-weight: 500;
  color: #6b7280;
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

@media (max-width: 768px) {
  .metrics-grid {
    grid-template-columns: 1fr;
  }

  .card-content {
    padding: 20px;
  }

  .insight-value {
    font-size: 24px;
  }
}
</style>
