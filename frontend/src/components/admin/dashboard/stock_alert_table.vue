<template>
  <!-- Stock Alert Table Component -->
  <div class="stock-alert-table">
    <div class="table-header">
      <h3>Stock Alert Breakdown</h3>
      <!-- Category Filter Dropdown -->
      <select v-model="filterCategory" class="filter-select">
        <option value="all">Filter Category</option>
        <option value="mountain">Mountain Bikes</option>
        <option value="road">Road Bikes</option>
      </select>
    </div>
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>STATUS</th>
            <th>COUNT</th>
            <th>TOTAL VALUE</th>
            <th>PERCENTAGE</th>
            <th>ACTION REQUIRED</th>
          </tr>
        </thead>
        <tbody>
          <!-- Loop through stock data and display each row -->
          <tr v-for="(item, index) in stockData" :key="index">
            <td>
              <!-- Status Badge with colored dot -->
              <span class="status-badge" :class="`status-${item.status.toLowerCase()}`">
                <span class="status-dot"></span>
                {{ item.status }}
              </span>
            </td>
            <td>{{ item.count }}</td>
            <td>${{ item.totalValue.toLocaleString() }}</td>
            <td>{{ item.percentage }}%</td>
            <td>
              <!-- Action Badge -->
              <span
                class="action-badge"
                :class="`action-${item.action.toLowerCase().replace(' ', '-')}`"
              >
                {{ item.action }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script>
export default {
  name: 'StockAlertTable',
  data() {
    return {
      // Current filter selection
      filterCategory: 'all',
      // Sample stock data (replace with real data from API)
      stockData: [
        {
          status: 'LOW',
          count: 10,
          totalValue: 43300.0,
          percentage: 12.5,
          action: 'Restock Needed',
        },
        {
          status: 'FULL',
          count: 10,
          totalValue: 43300.0,
          percentage: 12.5,
          action: 'Well Stock',
        },
        {
          status: 'Normal',
          count: 10,
          totalValue: 43300.0,
          percentage: 12.5,
          action: 'Monitor',
        },
      ],
    }
  },
}
</script>

<style scoped>
.stock-alert-table {
  background: #ffffff;
  border-radius: 12px;
  padding: 18px;
  box-shadow: 0 6px 18px rgba(2, 6, 23, 0.06);
  font-family: 'Poppins', sans-serif;
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  gap: 12px;
}

.table-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 400;
  color: #0f172a;
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

.table-container {
  overflow-x: auto;
  overflow-y: auto;
  max-height: 400px;
}

table {
  width: 100%;
  border-collapse: collapse;
  min-width: 720px;
  height: auto;
}

thead {
  background: #fbfdff;
}

th {
  padding: 10px 12px;
  text-align: left;
  font-size: 12px;
  font-weight: 700;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.6px;
}

td {
  padding: 12px;
  border-bottom: 1px solid #f1f5f9;
  font-size: 14px;
  color: #111827;
}

tbody tr:hover {
  background: #f8fafc;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 10px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.status-low {
  background: #fff1f2;
  color: #be123c;
}
.status-low .status-dot {
  background: #fb7185;
}

.status-full {
  background: #ecfdf5;
  color: #065f46;
}
.status-full .status-dot {
  background: #34d399;
}

.status-normal {
  background: #fffbeb;
  color: #b45309;
}
.status-normal .status-dot {
  background: #f59e0b;
}

.action-badge {
  display: inline-block;
  padding: 6px 10px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
}
.action-restock-needed {
  background: #fff1f2;
  color: #be123c;
}
.action-well-stock {
  background: #ecfdf5;
  color: #065f46;
}
.action-monitor {
  background: #fffbeb;
  color: #b45309;
}
</style>
