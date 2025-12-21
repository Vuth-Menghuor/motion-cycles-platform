<template>
  <div class="stock-alert-table">
    <div class="table-header">
      <h3>Stock Alert Breakdown</h3>
      <select v-model="filterCategory" class="filter-select">
        <option value="all">AllCategory</option>
        <option value="Mountain Bike">Mountain Bikes</option>
        <option value="Road Bike">Road Bikes</option>
      </select>
    </div>
    <div>
      <table>
        <thead>
          <tr>
            <th>Status</th>
            <th>Brand</th>
            <th>Category</th>
            <th>Current Stock</th>
            <th>Minimum Stock</th>
            <th>Last Updated</th>
            <th>Stock Alert</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in filteredStockData" :key="index">
            <td>
              <span class="status-badge" :class="getStatusClass(item.status)">
                <span class="status-dot"></span>
                {{ item.status }}
              </span>
            </td>
            <td>{{ item.brand }}</td>
            <td>{{ item.category }}</td>
            <td>{{ item.currentStock }}</td>
            <td>{{ item.minStock }}</td>
            <td>{{ item.lastUpdated }}</td>
            <td>
              <span class="action-badge" :class="getActionClass(item.stockAlert)">
                {{ item.stockAlert }}
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
  props: {
    stockData: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      filterCategory: 'all',
    }
  },
  computed: {
    filteredStockData() {
      if (this.filterCategory === 'all') {
        return this.stockData
      }
      return this.stockData.filter((item) => item.category === this.filterCategory)
    },
  },
  methods: {
    getStatusClass(status) {
      const classes = {
        LOW: 'status-low',
        FULL: 'status-full',
        Normal: 'status-normal',
      }
      return classes[status] || ''
    },
    getActionClass(alert) {
      const classes = {
        'Restock Needed': 'action-restock-needed',
        'Well Stock': 'action-well-stock',
        Monitor: 'action-monitor',
      }
      return classes[alert] || ''
    },
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
  font-family: 'Poppins', sans-serif;
  color: #374151;
  transition: all 0.2s ease;
  min-width: 140px;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
}

table {
  width: 100%;
  border-collapse: collapse;
  min-width: 720px;
}

thead {
  background: #fbfdff;
  position: sticky;
  top: 0;
  z-index: 1;
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
