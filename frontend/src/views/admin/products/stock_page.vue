<template>
  <div class="stock-page">
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

// Constants
const ITEMS_PER_PAGE = 50

// Reactive state
const selectAll = ref(false)
const currentPage = ref(1)
const router = useRouter()

// Sample data generation
const generateSampleStock = () => {
  const productNames = [
    'Trail Pro Carbon',
    'Road Race Elite',
    'Mountain Bike Aluminum',
    'Gravel Sport',
    'Time Trial Aero',
    'Cyclocross Race',
    'Touring Comfort',
    'Downhill Extreme',
  ]

  const categories = ['Mountain Bike', 'Road Bike']
  const brands = [
    'Cannondale',
    'Trek',
    'Bianchi',
    'Giant',
    'CERVÉLO',
    'Specialized',
    'Shimano',
    'Calnago',
  ]

  const stock = []

  for (let i = 0; i < 55; i++) {
    const randomNum = Math.floor(Math.random() * 10000)
      .toString()
      .padStart(4, '0')
    const currentStock = Math.floor(Math.random() * 100)
    const minimumStock = Math.floor(Math.random() * 20) + 5

    let status = 'In Stock'
    if (currentStock === 0) {
      status = 'Out of Stock'
    } else if (currentStock <= minimumStock) {
      status = 'Low Stock'
    }

    const lastUpdated = new Date()
    lastUpdated.setDate(lastUpdated.getDate() - Math.floor(Math.random() * 30))

    stock.push({
      id: `S${randomNum}K`,
      productId: `P${randomNum}I`,
      productName: productNames[Math.floor(Math.random() * productNames.length)],
      brand: brands[Math.floor(Math.random() * brands.length)],
      category: categories[Math.floor(Math.random() * categories.length)],
      currentStock: currentStock,
      minimumStock: minimumStock,
      status: status,
      lastUpdated: lastUpdated.toLocaleDateString(),
      selected: false,
    })
  }

  return stock
}

// Data
const stock = ref(generateSampleStock())

// Computed properties
const totalItems = computed(() => stock.value.length)
const totalPages = computed(() => Math.ceil(totalItems.value / ITEMS_PER_PAGE))
const startItem = computed(() => (currentPage.value - 1) * ITEMS_PER_PAGE + 1)
const endItem = computed(() => Math.min(currentPage.value * ITEMS_PER_PAGE, totalItems.value))

const paginatedStock = computed(() => {
  const start = (currentPage.value - 1) * ITEMS_PER_PAGE
  const end = start + ITEMS_PER_PAGE
  return stock.value.slice(start, end)
})

const selectedStock = computed(() => stock.value.filter((item) => item.selected))

const visiblePages = computed(() => {
  const pages = []
  const maxVisiblePages = 5
  let startPage = Math.max(1, currentPage.value - Math.floor(maxVisiblePages / 2))
  let endPage = Math.min(totalPages.value, startPage + maxVisiblePages - 1)

  if (endPage - startPage + 1 < maxVisiblePages) {
    startPage = Math.max(1, endPage - maxVisiblePages + 1)
  }

  for (let i = startPage; i <= endPage; i++) {
    pages.push(i)
  }

  return pages
})

// Methods
const toggleSelectAll = () => {
  paginatedStock.value.forEach((item) => {
    item.selected = selectAll.value
  })
}

const toggleRowSelection = (item) => {
  item.selected = !item.selected
  updateSelectAllState()
}

const updateSelectAllState = () => {
  const currentPageSelected = paginatedStock.value.filter((item) => item.selected).length
  const currentPageTotal = paginatedStock.value.length
  selectAll.value = currentPageSelected === currentPageTotal && currentPageTotal > 0
}

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    selectAll.value = false
  }
}

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

const bulkRestock = () => {
  const selectedItems = selectedStock.value

  if (selectedItems.length === 0) {
    alert('Please select at least one item to restock.')
    return
  }

  if (selectedItems.length === 1) {
    // Single item selected - navigate to add page with complete product details
    const item = selectedItems[0]
    restockProduct(item)
  } else {
    // Multiple items selected - for now, just navigate with the first item
    // You could implement bulk restocking logic here
    const item = selectedItems[0]
    restockProduct(item, selectedItems.length)
  }
}

const restockProduct = (item, bulkCount = 1) => {
  // For restocking, suggest a quantity higher than current stock
  const suggestedQuantity = item.currentStock + Math.max(5, Math.ceil(item.currentStock * 0.2))

  // Get the stock alert status
  const stockAlert = getStockAlert(item)

  // Generate complete product information based on available stock data
  const completeProductInfo = {
    productName: item.productName,
    brand: item.brand,
    category: item.category,
    quantity: suggestedQuantity.toString(), // Suggest higher quantity for restocking
    productId: item.productId,
    restockMode: 'true',
    stockAlert: stockAlert, // Pass the stock alert status
    // Generate additional product details based on category and brand
    highlight: `${item.productName} - ${item.category} by ${item.brand}`,
    description: `Complete description for ${item.productName}. This is a high-quality ${item.category.toLowerCase()} from ${item.brand}. Perfect for ${item.category === 'Mountain Bike' ? 'off-road adventures and trails' : 'road cycling and commuting'}.`,
    quality:
      item.brand === 'Cannondale' || item.brand === 'Trek' || item.brand === 'Specialized'
        ? 'High'
        : 'Standard',
    price: item.category === 'Mountain Bike' ? '2999' : '1899', // Default prices based on category
    color: item.category === 'Mountain Bike' ? 'Black' : 'Blue', // Default colors
    // Specs information based on category
    range: item.category === 'Road Bike' ? 'N/A' : '100km',
    hubMotor: item.category === 'Road Bike' ? 'N/A' : '750W',
    payload: '120kg',
    controller: item.category === 'Road Bike' ? 'Basic' : 'LCD Display',
    weight: item.category === 'Road Bike' ? '15kg' : '25kg',
    display: item.category === 'Road Bike' ? 'None' : 'Digital',
  }

  if (bulkCount > 1) {
    completeProductInfo.bulkCount = bulkCount.toString()
  }

  // Navigate to add page with complete product information
  router.push({
    path: '/admin/products/add',
    query: completeProductInfo,
  })
}

// Watchers
watch(
  () => stock.value.map((p) => p.selected),
  () => updateSelectAllState(),
  { deep: true },
)

watch(currentPage, () => {
  selectAll.value = false
})
</script>

<style scoped>
/* Page Layout */
.stock-page {
  min-height: auto;
}

/* Table Container */
.table-container {
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow-y: auto;
  max-height: 60vh;
  margin-bottom: 20px;
}

/* Stock Table */
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

/* Checkbox Column */
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

/* Product ID Column */
.product-id {
  font-family: 'Monaco', 'Menlo', monospace;
  font-weight: 500;
  color: #2b6cb0;
}

/* Product Name Column */
.product-name {
  font-weight: 500;
  max-width: 200px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Brand Column */
.brand {
  font-weight: 500;
  max-width: 150px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  color: #4a5568;
}

/* Category Column */
.category {
  font-weight: 500;
  color: #4a5568;
}

/* Stock Quantity Column */
.stock-quantity {
  font-weight: 600;
  color: #38a169;
}

/* Status Badge */
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

/* Alert Badge */
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

/* Alert Column */
.alert-column {
  width: 120px;
  text-align: center;
}

/* Bulk Actions */
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

/* Pagination */
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

/* Responsive Design */
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

  .action-buttons {
    gap: 4px;
  }

  .btn-action {
    width: 28px;
    height: 28px;
    font-size: 14px;
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
