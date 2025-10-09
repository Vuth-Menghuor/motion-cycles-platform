<template>
  <div class="admin-customers-container">
    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb">
      <router-link to="/admin/customers/list" class="breadcrumb-item">List Customers</router-link>
      <span class="breadcrumb-separator">></span>
      <router-link :to="`/admin/customers/view/${customer?.id}`" class="breadcrumb-item"
        >View Customer</router-link
      >
      <span class="breadcrumb-separator">></span>
      <span class="breadcrumb-item active">Edit Customer</span>
    </nav>

    <div class="customer-edit-content">
      <!-- Customer Header -->
      <div class="customer-header">
        <div class="customer-info">
          <h2><Icon icon="mdi:account-edit" /> Edit Customer</h2>
          <div class="customer-meta">
            <span class="customer-id">ID: {{ customer?.customer_id }}</span>
            <span class="customer-name">{{ customer?.name }}</span>
          </div>
        </div>
        <div class="customer-actions">
          <router-link :to="`/admin/customers/view/${customer?.id}`" class="btn btn-secondary">
            <Icon icon="mdi:arrow-left" />
            Back to View
          </router-link>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="!customer" class="loading-section">
        <div class="loading-card">
          <Icon icon="mdi:loading" class="loading-icon" />
          <h3>Loading customer details...</h3>
        </div>
      </div>

      <!-- Customer Not Found -->
      <div v-else-if="!customer.id" class="error-section">
        <div class="error-card">
          <Icon icon="mdi:alert-circle" class="error-icon" />
          <h3>Customer Not Found</h3>
          <p>The customer you're looking for doesn't exist or has been deleted.</p>
          <router-link to="/admin/customers/list" class="btn btn-primary">
            <Icon icon="mdi:arrow-left" />
            Back to Customers List
          </router-link>
        </div>
      </div>

      <!-- Edit Form -->
      <template v-else>
        <form @submit.prevent="saveCustomer" class="edit-form">
          <!-- Basic Information Section -->
          <div class="form-section">
            <div class="section-content">
              <div class="form-grid">
                <div class="form-group">
                  <label for="customer_id" class="form-label">Customer ID</label>
                  <input
                    id="customer_id"
                    v-model="form.customer_id"
                    type="text"
                    class="form-input"
                    readonly
                    disabled
                  />
                  <small class="form-help">ID cannot be changed</small>
                </div>

                <div class="form-group">
                  <label for="name" class="form-label">Full Name *</label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="form-input"
                    :class="{ 'is-invalid': errors.name }"
                    placeholder="Enter customer's full name"
                    required
                  />
                  <div v-if="errors.name" class="invalid-feedback">{{ errors.name }}</div>
                </div>

                <div class="form-group">
                  <label for="email" class="form-label">Email Address *</label>
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="form-input"
                    :class="{ 'is-invalid': errors.email }"
                    placeholder="Enter customer's email address"
                    required
                  />
                  <div v-if="errors.email" class="invalid-feedback">{{ errors.email }}</div>
                </div>

                <div class="form-group">
                  <label for="phone" class="form-label">Phone Number</label>
                  <input
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    class="form-input"
                    :class="{ 'is-invalid': errors.phone }"
                    placeholder="Enter customer's phone number"
                  />
                  <div v-if="errors.phone" class="invalid-feedback">{{ errors.phone }}</div>
                </div>

                <div class="form-group">
                  <label for="status" class="form-label">Account Status *</label>
                  <select
                    id="status"
                    v-model="form.status"
                    class="form-select"
                    :class="{ 'is-invalid': errors.status }"
                    required
                  >
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                  </select>
                  <div v-if="errors.status" class="invalid-feedback">{{ errors.status }}</div>
                </div>

                <div class="form-group">
                  <label for="password" class="form-label">New Password</label>
                  <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="form-input"
                    :class="{ 'is-invalid': errors.password }"
                    placeholder="Leave empty to keep current password"
                  />
                  <small class="form-help"
                    >Minimum 8 characters with uppercase, lowercase, and number</small
                  >
                  <div v-if="errors.password" class="invalid-feedback">{{ errors.password }}</div>
                </div>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="form-actions">
            <button type="button" @click="cancelEdit" class="btn btn-secondary">
              <Icon icon="mdi:close" />
              Cancel
            </button>
            <button type="submit" :disabled="isSubmitting" class="btn btn-primary">
              <Icon icon="mdi:content-save" />
              Save Changes
            </button>
          </div>
        </form>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { Icon } from '@iconify/vue'
import { useRouter } from 'vue-router'

// ===== PROPS =====
const props = defineProps({
  id: {
    type: [String, Number],
    required: true,
  },
})

// ===== COMPOSABLES =====
const router = useRouter()

// ===== REACTIVE DATA =====
const customer = ref(null)
const isSubmitting = ref(false)
const errors = reactive({})

// ===== FORM DATA =====
const form = reactive({
  customer_id: '',
  name: '',
  email: '',
  phone: '',
  status: 'active',
  password: '',
})

// ===== MOCK DATA =====
// TODO: Replace with actual API calls
const mockCustomers = [
  {
    id: 1,
    customer_id: 'CUST-001',
    name: 'John Doe',
    email: 'john.doe@example.com',
    phone: '+1-555-0123',
    registration_date: '2024-01-15T10:30:00Z',
    status: 'active',
  },
  {
    id: 2,
    customer_id: 'CUST-002',
    name: 'Jane Smith',
    email: 'jane.smith@example.com',
    phone: '+1-555-0124',
    registration_date: '2024-01-14T14:20:00Z',
    status: 'active',
  },
]

// ===== METHODS =====
/**
 * Load customer data by ID
 */
const loadCustomer = () => {
  const customerId = parseInt(props.id)
  const foundCustomer = mockCustomers.find((c) => c.id === customerId)

  if (foundCustomer) {
    customer.value = { ...foundCustomer }
    // Populate form (password intentionally left empty for security)
    Object.assign(form, {
      customer_id: foundCustomer.customer_id,
      name: foundCustomer.name,
      email: foundCustomer.email,
      phone: foundCustomer.phone,
      status: foundCustomer.status,
      password: '', // Don't populate password for security
    })
  } else {
    customer.value = null
    console.error('Customer not found')
  }
}

/**
 * Validate form data
 */
const validateForm = () => {
  // Clear previous errors
  Object.keys(errors).forEach((key) => (errors[key] = ''))
  let isValid = true

  // Required field validations
  if (!form.name?.trim()) {
    errors.name = 'Name is required'
    isValid = false
  } else if (form.name.trim().length < 2) {
    errors.name = 'Name must be at least 2 characters'
    isValid = false
  }

  if (!form.email?.trim()) {
    errors.email = 'Email is required'
    isValid = false
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email)) {
    errors.email = 'Please enter a valid email address'
    isValid = false
  }

  if (!form.status) {
    errors.status = 'Status is required'
    isValid = false
  }

  // Optional field validations
  if (form.phone?.trim() && !/^[\+]?[1-9][\d]{0,15}$/.test(form.phone.replace(/[\s\-\(\)]/g, ''))) {
    errors.phone = 'Please enter a valid phone number'
    isValid = false
  }

  // Password validation (optional, but must be strong if provided)
  if (form.password?.trim()) {
    if (form.password.length < 8) {
      errors.password = 'Password must be at least 8 characters long'
      isValid = false
    } else if (!/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/.test(form.password)) {
      errors.password = 'Password must contain uppercase, lowercase, and number'
      isValid = false
    }
  }

  return isValid
}

/**
 * Save customer data
 */
const saveCustomer = async () => {
  if (!validateForm()) return

  isSubmitting.value = true

  try {
    // Simulate API call - Replace with actual API call
    await new Promise((resolve) => setTimeout(resolve, 1000))

    // TODO: Implement actual API call to update customer
    // const response = await api.updateCustomer(customer.value.id, form)

    alert('Customer updated successfully!')
    router.push(`/admin/customers/view/${customer.value.id}`)
  } catch (error) {
    console.error('Error saving customer:', error)
    alert('Failed to save customer. Please try again.')
  } finally {
    isSubmitting.value = false
  }
}

/**
 * Handle cancel action with confirmation
 */
const cancelEdit = () => {
  if (confirm('Are you sure you want to cancel? Any unsaved changes will be lost.')) {
    router.push(`/admin/customers/view/${customer.value.id}`)
  }
}

// ===== LIFECYCLE =====
onMounted(() => {
  loadCustomer()
})
</script>

<style scoped>
/* ===== LAYOUT ===== */
.admin-customers-container {
  margin: 0 auto;
  font-family: 'Poppins', sans-serif;
  color: #333;
  background: #f8fafc;
  min-height: 100vh;
}

.customer-edit-content {
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: white;
}

/* ===== BREADCRUMB ===== */
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

/* ===== HEADER ===== */
.customer-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px;
  border-bottom: 1px solid #e9ecef;
  background: #f8f9fa;
}

.customer-info h2 {
  margin: 0 0 8px 0;
  font-size: 24px;
  font-weight: 600;
  color: #333;
  display: flex;
  align-items: center;
  gap: 10px;
}

.customer-meta {
  display: flex;
  gap: 16px;
  align-items: center;
}

.customer-id {
  color: #666;
  font-size: 14px;
  font-family: 'Monaco', 'Menlo', monospace;
  font-weight: 500;
}

.customer-name {
  color: #333;
  font-size: 14px;
  font-weight: 500;
}

.customer-actions {
  display: flex;
  gap: 12px;
}

/* ===== LOADING & ERROR STATES ===== */
.loading-section,
.error-section {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 400px;
}

.loading-card,
.error-card {
  background: white;
  border-radius: 6px;
  padding: 48px;
  text-align: center;
  border: 1px solid #e2e8f0;
  max-width: 400px;
  width: 100%;
}

.loading-icon {
  font-size: 48px;
  color: #14c9c9;
  animation: spin 1s linear infinite;
  margin-bottom: 16px;
}

.error-icon {
  font-size: 48px;
  color: #dc3545;
  margin-bottom: 16px;
}

.loading-card h3,
.error-card h3 {
  margin: 0 0 12px 0;
  color: #333;
}

.error-card p {
  margin: 0 0 24px 0;
  color: #666;
}

/* ===== FORM ===== */
.edit-form {
  padding: 0;
}

.form-section {
  background: white;
  border-bottom: 1px solid #e2e8f0;
}

.form-section:last-child {
  border-bottom: none;
}

.section-content {
  padding: 24px;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 24px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-label {
  font-size: 14px;
  font-weight: 500;
  color: #374151;
}

.form-input,
.form-select {
  padding: 12px 16px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
  background: white;
  transition:
    border-color 0.2s,
    box-shadow 0.2s;
}

.form-input:focus,
.form-select:focus {
  outline: none;
  border-color: #14c9c9;
  box-shadow: 0 0 0 3px rgba(20, 201, 201, 0.1);
}

.form-input:disabled,
.form-input[readonly] {
  background: #f9fafb;
  color: #6b7280;
  cursor: not-allowed;
}

.form-input.is-invalid,
.form-select.is-invalid {
  border-color: #dc3545;
}

.form-input.is-invalid:focus,
.form-select.is-invalid:focus {
  border-color: #dc3545;
  box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
}

.form-help {
  font-size: 12px;
  color: #6b7280;
}

.invalid-feedback {
  font-size: 12px;
  color: #dc3545;
}

/* ===== FORM ACTIONS ===== */
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 16px;
  padding: 24px 32px;
  background: #f8f9fa;
  border-top: 1px solid #e2e8f0;
}

/* ===== BUTTONS ===== */
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
}

.btn-secondary {
  background: #f8f9fa;
  color: #333;
  border-color: #dee2e6;
}

.btn-primary {
  background: #14c9c9;
  color: white;
  border-color: #14c9c9;
}

.btn-primary:disabled {
  background: #e9ecef;
  color: #6c757d;
  border-color: #adb5bd;
  cursor: not-allowed;
}

.loading-icon {
  animation: spin 1s linear infinite;
}

/* ===== ANIMATIONS ===== */
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
  .admin-customers-container {
    padding: 10px;
  }

  .customer-header {
    flex-direction: column;
    gap: 16px;
    align-items: flex-start;
  }

  .section-content {
    padding: 20px;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
    gap: 12px;
  }

  .btn {
    width: 100%;
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .customer-header {
    flex-direction: column;
    gap: 8px;
    align-items: flex-start;
  }

  .form-actions {
    padding: 20px 24px;
  }
}
</style>
