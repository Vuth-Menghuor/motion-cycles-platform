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
          <select v-model="selectedStatus" @change="applyFilters" class="filter-select">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>

          <button @click="clearFilters" class="btn btn-secondary clear-filters">
            Clear Filters
          </button>
        </div>
      </div>

      <div v-if="filteredCustomers.length === 0" class="no-customers">
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
              <th>Phone</th>
              <th>Registration Date</th>
              <th>Total Orders</th>
              <th>Status</th>
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
              <td>{{ customer.phone }}</td>
              <td>{{ formatDate(customer.registration_date) }}</td>
              <td>
                <span class="order-count">{{ customer.total_orders }}</span>
              </td>
              <td>
                <span :class="`status-badge status-${customer.status}`">
                  {{ formatStatus(customer.status) }}
                </span>
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
                  <router-link
                    :to="`/admin/customers/edit/${customer.id}`"
                    class="btn-action btn-edit"
                    title="Edit Customer"
                  >
                    <Icon icon="mdi:pencil" />
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

const mockCustomers = [
  {
    id: 1,
    customer_id: 'CUST-001',
    name: 'John Doe',
    email: 'john.doe@example.com',
    phone: '+1-555-0123',
    registration_date: '2024-01-15T10:30:00Z',
    total_orders: 5,
    status: 'active',
  },
  {
    id: 2,
    customer_id: 'CUST-002',
    name: 'Jane Smith',
    email: 'jane.smith@example.com',
    phone: '+1-555-0124',
    registration_date: '2024-01-14T14:20:00Z',
    total_orders: 3,
    status: 'active',
  },
  {
    id: 3,
    customer_id: 'CUST-003',
    name: 'Bob Johnson',
    email: 'bob.johnson@example.com',
    phone: '+1-555-0125',
    registration_date: '2024-01-13T09:15:00Z',
    total_orders: 2,
    status: 'inactive',
  },
  {
    id: 4,
    customer_id: 'CUST-004',
    name: 'Alice Brown',
    email: 'alice.brown@example.com',
    phone: '+1-555-0126',
    registration_date: '2024-01-12T16:45:00Z',
    total_orders: 7,
    status: 'active',
  },
  {
    id: 5,
    customer_id: 'CUST-005',
    name: 'Charlie Wilson',
    email: 'charlie.wilson@example.com',
    phone: '+1-555-0127',
    registration_date: '2024-01-11T11:30:00Z',
    total_orders: 1,
    status: 'active',
  },
  {
    id: 6,
    customer_id: 'CUST-006',
    name: 'Diana Prince',
    email: 'diana.prince@example.com',
    phone: '+1-555-0128',
    registration_date: '2024-01-10T14:15:00Z',
    total_orders: 4,
    status: 'active',
  },
  {
    id: 7,
    customer_id: 'CUST-007',
    name: 'Edward Norton',
    email: 'edward.norton@example.com',
    phone: '+1-555-0129',
    registration_date: '2024-01-09T09:45:00Z',
    total_orders: 6,
    status: 'inactive',
  },
  {
    id: 8,
    customer_id: 'CUST-008',
    name: 'Fiona Green',
    email: 'fiona.green@example.com',
    phone: '+1-555-0130',
    registration_date: '2024-01-08T16:20:00Z',
    total_orders: 2,
    status: 'active',
  },
  {
    id: 9,
    customer_id: 'CUST-009',
    name: 'George Lucas',
    email: 'george.lucas@example.com',
    phone: '+1-555-0131',
    registration_date: '2024-01-07T11:10:00Z',
    total_orders: 8,
    status: 'active',
  },
  {
    id: 10,
    customer_id: 'CUST-010',
    name: 'Helen Troy',
    email: 'helen.troy@example.com',
    phone: '+1-555-0132',
    registration_date: '2024-01-06T13:30:00Z',
    total_orders: 3,
    status: 'active',
  },
  {
    id: 11,
    customer_id: 'CUST-011',
    name: 'Ian Malcolm',
    email: 'ian.malcolm@example.com',
    phone: '+1-555-0133',
    registration_date: '2024-01-05T10:00:00Z',
    total_orders: 5,
    status: 'inactive',
  },
  {
    id: 12,
    customer_id: 'CUST-012',
    name: 'Julia Roberts',
    email: 'julia.roberts@example.com',
    phone: '+1-555-0134',
    registration_date: '2024-01-04T15:45:00Z',
    total_orders: 9,
    status: 'active',
  },
]

const customers = ref(mockCustomers.map((c) => ({ ...c, selected: false })))
const currentPage = ref(1)
const itemsPerPage = ref(10)
const searchQuery = ref('')
const selectedStatus = ref('')
const selectAll = ref(false)
let searchTimeout = null

const filteredCustomers = computed(() => {
  let filtered = customers.value

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filtered = filtered.filter(
      (c) =>
        c.customer_id.toLowerCase().includes(query) ||
        c.name.toLowerCase().includes(query) ||
        c.email.toLowerCase().includes(query),
    )
  }

  if (selectedStatus.value) {
    filtered = filtered.filter((c) => c.status === selectedStatus.value)
  }

  return filtered
})

const totalPages = computed(() => Math.ceil(filteredCustomers.value.length / itemsPerPage.value))
const paginatedCustomers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredCustomers.value.slice(start, start + itemsPerPage.value)
})

const totalItems = computed(() => filteredCustomers.value.length)
const startItem = computed(() => (currentPage.value - 1) * itemsPerPage.value + 1)
const endItem = computed(() => Math.min(currentPage.value * itemsPerPage.value, totalItems.value))

const selectedCustomers = computed(() => customers.value.filter((c) => c.selected))

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

const deleteCustomer = (customer) => {
  if (confirm(`Delete customer ${customer.name}?`)) {
    customers.value = customers.value.filter((c) => c.id !== customer.id)
    alert('Customer deleted successfully')
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
  currentPage.value = 1
}

const toggleSelectAll = () => {
  paginatedCustomers.value.forEach((c) => (c.selected = selectAll.value))
}

const updateSelectAllState = () => {
  const selectedCount = paginatedCustomers.value.filter((c) => c.selected).length
  selectAll.value = selectedCount === paginatedCustomers.value.length && selectedCount > 0
}

const toggleCustomerSelection = (customer) => {
  customer.selected = !customer.selected
  updateSelectAllState()
}

const bulkDelete = () => {
  const selectedNames = selectedCustomers.value.map((c) => c.name)
  if (confirm(`Delete ${selectedNames.length} selected customer(s)?`)) {
    customers.value = customers.value.filter((c) => !c.selected)
    selectAll.value = false
    if (paginatedCustomers.value.length === 0 && currentPage.value > 1) currentPage.value--
  }
}

onMounted(() => {})

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

.no-customers {
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

.status-badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  text-transform: uppercase;
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

@media (max-width: 480px) {
  .customers-table th:nth-child(6),
  .customers-table td:nth-child(6) {
    display: none;
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
