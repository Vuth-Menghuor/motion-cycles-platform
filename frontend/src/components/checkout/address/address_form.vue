<template>
  <div v-if="isVisible" class="modal-overlay">
    <div class="modal-content">
      <div class="modal-header">
        <h3>{{ editingAddress ? 'Edit Address' : 'Add New Address' }}</h3>
        <button class="close-btn" @click="$emit('close')">&times;</button>
      </div>

      <form @submit.prevent="handleSubmit" class="address-form">
        <!-- Name -->
        <div class="form-group">
          <label for="name">Full Name</label>
          <input v-model="form.name" type="text" id="name" required />
        </div>

        <!-- Street -->
        <div class="form-group">
          <label for="street">Street Address</label>
          <input v-model="form.streetAddress" type="text" id="street" required />
        </div>

        <!-- District -->
        <div class="form-group">
          <label for="district">District</label>
          <input v-model="form.district" type="text" id="district" required />
        </div>

        <!-- City -->
        <div class="form-group">
          <label for="city">City</label>
          <input v-model="form.city" type="text" id="city" required />
        </div>

        <!-- Province -->
        <div class="form-group">
          <label for="province">Province</label>
          <input v-model="form.province" type="text" id="province" required />
        </div>

        <!-- Country -->
        <div class="form-group">
          <label for="country">Country</label>
          <input v-model="form.country" type="text" id="country" required />
        </div>

        <!-- Phone -->
        <div class="form-group">
          <label for="phone">Phone</label>
          <input v-model="form.phone" type="text" id="phone" required />
        </div>

        <!-- Default Address Checkbox -->
        <div class="form-group checkbox">
          <input v-model="form.isDefault" type="checkbox" id="default" />
          <label for="default">Set as default address</label>
        </div>

        <!-- Actions -->
        <div class="form-actions">
          <button type="button" class="btn btn-secondary" @click="$emit('close')">Cancel</button>
          <button type="submit" class="btn btn-primary">
            {{ editingAddress ? 'Update Address' : 'Save Address' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits, reactive, watch } from 'vue'

const props = defineProps({
  isVisible: { type: Boolean, default: false },
  editingAddress: { type: Object, default: null },
})

const emit = defineEmits(['close', 'save', 'update'])

const form = reactive({
  id: null,
  name: '',
  streetAddress: '',
  district: '',
  city: '',
  province: '',
  country: '',
  phone: '',
  isDefault: false,
})

const resetForm = () => {
  Object.assign(form, {
    id: Date.now(),
    name: '',
    streetAddress: '',
    district: '',
    city: '',
    province: '',
    country: '',
    phone: '',
    isDefault: false,
  })
}

watch(
  () => props.editingAddress,
  (newVal) => {
    newVal ? Object.assign(form, { ...newVal }) : resetForm()
  },
  { immediate: true },
)

const handleSubmit = () => {
  form.fullAddress = `${form.streetAddress}, ${form.district}, ${form.city}, ${form.province}, ${form.country}`
  emit(props.editingAddress ? 'update' : 'save', { ...form })
  emit('close')
  resetForm()
}
</script>

<style scoped>
/* Overlay */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999;
}

/* Modal */
.modal-content {
  font-family: 'Poppins', sans-serif;
  background: #fff;
  border-radius: 12px;
  width: 500px;
  max-width: 90%;
  padding: 30px;
  animation: fadeIn 0.3s ease;
}

/* Header */
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.modal-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #2d2d2d;
}

.close-btn {
  background: none;
  border: none;
  font-size: 22px;
  cursor: pointer;
  color: #888;
}

.close-btn:hover {
  color: #000;
}

/* Form */
.address-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  font-size: 14px;
  font-weight: 500;
  margin-bottom: 6px;
  color: #2d2d2d;
}

.form-group input {
  padding: 10px 12px;
  border: 1px solid #d1d1d1;
  border-radius: 8px;
  font-size: 14px;
}

.form-group input:focus {
  border-color: #14c9c9;
  outline: none;
}

/* Checkbox */
.form-group.checkbox {
  flex-direction: row;
  align-items: center;
  gap: 8px;
}

/* Actions */
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

.btn {
  font-family: 'Poppins', sans-serif;
  padding: 10px 20px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  border: none;
}

.btn-secondary {
  background: #f5f5f5;
  color: #2d2d2d;
}

.btn-primary {
  background: #14c9c9;
  color: white;
  border: 1px solid #14c9c9;
}

/* Animations */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-15px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
