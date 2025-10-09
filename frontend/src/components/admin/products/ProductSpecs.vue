<template>
  <div class="form-section">
    <h2 class="section-title">Specifications</h2>

    <div class="spec-group">
      <span class="spec-label">Range</span>
      <input
        type="text"
        v-model="localSpecs.range"
        placeholder="Enter Range"
        :disabled="disabled"
      />
    </div>

    <div class="spec-group">
      <span class="spec-label">Hub Motor</span>
      <input
        type="text"
        v-model="localSpecs.hubMotor"
        placeholder="Enter Hub Motor"
        :disabled="disabled"
      />
    </div>

    <div class="spec-group">
      <span class="spec-label">Total Payload Capacity</span>
      <input
        type="text"
        v-model="localSpecs.payload"
        placeholder="Enter Total Payload"
        :disabled="disabled"
      />
    </div>

    <div class="spec-group">
      <span class="spec-label">Controller</span>
      <input
        type="text"
        v-model="localSpecs.controller"
        placeholder="Enter Controller"
        :disabled="disabled"
      />
    </div>

    <div class="spec-group">
      <span class="spec-label">Weight</span>
      <input
        type="text"
        v-model="localSpecs.weight"
        placeholder="Enter Weight"
        :disabled="disabled"
      />
    </div>

    <div class="spec-group">
      <span class="spec-label">Display</span>
      <input type="text" v-model="localSpecs.display" placeholder="-" :disabled="disabled" />
    </div>

    <div class="form-actions">
      <button class="btn btn-add" @click="$emit('add-product')" :disabled="disabled">
        <Icon icon="mdi:plus" /> {{ mode === 'edit' ? 'Update Product' : 'Add Product' }}
      </button>
      <button class="btn btn-discard" @click="$emit('discard')" :disabled="disabled">
        <Icon icon="mdi:close" /> Discard
      </button>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits, computed } from 'vue'
import { Icon } from '@iconify/vue'

const props = defineProps({
  specs: {
    type: Object,
    required: true,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  mode: {
    type: String,
    default: 'add',
    validator: (value) => ['add', 'edit'].includes(value),
  },
})

const emit = defineEmits(['update:specs', 'add-product', 'discard'])

const localSpecs = computed({
  get: () => props.specs,
  set: (value) => emit('update:specs', value),
})
</script>

<style scoped>
/* Form Section */
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

/* Specification Fields */
.spec-group {
  display: grid;
  grid-template-columns: 140px 1fr;
  gap: 16px;
  align-items: center;
  margin-bottom: 16px;
  padding: 0 20px 0 20px;
}

.spec-label {
  color: gray;
  font-size: 13px;
  font-weight: 400;
}

/* Input Styling */
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

/* Button Actions */
.form-actions {
  display: flex;
  gap: 12px;
  margin-top: 20px;
  padding: 0 20px 20px 20px;
}

.btn {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  transition: opacity 0.2s ease;
  display: flex;
  align-items: center;
  gap: 6px;
}

.btn:hover {
  opacity: 0.9;
}

.btn:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

.btn-add {
  background-color: #48bb78;
  color: white;
}

.btn-discard {
  background-color: #e2e8f0;
  color: #4a5568;
}

/* Responsive Design */
@media (max-width: 768px) {
  .spec-group {
    grid-template-columns: 1fr;
    gap: 8px;
  }

  .spec-label {
    font-weight: 400;
    color: gray;
  }
}
</style>
