<template>
  <div class="back-btn-container">
    <button @click="goBack" class="back-button">
      <svg class="back-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M15 19l-7-7 7-7"
        ></path>
      </svg>
      <span class="back-text">Back to {{ brandName || 'Home' }}</span>
    </button>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

defineProps({
  brandName: {
    type: String,
    default: '',
  },
})

const router = useRouter()

const goBack = () => {
  if (window.history.length > 1) {
    router.go(-1)
  } else {
    router.push('/')
  }
}
</script>

<style scoped>
.back-btn-container {
  padding: 20px 0;
  margin-bottom: 20px;
}

.back-button {
  display: flex;
  align-items: center;
  gap: 8px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px 12px;
  border-radius: 6px;
  transition: background-color 0.2s;
  font-family: 'Poppins', sans-serif;
}

.back-button:hover {
  background-color: #f3f4f6;
}

.back-icon {
  width: 20px;
  height: 20px;
  color: #374151;
}

.back-text {
  font-size: 16px;
  font-weight: 500;
  color: #374151;
}

@media (max-width: 768px) {
  .back-btn-container {
    padding: 16px 0;
  }

  .back-text {
    font-size: 14px;
  }
}
</style>
