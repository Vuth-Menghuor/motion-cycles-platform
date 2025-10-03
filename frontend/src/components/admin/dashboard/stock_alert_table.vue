<template>
  <div class="stock-alert-table">
    <div class="table-header">
      <h3>Stock Alert Breakdown</h3>
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
          <tr v-for="(item, index) in stockData" :key="index">
            <td>
              <span class="status-badge" :class="`status-${item.status.toLowerCase()}`">
                <span class="status-dot"></span>
                {{ item.status }}
              </span>
            </td>
            <td>{{ item.count }}</td>
            <td>${{ item.totalValue.toLocaleString() }}</td>
            <td>{{ item.percentage }}%</td>
            <td>
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
      filterCategory: 'all',
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
  background: white;
  border-radius: 12px;
  padding: 25px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.table-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #333;
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

.table-container {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead {
  background: #f8f9fa;
}

th {
  padding: 12px 16px;
  text-align: left;
  font-size: 12px;
  font-weight: 600;
  color: #666;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

td {
  padding: 16px;
  border-bottom: 1px solid #f0f0f0;
  font-size: 14px;
  color: #333;
}

tbody tr:hover {
  background: #f8f9fa;
}

.status-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 500;
}

.status-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.status-low {
  background: #ffebee;
  color: #c62828;
}

.status-low .status-dot {
  background: #ef5350;
}

.status-full {
  background: #e8f5e9;
  color: #2e7d32;
}

.status-full .status-dot {
  background: #66bb6a;
}

.status-normal {
  background: #fff8e1;
  color: #f57c00;
}

.status-normal .status-dot {
  background: #ffa726;
}

.action-badge {
  display: inline-block;
  padding: 6px 12px;
  border-radius: 6px;
  font-size: 13px;
  font-weight: 500;
}

.action-restock-needed {
  background: #ffebee;
  color: #c62828;
}

.action-well-stock {
  background: #e8f5e9;
  color: #2e7d32;
}

.action-monitor {
  background: #fff8e1;
  color: #f57c00;
}
</style>
