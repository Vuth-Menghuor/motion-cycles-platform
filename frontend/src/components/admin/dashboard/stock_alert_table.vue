<template>
  <div class="stock-alert-table">
    <div class="table-header">
      <h3>Stock Alert Breakdown</h3>
      <select v-model="filterCategory" class="filter-select">
        <option value="all">Filter Category</option>
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
              <span class="status-badge" :class="`status-${item.status.toLowerCase()}`">
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
              <span
                class="action-badge"
                :class="`action-${item.stockAlert.toLowerCase().replace(' ', '-')}`"
              >
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
  data() {
    return {
      filterCategory: 'all',
      stockData: [
        {
          brand: 'Cannondale',
          category: 'Mountain Bike',
          currentStock: 15,
          minStock: 10,
          status: 'LOW',
          stockAlert: 'Restock Needed',
          lastUpdated: '2025-10-11',
        },
        {
          brand: 'Trek',
          category: 'Road Bike',
          currentStock: 25,
          minStock: 5,
          status: 'FULL',
          stockAlert: 'Well Stock',
          lastUpdated: '2025-10-10',
        },
        {
          brand: 'Bianchi',
          category: 'Mountain Bike',
          currentStock: 8,
          minStock: 10,
          status: 'Normal',
          stockAlert: 'Monitor',
          lastUpdated: '2025-10-09',
        },
        {
          brand: 'Giant',
          category: 'Road Bike',
          currentStock: 12,
          minStock: 15,
          status: 'LOW',
          stockAlert: 'Restock Needed',
          lastUpdated: '2025-10-08',
        },
        {
          brand: 'Cervélo',
          category: 'Mountain Bike',
          currentStock: 30,
          minStock: 8,
          status: 'FULL',
          stockAlert: 'Well Stock',
          lastUpdated: '2025-10-07',
        },
        {
          brand: 'Specialized',
          category: 'Road Bike',
          currentStock: 5,
          minStock: 10,
          status: 'Normal',
          stockAlert: 'Monitor',
          lastUpdated: '2025-10-06',
        },
        {
          brand: 'Shimano',
          category: 'Mountain Bike',
          currentStock: 18,
          minStock: 12,
          status: 'LOW',
          stockAlert: 'Restock Needed',
          lastUpdated: '2025-10-05',
        },
        {
          brand: 'Colnago',
          category: 'Road Bike',
          currentStock: 22,
          minStock: 6,
          status: 'FULL',
          stockAlert: 'Well Stock',
          lastUpdated: '2025-10-04',
        },
      ],
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

table {
  width: 100%;
  border-collapse: collapse;
  min-width: 720px;
  height: auto;
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
