<template>
  <div class="form-section">
    <h2 class="section-title">Product Image</h2>

    <div class="image-upload-section">
      <div class="upload-buttons">
        <button class="btn btn-upload" @click="triggerFileUpload" :disabled="disabled">
          <Icon icon="mdi:upload" /> Upload
        </button>
        <button class="btn btn-cancel" @click="cancelUpload" :disabled="disabled">
          <Icon icon="mdi:close" /> Cancel
        </button>
      </div>

      <input
        ref="fileInput"
        type="file"
        multiple
        accept="image/*"
        @change="handleFileSelect"
        style="display: none"
        :disabled="disabled"
      />

      <div
        class="drop-zone"
        :class="{ disabled: disabled }"
        @dragover.prevent="disabled ? null : $event"
        @drop.prevent="disabled ? null : handleDrop($event)"
        :style="{ cursor: disabled ? 'not-allowed' : 'pointer' }"
      >
        <div class="drop-zone-content">
          <Icon icon="mdi:cloud-upload" class="drop-icon" />
          <p>Drag and drop images here or click upload</p>
          <span class="drop-hint">Supports multiple images</span>
        </div>
      </div>

      <div class="image-layout" v-if="images.length > 0">
        <div class="images-flex-group">
          <div
            v-for="(image, index) in images"
            :key="index"
            class="image-item"
            :class="{ 'large-image': index < 2, 'small-image': index >= 2 }"
            :style="{ backgroundImage: image ? `url(${image})` : 'none' }"
          >
            <div v-if="!image" class="image-placeholder">
              <Icon icon="mdi:image-plus" />
              <span>Image {{ index + 1 }}</span>
            </div>
            <button
              v-if="image"
              class="remove-btn"
              @click="removeImage(index)"
              title="Remove image"
            >
              <Icon icon="mdi:close" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Icon } from '@iconify/vue'

const props = defineProps({
  disabled: {
    type: Boolean,
    default: false,
  },
})

const fileInput = ref(null)
const images = ref([])
const isDragOver = ref(false)

const triggerFileUpload = () => {
  fileInput.value?.click()
}

const handleFileSelect = (event) => {
  const files = Array.from(event.target.files)
  processFiles(files)
}

const handleDrop = (event) => {
  if (props.disabled) return
  isDragOver.value = false
  const files = Array.from(event.dataTransfer.files)
  const imageFiles = files.filter((file) => file.type.startsWith('image/'))
  if (imageFiles.length > 0) {
    processFiles(imageFiles)
  }
}

const processFiles = (files) => {
  files.forEach((file) => {
    const reader = new FileReader()
    reader.onload = (e) => {
      images.value.push(e.target.result)
    }
    reader.readAsDataURL(file)
  })
}

const removeImage = (index) => {
  images.value.splice(index, 1)
}

const cancelUpload = () => {
  images.value = []
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}
</script>

<style scoped>
.form-section {
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  margin-bottom: 20px;
  font-family: 'Poppins', sans-serif;
  padding: 0 8px;
}

.section-title {
  font-size: 18px;
  font-weight: 500;
  margin-bottom: 20px;
  color: #1a202c;
  padding: 20px 20px 0 20px;
}

.image-upload-section {
  padding: 0 20px 20px 20px;
}

.upload-buttons {
  display: flex;
  gap: 12px;
  margin-bottom: 18px;
}

.btn {
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  transition: opacity 0.2s ease;
  display: flex;
  align-items: center;
  gap: 6px;
}

.btn:hover {
  opacity: 0.9;
}

.btn-upload {
  background-color: #4299e1;
  color: white;
}

.btn-cancel {
  background-color: #f56565;
  color: white;
}

.btn:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

.drop-zone {
  border: 2px dashed #cbd5e0;
  border-radius: 8px;
  padding: 40px 20px;
  text-align: center;
  background-color: #f7fafc;
  transition: all 0.3s ease;
  margin-bottom: 20px;
  cursor: pointer;
}

.drop-zone.disabled {
  cursor: not-allowed;
  opacity: 0.6;
  background-color: #f7fafc;
  border-color: #cbd5e0;
}

.drop-zone.disabled:hover,
.drop-zone.disabled.drag-over {
  border-color: #cbd5e0;
  background-color: #f7fafc;
}

.drop-zone-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.drop-icon {
  font-size: 48px;
  color: #cbd5e0;
  transition: color 0.3s ease;
}

.drop-zone:hover .drop-icon,
.drop-zone.drag-over .drop-icon {
  color: #4299e1;
}

.drop-zone p {
  margin: 0;
  font-size: 16px;
  color: #4a5568;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
}

.drop-hint {
  font-size: 14px;
  color: #718096;
  font-family: 'Poppins', sans-serif;
}

.image-layout {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.images-flex-group {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  align-items: flex-start;
}

.large-image {
  flex: 0 0 200px;
  aspect-ratio: 4/3;
  border-radius: 6px;
  overflow: hidden;
  position: relative;
}

.small-image {
  flex: 0 0 120px;
  aspect-ratio: 1;
  border-radius: 6px;
  overflow: hidden;
  position: relative;
}

.image-item {
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  border: 1px solid #e2e8f0;
  position: relative;
}

.image-placeholder {
  width: 100%;
  height: 100%;
  background-color: #f7fafc;
  border: 2px dashed #cbd5e0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 4px;
  color: #718096;
  font-size: 12px;
  font-family: 'Poppins', sans-serif;
  transition: all 0.3s ease;
}

.image-placeholder:hover {
  border-color: #4299e1;
  background-color: #ebf8ff;
}

.image-placeholder svg {
  font-size: 24px;
}

.remove-btn {
  position: absolute;
  top: 8px;
  right: 8px;
  background-color: rgba(245, 101, 101, 0.9);
  color: white;
  border: none;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  opacity: 0;
  transition: opacity 0.3s ease;
  font-size: 14px;
}

.image-item:hover .remove-btn {
  opacity: 1;
}

.remove-btn:hover {
  background-color: #f56565;
}

@media (max-width: 768px) {
  .images-flex-group {
    justify-content: center;
  }

  .large-image {
    flex: 0 0 160px;
  }

  .small-image {
    flex: 0 0 100px;
  }

  .drop-zone {
    padding: 30px 15px;
  }

  .drop-icon {
    font-size: 36px;
  }
}
</style>
