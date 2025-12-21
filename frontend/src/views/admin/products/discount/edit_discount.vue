<template>
  <div class="product-page">
    <nav class="breadcrumb">
      <router-link to="/admin/products/discount" class="breadcrumb-item"
        >Promotional Codes</router-link
      >
      <span class="breadcrumb-separator">></span>
      <span class="breadcrumb-item active">Edit Code</span>
    </nav>

    <div class="form-container">
      <!-- Loading State -->
      <div v-if="loading" class="loading-state">
        <Icon icon="mdi:loading" class="loading-icon" />
        <p>Loading discount details...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="error-state">
        <Icon icon="mdi:alert-circle" class="error-icon" />
        <p>{{ error }}</p>
        <router-link to="/admin/products/discount" class="btn btn-primary">
          <Icon icon="mdi:arrow-left" />
          Back to List
        </router-link>
      </div>

      <!-- Not Found -->
      <div v-else-if="!discount" class="error-state">
        <Icon icon="mdi:alert-circle" class="error-icon" />
        <p>The discount code you're looking for doesn't exist or has been deleted.</p>
        <router-link to="/admin/products/discount" class="btn btn-primary">
          <Icon icon="mdi:arrow-left" />
          Back to List
        </router-link>
      </div>

      <!-- Form -->
      <template v-else>
        <form v-if="!saving" @submit.prevent="saveDiscount" class="product-form">
          <div class="form-section">
            <h3 class="section-title">Discount Details</h3>

            <div class="form-row">
              <div class="form-group">
                <label>Discount Code *</label>
                <input
                  type="text"
                  v-model="form.code"
                  placeholder="e.g., WELCOME10"
                  required
                  :disabled="true"
                  class="form-input"
                />
                <small class="field-note">Code cannot be changed after creation</small>
              </div>
              <div class="form-group">
                <label>Discount Type *</label>
                <select v-model="form.type" required class="form-select">
                  <option value="">Select Type</option>
                  <option value="percentage">Percentage</option>
                  <option value="fixed">Fixed Amount</option>
                </select>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label>Discount Value *</label>
                <input
                  type="number"
                  v-model.number="form.value"
                  :placeholder="form.type === 'percentage' ? 'e.g., 10' : 'e.g., 50'"
                  :min="0"
                  :max="form.type === 'percentage' ? 100 : undefined"
                  step="0.01"
                  required
                  class="form-input"
                />
              </div>
            </div>
          </div>

          <div class="form-section">
            <h3 class="section-title">Validity Period</h3>

            <div class="form-row">
              <div class="form-group">
                <label>Start Date *</label>
                <input
                  type="date"
                  v-model="form.start_date"
                  :min="today"
                  required
                  class="form-input"
                />
              </div>
              <div class="form-group">
                <label>Expire Date *</label>
                <input
                  type="date"
                  v-model="form.expire_date"
                  :min="form.start_date || today"
                  required
                  class="form-input"
                />
              </div>
            </div>
          </div>

          <div class="form-actions">
            <router-link to="/admin/products/discount" class="btn btn-secondary"
              >Cancel</router-link
            >
            <button type="submit" class="btn btn-primary">
              <Icon icon="mdi:content-save" />
              Update Discount
            </button>
          </div>
        </form>

        <!-- Saving State -->
        <div v-else class="loading-state">
          <Icon icon="mdi:loading" class="loading-icon" />
          <p>Updating discount code...</p>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Icon } from '@iconify/vue'
import { discountsApi } from '@/services/api.js'

const route = useRoute()
const router = useRouter()

const discount = ref(null)
const loading = ref(false)
const error = ref(null)
const saving = ref(false)

const form = ref({
  code: '',
  type: '',
  value: '',
  start_date: '',
  expire_date: '',
  is_active: true,
})

const today = computed(() => {
  const date = new Date()
  return date.toISOString().split('T')[0]
})

const discountId = computed(() => route.params.id)

// Load discount data
const loadDiscount = async () => {
  try {
    loading.value = true
    error.value = null

    const response = await discountsApi.getDiscount(discountId.value)
    if (response.data.success) {
      discount.value = response.data.discount || response.data.data
      // Populate form
      form.value = {
        code: discount.value.code,
        type: discount.value.type,
        value: discount.value.value,
        start_date: discount.value.start_date,
        expire_date: discount.value.expire_date,
        is_active: discount.value.is_active,
      }
    } else {
      error.value = response.data.message || 'Failed to load discount'
    }
  } catch (err) {
    console.error('Error loading discount from API:', err)
    error.value = err.response?.data?.message || 'Failed to load discount'
  } finally {
    loading.value = false
  }
}

// Save discount
const saveDiscount = async () => {
  try {
    saving.value = true

    const discountData = {
      ...form.value,
    }

    await discountsApi.updateDiscount(discountId.value, discountData)

    alert('Discount code updated successfully!')
    router.push('/admin/products/discount')
  } catch (err) {
    const errorMessage = err.response?.data?.message || 'Failed to update discount code'
    alert(errorMessage)
    console.error('Error updating discount:', err)
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  loadDiscount()
})
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

.form-container {
  margin: 0 auto;
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

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.product-form {
  font-family: 'Poppins', sans-serif;
}

.form-section {
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 24px;
  padding: 24px;
}

.section-title {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 20px;
  color: #1a202c;
  border-bottom: 2px solid #e2e8f0;
  padding-bottom: 8px;
}

.form-row {
  display: flex;
  gap: 16px;
  margin-bottom: 16px;
}

.form-group {
  flex: 1;
  display: flex;
  flex-direction: column;
}

.form-group label {
  margin-bottom: 8px;
  color: #4a5568;
  font-size: 14px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
}

.form-input,
.form-select {
  padding: 10px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
  transition: border-color 0.2s ease;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #4299e1;
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

.form-input:disabled {
  background-color: #f7fafc;
  color: #a0aec0;
  cursor: not-allowed;
}

.form-select {
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 12px center;
  background-repeat: no-repeat;
  background-size: 16px;
  padding-right: 40px;
}

.field-note {
  font-size: 12px;
  color: #a0aec0;
  margin-top: 4px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 32px;
  padding-top: 24px;
  border-top: 1px solid #e2e8f0;
}

.btn-secondary {
  background-color: #e2e8f0;
  color: #4a5568;
  border: none;
}

.btn-secondary:hover {
  background-color: #cbd5e0;
  transform: translateY(-1px);
}

.btn-primary {
  background-color: #48bb78;
  color: white;
  border: none;
}

.btn-primary:hover {
  background-color: #38a169;
  transform: translateY(-1px);
}

@media (max-width: 768px) {
  .form-container {
    padding: 24px;
  }

  .form-row {
    flex-direction: column;
    gap: 12px;
  }

  .form-section {
    padding: 20px;
  }

  .form-actions {
    flex-direction: column;
    gap: 12px;
  }

  .btn {
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .form-container {
    padding: 16px;
  }

  .form-section {
    padding: 16px;
  }
}
</style>
