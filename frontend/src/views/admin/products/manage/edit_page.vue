<template>
  <div class="edit-product-page">
    <nav class="breadcrumb">
      <router-link to="/admin/products/manage" class="breadcrumb-item">Product List</router-link>
      <span class="breadcrumb-separator">></span>
      <router-link to="/admin/products/add" class="breadcrumb-item">Add Product</router-link>
      <span class="breadcrumb-separator">></span>
      <span class="breadcrumb-item active">{{
        hasQueryParams ? 'Edit Discount' : 'Edit Product'
      }}</span>
    </nav>

    <div class="product-selector">
      <div class="selector-container">
        <label for="productInput" class="selector-label">Enter Product ID to Edit:</label>
        <div class="input-group">
          <input
            id="productInput"
            v-model="selectedProductId"
            @blur="loadProduct"
            @keyup.enter="loadProduct"
            placeholder="e.g. P0000I or 1"
            class="product-input"
            :disabled="loading"
          />
          <button
            @click="loadProduct"
            class="load-btn"
            :disabled="!selectedProductId.trim() || loading"
          >
            <span v-if="loading">Loading...</span>
            <span v-else>Load Product</span>
          </button>
        </div>
        <div v-if="error && !loading" class="error-message">
          <Icon icon="mdi:alert-circle" />
          {{ error }}
        </div>
      </div>
    </div>

    <div v-if="selectedProductId" class="form-container">
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

      <div>
        <div class="form-column">
          <ProductImage v-model="productImages" />
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
    </div>

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
import { useRoute, useRouter } from 'vue-router'
import { productsApi, categoriesApi } from '@/services/api.js'
import ProductInfo from '@/components/admin/products/ProductInfo.vue'
import ProductDescription from '@/components/admin/products/ProductDescription.vue'
import ProductQuality from '@/components/admin/products/ProductQuality.vue'
import ProductDiscount from '@/components/admin/products/ProductDiscount.vue'
import ProductImage from '@/components/admin/products/ProductImage.vue'
import ProductPrice from '@/components/admin/products/ProductPrice.vue'
import ProductSpecs from '@/components/admin/products/ProductSpecs.vue'

const route = useRoute()
const router = useRouter()

const props = defineProps({
  id: {
    type: String,
    default: '',
  },
})

// Reactive data
const selectedProductId = ref('')
const isProductLoaded = ref(false)
const loading = ref(false)
const updating = ref(false)
const error = ref('')
const categories = ref([])

// Form data
const product = ref({
  id: '',
  name: '',
  brand: '',
  category: '',
  category_id: '',
  quantity: '',
  highlight: '',
  description: '',
  quality: '',
  price: '',
  discountCode: '',
  discountStartDate: '',
  discountExpireDate: '',
  color: '',
  image: '',
  additional_images: [],
})

// Combined images for ProductImage component (main + additional)
const productImages = computed({
  get: () => {
    const images = []
    if (product.value.image) {
      images.push(product.value.image)
    }
    if (product.value.additional_images && product.value.additional_images.length > 0) {
      images.push(...product.value.additional_images)
    }
    return images
  },
  set: (value) => {
    if (value && value.length > 0) {
      product.value.image = value[0]
      product.value.additional_images = value.slice(1)
    } else {
      product.value.image = ''
      product.value.additional_images = []
    }
  },
})

const specs = ref({
  range: '',
  hubMotor: '',
  payload: '',
  controller: '',
  weight: '',
  display: '',
})

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
  if (route.query.productName) {
    fields.name = true
  }
  if (route.query.brand) {
    fields.brand = true
  }
  if (route.query.category) {
    fields.category = true
  }
  if (route.query.quantity) {
    fields.quantity = true
  }
  if (route.query.quality) {
    fields.quality = true
  }
  if (route.query.price) {
    fields.price = true
  }
  if (route.query.color) {
    fields.color = true
  }
  return fields
})

// Load categories for dropdown
const loadCategories = async () => {
  try {
    const response = await categoriesApi.getCategories()
    categories.value = response.data
  } catch (err) {
    console.error('Error loading categories:', err)
  }
}

// Load product data from API
const loadProduct = async () => {
  const productId = selectedProductId.value.trim()

  if (!productId) {
    resetForm()
    return
  }

  // Basic validation - product ID should be numeric or in format P0000I
  const isValidId = /^\d+$/.test(productId) || /^P\d+I$/.test(productId)
  if (!isValidId) {
    error.value =
      'Invalid product ID format. Please enter a numeric ID (e.g., 1) or ID in format P0000I (e.g., P0001I)'
    resetForm()
    return
  }

  try {
    loading.value = true
    error.value = ''

    const response = await productsApi.getProduct(productId)

    // Transform API data to match form structure
    product.value = {
      id: response.data.id,
      name: response.data.title || '',
      brand: response.data.brand || '',
      category: response.data.category?.name || '',
      category_id: response.data.category?.id || '',
      quantity: response.data.quantity?.toString() || '',
      highlight: '', // Not in API
      description: response.data.description || '',
      quality: response.data.quality || '',
      price: response.data.price?.toString() || '',
      discountCode: '', // Will be set from discount data below
      discountType: '', // Will be set from discount data below
      discountValue: '', // Will be set from discount data below
      discountStartDate: '', // Will be set from discount data below
      discountExpireDate: '', // Will be set from discount data below
      color: response.data.color || '',
      image: response.data.image || '',
      additional_images: Array.isArray(response.data.additionalImages)
        ? response.data.additionalImages
            .map((img) => (typeof img === 'string' ? img : img.url))
            .filter((url) => url)
        : [],
    }

    // Load discount data if available
    if (
      response.data.discount &&
      Array.isArray(response.data.discount) &&
      response.data.discount.length > 0
    ) {
      const discountData = response.data.discount[0] // Take first discount

      // Set discount code (could be in 'code' or 'badge' field)
      product.value.discountCode = discountData.code || discountData.badge || ''

      // Set discount type
      product.value.discountType = discountData.type === 'percent' ? 'Percentage' : 'Fixed Amount'

      // Set discount value
      product.value.discountValue = discountData.value?.toString() || ''

      // Set discount dates
      if (discountData.start_date) {
        product.value.discountStartDate = discountData.start_date
      }
      if (discountData.expire_date) {
        product.value.discountExpireDate = discountData.expire_date
      }
    }

    // Load specs if available (assuming specs are stored in the database)
    specs.value = {
      range: response.data.specs?.range || '',
      hubMotor: response.data.specs?.hubMotor || '',
      payload: response.data.specs?.payload || '',
      controller: response.data.specs?.controller || '',
      weight: response.data.specs?.weight || '',
      display: response.data.specs?.display || '',
    }

    isProductLoaded.value = true
  } catch (err) {
    const statusCode = err.response?.status
    if (statusCode === 404) {
      error.value = `Product with ID "${productId}" not found. Please check the ID and try again.`
    } else if (statusCode === 500) {
      error.value = 'Server error occurred. Please try again later.'
    } else {
      error.value = `Failed to load product: ${err.response?.data?.message || err.message}`
    }
    console.error('Error loading product:', err)
    resetForm()
  } finally {
    loading.value = false
  }
}

// Update product via API
const updateProduct = async () => {
  try {
    updating.value = true
    error.value = ''

    // Prepare data for API
    const updateData = {
      name: product.value.name,
      description: product.value.description,
      pricing: parseFloat(product.value.price) || 0,
      category_id: product.value.category_id,
      brand: product.value.brand,
      color: product.value.color,
      quality: product.value.quality,
      quantity: parseInt(product.value.quantity) || 0,
      image: product.value.image,
      additional_images: product.value.additional_images,
      specs: specs.value,
    }

    // Handle discount data - use discount fields
    if (product.value.discountType && product.value.discountValue) {
      let discountTypeValue = 'fixed'
      if (product.value.discountType.toLowerCase().includes('percent')) {
        discountTypeValue = 'percent'
      }

      const discountData = {
        type: discountTypeValue,
        value: parseFloat(product.value.discountValue) || 0,
        badge: product.value.discountCode || 'Sale', // Use discount code as badge text, fallback to 'Sale'
      }

      // Add start and expire dates if provided
      if (product.value.discountStartDate) {
        discountData.start_date = product.value.discountStartDate
      }
      if (product.value.discountExpireDate) {
        discountData.expire_date = product.value.discountExpireDate
      }

      updateData.discount = [discountData]

      // Add badge if there's a discount
      updateData.badge = [product.value.discountCode || 'Sale']
    } else {
      // No discount provided, set to null
      updateData.discount = null
      updateData.badge = null
    }

    await productsApi.updateProduct(product.value.id, updateData)

    alert('Product updated successfully!')
    router.push('/admin/products')
  } catch (err) {
    error.value = `Failed to update product: ${err.response?.data?.message || err.message}`
    console.error('Error updating product:', err)
  } finally {
    updating.value = false
  }
}

const discardForm = () => {
  if (confirm('Are you sure you want to discard all changes?')) {
    loadProduct()
  }
}

const resetForm = () => {
  product.value = {
    id: '',
    name: '',
    brand: '',
    category: '',
    category_id: '',
    quantity: '',
    highlight: '',
    description: '',
    quality: '',
    price: '',
    discountCode: '',
    discountStartDate: '',
    discountExpireDate: '',
    color: '',
    image: '',
    additional_images: [],
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
  error.value = ''
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

watch(
  () => props.id,
  (newId) => {
    if (newId && newId !== selectedProductId.value) {
      selectedProductId.value = newId
      loadProduct()
    }
  },
)

// Lifecycle
onMounted(async () => {
  // Load categories for dropdowns
  await loadCategories()

  if (props.id) {
    selectedProductId.value = props.id
    loadProduct()
  }

  // Prefill form from query parameters (for advanced use cases)
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
    product.value.category_id = category
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
    product.value.discountStartDate = discountStartDate
  }
  if (discountEndDate && discountEndDate !== 'N/A') {
    product.value.discountExpireDate = discountEndDate
  }

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

.error-message {
  color: #e53e3e;
  font-size: 14px;
  margin-top: 8px;
  font-weight: 500;
  display: flex;
  align-items: center;
  gap: 6px;
}

.form-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin: 0 auto;
  width: auto;
  overflow-y: scroll;
  height: 72vh;
}

.form-column {
  display: flex;
  flex-direction: column;
  position: sticky;
  top: 0;
}

.form-column-left {
  display: flex;
  flex-direction: column;
}

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
