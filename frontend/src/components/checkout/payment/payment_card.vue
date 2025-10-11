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
// Define the props for the component
const props = defineProps({
  payment: { type: Object, required: true, validator: (p) => p.id && p.name },
  isSelected: { type: Boolean, default: false },
  radioGroupName: { type: String, default: 'payment-selection' },
})

// Define the events the component can emit
const emit = defineEmits(['select'])

// Function to select the payment method
const selectPayment = () => emit('select', props.payment)
</script>

<style scoped>
/* Content Section */
.content-section {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
}

/* Method Payment */
.method-payment {
  height: 18px;
  width: auto;
}

.method-payment.credit-card {
  height: 28px;
  width: auto;
}

/* Payment Card */
.payment-card {
  display: flex;
  align-items: center;
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

/* Radio Container */
.radio-container {
  position: relative;
  flex-shrink: 0;
  margin-top: 2px;
}

/* Radio Input */
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

/* Payment Name */
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
