<template>
  <div class="add-product-page">
    <nav class="breadcrumb">
      <router-link to="/admin/products/manage" class="breadcrumb-item">Product List</router-link>
      <span class="breadcrumb-separator">></span>
      <span class="breadcrumb-item active">{{
        route.query.restockMode
          ? 'Restock Product'
          : hasQueryParams
            ? 'Add Discount'
            : 'Add Product'
      }}</span>
    </nav>

    <div class="form-container">
      <div class="form-column">
        <ProductInfo
          :product="product"
          @update:product="product = $event"
          :prefilled-fields="prefilledFields"
          :restock-mode="!!route.query.restockMode"
          :stock-alert="route.query.stockAlert"
        />
        <ProductDescription :product="product" @update:product="product = $event" />
        <ProductQuality :product="product" @update:product="product = $event" />
        <ProductDiscount :product="product" @update:product="product = $event" />
      </div>

      <div>
        <div class="form-column">
          <ProductImage @update:images="productImages = $event" />
          <ProductPrice :product="product" @update:product="product = $event" />
          <ProductSpecs
            :specs="specs"
            @update:specs="specs = $event"
            @add-product="addProduct"
            @discard="discardForm"
            :prefilled-fields="prefilledFields"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import ProductInfo from '@/components/admin/products/ProductInfo.vue'
import ProductDescription from '@/components/admin/products/ProductDescription.vue'
import ProductQuality from '@/components/admin/products/ProductQuality.vue'
import ProductDiscount from '@/components/admin/products/ProductDiscount.vue'
import ProductImage from '@/components/admin/products/ProductImage.vue'
import ProductPrice from '@/components/admin/products/ProductPrice.vue'
import ProductSpecs from '@/components/admin/products/ProductSpecs.vue'
import { productsApi } from '@/services/api.js'

const route = useRoute()

// Product data
const product = ref({
  id: '',
  name: '',
  brand: '',
  category: '',
  quantity: '',
  description: '',
  quality: '',
  price: '',
  discountCode: '',
  discountType: '',
  discountValue: '',
  discountStartDate: '',
  discountExpireDate: '',
  color: '',
})

// Product specifications
const specs = ref({
  range: '',
  hubMotor: '',
  payload: '',
  controller: '',
  weight: '',
  display: '',
})

// Product images from ProductImage component
const productImages = ref([])

// Check if there are query parameters
const hasQueryParams = computed(() => {
  return (
    route.query.productName || route.query.brand || route.query.category || route.query.restockMode
  )
})

// Determine which fields are prefilled
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
  // For restocking, don't prefill quantity so user can modify it
  if (route.query.quantity && !route.query.restockMode) {
    fields.quantity = true
  }
  if (route.query.description) {
    fields.description = true
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
  if (route.query.discountCode) {
    fields.discountCode = true
  }
  if (route.query.discountType) {
    fields.discountType = true
  }
  if (route.query.discountValue) {
    fields.discountValue = true
  }
  if (route.query.discountStartDate) {
    fields.discountStartDate = true
  }
  if (route.query.discountEndDate) {
    fields.discountExpireDate = true
  }
  // Specs fields
  if (route.query.range) {
    fields.range = true
  }
  if (route.query.hubMotor) {
    fields.hubMotor = true
  }
  if (route.query.payload) {
    fields.payload = true
  }
  if (route.query.controller) {
    fields.controller = true
  }
  if (route.query.weight) {
    fields.weight = true
  }
  if (route.query.display) {
    fields.display = true
  }
  return fields
})

// Generate a random product ID
const generateProductId = () => {
  const randomNum = Math.floor(Math.random() * 10000)
    .toString()
    .padStart(4, '0')
  return `P${randomNum}I`
}

// Convert date format from MM/DD/YYYY to YYYY-MM-DD
const convertDateFormat = (dateString) => {
  if (!dateString || dateString === 'N/A') {
    return ''
  } else {
    const parts = dateString.split('/')
    if (parts.length === 3) {
      const [month, day, year] = parts
      return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
    } else {
      return dateString
    }
  }
}

// Add product to database
const addProduct = async () => {
  // Basic validation
  if (!product.value.name.trim()) {
    alert('Product name is required')
    return
  }
  if (!product.value.category) {
    alert('Product category is required')
    return
  }
  if (!product.value.price || parseFloat(product.value.price) <= 0) {
    alert('Product price is required and must be greater than 0')
    return
  }

  try {
    // Map category name to ID
    const categoryMap = {
      'Mountain Bike': 1,
      'Road Bike': 2,
    }

    // Handle images - use uploaded images if available, otherwise use default
    let mainImage = '/images/default-product.jpg'
    const additionalImages = []

    // Check if images were uploaded via ProductImage component
    if (productImages.value && productImages.value.length > 0) {
      // Use the first uploaded image as the main image
      mainImage = productImages.value[0]

      // Add additional images if more than one was uploaded
      if (productImages.value.length > 1) {
        for (let i = 1; i < productImages.value.length; i++) {
          additionalImages.push({
            url: productImages.value[i],
            alt: `${product.value.name} - View ${i}`,
          })
        }
      }
    }

    // Prepare product data for API
    const categoryId = categoryMap[product.value.category]
    if (!categoryId) {
      alert('Invalid category selected')
      return
    }

    const productData = {
      name: product.value.name,
      description: product.value.description,
      pricing: parseFloat(product.value.price) || 0,
      category_id: categoryId,
      brand: product.value.brand,
      color: product.value.color,
      badge: [], // Will be populated from discount data if applicable
      discount: [], // Will be populated from discount data
      specs: specs.value,
      additional_images: additionalImages,
      image: mainImage,
      quantity: parseInt(product.value.quantity) || 1,
    }

    // Handle discount data - use discount fields
    if (product.value.discountType && product.value.discountValue) {
      let discountTypeValue = 'fixed'
      if (product.value.discountType.toLowerCase().includes('percent')) {
        discountTypeValue = 'percentage'
      }

      const discountData = {
        type: discountTypeValue,
        value: parseFloat(product.value.discountValue) || 0,
        badge: product.value.discountCode || 'Sale', // Use discount code as badge text, fallback to 'Sale'
      }
      productData.discount = [discountData]

      // Add badge if there's a discount
      productData.badge = [product.value.discountCode || 'Sale']
    }

    // Create multiple products based on quantity
    const quantity = parseInt(product.value.quantity) || 1
    const createdProducts = []

    for (let i = 0; i < quantity; i++) {
      try {
        const response = await productsApi.createProduct(productData)
        createdProducts.push(response.data)
      } catch (productError) {
        console.error(`Error creating product ${i + 1}:`, productError)
        throw productError
      }
    }

    // Show success message
    alert(`${quantity} product${quantity > 1 ? 's' : ''} created successfully!`)

    // Reset form
    resetForm()

    // Optionally redirect to product list
    // router.push('/admin/products/manage')
  } catch (error) {
    console.error('Error creating product:', error)

    // Show error message
    if (error.response && error.response.data) {
      alert(`Error creating product: ${error.response.data.message || 'Unknown error'}`)
    } else {
      alert('Error creating product. Please check your connection and try again.')
    }
  }
}

// Discard form changes
const discardForm = () => {
  if (confirm('Are you sure you want to discard all changes?')) {
    resetForm()
  }
}

// Reset form to initial state
const resetForm = () => {
  product.value = {
    id: generateProductId(),
    name: '',
    brand: '',
    category: '',
    quantity: '',
    description: '',
    quality: '',
    price: '',
    discountCode: '',
    discountType: '',
    discountValue: '',
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
  productImages.value = [] // Reset uploaded images
}

// Initialize on mount
onMounted(() => {
  product.value.id = generateProductId()

  // Prefill form from query parameters
  const {
    productName,
    brand,
    category,
    discountCode,
    discountType,
    discountValue,
    discountStartDate,
    discountEndDate,
    currentStock,
    quantity,
    productId,
    restockMode,
    description,
    quality,
    price,
    color,
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
  if (currentStock) {
    product.value.quantity = currentStock
  }
  if (quantity) {
    product.value.quantity = quantity
  }
  if (productId && restockMode) {
    // For restocking, use the existing product ID
    product.value.id = productId
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

.form-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin: 0 auto;
  width: auto;
  overflow-y: scroll;
  height: 82vh;
}

.form-column {
  display: flex;
  flex-direction: column;
  position: sticky;
  top: 0;
}

@media (max-width: 768px) {
  .form-container {
    grid-template-columns: 1fr;
  }

  .add-product-page {
    padding: 15px;
  }
}
</style>
