<template>
  <div class="product-page">
    <nav class="breadcrumb">
      <span class="breadcrumb-item active">Promotional Codes</span>
      <span class="breadcrumb-separator">></span>
      <router-link to="/admin/products/discount/add" class="breadcrumb-item">Add Code</router-link>
    </nav>

    <div class="table-container">
      <div v-if="loading" class="loading-state">
        <Icon icon="mdi:loading" class="loading-icon" />
        <p>Loading discount codes...</p>
      </div>
      <div v-else-if="error" class="error-state">
        <Icon icon="mdi:alert-circle" class="error-icon" />
        <p>{{ error }}</p>
        <button @click="fetchDiscounts" class="btn-retry">Retry</button>
      </div>
      <table v-else class="products-table">
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
            <th>Code</th>
            <th>Type</th>
            <th>Value</th>
            <th>Status</th>
            <th>Valid Until</th>
            <th class="actions-column">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="discount in paginatedDiscounts"
            :key="discount.id"
            :class="{ selected: discount.selected }"
            @click="toggleDiscountSelection(discount)"
            class="clickable-row"
          >
            <td class="checkbox-column">
              <input
                type="checkbox"
                v-model="discount.selected"
                @change="updateSelectAllState"
                @click.stop
                class="checkbox"
              />
            </td>
            <td class="product-id">{{ discount.code }}</td>
            <td>
              <span class="quality-badge" :class="`quality-${discount.type}`">
                {{ discount.type === 'percentage' ? 'Percentage' : 'Fixed Amount' }}
              </span>
            </td>
            <td class="price">
              {{ discount.type === 'percentage' ? discount.value + '%' : '$' + discount.value }}
            </td>
            <td>
              <span class="quality-badge" :class="getStatusClass(discount)">
                {{ getStatusText(discount) }}
              </span>
            </td>
            <td>{{ formatDate(discount.expire_date) }}</td>
            <td class="actions-column">
              <div class="action-buttons">
                <button
                  @click="editDiscount(discount.id)"
                  class="btn-action btn-edit"
                  title="Edit Discount"
                >
                  <Icon icon="mdi:pencil" />
                </button>
                <span class="action-separator">|</span>
                <button
                  @click="deleteDiscount(discount)"
                  class="btn-action btn-delete"
                  title="Delete Discount"
                >
                  <Icon icon="mdi:delete" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="selectedDiscounts.length > 0" class="bulk-actions">
      <span class="selected-count">{{ selectedDiscounts.length }} discount code(s) selected</span>
      <div class="bulk-buttons">
        <button @click="bulkDelete" class="btn-bulk btn-bulk-delete">
          <Icon icon="mdi:delete" />
          Delete Selected
        </button>
      </div>
    </div>

    <div class="pagination-container">
      <div class="pagination-info">
        Showing {{ startItem }} to {{ endItem }} of {{ totalItems }} discount codes (Page
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
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter } from 'vue-router'
import { Icon } from '@iconify/vue'
import { discountsApi } from '@/services/api.js'

const router = useRouter()

const ITEMS_PER_PAGE = 10
const searchQuery = ref('')
const activeFilter = ref('all')
const currentPage = ref(1)
const selectAll = ref(false)
const discounts = ref([])
const loading = ref(false)
const error = ref(null)

const fetchDiscounts = async () => {
  try {
    loading.value = true
    error.value = null

    const params = {}
    if (activeFilter.value !== 'all') {
      params.active = activeFilter.value === 'active' ? '1' : '0'
    }
    if (searchQuery.value.trim()) {
      params.search = searchQuery.value.trim()
    }

    const response = await discountsApi.getDiscounts(params)

    // Handle different response structures
    let discountData = []
    if (response.data?.success && response.data?.data) {
      // Laravel API response with pagination
      if (response.data.data.data && Array.isArray(response.data.data.data)) {
        discountData = response.data.data.data
      } else if (Array.isArray(response.data.data)) {
        discountData = response.data.data
      }
    } else if (response.data?.data && Array.isArray(response.data.data)) {
      // Direct array response
      discountData = response.data.data
    } else if (Array.isArray(response.data)) {
      // Raw array response
      discountData = response.data
    }

    // Add selected property to each discount
    discounts.value = discountData.map((discount) => ({
      ...discount,
      selected: false,
    }))
  } catch (err) {
    error.value = 'Failed to load discount codes'
    console.error('Error fetching discounts:', err)
    console.error('Response:', err.response?.data)
    discounts.value = []
  } finally {
    loading.value = false
  }
}

// Load discounts when component mounts
onMounted(() => {
  fetchDiscounts()
})

// Watch for filter changes
watch([searchQuery, activeFilter], () => {
  currentPage.value = 1
  fetchDiscounts()
})

// Watchers for selection
watch(
  () => discounts.value.map((p) => p.selected),
  () => updateSelectAllState(),
  { deep: true },
)

watch(currentPage, () => {
  selectAll.value = false
})

// Computed properties for filtering and pagination
const filteredDiscounts = computed(() => {
  const data = discounts.value
  return Array.isArray(data) ? data : []
})

const totalItems = computed(() => filteredDiscounts.value.length)
const totalPages = computed(() => Math.ceil(totalItems.value / ITEMS_PER_PAGE))
const startItem = computed(() => (currentPage.value - 1) * ITEMS_PER_PAGE + 1)
const endItem = computed(() => Math.min(currentPage.value * ITEMS_PER_PAGE, totalItems.value))

const paginatedDiscounts = computed(() => {
  const data = filteredDiscounts.value
  if (!Array.isArray(data)) {
    return []
  }
  const start = (currentPage.value - 1) * ITEMS_PER_PAGE
  return data.slice(start, start + ITEMS_PER_PAGE)
})

const selectedDiscounts = computed(() => discounts.value.filter((discount) => discount.selected))

const visiblePages = computed(() => {
  const maxVisible = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
  let end = Math.min(totalPages.value, start + maxVisible - 1)

  if (end - start + 1 < maxVisible) {
    start = Math.max(1, end - maxVisible + 1)
  }

  return Array.from({ length: end - start + 1 }, (_, i) => start + i)
})

// Helper functions
const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString()
}

const getStatusText = (discount) => {
  if (!discount.is_active) return 'Inactive'
  const now = new Date()
  const expireDate = new Date(discount.expire_date)
  const startDate = new Date(discount.start_date)

  if (now < startDate) return 'Not Started'
  if (now > expireDate) return 'Expired'
  if (discount.usage_limit && discount.used_count >= discount.usage_limit) return 'Used Up'
  return 'Active'
}

const getStatusClass = (discount) => {
  const status = getStatusText(discount).toLowerCase().replace(' ', '-')
  return `status-${status}`
}

// Pagination methods
const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
  }
}

// Selection methods
const toggleSelectAll = () => {
  paginatedDiscounts.value.forEach((discount) => {
    discount.selected = selectAll.value
  })
}

const updateSelectAllState = () => {
  const currentPageSelected = paginatedDiscounts.value.filter(
    (discount) => discount.selected,
  ).length
  const currentPageTotal = paginatedDiscounts.value.length
  selectAll.value = currentPageSelected === currentPageTotal && currentPageTotal > 0
}

const toggleDiscountSelection = (discount) => {
  discount.selected = !discount.selected
  updateSelectAllState()
}

// Bulk actions
const bulkDelete = async () => {
  const selectedIds = selectedDiscounts.value.map((discount) => discount.id)
  if (confirm(`Delete ${selectedIds.length} selected discount code(s)?`)) {
    try {
      await Promise.all(selectedIds.map((id) => discountsApi.deleteDiscount(id)))
      await fetchDiscounts()
      alert('Selected discount codes deleted successfully!')
    } catch (err) {
      alert('Failed to delete some discount codes')
      console.error('Error deleting discount codes:', err)
    }
  }
}

// Delete discount
const deleteDiscount = async (discount) => {
  if (!confirm(`Are you sure you want to delete the discount code "${discount.code}"?`)) {
    return
  }

  try {
    await discountsApi.deleteDiscount(discount.id)
    await fetchDiscounts()
    alert('Discount code deleted successfully!')
  } catch (err) {
    alert('Failed to delete discount code')
    console.error('Error deleting discount:', err)
  }
}

// Edit discount
const editDiscount = (discountId) => {
  router.push(`/admin/products/discount/edit/${discountId}`)
}
</script>

<style scoped>
.product-page {
  min-height: auto;
}

.page-header {
  margin-bottom: 24px;
  padding: 20px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.page-title {
  font-size: 24px;
  font-weight: 600;
  color: #1a202c;
  margin-bottom: 8px;
  font-family: 'Poppins', sans-serif;
}

.page-description {
  color: #718096;
  font-size: 14px;
  line-height: 1.5;
  margin: 0;
  font-family: 'Poppins', sans-serif;
}

.breadcrumb {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
  padding: 12px 16px;
  background: white;
  border-radius: 6px;
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
  font-weight: 500;
  cursor: default;
}

.breadcrumb-separator {
  margin: 0 12px;
  color: #999;
}

.table-container {
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow-y: auto;
  max-height: 60vh;
  margin-bottom: 20px;
}

.loading-state,
.error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  text-align: center;
  color: #4a5568;
  font-family: 'Poppins', sans-serif;
}

.loading-icon {
  font-size: 48px;
  color: #4299e1;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

.error-icon {
  font-size: 48px;
  color: #e53e3e;
  margin-bottom: 16px;
}

.btn-retry {
  margin-top: 16px;
  padding: 8px 16px;
  background-color: #4299e1;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  transition: background-color 0.2s ease;
}

.btn-retry:hover {
  background-color: #3182ce;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.products-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
}

.products-table th {
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

.products-table td {
  padding: 16px 12px;
  border-bottom: 1px solid #e2e8f0;
  font-size: 14px;
  color: #2d3748;
}

.products-table tbody tr:hover {
  background-color: #f7fafc;
}

.products-table tbody tr.selected {
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

.price {
  font-weight: 600;
  color: #38a169;
}

.quality-badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  text-transform: uppercase;
}

.quality-percentage {
  background-color: #c6f6d5;
  color: #22543d;
}

.quality-fixed {
  background-color: #bee3f8;
  color: #2a4365;
}

.status-active {
  background-color: #c6f6d5;
  color: #22543d;
}

.status-inactive {
  background-color: #fed7d7;
  color: #742a2a;
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

.btn-edit {
  background-color: #4299e1;
  color: white;
}

.btn-edit:hover {
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

.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border: 1px solid #dee2e6;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  text-decoration: none;
  font-family: 'Poppins', sans-serif;
  background: white;
  color: #333;
  transition: all 0.2s ease;
}

.btn:hover {
  background: #f8f9fa;
  transform: translateY(-1px);
}

.btn-primary {
  background: #4299e1;
  color: white;
  border-color: #4299e1;
}

.btn-primary:hover {
  background: #3182ce;
  border-color: #3182ce;
}

@media (max-width: 1024px) {
  .products-table {
    font-size: 13px;
  }

  .products-table th,
  .products-table td {
    padding: 12px 8px;
  }

  .product-name {
    max-width: 150px;
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

  .products-table {
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
    order: 2;
    width: 100%;
    justify-content: center;
    margin: 8px 0;
  }
}

@media (max-width: 480px) {
  .products-table th:nth-child(5),
  .products-table td:nth-child(5),
  .products-table th:nth-child(6),
  .products-table td:nth-child(6) {
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
