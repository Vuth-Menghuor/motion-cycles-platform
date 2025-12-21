<template>
  <div class="back-btn-container">
    <button @click="goBack" class="back-button">
      <Icon icon="weui:back-filled" class="back-icon" />
      <span class="back-text">Back to {{ brandName || 'Home' }}</span>
    </button>
  </div>
</template>

<script setup>
import { Icon } from '@iconify/vue'
import { useRouter } from 'vue-router'

defineProps({
  brandName: {
    type: String,
    default: '',
  },
})

const router = useRouter()

const goBack = () => {
  router.push('/home')
}
</script>

<style scoped>
.back-btn-container {
  position: sticky;
  top: 0;
  left: 0;
  padding: 20px;
  background-color: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(5px);
  z-index: 1000;
  border-bottom: 1px solid #d0d5dd;
  display: flex;
  align-items: center;
}

.back-button {
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
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
  transition:
    background-color 0.2s ease,
    transform 0.2s ease;
}

.back-button:hover {
  background-color: #f9f9f9;
}

.back-icon {
  font-size: 1.2rem;
}

.back-text {
  font-size: 16px;
  font-weight: 500;
  color: #555;
}

@media (max-width: 768px) {
  .back-btn-container {
    padding: 16px 20px;
  }

  .back-text {
    font-size: 14px;
  }
}
</style>
