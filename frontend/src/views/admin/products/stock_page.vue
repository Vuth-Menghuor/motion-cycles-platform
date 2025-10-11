<template>
  <div class="stock-page">
    <nav class="breadcrumb">
      <span class="breadcrumb-item active">Stock Management</span>
    </nav>

    <!-- Stock Table -->
    <div class="table-container">
      <table class="stock-table">
        <thead>
          <tr>
            <th class="checkbox-column">
              <input
                type="checkbox"
                v-model="selectAll"
                @change="toggleSelectAll"
                class="checkbox"
              />
            </th>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Brand</th>
            <th>Category</th>
            <th>Current Stock</th>
            <th>Minimum Stock</th>
            <th>Status</th>
            <th>Last Updated</th>
            <th class="alert-column">Stock Alert</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="item in paginatedStock"
            :key="item.id"
            :class="{ selected: item.selected }"
            @click="toggleRowSelection(item)"
            class="clickable-row"
          >
            <td class="checkbox-column">
              <input
                type="checkbox"
                v-model="item.selected"
                @change.stop="updateSelectAllState"
                class="checkbox"
              />
            </td>
            <td class="product-id">{{ item.productId }}</td>
            <td class="product-name">{{ item.productName }}</td>
            <td class="brand">{{ item.brand }}</td>
            <td class="category">{{ item.category }}</td>
            <td class="stock-quantity">{{ item.currentStock }}</td>
            <td>{{ item.minimumStock }}</td>
            <td>
              <span
                class="status-badge"
                :class="`status-${item.status.toLowerCase().replace(' ', '-')}`"
              >
                {{ item.status }}
              </span>
            </td>
            <td>{{ item.lastUpdated }}</td>
            <td class="alert-column">
              <span
                class="alert-badge"
                :class="`alert-${getStockAlert(item).toLowerCase().replace(' ', '-')}`"
              >
                {{ getStockAlert(item) }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Bulk Actions (shown when items are selected) -->
    <div v-if="selectedStock.length > 0" class="bulk-actions">
      <span class="selected-count">{{ selectedStock.length }} item(s) selected</span>
      <div class="bulk-buttons">
        <button @click="bulkRestock" class="btn-bulk btn-bulk-restock">
          <Icon icon="mdi:package-variant-plus" />
          Restock Selected
        </button>
      </div>
    </div>

    <!-- Pagination -->
    <div class="pagination-container">
      <div class="pagination-info">
        Showing {{ startItem }} to {{ endItem }} of {{ totalItems }} stock items (Page
        {{ currentPage }} of {{ totalPages }})
      </div>
      <div class="pagination-controls">
        <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1" class="btn-page">
          <Icon icon="mdi:chevron-left" />
          Previous
        </button>

        <div class="page-numbers">
          <button
            v-for="page in visiblePages"
            :key="page"
            @click="goToPage(page)"
            :class="['btn-page-number', { active: page === currentPage }]"
          >
            {{ page }}
          </button>
        </div>

        <button
          @click="goToPage(currentPage + 1)"
          :disabled="currentPage === totalPages"
          class="btn-page"
        >
          Next
          <Icon icon="mdi:chevron-right" />
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Icon } from '@iconify/vue'
import { useRouter } from 'vue-router'

const ITEMS_PER_PAGE = 50
const selectAll = ref(false)
const currentPage = ref(1)
const router = useRouter()

// Sample data for generating stock
const sampleData = {
  names: [
    'Trail Pro Carbon',
    'Road Race Elite',
    'Mountain Bike Aluminum',
    'Gravel Sport',
    'Time Trial Aero',
    'Cyclocross Race',
    'Touring Comfort',
    'Downhill Extreme',
  ],
  categories: ['Mountain Bike', 'Road Bike'],
  brands: [
    'Cannondale',
    'Trek',
    'Bianchi',
    'Giant',
    'CERVÉLO',
    'Specialized',
    'Shimano',
    'Calnago',
  ],
}

// Utility functions
const generateId = (prefix) =>
  `${prefix}${Math.floor(Math.random() * 10000)
    .toString()
    .padStart(4, '0')}`
const randomItem = (arr) => arr[Math.floor(Math.random() * arr.length)]

// Generate sample stock data
const generateSampleStock = () => {
  return Array.from({ length: 55 }, () => {
    const currentStock = Math.floor(Math.random() * 100)
    const minimumStock = Math.floor(Math.random() * 20) + 5

    let status = 'In Stock'
    if (currentStock === 0) {
      status = 'Out of Stock'
    } else if (currentStock <= minimumStock) {
      status = 'Low Stock'
    } else {
      // Status remains 'In Stock'
    }

    const lastUpdated = new Date()
    lastUpdated.setDate(lastUpdated.getDate() - Math.floor(Math.random() * 30))

    return {
      id: generateId('S'),
      productId: generateId('P'),
      productName: randomItem(sampleData.names),
      brand: randomItem(sampleData.brands),
      category: randomItem(sampleData.categories),
      currentStock,
      minimumStock,
      status,
      lastUpdated: lastUpdated.toLocaleDateString(),
      selected: false,
    }
  })
}

const stock = ref(generateSampleStock())

// Computed properties
const totalItems = computed(() => stock.value.length)
const totalPages = computed(() => Math.ceil(totalItems.value / ITEMS_PER_PAGE))
const startItem = computed(() => (currentPage.value - 1) * ITEMS_PER_PAGE + 1)
const endItem = computed(() => Math.min(currentPage.value * ITEMS_PER_PAGE, totalItems.value))

const paginatedStock = computed(() => {
  const start = (currentPage.value - 1) * ITEMS_PER_PAGE
  return stock.value.slice(start, start + ITEMS_PER_PAGE)
})

const selectedStock = computed(() => stock.value.filter((item) => item.selected))

const visiblePages = computed(() => {
  const maxVisible = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
  let end = Math.min(totalPages.value, start + maxVisible - 1)

  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1)
  } else {
    // No adjustment needed for start page
  }

  return Array.from({ length: end - start + 1 }, (_, i) => start + i)
})

// Selection methods
const toggleSelectAll = () => {
  paginatedStock.value.forEach((item) => (item.selected = selectAll.value))
}

const toggleRowSelection = (item) => {
  item.selected = !item.selected
  updateSelectAllState()
}

const updateSelectAllState = () => {
  const selectedCount = paginatedStock.value.filter((item) => item.selected).length
  selectAll.value = selectedCount === paginatedStock.value.length && selectedCount > 0
}

// Pagination methods
const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    selectAll.value = false
  } else {
    // Page is out of range, do nothing
  }
}

// Stock alert function
const getStockAlert = (item) => {
  if (item.currentStock === 0) {
    return 'Critical'
  } else if (item.currentStock <= item.minimumStock) {
    return 'Low Stock'
  } else if (item.currentStock <= item.minimumStock * 1.5) {
    return 'Warning'
  } else {
    return 'Normal'
  }
}

// Bulk actions
const bulkRestock = () => {
  const selected = selectedStock.value
  if (!selected.length) {
    return alert('Please select at least one item to restock.')
  } else {
    // Proceed with restocking
  }

  const item = selected[0]
  const suggestedQuantity = item.currentStock + Math.max(5, Math.ceil(item.currentStock * 0.2))
  const stockAlert = getStockAlert(item)

  let highlight
  if (item.category === 'Mountain Bike') {
    highlight = `${item.productName} - ${item.category} by ${item.brand}`
  } else {
    highlight = `${item.productName} - ${item.category} by ${item.brand}`
  }

  let description
  if (item.category === 'Mountain Bike') {
    description = `Complete description for ${item.productName}. This is a high-quality ${item.category.toLowerCase()} from ${item.brand}. Perfect for off-road adventures and trails.`
  } else {
    description = `Complete description for ${item.productName}. This is a high-quality ${item.category.toLowerCase()} from ${item.brand}. Perfect for road cycling and commuting.`
  }

  let quality
  if (['Cannondale', 'Trek', 'Specialized'].includes(item.brand)) {
    quality = 'High'
  } else {
    quality = 'Standard'
  }

  let price
  if (item.category === 'Mountain Bike') {
    price = '2999'
  } else {
    price = '1899'
  }

  let color
  if (item.category === 'Mountain Bike') {
    color = 'Black'
  } else {
    color = 'Blue'
  }

  let range
  if (item.category === 'Road Bike') {
    range = 'N/A'
  } else {
    range = '100km'
  }

  let hubMotor
  if (item.category === 'Road Bike') {
    hubMotor = 'N/A'
  } else {
    hubMotor = '750W'
  }

  let controller
  if (item.category === 'Road Bike') {
    controller = 'Basic'
  } else {
    controller = 'LCD Display'
  }

  let weight
  if (item.category === 'Road Bike') {
    weight = '15kg'
  } else {
    weight = '25kg'
  }

  let display
  if (item.category === 'Road Bike') {
    display = 'None'
  } else {
    display = 'Digital'
  }

  const productInfo = {
    productName: item.productName,
    brand: item.brand,
    category: item.category,
    quantity: suggestedQuantity.toString(),
    productId: item.productId,
    restockMode: 'true',
    stockAlert,
    highlight: highlight,
    description: description,
    quality: quality,
    price: price,
    color: color,
    range: range,
    hubMotor: hubMotor,
    payload: '120kg',
    controller: controller,
    weight: weight,
    display: display,
  }

  if (selected.length > 1) {
    productInfo.bulkCount = selected.length.toString()
  } else {
    // Single item, no bulk count needed
  }

  router.push({ path: '/admin/products/add', query: productInfo })
}

// Watchers
watch(
  () => stock.value.map((p) => p.selected),
  () => updateSelectAllState(),
  { deep: true },
)
watch(currentPage, () => (selectAll.value = false))
</script>

<style scoped>
.stock-page {
  min-height: auto;
}

.table-container {
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow-y: auto;
  max-height: 60vh;
  margin-bottom: 20px;
}

.stock-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
}

.stock-table th {
  background-color: #f7fafc;
  color: #4a5568;
  font-weight: 600;
  font-size: 14px;
  text-align: left;
  padding: 16px 12px;
  border-bottom: 2px solid #e2e8f0;
  position: sticky;
  top: 0;
  z-index: 1;
}

.stock-table td {
  padding: 16px 12px;
  border-bottom: 1px solid #e2e8f0;
  font-size: 14px;
  color: #2d3748;
}

.stock-table tbody tr:hover {
  background-color: #f7fafc;
}

.stock-table tbody tr.selected {
  background-color: #ebf8ff;
}

.clickable-row {
  cursor: pointer;
}

.checkbox-column {
  width: 50px;
  text-align: center;
}

.checkbox {
  width: 16px;
  height: 16px;
  cursor: pointer;
  accent-color: #4299e1;
  display: block;
  margin: 0 auto;
}

.product-id {
  font-family: 'Monaco', 'Menlo', monospace;
  font-weight: 500;
  color: #2b6cb0;
}

.product-name {
  font-weight: 500;
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.brand {
  font-weight: 500;
  max-width: 150px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  color: #4a5568;
}

.category {
  font-weight: 500;
  color: #4a5568;
}

.stock-quantity {
  font-weight: 600;
  color: #38a169;
}

.status-badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  text-transform: uppercase;
}

.status-in-stock {
  background-color: #c6f6d5;
  color: #22543d;
}

.status-low-stock {
  background-color: #fed7d7;
  color: #742a2a;
}

.status-out-of-stock {
  background-color: #e2e8f0;
  color: #4a5568;
}

.alert-badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  text-transform: uppercase;
}

.alert-normal {
  background-color: #c6f6d5;
  color: #22543d;
}

.alert-warning {
  background-color: #fef5e7;
  color: #92400e;
}

.alert-low-stock {
  background-color: #fed7d7;
  color: #742a2a;
}

.alert-critical {
  background-color: #7f1d1d;
  color: #ffffff;
}

.alert-column {
  width: 120px;
  text-align: center;
}

.bulk-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 16px 20px;
  margin-top: 20px;
}

.selected-count {
  font-weight: 500;
  color: #4a5568;
  font-family: 'Poppins', sans-serif;
}

.bulk-buttons {
  display: flex;
  gap: 12px;
}

.btn-bulk {
  padding: 10px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s ease;
}

.btn-bulk-restock {
  background-color: #38a169;
  color: white;
}

.btn-bulk-restock:hover {
  background-color: #2f855a;
  transform: translateY(-1px);
}

.pagination-container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 20px;
  margin-top: 20px;
  font-family: 'Poppins', sans-serif;
}

.pagination-info {
  color: #4a5568;
  font-size: 14px;
  font-weight: 500;
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 8px;
}

.btn-page {
  padding: 8px 16px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  background-color: #ffffff;
  color: #4a5568;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s ease;
}

.btn-page:hover:not(:disabled) {
  background-color: #f7fafc;
  border-color: #cbd5e0;
}

.btn-page:disabled {
  background-color: #f7fafc;
  color: #a0aec0;
  cursor: not-allowed;
  border-color: #e2e8f0;
}

.page-numbers {
  display: flex;
  gap: 4px;
  margin: 0 12px;
}

.btn-page-number {
  padding: 8px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  background-color: #ffffff;
  color: #4a5568;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  min-width: 40px;
  transition: all 0.2s ease;
}

.btn-page-number:hover {
  background-color: #f7fafc;
  border-color: #cbd5e0;
}

.btn-page-number.active {
  background-color: #4299e1;
  color: white;
  border-color: #4299e1;
}

.breadcrumb {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
  padding: 12px 16px;
  border-radius: 5px;
  font-size: 13px;
  color: #666;
  background-color: white;
  border: 1px solid #e9ecef;
  font-family: 'Poppins', sans-serif;
}

.breadcrumb-item {
  color: grey;
  text-decoration: none;
  cursor: pointer;
  transition: color 0.3s;
}

.breadcrumb-item.active {
  color: #ff9934;
  font-weight: 400;
  cursor: default;
}

@media (max-width: 1024px) {
  .stock-table {
    font-size: 13px;
  }

  .stock-table th,
  .stock-table td {
    padding: 12px 8px;
  }

  .product-name {
    max-width: 150px;
  }

  .brand {
    max-width: 120px;
  }
}

@media (max-width: 768px) {
  .bulk-actions {
    flex-direction: column;
    gap: 12px;
    align-items: stretch;
  }

  .bulk-buttons {
    justify-content: center;
  }

  .stock-table {
    font-size: 12px;
  }

  .alert-column {
    width: 100px;
  }

  .pagination-container {
    flex-direction: column;
    gap: 16px;
    align-items: stretch;
  }

  .pagination-controls {
    justify-content: center;
  }

  .page-numbers {
    margin: 0 8px;
  }
}

@media (max-width: 480px) {
  .stock-table th:nth-child(6),
  .stock-table td:nth-child(6),
  .stock-table th:nth-child(7),
  .stock-table td:nth-child(7) {
    display: none;
  }

  .product-name {
    max-width: 120px;
  }

  .brand {
    max-width: 100px;
  }

  .pagination-controls {
    flex-wrap: wrap;
    gap: 4px;
  }

  .page-numbers {
    order: 2;
    width: 100%;
    justify-content: center;
    margin: 8px 0;
  }

  .btn-page {
    padding: 6px 12px;
    font-size: 13px;
  }
}
</style>
