<template>
  <nav class="breadcrumb">
    <router-link to="/admin/products/stock" class="breadcrumb-item">Stock Management</router-link>
    <span class="breadcrumb-separator">></span>
    <span class="breadcrumb-item active">Restock Product</span>
  </nav>

  <div v-if="loading" class="loading-state">
    <div class="loading-icon">⟳</div>
    <p>Loading product information...</p>
  </div>

  <div v-else-if="error" class="error-state">
    <div class="error-icon">⚠</div>
    <p>{{ error }}</p>
    <button @click="loadProduct" class="btn-retry">
      <Icon icon="mdi:refresh" />
      Retry
    </button>
  </div>

  <div v-else class="form-container">
    <div class="form-column form-left">
      <!-- Product Info - Read Only -->
      <div class="form-section">
        <h2 class="section-title">Product Information (Read Only)</h2>

        <div class="form-group">
          <label>Product Name</label>
          <input type="text" :value="product.name" readonly class="readonly-field" />
        </div>

        <div class="form-group">
          <label>Brand</label>
          <input type="text" :value="product.brand" readonly class="readonly-field" />
        </div>

        <div class="form-group">
          <label>Category</label>
          <input type="text" :value="product.category" readonly class="readonly-field" />
        </div>

        <div class="form-group">
          <label>Description</label>
          <textarea
            :value="product.description"
            readonly
            class="readonly-field textarea-field"
            rows="4"
          ></textarea>
        </div>

        <div class="form-group">
          <label>Quality</label>
          <input type="text" :value="product.quality" readonly class="readonly-field" />
        </div>
      </div>

      <!-- Quantity Section - Editable -->
      <div class="form-section">
        <h2 class="section-title">Restock Quantity</h2>

        <div class="form-group">
          <label>Current Stock</label>
          <input type="text" :value="product.currentStock" readonly class="readonly-field" />
        </div>

        <div class="form-group">
          <label>New Quantity *</label>
          <input
            type="number"
            v-model="product.quantity"
            class="editable-field"
            placeholder="Enter new quantity"
            min="1"
            required
          />
          <small class="field-hint">
            Current stock: {{ product.currentStock }} | Suggested: {{ suggestedQuantity }}
          </small>
        </div>
      </div>
    </div>

    <div>
      <div class="form-column">
        <!-- Product Image - Read Only -->
        <div class="form-section">
          <h2 class="section-title">Product Image (Read Only)</h2>
          <div class="image-preview">
            <img
              v-if="product.image"
              :src="product.image"
              alt="Product image"
              class="product-image"
            />
            <div v-else class="no-image">
              <Icon icon="mdi:image-off" />
              <p>No image available</p>
            </div>
          </div>
        </div>

        <!-- Price and Color - Read Only -->
        <div class="form-section">
          <h2 class="section-title">Price and Color (Read Only)</h2>

          <div class="form-group">
            <label>Price</label>
            <input type="text" :value="product.price" readonly class="readonly-field" />
          </div>

          <div class="form-group">
            <label>Color</label>
            <input type="text" :value="product.color" readonly class="readonly-field" />
          </div>
        </div>

        <!-- Specifications - Read Only -->
        <div class="form-section">
          <h2 class="section-title">Specifications (Read Only)</h2>

          <div class="form-group">
            <label>Range</label>
            <input type="text" :value="specs.range" readonly class="readonly-field" />
          </div>

          <div class="form-group">
            <label>Hub Motor</label>
            <input type="text" :value="specs.hubMotor" readonly class="readonly-field" />
          </div>

          <div class="form-group">
            <label>Total Payload Capacity</label>
            <input type="text" :value="specs.payload" readonly class="readonly-field" />
          </div>

          <div class="form-group">
            <label>Controller</label>
            <input type="text" :value="specs.controller" readonly class="readonly-field" />
          </div>

          <div class="form-group">
            <label>Weight</label>
            <input type="text" :value="specs.weight" readonly class="readonly-field" />
          </div>

          <div class="form-group">
            <label>Display</label>
            <input type="text" :value="specs.display" readonly class="readonly-field" />
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="form-section">
          <div class="action-buttons">
            <button @click="updateStock" class="btn-primary" :disabled="updating">
              <Icon icon="mdi:package-variant-plus" />
              {{ updating ? 'Updating Stock...' : 'Update Stock' }}
            </button>
            <button @click="cancel" class="btn-secondary">
              <Icon icon="mdi:cancel" />
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Icon } from '@iconify/vue'
import { productsApi } from '@/services/api.js'

const route = useRoute()
const router = useRouter()

// Reactive data
const product = ref({
  id: '',
  name: '',
  brand: '',
  category: '',
  quantity: '',
  currentStock: '',
  description: '',
  quality: '',
  price: '',
  color: '',
  image: '',
})

const specs = ref({
  range: '',
  hubMotor: '',
  payload: '',
  controller: '',
  weight: '',
  display: '',
})

const loading = ref(false)
const error = ref('')
const updating = ref(false)

// Computed property for suggested quantity
const suggestedQuantity = computed(() => {
  const current = parseInt(product.value.currentStock) || 0
  return current + Math.max(5, Math.ceil(current * 0.2))
})

// Load product data
const loadProduct = async () => {
  const productId = route.params.id

  if (!productId) {
    error.value = 'Product ID is required'
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
      quantity: (parseInt(response.data.quantity) || 0).toString(),
      currentStock: (parseInt(response.data.quantity) || 0).toString(),
      description: response.data.description || '',
      quality: response.data.quality || '',
      price: response.data.price?.toString() || '',
      color: response.data.color || '',
      image: response.data.image || '',
    }

    // Load specs if available
    specs.value = {
      range: response.data.specs?.range || '',
      hubMotor: response.data.specs?.hubMotor || '',
      payload: response.data.specs?.payload || '',
      controller: response.data.specs?.controller || '',
      weight: response.data.specs?.weight || '',
      display: response.data.specs?.display || '',
    }
  } catch (err) {
    const statusCode = err.response?.status
    if (statusCode === 404) {
      error.value = `Product with ID "${productId}" not found.`
    } else {
      error.value = `Failed to load product: ${err.response?.data?.message || err.message}`
    }
    console.error('Error loading product:', err)
  } finally {
    loading.value = false
  }
}

// Update stock quantity
const updateStock = async () => {
  // Validation
  const newQuantity = parseInt(product.value.quantity)
  if (!newQuantity || newQuantity < 1) {
    alert('Please enter a valid quantity (minimum 1)')
    return
  }

  if (newQuantity < parseInt(product.value.currentStock)) {
    if (!confirm('You are reducing the stock quantity. Are you sure?')) {
      return
    }
  }

  try {
    updating.value = true

    // Update product with new quantity
    const updateData = {
      quantity: newQuantity,
    }

    await productsApi.updateProduct(product.value.id, updateData)

    alert('Stock updated successfully!')
    router.push('/admin/products/stock')
  } catch (err) {
    error.value = `Failed to update stock: ${err.response?.data?.message || err.message}`
    console.error('Error updating stock:', err)
  } finally {
    updating.value = false
  }
}

// Cancel and go back
const cancel = () => {
  if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
    router.push('/admin/products/stock')
  }
}

// Load product on mount
onMounted(() => {
  loadProduct()
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
  background: white;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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

.form-left {
  .form-column-left {
    display: flex;
    flex-direction: column;
  }
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

.form-section {
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
  font-family: 'Poppins', sans-serif;
  padding: 20px;
}

.section-title {
  font-size: 18px;
  font-weight: 500;
  margin-bottom: 20px;
  color: #1a202c;
  padding-bottom: 10px;
  border-bottom: 2px solid #e2e8f0;
}

.form-group {
  margin-bottom: 16px;
}

label {
  display: block;
  margin-bottom: 8px;
  color: gray;
  font-size: 13px;
  font-weight: 400;
}

.readonly-field {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
  background-color: #f7fafc;
  color: #a0aec0;
  max-width: -webkit-fill-available;
  cursor: not-allowed;
  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease;
}

.readonly-field:focus {
  outline: none;
  border-color: #4299e1;
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

.editable-field {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
  background-color: #ffffff;
  color: #2d3748;
  max-width: -webkit-fill-available;
  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease;
}

.editable-field:focus {
  outline: none;
  border-color: #4299e1;
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

.textarea-field {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
  background-color: #f7fafc;
  color: #a0aec0;
  max-width: -webkit-fill-available;
  cursor: not-allowed;
  resize: vertical;
  min-height: 80px;
  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease;
}

.textarea-field:focus {
  outline: none;
  border-color: #4299e1;
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

.field-hint {
  display: block;
  margin-top: 4px;
  color: #718096;
  font-size: 12px;
  font-style: italic;
}

.image-preview {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 200px;
  border: 2px dashed #e2e8f0;
  border-radius: 8px;
  background-color: #f8fafc;
}

.product-image {
  max-width: 100%;
  max-height: 200px;
  object-fit: contain;
  border-radius: 4px;
}

.no-image {
  text-align: center;
  color: #a0aec0;
}

.no-image svg {
  font-size: 48px;
  margin-bottom: 10px;
}

.action-buttons {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  margin-top: 20px;
}

.btn-primary {
  padding: 12px 24px;
  background-color: #48bb78;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s ease;
}

.btn-primary:hover:not(:disabled) {
  background-color: #38a169;
  transform: translateY(-1px);
}

.btn-primary:disabled {
  background-color: #a0aec0;
  cursor: not-allowed;
  transform: none;
}

.btn-secondary {
  padding: 12px 24px;
  background-color: #e2e8f0;
  color: #4a5568;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s ease;
}

.btn-secondary:hover {
  background-color: #cbd5e0;
  transform: translateY(-1px);
}

@media (max-width: 768px) {
  .form-container {
    grid-template-columns: 1fr;
  }

  .action-buttons {
    flex-direction: column;
  }

  .btn-primary,
  .btn-secondary {
    width: 100%;
    justify-content: center;
  }
}
</style>
