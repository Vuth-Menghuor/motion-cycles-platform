<script setup lang="ts">
import { Icon } from '@iconify/vue'
</script>

<template>
  <div class="cart-header">
    <div class="step-indicator">
      <div class="step active">
        <div class="step-icon">
          <Icon icon="ion:cart" class="icon cart" />
        </div>
        <span>Cart</span>
      </div>
      <div class="step">
        <div class="step-icon">
          <Icon icon="carbon:location-filled" class="icon address" />
        </div>
        <span>Address</span>
      </div>
      <div class="step">
        <div class="step-icon">
          <Icon icon="fluent:payment-32-filled" class="icon payment" />
        </div>
        <span>Payment</span>
      </div>
    </div>
  </div>
</template>

<style scoped>
.cart-header {
  padding-bottom: 36px;
}
.icon {
  font-size: 18px;
}
.address,
.payment {
  color: grey;
}
.step-indicator {
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: relative;
}

.step {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
  flex: 1;
}

.step:not(:last-child)::after {
  content: '';
  position: absolute;
  top: 20px;
  left: 50%;
  width: 100%;
  height: 2px;
  background-color: #ccc;
  z-index: 0;
}

.step-icon {
  width: 46px;
  height: 46px;
  border-radius: 100%;
  background-color: white;
  display: flex;
  border: 1px solid #ccc;
  align-items: center;
  justify-content: center;
  z-index: 1;
}

.step.active .step-icon {
  background-color: #00bcd4;
  border: none;
  color: #fff;
}

.step span {
  margin-top: 10px;
  font-size: 12px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  color: #999;
}

.step.active span {
  color: #00bcd4;
}
</style>
