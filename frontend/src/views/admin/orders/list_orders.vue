<template>
  <div class="admin-orders-container">
    <nav class="breadcrumb">
      <span class="breadcrumb-item active">List Orders</span>
    </nav>

    <div class="orders-content">
      <div class="filters-section">
        <div class="search-box">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by Order ID, Customer Name, or Product Name..."
            class="search-input"
            @input="debouncedSearch"
          />
          <Icon icon="mdi:magnify" class="search-icon" />
        </div>

        <div class="filter-row">
          <select v-model="selectedStatus" @change="applyFilters" class="filter-select">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="confirmed">Confirmed</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>

          <select v-model="selectedPaymentMethod" @change="applyFilters" class="filter-select">
            <option value="">All Payment Methods</option>
            <option value="bakong">Bakong</option>
          </select>

          <select v-model="selectedCategory" @change="applyFilters" class="filter-select">
            <option value="">All Categories</option>
            <option value="mountain">Mountain Bike</option>
            <option value="road">Road Bike</option>
          </select>

          <button @click="clearFilters" class="btn btn-secondary clear-filters">
            Clear Filters
          </button>
        </div>
      </div>

      <div v-if="filteredOrders.length === 0" class="no-orders">
        <p>No orders found matching your criteria.</p>
      </div>

      <div v-else class="table-container">
        <table class="orders-table">
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
              <th>Order ID</th>
              <th>Customer Name</th>
              <th>Product Name</th>
              <th>Category</th>
              <th>Payment Method</th>
              <th>Total Amount</th>
              <th>Order Date</th>
              <th>Status</th>
              <th class="actions-column">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="order in paginatedOrders"
              :key="order.id"
              :class="{ selected: order.selected }"
              @click="toggleOrderSelection(order)"
              class="clickable-row"
            >
              <td class="checkbox-column">
                <input
                  type="checkbox"
                  v-model="order.selected"
                  @change="updateSelectAllState"
                  @click.stop
                  class="checkbox"
                />
              </td>
              <td>
                <span class="order-id">{{ order.order_number }}</span>
              </td>
              <td>{{ order.customer_name }}</td>
              <td>
                <div v-if="order.items && order.items.length > 0" class="product-name">
                  <div v-for="(item, index) in order.items.slice(0, 2)" :key="item.id">
                    {{ item.name }}{{ index < order.items.length - 1 && index < 1 ? ', ' : '' }}
                  </div>
                  <small v-if="order.items.length > 2"> +{{ order.items.length - 2 }} more </small>
                </div>
                <span v-else>N/A</span>
              </td>
              <td>
                <div v-if="order.items && order.items.length > 0">
                  <span
                    v-for="(item, index) in order.items.slice(0, 2)"
                    :key="item.id"
                    class="category-tag"
                  >
                    {{ getCategoryName(item.category)
                    }}{{ index < order.items.length - 1 && index < 1 ? ', ' : '' }}
                  </span>
                  <small v-if="order.items.length > 2"> +{{ order.items.length - 2 }} more </small>
                </div>
                <span v-else>N/A</span>
              </td>
              <td>{{ formatPaymentMethod(order.payment_method) }}</td>
              <td>
                <span class="price">${{ order.total_amount }}</span>
              </td>
              <td>{{ formatDate(order.created_at) }}</td>
              <td>
                <span :class="`status-badge status-${order.order_status}`">
                  {{ formatStatus(order.order_status) }}
                </span>
              </td>
              <td>
                <div class="action-buttons">
                  <router-link
                    :to="`/admin/orders/view/${order.id}`"
                    class="btn-action btn-view"
                    title="View Details"
                  >
                    <Icon icon="mdi:eye" />
                  </router-link>
                  <router-link
                    :to="`/admin/orders/edit/${order.id}`"
                    class="btn-action btn-edit"
                    title="Edit Order"
                  >
                    <Icon icon="mdi:pencil" />
                  </router-link>
                  <button
                    @click="deleteOrder(order)"
                    class="btn-action btn-delete"
                    title="Delete Order"
                  >
                    <Icon icon="mdi:delete" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="selectedOrders.length > 0" class="bulk-actions">
        <span class="selected-count">{{ selectedOrders.length }} order(s) selected</span>
        <div class="bulk-buttons">
          <button @click="bulkDelete" class="btn-bulk btn-bulk-delete">
            <Icon icon="mdi:delete" />
            Delete Selected
          </button>
        </div>
      </div>

      <div v-if="totalPages > 1" class="pagination-container">
        <div class="pagination-info">
          Showing {{ startItem }} to {{ endItem }} of {{ totalItems }} orders (Page
          {{ currentPage }} of {{ totalPages }})
        </div>
        <div class="pagination-controls">
          <button
            @click="changePage(currentPage - 1)"
            :disabled="currentPage === 1"
            class="btn-page"
          >
            <Icon icon="mdi:chevron-left" />
            Previous
          </button>

          <div class="page-numbers">
            <button
              v-for="page in visiblePages"
              :key="page"
              @click="changePage(page)"
              :class="['btn-page-number', { active: page === currentPage }]"
            >
              {{ page }}
            </button>
          </div>

          <button
            @click="changePage(currentPage + 1)"
            :disabled="currentPage === totalPages"
            class="btn-page"
          >
            Next
            <Icon icon="mdi:chevron-right" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Icon } from '@iconify/vue'

const ITEMS_PER_PAGE = 10
const currentPage = ref(1)
const selectAll = ref(false)
let searchTimeout = null

const searchQuery = ref('')
const selectedStatus = ref('')
const selectedPaymentMethod = ref('')
const selectedCategory = ref('')

const sampleData = {
  customers: [
    'John Doe',
    'Jane Smith',
    'Bob Johnson',
    'Alice Brown',
    'Charlie Wilson',
    'Diana Prince',
    'Edward Norton',
    'Fiona Green',
    'George Lucas',
    'Helen Troy',
    'Ian Malcolm',
    'Julia Roberts',
  ],
  products: ['Mountain Bike Pro', 'Road Bike Elite'],
  categories: ['mountain', 'road'],
  statuses: ['completed', 'processing', 'pending', 'confirmed', 'cancelled'],
}

const generateId = (prefix, num) => `${prefix}-${String(num).padStart(3, '0')}`
const randomItem = (arr) => arr[Math.floor(Math.random() * arr.length)]
const randomDate = () => {
  const date = new Date()
  date.setDate(date.getDate() - Math.floor(Math.random() * 30))
  return date.toISOString()
}

const mockOrders = Array.from({ length: 12 }, (_, i) => ({
  id: i + 1,
  order_number: generateId('ORD', i + 1),
  customer_name: randomItem(sampleData.customers),
  payment_method: 'bakong',
  total_amount: randomItem([299.99, 399.99, 699.98]),
  created_at: randomDate(),
  order_status: randomItem(sampleData.statuses),
  items: [
    {
      id: i + 1,
      name: randomItem(sampleData.products),
      category: randomItem(sampleData.categories),
      quantity: 1,
      price: randomItem([299.99, 399.99]),
    },
  ],
  selected: false,
}))

const orders = ref(mockOrders)

const filteredOrders = computed(() => {
  let filtered = orders.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(
      (order) =>
        order.order_number.toLowerCase().includes(query) ||
        order.customer_name.toLowerCase().includes(query) ||
        order.items.some((item) => item.name.toLowerCase().includes(query)),
    )
  }

  if (selectedStatus.value) {
    filtered = filtered.filter((order) => order.order_status === selectedStatus.value)
  }

  if (selectedPaymentMethod.value) {
    filtered = filtered.filter((order) => order.payment_method === selectedPaymentMethod.value)
  }

  if (selectedCategory.value) {
    filtered = filtered.filter((order) =>
      order.items.some((item) => item.category === selectedCategory.value),
    )
  }

  return filtered
})

const totalPages = computed(() => Math.ceil(filteredOrders.value.length / ITEMS_PER_PAGE))
const paginatedOrders = computed(() => {
  const start = (currentPage.value - 1) * ITEMS_PER_PAGE
  return filteredOrders.value.slice(start, start + ITEMS_PER_PAGE)
})

const totalItems = computed(() => filteredOrders.value.length)
const startItem = computed(() => (currentPage.value - 1) * ITEMS_PER_PAGE + 1)
const endItem = computed(() => Math.min(currentPage.value * ITEMS_PER_PAGE, totalItems.value))

const selectedOrders = computed(() => orders.value.filter((order) => order.selected))

const visiblePages = computed(() => {
  const maxVisible = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
  let end = Math.min(totalPages.value, start + maxVisible - 1)

  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1)
  }

  return Array.from({ length: end - start + 1 }, (_, i) => start + i)
})

const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) currentPage.value = page
}

const formatStatus = (status) => status.charAt(0).toUpperCase() + status.slice(1)

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const formatPaymentMethod = (method) => {
  const methods = { bakong: 'Bakong' }
  return methods[method] || method.charAt(0).toUpperCase() + method.slice(1)
}

const getCategoryName = (category) => {
  const categories = { mountain: 'Mountain', road: 'Road' }
  return categories[category] || category.charAt(0).toUpperCase() + category.slice(1)
}

const deleteOrder = (order) => {
  if (confirm(`Delete order ${order.order_number}?`)) {
    orders.value = orders.value.filter((o) => o.id !== order.id)
    alert('Order deleted successfully')
  }
}

const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => (currentPage.value = 1), 500)
}

const applyFilters = () => (currentPage.value = 1)

const clearFilters = () => {
  searchQuery.value = ''
  selectedStatus.value = ''
  selectedPaymentMethod.value = ''
  selectedCategory.value = ''
  currentPage.value = 1
}

const toggleSelectAll = () => {
  paginatedOrders.value.forEach((order) => (order.selected = selectAll.value))
}

const updateSelectAllState = () => {
  const selectedCount = paginatedOrders.value.filter((order) => order.selected).length
  selectAll.value = selectedCount === paginatedOrders.value.length && selectedCount > 0
}

const toggleOrderSelection = (order) => {
  order.selected = !order.selected
  updateSelectAllState()
}

const bulkDelete = () => {
  const selectedIds = selectedOrders.value.map((order) => order.order_number)
  if (confirm(`Delete ${selectedIds.length} selected order(s)?`)) {
    orders.value = orders.value.filter((order) => !order.selected)
    selectAll.value = false
    if (paginatedOrders.value.length === 0 && currentPage.value > 1) currentPage.value--
  }
}

onMounted(() => {})

watch(
  () => orders.value.map((o) => o.selected),
  () => updateSelectAllState(),
  { deep: true },
)
watch(currentPage, () => (selectAll.value = false))
</script>

<style scoped>
.breadcrumb {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
  padding: 12px 16px;
  background-color: white;
  border-radius: 5px;
  font-size: 13px;
  color: #666;
  border: 1px solid #e9ecef;
  font-family: 'Poppins', sans-serif;
}

.breadcrumb-item {
  color: grey;
  text-decoration: none;
  cursor: pointer;
  transition: color 0.3s;
}

.breadcrumb-item:hover {
  color: #ff9934;
  text-decoration: underline;
}

.breadcrumb-item.active {
  color: #ff9934;
  font-weight: 400;
  cursor: default;
}

.breadcrumb-separator {
  margin: 0 12px;
  color: #999;
}

.admin-orders-container {
  margin: 0 auto;
  font-family: 'Poppins', sans-serif;
}

.btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-primary {
  background: #14c9c9;
  color: white;
}

.btn-primary:hover {
  background: #0fa5a5;
}

.btn-secondary {
  background: #f8f9fa;
  color: #333;
  border: 1px solid #dee2e6;
}

.btn-secondary:hover {
  background: #e9ecef;
}

.btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.8rem;
}

.btn-danger {
  background: #dc3545;
  color: white;
}

.btn-danger:hover {
  background: #c82333;
}

.filters-section {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.search-box {
  position: relative;
  margin-bottom: 1rem;
}

.search-input {
  width: 100%;
  padding: 0.75rem 2.5rem 0.75rem 1rem;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  font-size: 0.9rem;
  transition: border-color 0.2s;
  max-width: -webkit-fill-available;
}

.search-input:focus {
  outline: none;
  border-color: #14c9c9;
}

.search-icon {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  color: #666;
  font-size: 1.2rem;
}

.filter-row {
  display: flex;
  gap: 1rem;
  align-items: center;
  flex-wrap: wrap;
}

.filter-select {
  padding: 0.5rem;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  font-size: 0.9rem;
  min-width: 150px;
  background: white;
}

.filter-select:focus {
  outline: none;
  border-color: #14c9c9;
}

.clear-filters {
  margin-left: auto;
}

.category-tag {
  display: inline-block;
  background: #e9ecef;
  color: #495057;
  padding: 0.25rem 0.5rem;
  border-radius: 3px;
  font-size: 0.8rem;
  margin-right: 0.25rem;
  margin-bottom: 0.25rem;
}

.no-orders {
  text-align: center;
  padding: 3rem;
  color: #666;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.table-container {
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow-y: auto;
  height: 500px;
  margin-bottom: 20px;
}

.orders-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
}

.orders-table th {
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

.orders-table td {
  padding: 16px 12px;
  border-bottom: 1px solid #e2e8f0;
  font-size: 14px;
  color: #2d3748;
}

.orders-table tbody tr:hover {
  background-color: #f7fafc;
}

.orders-table tbody tr.selected {
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

.order-id {
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

.price {
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

.status-pending {
  background-color: #fef5e7;
  color: #d97706;
}

.status-processing {
  background-color: #dbeafe;
  color: #2563eb;
}

.status-completed,
.status-confirmed {
  background-color: #dcfce7;
  color: #166534;
}

.status-failed,
.status-cancelled {
  background-color: #fee2e2;
  color: #dc2626;
}

.actions-column {
  width: 80px;
  text-align: center;
}

.action-buttons {
  display: flex;
  gap: 8px;
  justify-content: center;
  margin: 0 auto;
  align-items: center;
}

.btn-action {
  width: 32px;
  height: 32px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  font-size: 16px;
  margin: 0 auto;
}

.btn-view {
  background-color: #4299e1;
  color: white;
}

.btn-view:hover {
  background-color: #3182ce;
  transform: translateY(-1px);
}

.btn-edit {
  background-color: #ed8936;
  color: white;
}

.btn-edit:hover {
  background-color: #dd6b20;
  transform: translateY(-1px);
}

.btn-delete {
  background-color: #e53e3e;
  color: white;
}

.btn-delete:hover {
  background-color: #c53030;
  transform: translateY(-1px);
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

.btn-bulk-delete {
  background-color: #e53e3e;
  color: white;
}

.btn-bulk-delete:hover {
  background-color: #c53030;
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

@media (max-width: 1024px) {
  .orders-table {
    font-size: 13px;
  }

  .orders-table th,
  .orders-table td {
    padding: 12px 8px;
  }

  .product-name {
    max-width: 150px;
  }
}

@media (max-width: 768px) {
  .table-container {
    overflow-x: auto;
  }

  .orders-table {
    min-width: 1000px;
    font-size: 12px;
  }

  .actions-column {
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

  .filter-row {
    flex-direction: column;
    align-items: stretch;
  }

  .filter-select {
    min-width: auto;
    width: 100%;
  }

  .clear-filters {
    margin-left: 0;
    margin-top: 0.5rem;
    align-self: flex-start;
  }
}

@media (max-width: 480px) {
  .orders-table th:nth-child(7),
  .orders-table td:nth-child(7),
  .orders-table th:nth-child(8),
  .orders-table td:nth-child(8) {
    display: none;
  }

  .product-name {
    max-width: 120px;
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
