<template>
  <!-- Admin Dashboard Page Layout -->
  <div class="dashboard">
    <!-- Statistics Cards Grid -->
    <div class="stats-grid">
      <Stat_card title="Total Products" :value="14000" color="blue" icon="mdi:package-variant" />
      <Stat_card
        title="Total Revenue"
        :value="14000"
        prefix="$"
        color="green"
        icon="streamline-ultimate:money-bag-dollar-bold"
        :actions="['view']"
      />
      <Stat_card
        title="Total Customer"
        :value="1300"
        color="red"
        icon="ic:round-groups"
        :actions="['view']"
      />
    </div>

    <!-- Charts Row: Revenue Chart and Category Distribution -->
    <div class="chart-row">
      <Revenue_chart />
      <Category_chart />
    </div>

    <!-- Stock Section: Alert Table -->
    <div class="stock-section">
      <Stock_alert_table />
    </div>

    <!-- Stock Status Chart -->
    <div class="stock-chart-row">
      <Stock_status_chart />
    </div>
  </div>
</template>

<script setup>
// Import dashboard components
import Category_chart from '@/components/admin/dashboard/category_chart.vue'
import Revenue_chart from '@/components/admin/dashboard/revenue_chart.vue'
import Stat_card from '@/components/admin/dashboard/stat_card.vue'
import Stock_alert_table from '@/components/admin/dashboard/stock_alert_table.vue'
import Stock_status_chart from '@/components/admin/dashboard/stock_status_chart.vue'
</script>

<style scoped>
.dashboard {
  max-width: auto;
  margin: 0 auto;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 16px;
  margin-bottom: 25px;
}

.chart-row {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 20px;
  margin-bottom: 25px;
}

.stock-section {
  display: grid;
  grid-template-columns: 1fr;
  gap: 20px;
  margin-bottom: 25px;
}

.stock-chart-row {
  margin-bottom: 25px;
}
</style>
