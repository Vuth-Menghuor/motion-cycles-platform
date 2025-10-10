<template>
  <div class="brand-header">
    <button class="back-btn" @click="goBack">
      <Icon icon="weui:back-filled" class="back-icon" />
      <span>Back</span>
    </button>
  </div>
  <div class="gallery-wrapper">
    <div class="gallery-container">
      <div class="gallery-column">
        <div v-for="(img, idx) in gridImages" :key="idx" class="grid-item">
          <img :src="img.url || img" :alt="img.alt || `${title} - Image ${idx + 1}`" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Icon } from '@iconify/vue'
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

const image = route.query.image
const title = route.query.title
const additionalImages = route.query.additionalImages
  ? JSON.parse(route.query.additionalImages)
  : []

const gridImages = computed(() => [image, ...additionalImages.slice(0, 7)])

const goBack = () => {
  router.push(`/bike/${route.params.id}`).then(() => window.scrollTo(0, 0))
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
  padding: 10px 20px;
  border-radius: 4px;
  cursor: pointer;
  transition:
    background-color 0.2s ease,
    transform 0.2s ease;
}

.back-icon {
  font-size: 1.2rem;
}

.gallery-container {
  width: 80%;
  max-width: 1600px;
  margin: 0 auto;
  padding: 20px;
}

.gallery-column {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.grid-item {
  position: relative;
  overflow: hidden;
}

.grid-item img {
  width: 100%;
  height: auto;
  object-fit: cover;
  transition: transform 0.3s ease;
  box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
}
</style>
