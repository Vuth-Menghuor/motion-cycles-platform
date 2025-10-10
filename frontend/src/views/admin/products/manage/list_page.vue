<template>
  <div class="product-page">
    <nav class="breadcrumb">
      <span class="breadcrumb-item active">Product List</span>
      <span class="breadcrumb-separator">></span>
      <router-link to="/admin/products/add" class="breadcrumb-item">Add Product</router-link>
      <span class="breadcrumb-separator">></span>
      <router-link to="/admin/products/edit/1" class="breadcrumb-item">Edit Product</router-link>
    </nav>

    <div class="table-container">
      <table class="products-table">
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
            <th>Quality</th>
            <th>Price</th>
            <th>Color</th>
            <th>Quantity</th>
            <th class="actions-column">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="product in paginatedProducts"
            :key="product.id"
            :class="{ selected: product.selected }"
            @click="toggleProductSelection(product)"
            class="clickable-row"
          >
            <td class="checkbox-column">
              <input
                type="checkbox"
                v-model="product.selected"
                @change="updateSelectAllState"
                @click.stop
                class="checkbox"
              />
            </td>
            <td class="product-id">{{ product.id }}</td>
            <td class="product-name">{{ product.name }}</td>
            <td>{{ product.brand }}</td>
            <td>{{ product.category }}</td>
            <td>
              <span
                class="quality-badge"
                :class="`quality-${product.quality.toLowerCase().replace(' ', '-')}`"
              >
                {{ product.quality }}
              </span>
            </td>
            <td class="price">${{ product.price }}</td>
            <td>{{ product.color }}</td>
            <td>{{ product.quantity }}</td>
            <td class="actions-column">
              <div class="action-buttons">
                <button
                  @click="editProduct(product.id)"
                  class="btn-action btn-edit"
                  title="Edit Product"
                >
                  <Icon icon="mdi:pencil" />
                </button>
                <span class="action-separator">|</span>
                <button
                  @click="deleteProduct(product.id)"
                  class="btn-action btn-delete"
                  title="Delete Product"
                >
                  <Icon icon="mdi:delete" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="selectedProducts.length > 0" class="bulk-actions">
      <span class="selected-count">{{ selectedProducts.length }} product(s) selected</span>
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

// Reactive state
const selectAll = ref(false)
const currentPage = ref(1)
const router = useRouter()

// Generate sample products for demo
const generateSampleProducts = () => {
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
  const categories = ['Mountain Bike', 'Road Bike']
  const qualities = ['New', 'Second Hand']
  const colors = ['Black', 'White', 'Red', 'Blue', 'Green', 'Silver', 'Gray', 'Yellow']
  const mountainBikeTypes = ['Trail', 'Enduro', 'Downhill', 'Cross Country', 'All Mountain']
  const roadBikeTypes = ['Road Race', 'Gravel', 'Time Trial', 'Cyclocross', 'Touring']

  const products = []

  for (let i = 0; i < 60; i++) {
    const randomNum = Math.floor(Math.random() * 10000)
      .toString()
      .padStart(4, '0')
    const category = categories[Math.floor(Math.random() * categories.length)]
    const bikeTypes = category === 'Mountain Bike' ? mountainBikeTypes : roadBikeTypes
    const bikeType = bikeTypes[Math.floor(Math.random() * bikeTypes.length)]
    const modelSuffix = ['Pro', 'Elite', 'Race', 'Sport', 'Carbon', 'Aluminum'][
      Math.floor(Math.random() * 6)
    ]

    products.push({
      id: `P${randomNum}I`,
      name: `${bikeType} ${modelSuffix}`,
      brand: brands[Math.floor(Math.random() * brands.length)],
      category: category,
      quality: qualities[Math.floor(Math.random() * qualities.length)],
      price: (Math.floor(Math.random() * 4000) + 500).toString(),
      color: colors[Math.floor(Math.random() * colors.length)],
      quantity: (Math.floor(Math.random() * 50) + 1).toString(),
      selected: false,
    })
  }

  return products
}

const products = ref(generateSampleProducts())

// Pagination computed properties
const totalItems = computed(() => products.value.length)
const totalPages = computed(() => Math.ceil(totalItems.value / ITEMS_PER_PAGE))
const startItem = computed(() => (currentPage.value - 1) * ITEMS_PER_PAGE + 1)
const endItem = computed(() => Math.min(currentPage.value * ITEMS_PER_PAGE, totalItems.value))

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * ITEMS_PER_PAGE
  const end = start + ITEMS_PER_PAGE
  return products.value.slice(start, end)
})

const selectedProducts = computed(() => products.value.filter((product) => product.selected))

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

// Selection methods
const toggleSelectAll = () => {
  paginatedProducts.value.forEach((product) => {
    product.selected = selectAll.value
  })
}

const updateSelectAllState = () => {
  const currentPageSelected = paginatedProducts.value.filter((product) => product.selected).length
  const currentPageTotal = paginatedProducts.value.length
  selectAll.value = currentPageSelected === currentPageTotal && currentPageTotal > 0
}

const toggleProductSelection = (product) => {
  product.selected = !product.selected
  updateSelectAllState()
}

// Pagination methods
const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page
    selectAll.value = false
  }
}

// Product actions
const editProduct = (productId) => {
  console.log('Edit product:', productId)
  router.push(`/admin/products/edit/${productId}`)
}

const deleteProduct = (productId) => {
  if (confirm(`Delete product ${productId}?`)) {
    const index = products.value.findIndex((product) => product.id === productId)
    if (index > -1) {
      products.value.splice(index, 1)
      if (paginatedProducts.value.length === 0 && currentPage.value > 1) {
        currentPage.value--
      }
    }
  }
}

const bulkDelete = () => {
  const selectedIds = selectedProducts.value.map((product) => product.id)
  if (confirm(`Delete ${selectedIds.length} selected product(s)?`)) {
    products.value = products.value.filter((product) => !product.selected)
    selectAll.value = false
    if (paginatedProducts.value.length === 0 && currentPage.value > 1) {
      currentPage.value--
    }
  }
}

// Watchers
watch(
  () => products.value.map((p) => p.selected),
  () => updateSelectAllState(),
  { deep: true },
)

watch(currentPage, () => {
  selectAll.value = false
})
</script>

<style scoped>
.product-page {
  min-height: auto;
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

.quality-new {
  background-color: #c6f6d5;
  color: #22543d;
}

.quality-second-hand {
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
    margin: 0 8px;
  }
}

@media (max-width: 480px) {
  .products-table th:nth-child(6),
  .products-table td:nth-child(6),
  .products-table th:nth-child(7),
  .products-table td:nth-child(7),
  .products-table th:nth-child(8),
  .products-table td:nth-child(8) {
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
