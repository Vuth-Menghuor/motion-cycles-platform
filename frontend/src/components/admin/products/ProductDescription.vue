<template>
  <div class="form-section">
    <h2 class="section-title">Description</h2>

    <div class="form-group">
      <label>Highlight</label>
      <textarea
        v-model="localProduct.highlight"
        placeholder="Add highlight text..."
        :disabled="disabled"
        :class="{ 'prefilled-field': prefilledFields?.highlight }"
      ></textarea>
    </div>

    <div class="form-group">
      <label>About This Product</label>
      <textarea
        v-model="localProduct.description"
        placeholder="Add product description..."
        :disabled="disabled"
        :class="{ 'prefilled-field': prefilledFields?.description }"
      ></textarea>
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

textarea {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
  resize: vertical;
  min-height: 100px;
  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease;
  background-color: #ffffff;
  line-height: 1.5;
  max-width: -webkit-fill-available;
}

textarea:focus {
  outline: none;
  border-color: #4299e1;
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

textarea::placeholder {
  color: #a0aec0;
}

textarea:disabled {
  background-color: #f7fafc;
  color: #a0aec0;
  cursor: not-allowed;
  opacity: 0.6;
}

.prefilled-field {
  background-color: #f0fff4 !important;
  border-color: #48bb78 !important;
  color: #22543d !important;
}
</style>
