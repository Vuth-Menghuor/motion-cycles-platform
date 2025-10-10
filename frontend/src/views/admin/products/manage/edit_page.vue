<template>
  <div class="edit-product-page">
    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb">
      <router-link to="/admin/products/manage" class="breadcrumb-item">Product List</router-link>
      <span class="breadcrumb-separator">></span>
      <router-link to="/admin/products/add" class="breadcrumb-item">Add Product</router-link>
      <span class="breadcrumb-separator">></span>
      <span class="breadcrumb-item active">{{
        hasQueryParams ? 'Edit Discount' : 'Edit Product'
      }}</span>
    </nav>

    <!-- Product ID Selector -->
    <div class="product-selector">
      <div class="selector-container">
        <label for="productInput" class="selector-label">Enter Product ID to Edit:</label>
        <div class="input-group">
          <input
            id="productInput"
            v-model="selectedProductId"
            @blur="loadProduct"
            @keyup.enter="loadProduct"
            placeholder="e.g. P0000I"
            class="product-input"
          />
          <button @click="loadProduct" class="load-btn" :disabled="!selectedProductId.trim()">
            Load Product
          </button>
        </div>
      </div>
    </div>

    <!-- Edit Form (only shown when product is selected) -->
    <div v-if="selectedProductId" class="form-container">
      <!-- Left Column -->
      <div class="form-column form-column-left">
        <ProductInfo
          :product="product"
          @update:product="product = $event"
          :disabled="isFormDisabled"
          :prefilled-fields="prefilledFields"
        />
        <ProductDescription
          :product="product"
          @update:product="product = $event"
          :disabled="isFormDisabled"
        />
        <ProductQuality
          :product="product"
          @update:product="product = $event"
          :disabled="isFormDisabled"
          :prefilled-fields="prefilledFields"
        />
        <ProductDiscount
          :product="product"
          @update:product="product = $event"
          :disabled="isFormDisabled"
        />
      </div>

      <!-- Right Column -->
      <div class="form-column">
        <ProductImage :disabled="isFormDisabled" />
        <ProductPrice
          :product="product"
          @update:product="product = $event"
          :disabled="isFormDisabled"
          :prefilled-fields="prefilledFields"
        />
        <ProductSpecs
          :specs="specs"
          @update:specs="specs = $event"
          @add-product="updateProduct"
          @discard="discardForm"
          :disabled="isFormDisabled"
          mode="edit"
        />
      </div>
    </div>

    <!-- Placeholder when no product is selected -->
    <div v-else class="no-selection-message">
      <div class="message-container">
        <h2>Enter a Product ID to Edit</h2>
        <p>Please enter a product ID above and click "Load Product" to start editing.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import ProductInfo from '@/components/admin/products/ProductInfo.vue'
import ProductDescription from '@/components/admin/products/ProductDescription.vue'
import ProductQuality from '@/components/admin/products/ProductQuality.vue'
import ProductDiscount from '@/components/admin/products/ProductDiscount.vue'
import ProductImage from '@/components/admin/products/ProductImage.vue'
import ProductPrice from '@/components/admin/products/ProductPrice.vue'
import ProductSpecs from '@/components/admin/products/ProductSpecs.vue'

// Get route instance
const route = useRoute()

// Props
const props = defineProps({
  id: {
    type: String,
    default: '',
  },
})

// Constants
const API_DELAY = 500

// Reactive data
const selectedProductId = ref('')
const isProductLoaded = ref(false)

// Set initial product ID from route param
if (props.id) {
  selectedProductId.value = props.id
}

// Computed properties
const isFormDisabled = computed(() => !isProductLoaded.value)

const hasQueryParams = computed(() => {
  return (
    route.query.productName ||
    route.query.brand ||
    route.query.category ||
    route.query.quantity ||
    route.query.restockMode
  )
})

const prefilledFields = computed(() => {
  const fields = {}
  if (route.query.productName) fields.name = true
  if (route.query.brand) fields.brand = true
  if (route.query.category) fields.category = true
  if (route.query.quantity) fields.quantity = true
  if (route.query.quality) fields.quality = true
  if (route.query.price) fields.price = true
  if (route.query.color) fields.color = true
  return fields
})

// Form data
const product = ref({
  id: '',
  name: '',
  brand: '',
  category: '',
  quantity: '',
  highlight: '',
  description: '',
  quality: '',
  price: '',
  discountCode: '',
  discountStartDate: '',
  discountExpireDate: '',
  color: '',
})

const specs = ref({
  range: '',
  hubMotor: '',
  payload: '',
  controller: '',
  weight: '',
  display: '',
})

// Mock data - In production, this would come from API
const MOCK_PRODUCTS = {
  P0000I: {
    id: 'P0000I',
    name: 'Electric Bike Model A',
    brand: 'Cannondale',
    category: 'Road Bike',
    quantity: '25',
    highlight: 'High-performance electric bike',
    description:
      'This is a detailed description of the Electric Bike Model A with advanced features and specifications.',
    quality: 'High',
    price: '2999',
    discountCode: 'ELECTRIC25',
    discountStartDate: '2025-10-01',
    discountExpireDate: '2025-12-31',
    color: 'Black',
  },
  P0001I: {
    id: 'P0001I',
    name: 'Mountain Bike Pro',
    brand: 'Trek',
    category: 'Mountain Bike',
    quantity: '15',
    highlight: 'Professional mountain biking experience',
    description:
      'Designed for extreme terrains and professional riders, this mountain bike offers superior performance.',
    quality: 'Premium',
    price: '1899',
    discountCode: 'MOUNTAIN15',
    discountStartDate: '2025-10-15',
    discountExpireDate: '2025-11-15',
    color: 'Red',
  },
  P0002I: {
    id: 'P0002I',
    name: 'City Cruiser',
    brand: 'Giant',
    category: 'Road Bike',
    quantity: '30',
    highlight: 'Comfortable urban commuting',
    description: 'Perfect for city commuting with comfortable seating and reliable performance.',
    quality: 'Standard',
    price: '899',
    discountCode: 'CITY10',
    discountStartDate: '2025-09-01',
    discountExpireDate: '2025-10-31',
    color: 'Blue',
  },
}

const MOCK_SPECS = {
  P0000I: {
    range: '100km',
    hubMotor: '750W',
    payload: '150kg',
    controller: 'LCD Display',
    weight: '25kg',
    display: 'Digital',
  },
  P0001I: {
    range: 'N/A',
    hubMotor: 'N/A',
    payload: '120kg',
    controller: 'Manual',
    weight: '18kg',
    display: 'Analog',
  },
  P0002I: {
    range: 'N/A',
    hubMotor: 'N/A',
    payload: '100kg',
    controller: 'Basic',
    weight: '15kg',
    display: 'None',
  },
}

// Methods
const convertDateFormat = (dateString) => {
  // Convert from MM/DD/YYYY to YYYY-MM-DD format
  if (!dateString || dateString === 'N/A') return ''

  const parts = dateString.split('/')
  if (parts.length === 3) {
    const [month, day, year] = parts
    return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
  }
  return dateString
}
const loadProduct = () => {
  const productId = selectedProductId.value.trim()

  if (!productId) {
    resetForm()
    return
  }

  if (MOCK_PRODUCTS[productId]) {
    // Simulate API call delay
    setTimeout(() => {
      product.value = { ...MOCK_PRODUCTS[productId] }
      specs.value = { ...MOCK_SPECS[productId] }
      isProductLoaded.value = true
    }, API_DELAY)
  } else {
    alert(`Product with ID "${productId}" not found. Please check the ID and try again.`)
    selectedProductId.value = ''
    resetForm()
  }
}

const updateProduct = () => {
  console.log('Product updated:', product.value)
  console.log('Specifications:', specs.value)
  // TODO: Implement actual update logic
  alert('Product updated successfully!')
}

const discardForm = () => {
  if (confirm('Are you sure you want to discard all changes?')) {
    loadProduct() // Reload original data
  }
}

const resetForm = () => {
  product.value = {
    id: '',
    name: '',
    brand: '',
    category: '',
    quantity: '',
    highlight: '',
    description: '',
    quality: '',
    price: '',
    discountCode: '',
    discountStartDate: '',
    discountExpireDate: '',
    color: '',
  }
  specs.value = {
    range: '',
    hubMotor: '',
    payload: '',
    controller: '',
    weight: '',
    display: '',
  }
  isProductLoaded.value = false
}

// Watchers
watch(
  () => selectedProductId.value,
  (newId) => {
    if (!newId) {
      resetForm()
    }
  },
)

// Watch for route param changes
watch(
  () => props.id,
  (newId) => {
    if (newId && newId !== selectedProductId.value) {
      selectedProductId.value = newId
      loadProduct()
    }
  },
)

// Auto-load product on mount if ID is provided
onMounted(() => {
  if (props.id) {
    loadProduct()
  }

  // Check for query parameters from discount management page
  const {
    productName,
    brand,
    category,
    quantity,
    highlight,
    description,
    quality,
    price,
    color,
    discountCode,
    discountType,
    discountValue,
    discountStartDate,
    discountEndDate,
    range,
    hubMotor,
    payload,
    controller,
    weight,
    display,
  } = route.query

  if (productName) {
    product.value.name = productName
  }
  if (brand) {
    product.value.brand = brand
  }
  if (category) {
    product.value.category = category
  }
  if (quantity) {
    product.value.quantity = quantity
  }
  if (highlight) {
    product.value.highlight = highlight
  }
  if (description) {
    product.value.description = description
  }
  if (quality) {
    product.value.quality = quality
  }
  if (price) {
    product.value.price = price
  }
  if (color) {
    product.value.color = color
  }
  if (discountCode) {
    product.value.discountCode = discountCode
  }
  if (discountType && discountType !== 'N/A') {
    product.value.discountType = discountType
  }
  if (discountValue && discountValue !== 'N/A') {
    product.value.discountValue = discountValue
  }
  if (discountStartDate && discountStartDate !== 'N/A') {
    // Convert date from MM/DD/YYYY to YYYY-MM-DD format for HTML date input
    const convertedStartDate = convertDateFormat(discountStartDate)
    product.value.discountStartDate = convertedStartDate
  }
  if (discountEndDate && discountEndDate !== 'N/A') {
    // Convert date from MM/DD/YYYY to YYYY-MM-DD format for HTML date input
    const convertedEndDate = convertDateFormat(discountEndDate)
    product.value.discountExpireDate = convertedEndDate
  }

  // Handle specs information
  if (range) {
    specs.value.range = range
  }
  if (hubMotor) {
    specs.value.hubMotor = hubMotor
  }
  if (payload) {
    specs.value.payload = payload
  }
  if (controller) {
    specs.value.controller = controller
  }
  if (weight) {
    specs.value.weight = weight
  }
  if (display) {
    specs.value.display = display
  }
})
</script>

<style scoped>
/* Breadcrumb Navigation */
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

/* Product Selector */
.product-selector {
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
  padding: 20px;
  font-family: 'Poppins', sans-serif;
}

.selector-container {
  max-width: 500px;
}

.selector-label {
  display: block;
  margin-bottom: 8px;
  color: gray;
  font-size: 13px;
  font-weight: 400;
}

.input-group {
  display: flex;
  gap: 12px;
  align-items: center;
}

.product-input {
  flex: 1;
  padding: 10px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
  background-color: #ffffff;
  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease;
}

.product-input:focus {
  outline: none;
  border-color: #4299e1;
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

.product-input::placeholder {
  color: #a0aec0;
}

.load-btn {
  padding: 10px 16px;
  border: none;
  border-radius: 6px;
  background-color: #4299e1;
  color: white;
  font-size: 14px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  cursor: pointer;
  transition: opacity 0.2s ease;
}

.load-btn:hover:not(:disabled) {
  opacity: 0.9;
}

.load-btn:disabled {
  background-color: #cbd5e0;
  cursor: not-allowed;
  opacity: 0.6;
}

/* Form Layout */
.form-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin: 0 auto;
  width: auto;
  overflow-y: scroll;
  height: 68vh;
}

.form-column {
  display: flex;
  flex-direction: column;
}

.form-column-left {
  position: sticky;
  top: 20px;
  height: fit-content;
}

/* Empty State */
.no-selection-message {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
}

.message-container {
  text-align: center;
  color: #666;
}

.message-container h2 {
  font-size: 24px;
  font-weight: 500;
  margin-bottom: 10px;
  color: #1a202c;
  font-family: 'Poppins', sans-serif;
}

.message-container p {
  font-size: 16px;
  color: #718096;
  font-family: 'Poppins', sans-serif;
}

/* Responsive Design */
@media (max-width: 768px) {
  .form-container {
    grid-template-columns: 1fr;
  }

  .edit-product-page {
    padding: 15px;
  }

  .form-column-left {
    position: static;
  }
}
</style>
