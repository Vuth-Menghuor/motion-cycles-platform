<template>
  <div class="analytics-page">
    <MetricsCards
      :total-revenue="analyticsData.totalRevenue"
      :net-profit="analyticsData.netProfit"
      :total-orders="analyticsData.totalOrders"
      :active-customers="analyticsData.activeCustomers"
      :total-expenses="analyticsData.totalExpenses"
    />

    <ComprehensiveAnalyticsTable />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import MetricsCards from '@/components/admin/analytics/MetricsCards.vue'
import ComprehensiveAnalyticsTable from '@/components/admin/analytics/ComprehensiveAnalyticsTable.vue'

// Analytics data for the dashboard
const analyticsData = ref({
  totalRevenue: 125430,
  netProfit: 32150,
  totalOrders: 1250,
  activeCustomers: 890,
  totalExpenses: 93480,
})
</script>

<style scoped>
.analytics-page {
  background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
  font-family: 'Poppins', sans-serif;
}

@media (max-width: 768px) {
  .analytics-page {
    padding: 16px;
  }
}
</style>
