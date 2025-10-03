<template>
  <div class="payment-card" :class="{ selected: isSelected }" @click="selectPayment">
    <!-- Radio button -->
    <div class="radio-container">
      <input
        type="radio"
        :id="`payment-${payment.id}`"
        :name="radioGroupName"
        :checked="isSelected"
        @change="selectPayment"
        class="radio-input"
      />
      <label :for="`payment-${payment.id}`" class="radio-label"></label>
    </div>

    <div class="content-section">
      <h3 class="payment-name">{{ payment.name }}</h3>
      <img
        :src="payment.image"
        class="method-payment"
        :class="{ 'credit-card': payment.id === 2 }"
      />
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue'

const props = defineProps({
  payment: {
    type: Object,
    required: true,
    validator: (payment) => {
      return payment.id && payment.name
    },
  },
  isSelected: {
    type: Boolean,
    default: false,
  },
  radioGroupName: {
    type: String,
    default: 'payment-selection',
  },
})

const emit = defineEmits(['select'])

const selectPayment = () => {
  emit('select', props.payment)
}
</script>

<style scoped>
.content-section {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
}

.method-payment {
  height: 18px; /* Default size (Bakong KHQR) */
  width: auto;
}

.method-payment.credit-card {
  height: 28px; /* Bigger size for Credit Card */
  width: auto;
}

.payment-card {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  padding: 20px;
  border: 2px solid #e5e5e5;
  border-radius: 12px;
  background: white;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-bottom: 16px;
}

.payment-card:hover {
  border-color: #d1d1d1;
}

.payment-card.selected {
  border-color: #14c9c9;
}

.radio-container {
  position: relative;
  flex-shrink: 0;
  margin-top: 2px;
}

.radio-input {
  appearance: none;
  width: 20px;
  height: 20px;
  border: 1px solid #d1d1d1;
  border-radius: 50%;
  background: white;
  cursor: pointer;
  transition: all 0.2s ease;
}

.radio-input:checked {
  border-color: #14c9c9;
  background: #14c9c9;
}

.radio-input:checked::before {
  content: '';
  position: absolute;
  top: 44.5%;
  left: 55%;
  transform: translate(-50%, -50%);
  width: 8px;
  height: 8px;
  background: white;
  border-radius: 50%;
}

.payment-name {
  font-size: 16px;
  font-weight: 500;
  color: #2d2d2d;
  margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .payment-card {
    padding: 16px;
  }
}
</style>
