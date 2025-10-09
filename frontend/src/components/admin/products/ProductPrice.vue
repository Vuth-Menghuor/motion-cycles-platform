<template>
  <div class="form-section">
    <h2 class="section-title">Price and Colors</h2>

    <div class="form-group">
      <label>Product Price</label>
      <input
        type="text"
        v-model="localProduct.price"
        placeholder="Enter price"
        :disabled="disabled"
      />
    </div>

    <div class="form-group">
      <label>Discount Code</label>
      <input
        type="text"
        v-model="localProduct.discountCode"
        placeholder="Enter discount code (optional)"
        :disabled="disabled"
      />
    </div>

    <div class="form-group">
      <label>Product Color</label>
      <select v-model="localProduct.color" :disabled="disabled">
        <option value="">Select Colors</option>
        <option value="Red">Red</option>
        <option value="Blue">Blue</option>
        <option value="Black">Black</option>
        <option value="White">White</option>
      </select>
    </div>

    <div class="warning-message">
      <Icon icon="mdi:alert" />
      Please do not choose a fixture date when choosing the data of sale, we are not responsible for
      the problems.
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits, computed } from 'vue'
import { Icon } from '@iconify/vue'

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
  padding: 0 20px 0 20px;
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
  background-color: #ffffff;
  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease;
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

input:disabled,
select:disabled {
  background-color: #f7fafc;
  color: #a0aec0;
  cursor: not-allowed;
  opacity: 0.6;
}

.warning-message {
  background-color: #fef5e7;
  border: 1px solid #f6ad55;
  color: #c05621;
  padding: 12px 16px;
  border-radius: 6px;
  font-size: 14px;
  line-height: 1.5;
  display: flex;
  align-items: flex-start;
  gap: 8px;
  margin: 0 20px 20px 20px;
  font-family: 'Poppins', sans-serif;
}
</style>
