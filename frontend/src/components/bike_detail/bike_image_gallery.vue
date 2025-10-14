<template>
  <div class="image-section">
    <div class="main-image">
      <img :src="selectedImage" :alt="title" @click="openLightbox" />
    </div>

    <div class="thumbnail-grid">
      <div
        v-for="(thumbnail, index) in visibleThumbnails"
        :key="index"
        class="thumbnail-item"
        @click="selectImage(thumbnail.src)"
        :class="{ active: thumbnail.src === selectedImage }"
      >
        <img
          :src="thumbnail.src"
          :alt="thumbnail.alt"
          :class="{ grayscale: index === 3 && thumbnails.length > 4 }"
        />
        <div v-if="index === 3 && thumbnails.length > 4" class="overlay" @click.stop="goToGallery">
          +{{ thumbnails.length - 4 }} more
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'

// Define props for the component
const props = defineProps({
  image: { type: String, required: true },
  title: { type: String, required: true },
  additionalImages: { type: Array, default: () => [] },
})

// Get router and route instances
const router = useRouter()
const route = useRoute()

// Reactive data for selected image and state
const selectedImage = ref(props.image)
const currentImageIndex = ref(0)
const lightboxOpen = ref(false)

// Computed property for all thumbnails
const thumbnails = computed(() => {
  if (
    props.additionalImages &&
    Array.isArray(props.additionalImages) &&
    props.additionalImages.length > 0
  ) {
    // Include main image plus additional images
    const mainImage = { src: props.image, alt: props.title }
    const additional = props.additionalImages.map((img, i) => ({
      src: img.url,
      alt: img.alt || `${props.title} - View ${i + 1}`,
    }))
    return [mainImage, ...additional]
  } else {
    // Only main image
    return [{ src: props.image, alt: props.title }]
  }
})

// Computed property for visible thumbnails (first 4)
const visibleThumbnails = computed(() => thumbnails.value.slice(0, 4))

// Function to select an image
const selectImage = (imageSrc) => {
  selectedImage.value = imageSrc
  currentImageIndex.value = thumbnails.value.findIndex((t) => t.src === imageSrc)
}

// Function to open lightbox
const openLightbox = () => {
  lightboxOpen.value = true
}

// Function to navigate to full gallery
const goToGallery = () => {
  router.push({
    name: 'BikeGallery',
    params: { id: route.params.id },
    query: {
      image: props.image,
      title: props.title,
      additionalImages: JSON.stringify(props.additionalImages || []),
    },
  })
}

// Watch for changes in additional images
watch(
  () => props.additionalImages,
  () => {
    currentImageIndex.value = 0
    selectedImage.value = props.image
  },
  { deep: true },
)
</script>

<style scoped>
.image-section {
  display: flex;
  justify-content: center;
  align-items: flex-start;
  gap: 24px;
}

.main-image {
  flex: 1;
  background: white;
  border: 1px solid #e2e8f0;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 580px;
}

.main-image img {
  width: 100%;
  height: 100%;
  object-fit: contain;
  height: 100%;
  cursor: pointer;
}

.thumbnail-grid {
  flex: 1;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  grid-template-rows: repeat(2, 1fr);
  gap: 8px;
}

.thumbnail-item {
  position: relative;
  cursor: pointer;
  overflow: hidden;
  border: 1px solid #e2e8f0;
  height: 284px;
}

.thumbnail-item img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: 0.3s;
}

.thumbnail-item:hover img {
  transform: scale(1.05);
}

.thumbnail-item img.grayscale {
  filter: grayscale(100%) brightness(60%);
}

.overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 1.2rem;
}

@media (max-width: 768px) {
  .image-section {
    flex-direction: column;
  }
  .thumbnail-grid {
    grid-template-columns: repeat(4, 1fr);
    grid-template-rows: 1fr;
  }
}
</style>
