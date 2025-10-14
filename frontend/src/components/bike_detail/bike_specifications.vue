<template>
  <div class="specifications-container">
    <div class="tab-navigation">
      <button
        class="tab-button"
        :class="{ 'active-tab': activeTab === 'specifications' }"
        @click="activeTab = 'specifications'"
      >
        Specifications
      </button>
      <button
        class="tab-button"
        :class="{ 'active-tab': activeTab === 'components' }"
        @click="activeTab = 'components'"
      >
        Components
      </button>
      <button
        class="tab-button geometry-tab"
        :class="{ 'active-tab': activeTab === 'geometry' }"
        @click="activeTab = 'geometry'"
      >
        Geometry
      </button>
    </div>

    <div v-if="activeTab === 'specifications'" class="specifications-card">
      <h2 class="specifications-title">Specifications</h2>
      <div class="specifications-list">
        <div v-for="(spec, index) in specifications" :key="index" class="specification-row">
          <span class="spec-label">{{ spec.label }}</span>
          <span class="spec-value">{{ spec.value }}</span>
        </div>
      </div>
    </div>

    <div v-if="activeTab === 'components'" class="specifications-card">
      <h2 class="specifications-title">Components</h2>
      <div class="specifications-list">
        <p>Components content goes here...</p>
      </div>
    </div>

    <div v-if="activeTab === 'geometry'" class="specifications-card">
      <h2 class="specifications-title">Geometry</h2>
      <div class="specifications-list">
        <p>Geometry contents goes here...</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  specs: {
    type: [Object, Array],
    default: () => [],
  },
})

const activeTab = ref('specifications')

const specifications = computed(() => {
  if (!props.specs) return []

  let specsObj = props.specs

  // Handle string format (JSON string from API)
  if (typeof props.specs === 'string') {
    try {
      specsObj = JSON.parse(props.specs)
    } catch {
      return []
    }
  }

  // Handle array format (legacy)
  if (Array.isArray(specsObj)) {
    return specsObj.map((spec, index) => ({
      label: `Specification ${index + 1}`,
      value: spec.text || spec,
    }))
  }

  // Handle object format (current API response)
  if (typeof specsObj === 'object') {
    return Object.entries(specsObj)
      .map(([key, value]) => ({
        label: key.charAt(0).toUpperCase() + key.slice(1).replace(/([A-Z])/g, ' $1'),
        value: value || '-',
      }))
      .filter((spec) => spec.value !== '-')
  }

  return []
})
</script>

<style scoped>
.specifications-container {
  width: 100%;
  font-family: 'Poppins', sans-serif;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 18px;
}

.tab-navigation {
  display: flex;
  justify-content: space-between;
  border: 1px solid #e2e8f0;
  padding: 4px;
  border-radius: 10px;
  margin-bottom: 16px;
}

.tab-button {
  border-radius: 8px;
  height: 40px;
  width: 100%;
  border: none;
  background-color: white;
  font-family: 'Poppins', sans-serif;
  transition: background-color 0.3s;
}

.tab-button.active-tab {
  background-color: #3b82f6;
  font-weight: 500;
  color: white;
}

.specifications-card {
  background-color: white;
  overflow: hidden;
  width: 50%;
}

.specifications-card h2 {
  margin: 16px 0 18px 0;
}

.specifications-title {
  font-size: 24px;
  font-weight: 600;
  color: black;
}

.specification-row {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 16px 0;
  transition: background-color 0.2s ease;
}

.specification-row:hover {
  background-color: #f9fafb;
}

.spec-label {
  color: #6b7280;
  font-weight: 500;
  width: 300px;
  flex-shrink: 0;
}

.spec-value {
  color: #111827;
  font-weight: 500;
  text-align: left;
  flex: 1;
}
</style>
