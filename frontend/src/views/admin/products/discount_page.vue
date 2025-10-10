<template>
  <div class="discount-page">
    <nav class="breadcrumb">
      <span class="breadcrumb-item active">Discount Management</span>
    </nav>

    <div class="table-container">
      <table class="discounts-table">
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
            <th>Discount Code</th>
            <th>Product Name</th>
            <th>Brand</th>
            <th>Category</th>
            <th>Type</th>
            <th>Value</th>
            <th>Status</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th class="actions-column">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="discount in paginatedDiscounts"
            :key="discount.id"
            :class="{ selected: discount.selected, 'no-discount-row': !discount.hasDiscount }"
            @click="toggleOrderSelection(discount)"
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
            <td class="product-id">{{ discount.id }}</td>
            <td class="discount-code">{{ discount.code }}</td>
            <td class="discount-product">{{ discount.product }}</td>
            <td class="discount-brand">{{ discount.brand }}</td>
            <td class="discount-category">{{ discount.category }}</td>
            <td>{{ discount.type }}</td>
            <td>
              {{ discount.type === 'Percentage' ? discount.value + '%' : '$' + discount.value }}
            </td>
            <td>
              <span
                class="status-badge"
                :class="`status-${discount.status.toLowerCase().replace(' ', '-')}`"
              >
                {{ discount.status }}
              </span>
            </td>
            <td>{{ discount.startDate }}</td>
            <td>{{ discount.endDate }}</td>
            <td class="actions-column">
              <div class="action-buttons">
                <template v-if="discount.hasDiscount">
                  <button
                    @click="editProduct(discount.id)"
                    class="btn-action btn-edit"
                    title="Edit Product"
                  >
                    <Icon icon="mdi:pencil" />
                  </button>
                </template>
                <template v-else>
                  <button
                    @click="addDiscount(discount)"
                    class="btn-action btn-add-discount"
                    title="Add Discount"
                    type="button"
                  >
                    <Icon icon="mdi:plus" />
                  </button>
                </template>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="selectedDiscounts.length > 0" class="bulk-actions">
      <span class="selected-count">{{ selectedDiscounts.length }} product(s) selected</span>
      <div class="bulk-buttons">
        <button @click="bulkDelete" class="btn-bulk btn-bulk-delete">
          <Icon icon="mdi:delete" />
          Delete Selected
        </button>
      </div>
    </div>

    <div class="pagination-container">
      <div class="pagination-info">
        Showing {{ startItem }} to {{ endItem }} of {{ totalItems }} products (Page
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

const sampleData = {
  categories: ['Road Bike', 'Mountain Bike'],
  types: ['Percentage', 'Fixed Amount'],
  statuses: ['Active', 'Inactive', 'Expired', 'No Discount'],
  codes: [
    'SUMMER2025',
    'WELCOME10',
    'LOYALTY15',
    'CLEARANCE20',
    'FLASH50',
    'HOLIDAY25',
    'SAVE30',
    'DEAL40',
    'OFFER35',
    'PROMO45',
  ],
  products: [
    'Trail Pro Carbon',
    'Road Race Elite',
    'Mountain Bike Aluminum',
    'Gravel Sport',
    'Time Trial Aero',
    'Cyclocross Race',
    'Touring Comfort',
    'Downhill Extreme',
    'Urban Commuter',
    'Hybrid Adventure',
    'Electric City Bike',
    'Folding Compact',
    'Kids Balance Bike',
    'Cargo Family Bike',
  ],
  brands: [
    'Cannondale',
    'Trek',
    'Bianchi',
    'Giant',
    'Cervélo',
    'Specialized',
    'Shimano',
    'Calnago',
  ],
}

const generateId = () =>
  `P${Math.floor(Math.random() * 10000)
    .toString()
    .padStart(4, '0')}I`

const randomItem = (array) => array[Math.floor(Math.random() * array.length)]

const generateDiscount = (hasDiscount = true) => {
  if (!hasDiscount) {
    return {
      id: generateId(),
      code: 'N/A',
      product: randomItem(sampleData.products),
      brand: randomItem(sampleData.brands),
      category: randomItem(sampleData.categories),
      type: 'N/A',
      value: 'N/A',
      status: 'No Discount',
      startDate: 'N/A',
      endDate: 'N/A',
      selected: false,
      hasDiscount: false,
    }
  }

  const type = randomItem(sampleData.types)
  const value =
    type === 'Percentage'
      ? Math.floor(Math.random() * 50) + 5
      : Math.floor(Math.random() * 100) + 10
  const startDate = new Date()
  startDate.setDate(startDate.getDate() - Math.floor(Math.random() * 30))
  const endDate = new Date(startDate)
  endDate.setDate(endDate.getDate() + Math.floor(Math.random() * 90) + 30)

  return {
    id: generateId(),
    code: randomItem(sampleData.codes),
    product: randomItem(sampleData.products),
    brand: randomItem(sampleData.brands),
    category: randomItem(sampleData.categories),
    type,
    value: value.toString(),
    status: randomItem(sampleData.statuses.slice(0, 3)),
    startDate: startDate.toLocaleDateString(),
    endDate: endDate.toLocaleDateString(),
    selected: false,
    hasDiscount: true,
  }
}

const generateSampleDiscounts = () => {
  const discounts = []
  const discountCount = Math.floor(45 * 0.6)

  for (let i = 0; i < discountCount; i++) discounts.push(generateDiscount(true))
  for (let i = 0; i < 45 - discountCount; i++) discounts.push(generateDiscount(false))

  return discounts
}

const discounts = ref(generateSampleDiscounts())

const totalItems = computed(() => discounts.value.length)
const totalPages = computed(() => Math.ceil(totalItems.value / ITEMS_PER_PAGE))
const startItem = computed(() => (currentPage.value - 1) * ITEMS_PER_PAGE + 1)
const endItem = computed(() => Math.min(currentPage.value * ITEMS_PER_PAGE, totalItems.value))

const paginatedDiscounts = computed(() => {
  const start = (currentPage.value - 1) * ITEMS_PER_PAGE
  return discounts.value.slice(start, start + ITEMS_PER_PAGE)
})

const selectedDiscounts = computed(() => discounts.value.filter((d) => d.selected))

const visiblePages = computed(() => {
  const maxVisible = 5
  let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2))
  let end = Math.min(totalPages.value, start + maxVisible - 1)

  if (end - start + 1 < maxVisible) start = Math.max(1, end - maxVisible + 1)

  return Array.from({ length: end - start + 1 }, (_, i) => start + i)
})

const toggleSelectAll = () =>
  paginatedDiscounts.value.forEach((d) => (d.selected = selectAll.value))

const toggleOrderSelection = (discount) => {
  discount.selected = !discount.selected
  updateSelectAllState()
}

const updateSelectAllState = () => {
  const selected = paginatedDiscounts.value.filter((d) => d.selected).length
  selectAll.value = selected === paginatedDiscounts.value.length && selected > 0
}

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    selectAll.value = false
  }
}

const createProductInfo = (product) => ({
  productName: product.product,
  brand: product.brand,
  category: product.category,
  quantity: product.hasDiscount ? '25' : '15',
  highlight: `${product.product} - ${product.category}`,
  description: `Complete description for ${product.product}. This is a high-quality ${product.category.toLowerCase()} from ${product.brand}.`,
  quality: product.hasDiscount ? 'High' : 'Standard',
  price: product.hasDiscount ? '2999' : '1899',
  color: product.hasDiscount ? 'Black' : 'Blue',
  discountCode: product.code !== 'N/A' ? product.code : '',
  discountType: product.type !== 'N/A' ? product.type : '',
  discountValue: product.value !== 'N/A' ? product.value : '',
  discountStartDate: product.startDate !== 'N/A' ? product.startDate : '',
  discountEndDate: product.endDate !== 'N/A' ? product.endDate : '',
  range: product.category === 'Road Bike' ? 'N/A' : '100km',
  hubMotor: product.category === 'Road Bike' ? 'N/A' : '750W',
  payload: '120kg',
  controller: product.category === 'Road Bike' ? 'Basic' : 'LCD Display',
  weight: product.category === 'Road Bike' ? '15kg' : '25kg',
  display: product.category === 'Road Bike' ? 'None' : 'Digital',
})

const editProduct = (productId) => {
  const product = discounts.value.find((d) => d.id === productId)
  if (product) {
    router.push({
      path: `/admin/products/edit/${productId}`,
      query: createProductInfo(product),
    })
  } else {
    router.push(`/admin/products/edit/${productId}`)
  }
}

const addDiscount = (discount) => {
  router.push({
    path: '/admin/products/add',
    query: {
      productName: discount.product,
      brand: discount.brand,
      category: discount.category,
      discountCode: discount.code !== 'N/A' ? discount.code : '',
      discountType: discount.type !== 'N/A' ? discount.type : '',
      discountValue: discount.value !== 'N/A' ? discount.value : '',
      discountStartDate: discount.startDate !== 'N/A' ? discount.startDate : '',
      discountEndDate: discount.endDate !== 'N/A' ? discount.endDate : '',
    },
  })
}

const bulkDelete = () => {
  const count = selectedDiscounts.value.length
  if (confirm(`Delete ${count} selected discount(s)?`)) {
    discounts.value = discounts.value.filter((d) => !d.selected)
    selectAll.value = false
    if (paginatedDiscounts.value.length === 0 && currentPage.value > 1) {
      currentPage.value--
    }
  }
}

watch(() => discounts.value.map((p) => p.selected), updateSelectAllState, { deep: true })
watch(currentPage, () => (selectAll.value = false))
</script>

<style scoped>
.discount-page {
  min-height: auto;
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

.table-container {
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow-y: auto;
  max-height: 60vh;
  margin-bottom: 20px;
}

.discounts-table {
  width: 100%;
  border-collapse: collapse;
  font-family: 'Poppins', sans-serif;
}

.discounts-table th {
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

.discounts-table td {
  padding: 16px 12px;
  border-bottom: 1px solid #e2e8f0;
  font-size: 14px;
  color: #2d3748;
}

.discounts-table tbody tr.no-discount-row {
  background-color: #fef5e7;
}

.discounts-table tbody tr.no-discount-row:hover {
  background-color: #fdeaa7;
}

.discounts-table tbody tr.selected {
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

.discount-code {
  font-family: 'Monaco', 'Menlo', monospace;
  font-weight: 500;
  color: #38a169;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.discount-product {
  font-weight: 500;
  max-width: 180px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  color: #2d3748;
}

.discount-brand {
  font-weight: 500;
  max-width: 150px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  color: #2d3748;
}

.discount-category {
  font-weight: 500;
  max-width: 150px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  color: #2d3748;
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
  background-color: #c6f6d5;
  color: #22543d;
}

.status-inactive {
  background-color: #fed7d7;
  color: #742a2a;
}

.status-no-discount {
  background-color: #e2e8f0;
  color: #4a5568;
}

.actions-column {
  display: flex;
}

.action-buttons {
  display: flex;
  gap: 8px;
  justify-content: flex-start;
  align-items: center;
}

.action-separator {
  color: #cbd5e0;
  font-weight: bold;
  font-size: 14px;
  margin: 0 4px;
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

.btn-add-discount {
  background-color: #48bb78;
  color: white;
}

.btn-add-discount:hover {
  background-color: #38a169;
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
  .discounts-table {
    font-size: 13px;
  }

  .discounts-table th,
  .discounts-table td {
    padding: 12px 8px;
  }

  .discount-category {
    max-width: 120px;
  }

  .discount-brand {
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

  .discounts-table {
    font-size: 12px;
  }

  .actions-column {
    width: 140px;
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
  .discounts-table th:nth-child(9),
  .discounts-table td:nth-child(9),
  .discounts-table th:nth-child(10),
  .discounts-table td:nth-child(10) {
    display: none;
  }

  .discount-name {
    max-width: 120px;
  }

  .discount-product {
    max-width: 140px;
  }

  .discount-brand {
    max-width: 100px;
  }

  .discount-category {
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
