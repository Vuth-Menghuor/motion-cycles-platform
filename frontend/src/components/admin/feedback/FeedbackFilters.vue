<template>
  <div class="filters-section">
    <div class="filters-container">
      <div class="search-group">
        <label class="filter-label">Search Customer:</label>
        <input
          v-model="customerSearch"
          type="text"
          placeholder="Search by customer name..."
          class="filter-input search-input-wide"
        />
      </div>

      <div class="filter-row">
        <div class="filter-group">
          <label class="filter-label">Rating:</label>
          <select v-model="ratingFilter" class="filter-select">
            <option value="">All Ratings</option>
            <option value="5">5 Stars</option>
            <option value="4">4 Stars</option>
            <option value="3">3 Stars</option>
            <option value="2">2 Stars</option>
            <option value="1">1 Star</option>
          </select>
        </div>

        <div class="filter-group">
          <label class="filter-label">Product:</label>
          <select v-model="productFilter" class="filter-select">
            <option value="Trail Pro Carbon">Trail Pro Carbon</option>
            <option value="Road Race Elite">Road Race Elite</option>
            <option value="Mountain Bike Aluminum">Mountain Bike Aluminum</option>
            <option value="Gravel Sport">Gravel Sport</option>
            <option value="Time Trial Aero">Time Trial Aero</option>
            <option value="Cyclocross Race">Cyclocross Race</option>
            <option value="Touring Comfort">Touring Comfort</option>
            <option value="Downhill Extreme">Downhill Extreme</option>
          </select>
        </div>

        <div class="filter-group">
          <label class="filter-label">Date:</label>
          <input v-model="dateFilter" type="date" class="filter-input" placeholder="Select date" />
        </div>

        <div class="filter-group">
          <button @click="clearFilters" class="btn-clear-filters">
            <Icon icon="mdi:filter-remove" />
            Clear Filters
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Icon } from '@iconify/vue'

// Props
const props = defineProps({
  customerSearch: String,
  ratingFilter: String,
  productFilter: String,
  dateFilter: String,
})

// Emits
const emit = defineEmits([
  'update:customerSearch',
  'update:ratingFilter',
  'update:productFilter',
  'update:dateFilter',
  'clearFilters',
])

// Computed properties for v-model
const customerSearch = computed({
  get: () => props.customerSearch,
  set: (value) => emit('update:customerSearch', value),
})

const ratingFilter = computed({
  get: () => props.ratingFilter,
  set: (value) => emit('update:ratingFilter', value),
})

const productFilter = computed({
  get: () => props.productFilter,
  set: (value) => emit('update:productFilter', value),
})

const dateFilter = computed({
  get: () => props.dateFilter,
  set: (value) => emit('update:dateFilter', value),
})

// Methods
const clearFilters = () => {
  emit('clearFilters')
}
</script>

<style scoped>
/* Filters Section */
.filters-section {
  background: #ffffff;
  border-radius: 6px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 20px;
  margin-bottom: 20px;
}

.filters-container {
  display: flex;
  justify-content: space-between;
  align-items: end;
  flex-wrap: wrap;
  gap: 20px;
}

.search-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  min-width: 200px;
  max-width: 300px;
}

.filter-row {
  display: flex;
  gap: 16px;
  align-items: end;
  flex-wrap: wrap;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
  min-width: 160px;
}

.filter-label {
  font-size: 14px;
  font-weight: 400;
  color: #4a5568;
  font-family: 'Poppins', sans-serif;
}

.filter-select {
  padding: 10px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 400;
  font-family: 'Poppins', sans-serif;
  background-color: white;
  cursor: pointer;
  transition: border-color 0.2s ease;
}

.filter-select:focus {
  outline: none;
  border-color: #4299e1;
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

.filter-input {
  padding: 10px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 400;
  font-family: 'Poppins', sans-serif;
  background-color: white;
  transition: border-color 0.2s ease;
}

.filter-input:focus {
  outline: none;
  border-color: #4299e1;
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

.search-input-wide {
  width: 100%;
}

.btn-clear-filters {
  padding: 10px 16px;
  border: 1px solid #e2e8f0;
  border-radius: 6px;
  background-color: #f7fafc;
  color: #4a5568;
  cursor: pointer;
  font-size: 14px;
  font-weight: 400;
  font-family: 'Poppins', sans-serif;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s ease;
}

.btn-clear-filters:hover {
  background-color: #edf2f7;
  border-color: #cbd5e0;
}

/* Responsive Design */
@media (max-width: 1024px) {
  .filters-container {
    gap: 16px;
  }

  .search-group {
    min-width: 180px;
    max-width: 250px;
  }

  .filter-row {
    gap: 12px;
  }

  .filter-group {
    min-width: 140px;
  }

  .customer-info {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
}

@media (max-width: 768px) {
  .filters-container {
    flex-direction: column;
    gap: 16px;
    justify-content: flex-start;
  }

  .search-group {
    min-width: auto;
    width: 100%;
    max-width: none;
  }

  .filter-row {
    flex-direction: column;
    gap: 12px;
  }

  .filter-group {
    min-width: auto;
    width: 100%;
  }
}
</style>
