<script setup lang="ts">
import { Icon } from '@iconify/vue'
import { useRoute } from 'vue-router'
import { computed } from 'vue'

const route = useRoute()

// Coerce to number for safety
const currentStep = computed(() => Number(route.meta.step) || 1)
</script>

<template>
  <div class="cart-header">
    <div class="step-indicator">
      <!-- Step 1 -->
      <div class="step" :class="{ active: currentStep >= 1 }">
        <div class="step-icon">
          <Icon icon="ion:cart" class="icon cart" />
        </div>
        <span>Cart</span>
      </div>

      <!-- Step 2 -->
      <div class="step" :class="{ active: currentStep >= 2 }">
        <div class="step-icon">
          <Icon icon="carbon:location-filled" class="icon address" />
        </div>
        <span>Address</span>
      </div>

      <!-- Step 3 -->
      <div class="step" :class="{ active: currentStep >= 3 }">
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
  transition: color 0.3s ease;
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

.step.active:not(:last-child)::after {
  background-color: #00bcd4;
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
  color: white; /* now inherited by .icon */
}

.step span {
  margin-top: 10px;
  font-size: 12px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  color: #999;
  transition: color 0.3s ease;
}

.step.active span {
  color: #00bcd4;
}
</style>
