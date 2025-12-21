<template>
  <div class="filters-sidebar" :class="{ 'show-mobile': showFilters }">
    <div class="filters-header">
      <h3>Filters</h3>
      <button class="clear-filters-btn" @click="clearAllFilters">Clear All</button>
    </div>

    <!-- Price Range Filter -->
    <div class="filter-section">
      <h4>Price Range</h4>
      <div class="filter-options">
        <label v-for="range in priceRanges" :key="range.label" class="filter-option">
          <input type="radio" :value="range.label" v-model="selectedPriceRange" />
          <span class="checkmark"></span>
          {{ range.label }}
        </label>
      </div>
    </div>

    <!-- Discount Filter -->
    <div class="filter-section">
      <h4>Discount Status</h4>
      <div class="filter-options">
        <label v-for="status in discountStatuses" :key="status.value" class="filter-option">
          <input
            type="checkbox"
            :value="status.value"
            :checked="selectedDiscountStatuses.includes(status.value)"
            @change="toggleDiscountFilter(status.value)"
          />
          <span class="checkmark checkbox"></span>
          {{ status.label }}
        </label>
      </div>
    </div>

    <!-- Category Filter -->
    <div class="filter-section">
      <h4>Category</h4>
      <div class="filter-options">
        <label class="filter-option">
          <input type="radio" value="" v-model="selectedCategory" />
          <span class="checkmark"></span>
          All Categories
        </label>
        <label v-for="category in categories" :key="category.id" class="filter-option">
          <input type="radio" :value="category.id" v-model="selectedCategory" />
          <span class="checkmark"></span>
          {{ category.name }}
        </label>
      </div>
    </div>

    <!-- Color Filter -->
    <div class="filter-section">
      <h4>Color</h4>
      <div class="filter-options">
        <label v-for="color in availableColors" :key="color" class="filter-option">
          <input
            type="checkbox"
            :value="color"
            :checked="selectedColors.includes(color)"
            @change="toggleColorFilter(color)"
          />
          <span class="checkmark checkbox"></span>
          {{ color }}
        </label>
      </div>
    </div>

    <!-- Brand Filter -->
    <div class="filter-section">
      <h4>Brand</h4>
      <div class="filter-options">
        <label v-for="brand in availableBrands" :key="brand" class="filter-option">
          <input
            type="checkbox"
            :value="brand"
            :checked="selectedBrands.includes(brand)"
            @change="toggleBrandFilter(brand)"
          />
          <span class="checkmark checkbox"></span>
          {{ brand }}
        </label>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

// Define props for the filter component
const props = defineProps({
  bikes: { type: Array, required: true },
  showFilters: { type: Boolean, default: false },
  selectedPriceRange: { type: String, default: '' },
  selectedColors: { type: Array, default: () => [] },
  selectedBrands: { type: Array, default: () => [] },
  selectedDiscountStatuses: { type: Array, default: () => [] },
  categories: { type: Array, default: () => [] },
  selectedCategory: { type: String, default: '' },
})

// Define emits for updating filters
const emit = defineEmits([
  'update:selectedPriceRange',
  'update:selectedColors',
  'update:selectedBrands',
  'update:selectedDiscountStatuses',
  'update:selectedCategory',
  'clear-filters',
  'filters-changed',
])

// Price range options
const priceRanges = [
  { label: '$500 - $1,000', min: 500, max: 1000 },
  { label: '$1,000 - $2,500', min: 1000, max: 2500 },
  { label: '$2,500 - $5,000', min: 2500, max: 5000 },
  { label: '$5,000 - $10,000', min: 5000, max: 10000 },
  { label: 'Greater than $10,000', min: 10000, max: Infinity },
]

// Discount status options
const discountStatuses = [
  { label: 'On Sale', value: 'discounted' },
  { label: 'Regular Price', value: 'regular' },
]

// Computed for unique available colors from bikes
const availableColors = computed(() => {
  if (!Array.isArray(props.bikes)) return []
  return [...new Set(props.bikes.map((b) => b.color))].sort()
})

// Computed for unique available brands from bikes
const availableBrands = computed(() => {
  if (!Array.isArray(props.bikes)) return []
  return [...new Set(props.bikes.map((b) => b.brand).filter(Boolean))].sort()
})

// Computed for selected price range with getter/setter
const selectedPriceRange = computed({
  get: () => props.selectedPriceRange,
  set: (v) => {
    emit('update:selectedPriceRange', v)
    emitFiltersChanged()
  },
})

// Computed for selected colors with getter/setter
const selectedColors = computed({
  get: () => props.selectedColors,
  set: (v) => {
    emit('update:selectedColors', v)
    emitFiltersChanged()
  },
})

// Computed for selected brands with getter/setter
const selectedBrands = computed({
  get: () => props.selectedBrands,
  set: (v) => {
    emit('update:selectedBrands', v)
    emitFiltersChanged()
  },
})

// Computed for selected discount statuses with getter/setter
const selectedDiscountStatuses = computed({
  get: () => props.selectedDiscountStatuses,
  set: (v) => {
    emit('update:selectedDiscountStatuses', v)
    emitFiltersChanged()
  },
})

// Computed for selected category with getter/setter
const selectedCategory = computed({
  get: () => props.selectedCategory,
  set: (v) => {
    emit('update:selectedCategory', v)
    emitFiltersChanged()
  },
})

// Function to emit filter changes
const emitFiltersChanged = () => {
  emit('filters-changed', {
    priceRange: selectedPriceRange.value,
    colors: selectedColors.value,
    brands: selectedBrands.value,
    discountStatuses: selectedDiscountStatuses.value,
    category: selectedCategory.value,
  })
}

// Function to clear all filters
const clearAllFilters = () => {
  emit('update:selectedPriceRange', '')
  emit('update:selectedColors', [])
  emit('update:selectedBrands', [])
  emit('update:selectedDiscountStatuses', [])
  emit('update:selectedCategory', '')
  emit('clear-filters')
  emitFiltersChanged()
}

// Function to toggle an item in an array filter
const toggleFilter = (array, value) => {
  const current = [...array]
  const index = current.indexOf(value)
  if (index > -1) {
    current.splice(index, 1) // Remove if exists
  } else {
    current.push(value) // Add if not exists
  }
  return current
}

// Function to toggle color filter
const toggleColorFilter = (color) => {
  emit('update:selectedColors', toggleFilter(props.selectedColors, color))
  emitFiltersChanged()
}

// Function to toggle brand filter
const toggleBrandFilter = (brand) => {
  emit('update:selectedBrands', toggleFilter(props.selectedBrands, brand))
  emitFiltersChanged()
}

// Function to toggle discount filter
const toggleDiscountFilter = (status) => {
  emit('update:selectedDiscountStatuses', toggleFilter(props.selectedDiscountStatuses, status))
  emitFiltersChanged()
}

// Expose price ranges for parent component
defineExpose({ priceRanges })
</script>

<style scoped>
/* Filters Sidebar */
.filters-sidebar {
  width: 280px;
  background: white;
  border: 1px solid #e5e5e5;
  border-radius: 12px;
  padding: 24px;
  height: fit-content;
  position: sticky;
  top: 20px;
}

/* Filters Header */
.filters-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 24px;
  padding-bottom: 16px;
  border-bottom: 1px solid #f0f0f0;
}

.filters-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
}

.clear-filters-btn {
  background: none;
  border: none;
  color: #6366f1;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  padding: 4px 8px;
  border-radius: 6px;
  transition: background-color 0.2s ease;
}

.clear-filters-btn:hover {
  background: #f0f0ff;
}

/* Filter Section */
.filter-section {
  margin-top: 32px;
}

.filter-section h4 {
  margin: 0 0 16px 0;
  font-size: 16px;
  font-weight: 600;
  color: #374151;
}

/* Filter Options */
.filter-options {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.filter-option {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  font-size: 14px;
  color: #4b5563;
  position: relative;
}

.filter-option input {
  opacity: 0;
  position: absolute;
}

/* Checkmark */
.checkmark {
  width: 18px;
  height: 18px;
  border: 2px solid #d1d5db;
  border-radius: 50%;
  position: relative;
  transition: all 0.2s ease;
  flex-shrink: 0;
}

.checkmark.checkbox {
  border-radius: 4px;
}

.filter-option input:checked + .checkmark {
  background: #6366f1;
  border-color: #6366f1;
}

.filter-option input:checked + .checkmark:after {
  content: '';
  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%);
  width: 6px;
  height: 6px;
  background: white;
  border-radius: 50%;
}

.filter-option input:checked + .checkmark.checkbox:after {
  width: 4px;
  height: 8px;
  border: solid white;
  border-width: 0 2px 2px 0;
  background: none;
  border-radius: 0;
  transform: translate(-50%, -60%) rotate(45deg);
}
</style>
