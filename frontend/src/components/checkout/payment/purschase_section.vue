<template>
  <div class="purchase-container">
    <!-- Buyer Information -->
    <div class="buyer-info">
      <div class="form-group">
        <label for="buyerName">Name</label>
        <input
          id="buyerName"
          type="text"
          v-model="buyerName"
          placeholder="Enter your name"
          @input="emitForm"
        />
      </div>
      <div class="form-group">
        <label for="buyerPhone">Phone number</label>
        <input
          id="buyerPhone"
          type="text"
          v-model="buyerPhone"
          placeholder="Phone number"
          @input="emitForm"
        />
      </div>
    </div>

    <!-- Payment Method -->
    <div class="payment-info">
      <h3>Payment Method</h3>
      <div class="payment-card selected">
        <img :src="selectedPayment.image" alt="Payment" class="payment-logo" />
        <div class="payment-details">
          <h4>{{ selectedPayment.name }}</h4>
          <p>{{ selectedPayment.description }}</p>
        </div>
        <div class="check-icon">
          <Icon icon="ion:checkbox" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Icon } from '@iconify/vue'
import image1 from '@/assets/images/payment_method/bakong_khqr.png'

const emit = defineEmits(['update:formData'])

const buyerName = ref('')
const buyerPhone = ref('')

const selectedPayment = ref({
  id: 1,
  name: 'Bakong KHQR',
  description: 'Scan to pay with any banking app',
  image: image1,
})

const emitForm = () =>
  emit('update:formData', {
    buyerName: buyerName.value,
    buyerPhone: buyerPhone.value,
    payment: selectedPayment.value,
  })
</script>

<style scoped>
/* Purchase Container */
.purchase-container {
  max-width: 800px;
  font-family: 'Poppins', sans-serif;
}

/* Buyer Info */
.buyer-info {
  margin-bottom: 32px;
}

.form-group {
  display: flex;
  flex-direction: column;
  margin-bottom: 16px;
}

.form-group label {
  font-size: 14px;
  margin-bottom: 6px;
  color: #555;
}

.form-group input {
  padding: 12px;
  border-radius: 8px;
  border: 1px solid #e5e5e5;
  font-size: 14px;
  outline: none;
}

.form-group input:focus {
  border-color: #14c9c9;
}

/* Payment Info */
.payment-info {
  margin-bottom: 32px;
}

.payment-info h3 {
  font-size: 16px;
  font-weight: 600;
  margin-bottom: 16px;
  color: #2d2d2d;
}

/* Payment Card */
.payment-card {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  border: 1px solid #e5e5e5;
  border-radius: 12px;
  background: white;
  position: relative;
}

.payment-card.selected {
  border-color: #f2f4f7;
}

/* Payment Logo */
.payment-logo {
  width: 60px;
  height: auto;
}

/* Payment Details */
.payment-details h4 {
  margin: 0;
  font-size: 15px;
  font-weight: 500;
}

.payment-details p {
  margin: 2px 0 0;
  font-size: 13px;
  color: #777;
}

/* Check Icon */
.check-icon {
  margin-left: auto;
  font-size: 24px;
  color: #808080;
  border-radius: 4px;
}
</style>
