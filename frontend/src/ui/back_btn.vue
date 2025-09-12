<template>
  <div class="brand-header">
    <button class="back-btn" @click="goBack">
      <Icon icon="weui:back-filled" class="back-icon" />
      <span>Back</span>
    </button>
    <div class="brand-logo-header">
      <span>{{ brandName.toUpperCase() }}</span>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { Icon } from '@iconify/vue'

const props = defineProps({
  brandName: {
    type: String,
    required: true,
  },
})

const router = useRouter()

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
  padding: 20px;
  background-color: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(5px);
  z-index: 1000;
  border-bottom: 1px solid #d0d5dd;
  display: flex;
  align-items: center;
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
  flex: 1;
  text-align: center;
  margin-right: 5.2rem;
  font-family: '911Porscha', sans-serif;
  font-size: 1.4rem;
  font-weight: 500;
}
</style>
