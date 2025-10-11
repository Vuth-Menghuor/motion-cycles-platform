<template>
  <div class="form-section">
    <h2 class="section-title">Quality</h2>

    <div class="form-group">
      <label>Product Quality</label>
      <select v-model="localProduct.quality" :disabled="disabled || prefilledFields.quality">
        <option value="">Select Quality</option>
        <option value="New">New</option>
        <option value="Refurbished">Refurbished</option>
        <option value="Used">Used</option>
      </select>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  product: {
    type: Object,
    required: true,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  prefilledFields: {
    type: Object,
    default: () => ({}),
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
  padding: 0 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
  font-family: 'Poppins', sans-serif;
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
  padding: 0 20px 20px 20px;
}

label {
  display: block;
  margin-bottom: 8px;
  color: gray;
  font-size: 13px;
  font-weight: 400;
}

select {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
  background-color: #ffffff;
  cursor: pointer;
  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease;
  appearance: none;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 12px center;
  background-repeat: no-repeat;
  background-size: 16px;
  padding-right: 40px;
}

select:focus {
  outline: none;
  border-color: #4299e1;
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

select:disabled {
  background-color: #f7fafc;
  color: #a0aec0;
  cursor: not-allowed;
  opacity: 0.6;
}

select:disabled {
  background-color: #e6fffa;
  color: #38a169;
  border-color: #9ae6b4;
  opacity: 1;
}
</style>
