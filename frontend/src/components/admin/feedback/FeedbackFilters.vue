<template>
  <div class="filters-section">
    <div class="search-box">
      <input
        v-model="customerSearch"
        type="text"
        placeholder="Search by customer name..."
        class="search-input"
        @input="debouncedSearch"
      />
      <Icon icon="mdi:magnify" class="search-icon" />
    </div>

    <div class="filter-row">
      <select v-model="ratingFilter" @change="applyFilters" class="filter-select">
        <option value="">All Ratings</option>
        <option value="5">5 Stars</option>
        <option value="4">4 Stars</option>
        <option value="3">3 Stars</option>
        <option value="2">2 Stars</option>
        <option value="1">1 Star</option>
      </select>

      <select v-model="categoryFilter" @change="applyFilters" class="filter-select">
        <option value="">All Categories</option>
        <option value="mountain">Mountain Bike</option>
        <option value="road">Road Bike</option>
      </select>

      <select v-model="brandFilter" @change="applyFilters" class="filter-select">
        <option value="">All Brands</option>
        <option value="Trek">Trek</option>
        <option value="Giant">Giant</option>
        <option value="Specialized">Specialized</option>
        <option value="Cannondale">Cannondale</option>
        <option value="Santa Cruz">Santa Cruz</option>
      </select>

      <input
        v-model="dateFilter"
        type="date"
        @change="applyFilters"
        class="filter-input"
        placeholder="Select date"
      />

      <button @click="clearFilters" class="btn btn-secondary clear-filters">Clear Filters</button>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Icon } from '@iconify/vue'

// Props
const props = defineProps({
  customerSearch: String,
  ratingFilter: String,
  categoryFilter: String,
  brandFilter: String,
  dateFilter: String,
})

// Emits
const emit = defineEmits([
  'update:customerSearch',
  'update:ratingFilter',
  'update:categoryFilter',
  'update:brandFilter',
  'update:dateFilter',
  'clearFilters',
])

// State
let searchTimeout = ref(null)

// Computed properties for v-model
const customerSearch = computed({
  get: () => props.customerSearch,
  set: (value) => emit('update:customerSearch', value),
})

const ratingFilter = computed({
  get: () => props.ratingFilter,
  set: (value) => emit('update:ratingFilter', value),
})

const categoryFilter = computed({
  get: () => props.categoryFilter,
  set: (value) => emit('update:categoryFilter', value),
})

const brandFilter = computed({
  get: () => props.brandFilter,
  set: (value) => emit('update:brandFilter', value),
})

const dateFilter = computed({
  get: () => props.dateFilter,
  set: (value) => emit('update:dateFilter', value),
})

// Methods
const debouncedSearch = () => {
  clearTimeout(searchTimeout.value)
  searchTimeout.value = setTimeout(() => {
    // Search is handled by v-model
  }, 500)
}

const applyFilters = () => {
  // Filters are applied automatically through v-model
}

const clearFilters = () => {
  emit('clearFilters')
}
</script>

<style scoped>
.filters-section {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.search-box {
  position: relative;
  margin-bottom: 1rem;
}

.search-input {
  width: 100%;
  padding: 0.75rem 2.5rem 0.75rem 1rem;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  font-size: 0.9rem;
  transition: border-color 0.2s;
  max-width: -webkit-fill-available;
}

.search-input:focus {
  outline: none;
  border-color: #14c9c9;
}

.search-icon {
  position: absolute;
  right: 0.75rem;
  top: 50%;
  transform: translateY(-50%);
  color: #666;
  font-size: 1.2rem;
}

.filter-row {
  display: flex;
  gap: 1rem;
  align-items: center;
  flex-wrap: wrap;
}

.filter-select {
  padding: 0.5rem;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  font-size: 0.9rem;
  min-width: 150px;
  background: white;
}

.filter-select:focus {
  outline: none;
  border-color: #14c9c9;
}

.filter-input {
  padding: 0.5rem;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  font-size: 0.9rem;
  min-width: 150px;
  background: white;
}

.filter-input:focus {
  outline: none;
  border-color: #14c9c9;
}

.clear-filters {
  margin-left: auto;
}

.btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-primary {
  background: #14c9c9;
  color: white;
}

.btn-primary:hover {
  background: #0fa5a5;
}

.btn-secondary {
  background: #f8f9fa;
  color: #333;
  border: 1px solid #dee2e6;
}

.btn-secondary:hover {
  background: #e9ecef;
}

/* Responsive Design */
@media (max-width: 768px) {
  .filter-row {
    flex-direction: column;
    align-items: stretch;
  }

  .filter-select,
  .filter-input {
    min-width: auto;
    width: 100%;
  }

  .clear-filters {
    margin-left: 0;
    margin-top: 0.5rem;
    align-self: flex-start;
  }
}
</style>
