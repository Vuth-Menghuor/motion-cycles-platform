<template>
  <div class="form-section">
    <h2 class="section-title">Product</h2>

    <div class="form-group">
      <label>Product ID</label>
      <input
        type="text"
        v-model="localProduct.id"
        placeholder="Product ID will be generated"
        disabled
        readonly
      />
    </div>

    <div class="form-group">
      <label>Product Name</label>
      <input
        type="text"
        v-model="localProduct.name"
        placeholder="Enter product name"
        :disabled="disabled"
      />
    </div>

    <div class="form-group">
      <label>Product Brand</label>
      <select v-model="localProduct.brand" :disabled="disabled">
        <option value="">Select Brand</option>
        <option value="Brand A">Brand A</option>
        <option value="Brand B">Brand B</option>
        <option value="Brand C">Brand C</option>
      </select>
    </div>

    <div class="form-group">
      <label>Product Category</label>
      <select v-model="localProduct.category" :disabled="disabled">
        <option value="">Select Category</option>
        <option value="Electronics">Electronics</option>
        <option value="Vehicles">Vehicles</option>
        <option value="Accessories">Accessories</option>
      </select>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits, computed } from 'vue'

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:product'])

const localProduct = computed({
  get: () => props.product,
  set: (value) => emit('update:product', value),
})
</script>

<style scoped>
.form-section {
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
  font-family: 'Poppins', sans-serif;
  padding: 0 8px;
}

.section-title {
  font-size: 18px;
  font-weight: 500;
  margin-bottom: 20px;
  color: #1a202c;
  padding: 20px 20px 0 20px;
}

.form-group {
  margin-bottom: 16px;
  padding: 0 20px;
}

.form-group:last-child {
  padding-bottom: 20px;
}

label {
  display: block;
  margin-bottom: 8px;
  color: gray;
  font-size: 13px;
  font-weight: 400;
}

input,
select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease;
  background-color: #ffffff;
  max-width: -webkit-fill-available;
}

input:focus,
select:focus {
  outline: none;
  border-color: #4299e1;
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

select {
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 12px center;
  background-repeat: no-repeat;
  background-size: 16px;
  padding-right: 40px;
}

input::placeholder {
  color: #a0aec0;
}

input:disabled,
select:disabled {
  background-color: #f7fafc;
  color: #a0aec0;
  cursor: not-allowed;
  opacity: 0.6;
}

/* Special styling for Product ID field */
.form-group:first-child input:disabled {
  background-color: #e6fffa;
  color: #38a169;
  font-weight: 500;
  border-color: #9ae6b4;
  cursor: not-allowed;
}
</style>
