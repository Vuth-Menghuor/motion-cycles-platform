<template>
  <div class="form-section">
    <h2 class="section-title">Discount Code</h2>

    <div class="form-group">
      <label>Discount Code</label>
      <input
        type="text"
        v-model="localProduct.discountCode"
        placeholder="Enter discount code (optional)"
        :disabled="disabled"
      />
    </div>

    <!-- Validity Period (only shown when discount code is entered) -->
    <div v-if="localProduct.discountCode" class="validity-section">
      <h3 class="validity-title">Discount Validity Period</h3>

      <div class="form-group">
        <label>Start Date</label>
        <input
          type="date"
          v-model="localProduct.discountStartDate"
          :min="today"
          :disabled="disabled"
        />
      </div>

      <div class="form-group">
        <label>Expire Date</label>
        <input
          type="date"
          v-model="localProduct.discountExpireDate"
          :min="localProduct.discountStartDate || today"
          :disabled="disabled"
        />
      </div>
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

// Computed property for today's date in YYYY-MM-DD format
const today = computed(() => {
  const date = new Date()
  return date.toISOString().split('T')[0]
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

input {
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

input:focus {
  outline: none;
  border-color: #4299e1;
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

input:disabled {
  background-color: #f7fafc;
  color: #a0aec0;
  cursor: not-allowed;
  opacity: 0.6;
}

/* Validity Period Section */
.validity-section {
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  padding: 16px;
  margin: 0 20px 20px 20px;
}

.validity-title {
  font-size: 16px;
  font-weight: 500;
  color: #2d3748;
  margin: 0 0 16px 0;
  font-family: 'Poppins', sans-serif;
}
</style>
