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
          :class="{ grayscale: shouldShowMoreOverlay(index) }"
        />
        <div v-if="shouldShowMoreOverlay(index)" class="overlay" @click.stop="goToGallery">
          +{{ additionalImagesCount }} more
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'

// Props
const props = defineProps({
  image: { type: String, required: true },
  title: { type: String, required: true },
  additionalImages: { type: Array, default: () => [] },
})

// Router
const router = useRouter()
const route = useRoute()

// Reactive state
const selectedImage = ref(props.image)
const lightboxOpen = ref(false)

// Computed properties
const allImages = computed(() => {
  const images = [createImageObject(props.image, props.title)]

  if (hasAdditionalImages.value) {
    images.push(
      ...props.additionalImages.map((img, index) =>
        createImageObject(img, `${props.title} - View ${index + 1}`),
      ),
    )
  }

  return images
})

const visibleThumbnails = computed(() => allImages.value.slice(0, 4))

const hasAdditionalImages = computed(
  () => Array.isArray(props.additionalImages) && props.additionalImages.length > 0,
)

const additionalImagesCount = computed(() => Math.max(0, allImages.value.length - 4))

// Helper functions
function createImageObject(imageData, defaultAlt) {
  if (typeof imageData === 'string') {
    // Handle string URLs
    return { src: imageData, alt: defaultAlt }
  } else if (imageData && typeof imageData === 'object' && imageData.url) {
    // Handle objects with url/alt properties
    return { src: imageData.url, alt: imageData.alt || defaultAlt }
  }
  return null
}

function shouldShowMoreOverlay(index) {
  return index === 3 && allImages.value.length > 4
}

// Event handlers
function selectImage(imageSrc) {
  selectedImage.value = imageSrc
}

function openLightbox() {
  lightboxOpen.value = true
}

function goToGallery() {
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

// Watchers
watch(
  () => props.additionalImages,
  () => {
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
  transition: transform 0.3s;
}

.thumbnail-item:hover img {
  transform: scale(1.05);
}

/* .thumbnail-item.active {
  border-color: #4299e1;
  box-shadow: 0 0 0 2px rgba(66, 153, 225, 0.2);
} */

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
  cursor: pointer;
}

/* Responsive design */
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
