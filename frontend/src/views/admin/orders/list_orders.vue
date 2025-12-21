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
          <select v-model="selectedCategory" @change="applyFilters" class="filter-select">
            <option value="">All Categories</option>
            <option value="mountain">Mountain Bike</option>
            <option value="road">Road Bike</option>
          </select>

          <select v-model="selectedBrand" @change="applyFilters" class="filter-select">
            <option value="">All Brands</option>
            <option value="Cannondale">Cannondale</option>
            <option value="Trek">Trek</option>
            <option value="Bianchi">Bianchi</option>
            <option value="Giant">Giant</option>
            <option value="Cervélo">Cervélo</option>
            <option value="Specialized">Specialized</option>
            <option value="Shimano">Shimano</option>
            <option value="Colnago">Colnago</option>
          </select>

          <button @click="clearFilters" class="btn btn-secondary clear-filters">
            Clear Filters
          </button>
        </div>
      </div>

      <div v-if="isLoading" class="loading-section">
        <div class="loading-card">
          <Icon icon="mdi:loading" class="loading-icon" />
          <h3>Loading orders...</h3>
        </div>
      </div>

      <div v-else-if="error" class="error-section">
        <div class="error-card">
          <Icon icon="mdi:alert-circle" class="error-icon" />
          <h3>Error Loading Orders</h3>
          <p>{{ error }}</p>
          <button @click="loadOrders" class="btn btn-primary">Try Again</button>
        </div>
      </div>

      <div v-else-if="filteredOrders.length === 0" class="no-orders">
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
              <th>Brand</th>
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
              <td>
                <div v-if="order.items && order.items.length > 0">
                  <span
                    v-for="(item, index) in order.items.slice(0, 2)"
                    :key="item.id"
                    class="brand-tag"
                  >
                    {{ item.brand || 'N/A'
                    }}{{ index < order.items.length - 1 && index < 1 ? ', ' : '' }}
                  </span>
                  <small v-if="order.items.length > 2"> +{{ order.items.length - 2 }} more </small>
                </div>
                <span v-else>N/A</span>
              </td>
              <td>{{ formatPaymentMethod(order.payment_method) }}</td>
              <td>
                <span class="price">${{ calculateOrderTotal(order).toFixed(2) }}</span>
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
import { ordersApi } from '@/services/api'

const ITEMS_PER_PAGE = 10
const currentPage = ref(1)
const selectAll = ref(false)
let searchTimeout = null

const searchQuery = ref('')
const selectedCategory = ref('')
const selectedBrand = ref('')
const isLoading = ref(false)
const error = ref(null)

// Orders data from API
const orders = ref([])
const totalOrders = ref(0)

// Load orders from API
const loadOrders = async () => {
  try {
    isLoading.value = true
    error.value = null

    const response = await ordersApi.adminListOrders()
    if (response.data.success) {
      // Add selected property to each order for bulk operations
      orders.value = response.data.orders.data.map((order) => ({
        ...order,
        selected: false,
      }))
      totalOrders.value = response.data.orders.total
    } else {
      error.value = 'Failed to load orders'
    }
  } catch (err) {
    console.error('Error loading orders:', err)
    error.value = err.response?.data?.message || 'Failed to load orders'
  } finally {
    isLoading.value = false
  }
}

// Filter orders based on search and filters (client-side filtering for now)
const filteredOrders = computed(() => {
  let filtered = orders.value

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(
      (order) =>
        order.order_number.toLowerCase().includes(query) ||
        order.customer_name.toLowerCase().includes(query) ||
        (order.items && order.items.some((item) => item.name.toLowerCase().includes(query))),
    )
  }

  // Apply category filter
  if (selectedCategory.value) {
    filtered = filtered.filter(
      (order) =>
        order.items && order.items.some((item) => item.category === selectedCategory.value),
    )
  }

  // Apply brand filter
  if (selectedBrand.value) {
    filtered = filtered.filter(
      (order) => order.items && order.items.some((item) => item.brand === selectedBrand.value),
    )
  }

  return filtered
})

// Calculate total pages based on filtered results
const totalPages = computed(() => Math.ceil(filteredOrders.value.length / ITEMS_PER_PAGE))

// Get orders for current page
const paginatedOrders = computed(() => {
  const start = (currentPage.value - 1) * ITEMS_PER_PAGE
  return filteredOrders.value.slice(start, start + ITEMS_PER_PAGE)
})

// Total items count
const totalItems = computed(() => filteredOrders.value.length)

// Start item number for pagination
const startItem = computed(() => (currentPage.value - 1) * ITEMS_PER_PAGE + 1)

// End item number for pagination
const endItem = computed(() => Math.min(currentPage.value * ITEMS_PER_PAGE, totalItems.value))

// Calculate total amount for an order (accounting for item discounts and promo discounts)
const calculateOrderTotal = (order) => {
  const subtotal = parseFloat(order.subtotal || 0)
  const shipping = parseFloat(order.shipping_amount || 0)
  const promoDiscount = parseFloat(order.discount_amount || 0)

  // Calculate item discount
  const itemDiscount = (order.items || []).reduce((total, item) => {
    const discount = item.discount?.[0]
    if (!discount) return total

    const pricing = parseFloat(item.price) || 0
    let discountValue = 0

    if (discount.type === 'percent') {
      discountValue = pricing * (discount.value / 100) * item.quantity
    } else if (discount.type === 'fixed') {
      discountValue = discount.value * item.quantity
    }
    return total + discountValue
  }, 0)

  return subtotal - itemDiscount + shipping - promoDiscount
}

// Get selected orders
const selectedOrders = computed(() => orders.value.filter((order) => order.selected))

// Calculate visible page numbers for pagination
const visiblePages = computed(() => {
  const maxVisible = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
  let end = Math.min(totalPages.value, start + maxVisible - 1)

  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1)
  }

  return Array.from({ length: end - start + 1 }, (_, i) => start + i)
})

// Change to a specific page
const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

// Format status for display
const formatStatus = (status) => status.charAt(0).toUpperCase() + status.slice(1)

// Format date for display
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

// Format payment method for display
const formatPaymentMethod = (method) => {
  if (!method) return 'N/A'
  const methods = { bakong: 'Bakong' }
  if (methods[method]) {
    return methods[method]
  } else {
    return method.charAt(0).toUpperCase() + method.slice(1)
  }
}

// Get category name for display
const getCategoryName = (category) => {
  // If category is an object (from API relationship), get the name
  if (category && typeof category === 'object' && category.name) {
    // Map category names to display names
    const categoryDisplayMap = {
      Mountain: 'Mountain Bike',
      Road: 'Road Bike',
    }
    return categoryDisplayMap[category.name] || category.name
  }

  // If category is a string, use it directly
  if (category && typeof category === 'string') {
    const categoryDisplayMap = {
      mountain: 'Mountain Bike',
      road: 'Road Bike',
    }
    return categoryDisplayMap[category] || category
  }

  return 'Unknown'
}

// Delete a single order
const deleteOrder = async (order) => {
  if (!confirm(`Delete order ${order.order_number}?`)) return

  try {
    // For now, just remove from local state since we don't have a delete API endpoint
    // In a real implementation, you'd call an API endpoint to delete the order
    orders.value = orders.value.filter((o) => o.id !== order.id)
    alert('Order deleted successfully')
  } catch (error) {
    console.error('Error deleting order:', error)
    alert('Failed to delete order')
  }
}

// Debounced search to avoid too many filters
const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => (currentPage.value = 1), 500)
}

// Apply filters and reset to first page
const applyFilters = () => (currentPage.value = 1)

// Clear all filters
const clearFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  currentPage.value = 1
}

// Toggle select all for current page
const toggleSelectAll = () => {
  paginatedOrders.value.forEach((order) => (order.selected = selectAll.value))
}

// Update select all state based on selected items
const updateSelectAllState = () => {
  const selectedCount = paginatedOrders.value.filter((order) => order.selected).length
  selectAll.value = selectedCount === paginatedOrders.value.length && selectedCount > 0
}

// Toggle selection for a single order
const toggleOrderSelection = (order) => {
  order.selected = !order.selected
  updateSelectAllState()
}

// Bulk delete selected orders
const bulkDelete = async () => {
  const selectedIds = selectedOrders.value.map((order) => order.order_number)
  if (!confirm(`Delete ${selectedIds.length} selected order(s)?`)) return

  try {
    // For now, just remove from local state since we don't have a bulk delete API endpoint
    orders.value = orders.value.filter((order) => !order.selected)
    selectAll.value = false
    if (paginatedOrders.value.length === 0 && currentPage.value > 1) {
      currentPage.value--
    }
    alert('Orders deleted successfully')
  } catch (error) {
    console.error('Error deleting orders:', error)
    alert('Failed to delete orders')
  }
}

// Lifecycle hooks
onMounted(() => {
  loadOrders()
})

// Watchers
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
  color: #666;
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
  /* font-weight: 500; */
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

.brand-tag {
  display: inline-block;
  background: #e3f2fd;
  color: #1565c0;
  padding: 0.25rem 0.5rem;
  border-radius: 3px;
  font-size: 0.8rem;
  margin-right: 0.25rem;
  margin-bottom: 0.25rem;
  font-weight: 500;
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

.status-completed {
  background-color: #dcfce7;
  color: #166534;
}

.status-pending {
  background: #fef3c7;
  color: #d97706;
  padding: 0.25rem 0.5rem;
  border-radius: 3px;
  font-size: 0.8rem;
  font-weight: 500;
}

.status-processing {
  background: #dbeafe;
  color: #2563eb;
  padding: 0.25rem 0.5rem;
  border-radius: 3px;
  font-size: 0.8rem;
  font-weight: 500;
}

.status-shipped {
  background: #fef3c7;
  color: #d97706;
  padding: 0.25rem 0.5rem;
  border-radius: 3px;
  font-size: 0.8rem;
  font-weight: 500;
}

.status-delivered {
  background: #d1fae5;
  color: #065f46;
  padding: 0.25rem 0.5rem;
  border-radius: 3px;
  font-size: 0.8rem;
  font-weight: 500;
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
