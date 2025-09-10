<template>
  <div class="image-section">
    <!-- Main Image -->
    <div class="main-image">
      <img :src="mainImage" :alt="imageAlt" @click="openLightbox" />
    </div>

    <!-- Thumbnail Grid (limit 4) -->
    <div class="thumbnail-grid">
      <div
        v-for="(thumbnail, index) in visibleThumbnails"
        :key="index"
        class="thumbnail-item"
        @click="selectImage(thumbnail.src)"
        :class="{ active: thumbnail.src === mainImage }"
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

// Props
const props = defineProps({
  image: { type: String, required: true },
  title: { type: String, required: true },
  additionalImages: { type: Array, default: () => [] },
})

const router = useRouter()
const route = useRoute()

// State
const selectedImage = ref(props.image)
const currentImageIndex = ref(0)
const lightboxOpen = ref(false)

// Thumbnails (main + additional)
const thumbnails = computed(() => {
  if (props.additionalImages.length > 0) {
    return [{ src: props.image, alt: props.title }].concat(
      props.additionalImages.map((img, i) => ({
        src: img.url,
        alt: img.alt || `${props.title} - View ${i + 1}`, //  use provided alt
      })),
    )
  }
  return [{ src: props.image, alt: props.title }]
})

// Displayed in main area
const mainImage = computed(() => selectedImage.value)
const imageAlt = computed(() => props.title)

// Show only 4 thumbnails
const visibleThumbnails = computed(() => thumbnails.value.slice(0, 4))

// Current image in lightbox

// Methods
const selectImage = (imageSrc) => {
  selectedImage.value = imageSrc
  currentImageIndex.value = thumbnails.value.findIndex((t) => t.src === imageSrc)
}

const openLightbox = () => {
  lightboxOpen.value = true
}

const goToGallery = () => {
  router.push({
    name: 'BikeGallery',
    params: { id: route.params.id },
    query: {
      image: props.image,
      title: props.title,
      additionalImages: JSON.stringify(props.additionalImages),
    },
  })
}

// Reset index when additionalImages changes
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
}
.main-image img {
  width: 105%;
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
