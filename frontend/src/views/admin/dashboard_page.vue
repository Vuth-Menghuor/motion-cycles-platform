<template>
  <div class="dashboard">
    <!-- Page Header -->
    <div class="page-header">
      <div class="tabs">
        <button class="tab active">
          <i class="icon-dashboard"></i>
          Dashboard
        </button>
        <button class="tab">
          <i class="icon-plus"></i>
          Add Product
        </button>
        <button class="tab">
          <i class="icon-list"></i>
          All Order List
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <Stat_card title="Total Products" :value="14000" color="blue" icon="package" />
      <Stat_card title="Total Revenue" :value="14000" prefix="$" color="green" icon="dollar" />
      <Stat_card title="Total Customer" :value="1300" color="red" icon="users" />
    </div>

    <!-- Revenue Chart and Category Distribution -->
    <div class="chart-row">
      <Revenue_chart />
      <Category_chart />
    </div>

    <!-- Stock Alert Section -->
    <div class="stock-section">
      <Stock_alert_table />
      <Stock_status_chart />
    </div>
  </div>
</template>

<script setup>
import Category_chart from '@/components/admin/dashboard/category_chart.vue'
import Revenue_chart from '@/components/admin/dashboard/revenue_chart.vue'
import Stat_card from '@/components/admin/dashboard/stat_card.vue'
import Stock_alert_table from '@/components/admin/dashboard/stock_alert_table.vue'
import Stock_status_chart from '@/components/admin/dashboard/stock_status_chart.vue'
</script>

<style scoped>
.dashboard {
  max-width: 1400px;
  margin: 0 auto;
}

.page-header {
  margin-bottom: 25px;
}

.tabs {
  display: flex;
  gap: 10px;
  background: white;
  padding: 15px;
  border-radius: 8px;
}

.tab {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 20px;
  border: none;
  background: transparent;
  cursor: pointer;
  border-radius: 6px;
  transition: background 0.3s;
  color: #666;
}

.tab:hover {
  background: #f5f5f5;
}

.tab.active {
  background: #e8f4f8;
  color: #2196f3;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
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
  grid-template-columns: 2fr 1fr;
  gap: 20px;
}

@media (max-width: 1024px) {
  .chart-row,
  .stock-section {
    grid-template-columns: 1fr;
  }
}
</style>
