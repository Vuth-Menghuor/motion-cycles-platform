<template>
  <!-- Statistics Card Component -->
  <div class="stat-card" :class="`stat-card-${color}`">
    <!-- Main Content: Title, Value and Icon -->
    <div class="stat-header">
      <div class="stat-label">{{ title }}</div>
    </div>
    <div class="stat-content">
      <div class="stat-main">
        <div class="stat-title-value">
          <span class="stat-title">{{ shortTitle }}</span>
          <span class="stat-value">{{ formattedValue }}</span>
        </div>
      </div>
    </div>

    <!-- Action Buttons (Add, Edit, View) -->
    <div class="stat-actions">
      <div class="action-group">
        <button v-if="actions.includes('add')" class="action-icon" title="Add">
          <Icon :icon="addIcon" class="action" />
          <span class="action-text">New</span>
        </button>
        <button v-if="actions.includes('edit')" class="action-icon" title="Edit">
          <Icon :icon="editIcon" class="action" />
          <span class="action-text">Edit</span>
        </button>
        <button v-if="actions.includes('view')" class="action-icon" title="View">
          <Icon :icon="viewIcon" class="action" />
          <span class="action-text">View</span>
        </button>
      </div>
      <div class="stat-icon">
        <div class="icon-bg">
          <Icon :icon="icon" class="main-icon" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Icon } from '@iconify/vue'

// Define props
const props = defineProps({
  // Title displayed at the top of the card
  title: {
    type: String,
    required: true,
  },
  // Numeric value to display
  value: {
    type: Number,
    required: true,
  },
  // Prefix for the value (e.g., '$')
  prefix: {
    type: String,
    default: '',
  },
  // Color theme: 'blue', 'green', 'red', 'custom-blue', 'custom-teal', 'custom-red'
  color: {
    type: String,
    default: 'blue',
    validator: (value) =>
      ['blue', 'green', 'red', 'custom-blue', 'custom-teal', 'custom-red'].includes(value),
  },
  // Icon name to display (e.g., 'package', 'dollar', 'users')
  icon: {
    type: String,
    default: 'package',
  },
  // Actions to display: array of 'add', 'edit', 'view'
  actions: {
    type: Array,
    default: () => ['add', 'edit', 'view'],
    validator: (value) => value.every((action) => ['add', 'edit', 'view'].includes(action)),
  },
  // Custom icons for actions
  addIcon: {
    type: String,
    default: 'fluent-mdl2:product-release',
  },
  editIcon: {
    type: String,
    default: 'ic:baseline-edit',
  },
  viewIcon: {
    type: String,
    default: 'mdi:eye',
  },
})

// Computed properties
// Format the value with commas for readability
const formattedValue = computed(() => {
  return props.value.toLocaleString()
})

// Create a shorter version of the title
const shortTitle = computed(() => {
  let title = props.title.replace(/^Total\s+/i, '') // Remove "Total" from the beginning
  return title.length > 10 ? title.substring(0, 10) + '...' : title
})
</script>

<style scoped>
.stat-card {
  background-color: #3399cc;
  color: #fff;
  border-radius: 0px;
  padding: 20px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  gap: 15px;
  min-height: 180px;
  font-family: 'Poppins', sans-serif;
  border-radius: 2px;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 0;
}

.stat-card-blue,
.stat-card-green,
.stat-card-red {
  color: white;
}

.stat-card-blue {
  background: linear-gradient(135deg, #667eea);
}

.stat-card-green {
  background: linear-gradient(135deg, #3399cc);
}

.stat-card-red {
  background: linear-gradient(135deg, #33b5aa);
}

.stat-header {
  margin-bottom: 4px;
  position: relative;
  z-index: 1;
}

.stat-label {
  font-size: 14px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.9);
}

.stat-content {
  display: flex;
  justify-content: center;
  align-items: center;
  flex: 1;
  position: relative;
  z-index: 1;
}

.stat-main {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.stat-title-value {
  display: flex;
  align-items: baseline;
  gap: 6px;
  flex-wrap: wrap;
}

.stat-title {
  font-size: 12px;
  font-weight: 500;
  color: rgba(255, 255, 255, 0.9);
  white-space: nowrap;
}

.stat-value {
  font-size: 38px;
  font-weight: bold;
  text-decoration: underline;
  text-decoration-thickness: 1.5px;
  line-height: 1;
  color: white;
}

.stat-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 8px;
  margin-top: 6px;
  position: relative;
  z-index: 1;
}

.action-group {
  display: flex;
  gap: 20px;
}

.action-icon {
  background: transparent;
  border: none;
  color: white;
  padding: 8px;
  border-radius: 6px;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
  transition:
    transform 0.12s ease,
    background 0.12s ease;
}

.action-icon:hover {
  background: rgba(255, 255, 255, 0.1);
}

.action-text {
  font-size: 12px;
  font-weight: 400;
  color: rgba(255, 255, 255, 0.8);
  text-align: center;
  line-height: 1;
  font-family: 'Poppins', sans-serif;
}

.action {
  width: 16px;
  height: 16px;
  flex-shrink: 0;
}

.stat-icon {
  display: flex;
  align-items: center;
}

.icon-bg {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  padding: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.main-icon {
  width: 24px;
  height: 24px;
  color: #d9d9d9;
}
</style>
