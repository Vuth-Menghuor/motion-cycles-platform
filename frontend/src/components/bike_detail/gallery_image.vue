<template>
  <div class="header-wrapper">
    <button class="back-btn" @click="goBack">Back</button>
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
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()

// Get data from query
const image = route.query.image
const title = route.query.title
const additionalImages = route.query.additionalImages
  ? JSON.parse(route.query.additionalImages)
  : []

// First 7 images
const gridImages = computed(() => [image, ...additionalImages.slice(0, 7)])

const goBack = () => {
  const bikeId = route.params.id // assuming your route is /bike/:id/gallery
  router.push(`/bike/${bikeId}`)
}
</script>

<style scoped>
.header-wrapper {
  position: sticky; /* makes it stick when scrolling */
  top: 0; /* stick to top of viewport */
  left: 0;
  width: 100%;
  padding: 14px 18px 16px 18px;
  background-color: rgba(255, 255, 255, 0.9); /* semi-transparent overlay */
  backdrop-filter: blur(5px); /* adds a subtle blur effect behind */
  z-index: 1000; /* ensure it's above other content */
  border-bottom: 1px solid #d0d5dd;
  display: flex;
  align-items: center;
}

.back-btn {
  align-items: center;
  text-align: center;
  font-size: 0.9rem;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  height: 44px;
  background-color: white;
  border: 1px solid #d0d5dd;
  width: 120px;
  cursor: pointer;
  transition:
    background-color 0.2s ease,
    transform 0.2s ease;
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
