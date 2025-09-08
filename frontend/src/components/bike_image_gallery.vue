<template>
  <div class="image-section">
    <div class="main-image">
      <img :src="mainImage" :alt="imageAlt" />
    </div>
    <div class="thumbnail-grid">
      <div
        v-for="(thumbnail, index) in thumbnails"
        :key="index"
        class="thumbnail-item"
        @click="selectImage(thumbnail.src)"
        :class="{ active: thumbnail.src === mainImage }"
      >
        <img :src="thumbnail.src" :alt="thumbnail.alt" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  image: {
    type: String,
    required: true,
  },
  title: {
    type: String,
    required: true,
  },
  additionalImages: {
    type: Array,
    default: () => [],
  },
})

const selectedImage = ref(props.image)

const mainImage = computed(() => selectedImage.value)
const imageAlt = computed(() => props.title)

const thumbnails = computed(() => {
  // If additional images are provided, use them; otherwise, use the same image for all thumbnails
  if (props.additionalImages.length > 0) {
    return props.additionalImages.map((img, index) => ({
      src: img,
      alt: `${props.title} - View ${index + 1}`,
    }))
  } else {
    // Default behavior: show same image in all thumbnails
    return Array.from({ length: 4 }, (_, index) => ({
      src: props.image,
      alt: `${props.title} - View ${index + 1}`,
    }))
  }
})

const selectImage = (imageSrc) => {
  selectedImage.value = imageSrc
}
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
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  border: 1px solid #e2e8f0;
}

.main-image img {
  width: 100%;
  object-fit: fill;
}

.thumbnail-grid {
  flex: 1;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  grid-template-rows: repeat(2, 1fr);
  gap: 8px;
}

.thumbnail-item {
  background: white;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
  border: 1px solid #e2e8f0;
}

.thumbnail-item:hover {
  transform: scale(1.02);
}

.thumbnail-item img {
  width: 99%;
  object-fit: fill;
}

/* Responsive Design */
@media (max-width: 768px) {
  .image-section {
    flex-direction: column;
    gap: 16px;
  }

  .thumbnail-grid {
    flex: none;
    grid-template-columns: repeat(4, 1fr);
    grid-template-rows: 1fr;
  }
}
</style>
