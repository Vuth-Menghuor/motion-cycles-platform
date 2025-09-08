<template>
  <div class="specifications-container">
    <!-- Tab Navigation -->
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

    <!-- Specifications Section -->
    <div v-if="activeTab === 'specifications'" class="specifications-card">
      <h2 class="specifications-title">Specifications</h2>

      <div class="specifications-list">
        <div v-for="(spec, index) in specifications" :key="index" class="specification-row">
          <span class="spec-label">{{ spec.label }}</span>
          <span class="spec-value">{{ spec.value }}</span>
        </div>
      </div>
    </div>

    <!-- Other tabs content -->
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

<script>
export default {
  name: 'SpecificationsTable',
  data() {
    return {
      activeTab: 'specifications',
      specifications: [
        {
          label: 'Range',
          value: ' Up to 60 Miles',
        },
        { label: 'Hub Motor', value: '750W Brush less gear motor' },
        { label: 'Total Payload Capacity', value: '400 lb' },
        { label: 'Controller', value: '30v/10A' },
        { label: 'Weight', value: '33/35 lb' },
        { label: 'Display', value: '-' },
        { label: 'Battery', value: '-' },
      ],
    }
  },
}
</script>

<style scoped>
.specifications-container {
  width: 100%;
  font-family: 'Poppins', sans-serif;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 18px;
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

.tab-navigation {
  display: flex;
  justify-content: space-between;
  border: 1px solid #e2e8f0;
  padding: 4px;
  border-radius: 10px;
  margin-bottom: 16px;
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

.specification-row {
  display: flex;
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

.tab-content {
  padding: 24px;
  color: #6b7280;
}
</style>
