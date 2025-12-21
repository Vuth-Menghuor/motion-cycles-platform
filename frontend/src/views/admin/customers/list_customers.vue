<template>
  <div class="admin-customers-container">
    <nav class="breadcrumb">
      <span class="breadcrumb-item active">List Customers</span>
    </nav>

    <div class="customers-content">
      <div class="filters-section">
        <div class="search-box">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by Customer ID, Name, or Email..."
            class="search-input"
            @input="debouncedSearch"
          />
          <Icon icon="mdi:magnify" class="search-icon" />
        </div>

        <div class="filter-row">
          <button @click="clearFilters" class="btn btn-secondary clear-filters">
            Clear Filters
          </button>
        </div>
      </div>

      <div v-if="isLoading" class="loading-section">
        <div class="loading-card">
          <Icon icon="mdi:loading" class="loading-icon" />
          <h3>Loading customers...</h3>
        </div>
      </div>

      <div v-else-if="errorMessage" class="error-section">
        <div class="error-card">
          <Icon icon="mdi:alert-circle" class="error-icon" />
          <h3>Error Loading Customers</h3>
          <p>{{ errorMessage }}</p>
          <button @click="loadUsers" class="btn btn-primary">Try Again</button>
        </div>
      </div>

      <div v-else-if="filteredCustomers.length === 0" class="no-customers">
        <p>No customers found matching your criteria.</p>
      </div>

      <div v-else class="table-container">
        <table class="customers-table">
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
              <th>Customer ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Registration Date</th>
              <th>Total Orders</th>
              <th class="actions-column">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="customer in paginatedCustomers"
              :key="customer.id"
              :class="{ selected: customer.selected }"
              @click="toggleCustomerSelection(customer)"
              class="clickable-row"
            >
              <td class="checkbox-column">
                <input
                  type="checkbox"
                  v-model="customer.selected"
                  @change="updateSelectAllState"
                  @click.stop
                  class="checkbox"
                />
              </td>
              <td>
                <span class="customer-id">{{ customer.customer_id }}</span>
              </td>
              <td>{{ customer.name }}</td>
              <td>{{ customer.email }}</td>
              <td>{{ formatDate(customer.registration_date) }}</td>
              <td>
                <span class="order-count">{{ customer.total_orders }}</span>
              </td>
              <td>
                <div class="action-buttons">
                  <router-link
                    :to="`/admin/customers/view/${customer.id}`"
                    class="btn-action btn-view"
                    title="View Details"
                  >
                    <Icon icon="mdi:eye" />
                  </router-link>
                  <button
                    @click="deleteCustomer(customer)"
                    class="btn-action btn-delete"
                    title="Delete Customer"
                  >
                    <Icon icon="mdi:delete" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="selectedCustomers.length > 0" class="bulk-actions">
        <span class="selected-count">{{ selectedCustomers.length }} customer(s) selected</span>
        <div class="bulk-buttons">
          <button @click="bulkDelete" class="btn-bulk btn-bulk-delete">
            <Icon icon="mdi:delete" />
            Delete Selected
          </button>
        </div>
      </div>

      <div v-if="totalPages > 1" class="pagination-container">
        <div class="pagination-info">
          Showing {{ startItem }} to {{ endItem }} of {{ totalItems }} customers (Page
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
import { usersApi } from '@/services/api'

// Reactive data
const customers = ref([])
const currentPage = ref(1)
const itemsPerPage = ref(10)
const searchQuery = ref('')
const selectAll = ref(false)
const isLoading = ref(false)
const errorMessage = ref('')
let searchTimeout = null

// Computed properties

// Filter customers based on search and status
const filteredCustomers = computed(() => {
  let filtered = customers.value

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(
      (c) =>
        c.customer_id.toLowerCase().includes(query) ||
        c.name.toLowerCase().includes(query) ||
        c.email.toLowerCase().includes(query),
    )
  }
  return filtered
})

// Calculate total pages
const totalPages = computed(() => Math.ceil(filteredCustomers.value.length / itemsPerPage.value))

// Get customers for current page
const paginatedCustomers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredCustomers.value.slice(start, start + itemsPerPage.value)
})

// Total items count
const totalItems = computed(() => filteredCustomers.value.length)

// Start item number for pagination
const startItem = computed(() => (currentPage.value - 1) * itemsPerPage.value + 1)

// End item number for pagination
const endItem = computed(() => Math.min(currentPage.value * itemsPerPage.value, totalItems.value))

// Get selected customers
const selectedCustomers = computed(() => customers.value.filter((c) => c.selected))

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

// Functions

// Load users from API
const loadUsers = async () => {
  try {
    isLoading.value = true
    errorMessage.value = ''

    const params = {}
    if (searchQuery.value) {
      params.search = searchQuery.value
    }

    const response = await usersApi.getUsers(params)

    if (response.data.success) {
      let customersData = response.data.data.map((c) => ({ ...c, selected: false }))

      customers.value = customersData
    } else {
      errorMessage.value = response.data.message || 'Failed to load users'
    }
  } catch (error) {
    console.error('Error loading users:', error)
    errorMessage.value = error.response?.data?.message || 'Failed to load users'
  } finally {
    isLoading.value = false
  }
}

// Change to a specific page
const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

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

// Delete a single customer
const deleteCustomer = async (customer) => {
  if (!confirm(`Delete customer ${customer.name}?`)) {
    return
  }

  try {
    const response = await usersApi.deleteUser(customer.id)
    if (response.data.success) {
      // Remove from local array
      customers.value = customers.value.filter((c) => c.id !== customer.id)
      alert('Customer deleted successfully')
    } else {
      alert(response.data.message || 'Failed to delete customer')
    }
  } catch (error) {
    console.error('Error deleting customer:', error)
    alert(error.response?.data?.message || 'Failed to delete customer')
  }
}

// Debounced search to avoid too many filters
const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    currentPage.value = 1
    loadUsers()
  }, 500)
}

// Clear all filters
const clearFilters = () => {
  searchQuery.value = ''
  currentPage.value = 1
  loadUsers()
}

// Toggle select all for current page
const toggleSelectAll = () => {
  paginatedCustomers.value.forEach((c) => (c.selected = selectAll.value))
}

// Update select all state based on selected items
const updateSelectAllState = () => {
  const selectedCount = paginatedCustomers.value.filter((c) => c.selected).length
  selectAll.value = selectedCount === paginatedCustomers.value.length && selectedCount > 0
}

// Toggle selection for a single customer
const toggleCustomerSelection = (customer) => {
  customer.selected = !customer.selected
  updateSelectAllState()
}

// Bulk delete selected customers
const bulkDelete = async () => {
  const selectedNames = selectedCustomers.value.map((c) => c.name)
  if (!confirm(`Delete ${selectedNames.length} selected customer(s)?`)) {
    return
  }

  try {
    // Delete each selected customer
    const deletePromises = selectedCustomers.value.map((customer) =>
      usersApi.deleteUser(customer.id),
    )

    await Promise.all(deletePromises)

    // Remove from local array
    customers.value = customers.value.filter((c) => !c.selected)
    selectAll.value = false

    if (paginatedCustomers.value.length === 0 && currentPage.value > 1) {
      currentPage.value--
    }

    alert('Selected customers deleted successfully')
  } catch (error) {
    console.error('Error deleting customers:', error)
    alert(error.response?.data?.message || 'Failed to delete some customers')
    // Reload users to get current state
    loadUsers()
  }
}

// Lifecycle hooks
onMounted(() => {
  loadUsers()
})

// Watchers
watch(
  () => customers.value.map((c) => c.selected),
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

.admin-customers-container {
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

.loading-section,
.error-section {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
}

.loading-card,
.error-card {
  background: white;
  border-radius: 8px;
  padding: 48px;
  text-align: center;
  border: 1px solid #e2e8f0;
  max-width: 400px;
  width: 100%;
}

.loading-icon {
  font-size: 48px;
  color: #14c9c9;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

.error-icon {
  font-size: 48px;
  color: #dc3545;
  margin-bottom: 16px;
}

.loading-card h3,
.error-card h3 {
  margin: 0 0 12px 0;
  color: #333;
}

.error-card p {
  margin: 0 0 24px 0;
  color: #666;
}

.table-container {
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow-y: auto;
  height: 500px;
  margin-bottom: 20px;
}

.customers-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
}

.customers-table th {
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

.customers-table td {
  padding: 16px 12px;
  border-bottom: 1px solid #e2e8f0;
  font-size: 14px;
  color: #2d3748;
}

.customers-table tbody tr:hover {
  background-color: #f7fafc;
}

.customers-table tbody tr.selected {
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

.customer-id {
  font-family: 'Monaco', 'Menlo', monospace;
  font-weight: 500;
  color: #2b6cb0;
}

.order-count {
  font-weight: 600;
  color: #38a169;
}

.status-active {
  background-color: #dcfce7;
  color: #166534;
}

.status-inactive {
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
  .customers-table {
    font-size: 13px;
  }

  .customers-table th,
  .customers-table td {
    padding: 12px 8px;
  }
}

@media (max-width: 768px) {
  .table-container {
    overflow-x: auto;
  }

  .customers-table {
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

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
