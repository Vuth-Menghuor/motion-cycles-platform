<template>
  <div class="add-product-page">
    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb">
      <router-link to="/admin/products/manage" class="breadcrumb-item">Product List</router-link>
      <span class="breadcrumb-separator">></span>
      <span class="breadcrumb-item active">Add Product</span>
      <span class="breadcrumb-separator">></span>
      <router-link to="/admin/products/edit/1" class="breadcrumb-item">Edit Product</router-link>
    </nav>

    <div class="form-container">
      <!-- Left Column -->
      <div class="form-column">
        <ProductInfo :product="product" @update:product="product = $event" />
        <ProductDescription :product="product" @update:product="product = $event" />
        <ProductQuality :product="product" @update:product="product = $event" />
      </div>

      <!-- Right Column -->
      <div class="form-column">
        <ProductImage />
        <ProductPrice :product="product" @update:product="product = $event" />
        <ProductSpecs
          :specs="specs"
          @update:specs="specs = $event"
          @add-product="addProduct"
          @discard="discardForm"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import ProductInfo from '@/components/admin/products/ProductInfo.vue'
import ProductDescription from '@/components/admin/products/ProductDescription.vue'
import ProductQuality from '@/components/admin/products/ProductQuality.vue'
import ProductImage from '@/components/admin/products/ProductImage.vue'
import ProductPrice from '@/components/admin/products/ProductPrice.vue'
import ProductSpecs from '@/components/admin/products/ProductSpecs.vue'

// Reactive data
const product = ref({
  id: '',
  name: '',
  brand: '',
  category: '',
  highlight: '',
  description: '',
  quality: '',
  price: '',
  discountCode: '',
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

// Methods
const generateProductId = () => {
  const randomNum = Math.floor(Math.random() * 10000)
    .toString()
    .padStart(4, '0')
  return `P${randomNum}I`
}

const addProduct = () => {
  console.log('Product added:', product.value)
  console.log('Specifications:', specs.value)
  // TODO: Implement actual save logic
}

const discardForm = () => {
  if (confirm('Are you sure you want to discard all changes?')) {
    resetForm()
  }
}

const resetForm = () => {
  product.value = {
    id: generateProductId(),
    name: '',
    brand: '',
    category: '',
    highlight: '',
    description: '',
    quality: '',
    price: '',
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
}

// Initialize with random product ID
onMounted(() => {
  product.value.id = generateProductId()
})
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

.form-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin: 0 auto;
  width: auto;
  overflow-y: scroll;
  height: 80vh;
}

.form-column {
  display: flex;
  flex-direction: column;
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
