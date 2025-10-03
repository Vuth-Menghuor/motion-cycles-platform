<template>
  <div class="brand-header">
    <button class="back-btn" @click="goBack">
      <Icon icon="weui:back-filled" class="back-icon" />
      <span>Back</span>
    </button>
    <div class="brand-logo-header">
      <span>{{ brandName.toUpperCase() }}</span>
    </div>
    <div class="cart-and-user-container">
      <div class="cart-container">
        <button class="cart-button">
          <Icon icon="ion:cart" class="cart-icon" />
          <span class="cart-badge" v-if="count > 0">{{ count }}</span>
        </button>
      </div>
      <div class="account-container">
        <router-link to="/authentication/sign_in">
          <button class="user-account">
            <Icon icon="mdi:user" class="account-icon" />
          </button>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { Icon } from '@iconify/vue'
import { useCartStore } from '@/stores/cart'
import { storeToRefs } from 'pinia'

const props = defineProps({
  brandName: {
    type: String,
    required: true,
  },
})

const router = useRouter()
const cartStore = useCartStore()
const { count } = storeToRefs(cartStore)

const goBack = () => {
  router.push('/').then(() => {
    window.scrollTo(0, 0)
  })
}
</script>

<style scoped>
.brand-header {
  position: sticky;
  top: 0;
  left: 0;
  background-color: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(5px);
  z-index: 1000;
  border-bottom: 1px solid #d0d5dd;
  display: flex;
  align-items: center;
  display: flex;
  justify-content: space-around;
  padding: 1rem 2rem;
  gap: 1rem;
  flex-wrap: wrap;
}

.back-btn {
  display: flex;
  gap: 8px;
  align-items: center;
  font-size: 0.9rem;
  font-weight: bold;
  color: #555;
  font-family: 'Poppins', sans-serif;
  height: 44px;
  background-color: white;
  border: 1px solid grey;
  padding: 10px 20px 10px 20px;
  border-radius: 4px;
  cursor: pointer;
  transition:
    background-color 0.2s ease,
    transform 0.2s ease;
}

.back-icon {
  font-size: 1.2rem;
}

.brand-logo-header {
  flex: 1; /* Allows it to take up available space */
  text-align: center;
  font-family: '911Porscha', sans-serif;
  font-size: 1.4rem;
  font-weight: 500;
}

.cart-and-user-container {
  display: flex;
  align-items: center;
  gap: 10px; /* Adjust the gap between the cart and user buttons */
}

/* Add styles for the cart and user buttons */
.cart-button,
.user-account {
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 42px;
  height: 42px;
  border: 1px solid grey;
  background: rgba(255, 255, 255, 0.1);
  background: white;
  border-color: rgba(0, 0, 0, 0.2);
  border-radius: 2px;
  cursor: pointer;
}

.cart-badge {
  position: absolute;
  top: 0;
  right: 0;
  transform: translate(50%, -50%);
  background-color: #ff4d4f;
  color: white;
  font-size: 0.7rem;
  font-weight: bold;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 18px;
  height: 18px;
  border-radius: 30%;
  font-weight: 600;
}

.cart-icon,
.account-icon {
  font-size: 1.3rem;
  color: black;
}
</style>
