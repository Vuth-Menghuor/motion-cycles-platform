<template>
  <div v-if="selectedCount > 0" class="bulk-actions">
    <span class="selected-count">{{ selectedCount }} feedback(s) selected</span>
    <div class="bulk-buttons">
      <button @click="$emit('mark-reviewed')" class="btn-bulk btn-bulk-reviewed">
        <Icon icon="mdi:check-circle" />
        Mark as Reviewed
      </button>
      <button @click="$emit('delete-selected')" class="btn-bulk btn-bulk-delete">
        <Icon icon="mdi:delete" />
        Delete Selected
      </button>
    </div>
  </div>
</template>

<script setup>
import { Icon } from '@iconify/vue'

// Props
defineProps({
  selectedCount: {
    type: Number,
    required: true,
  },
})

// Emits
defineEmits(['mark-reviewed', 'delete-selected'])
</script>

<style scoped>
.bulk-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #ffffff;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  padding: 16px 20px;
  margin-top: 20px;
}

.selected-count {
  font-weight: 500;
  color: #4a5568;
  font-family: 'Poppins', sans-serif;
}

.bulk-buttons {
  display: flex;
  gap: 12px;
}

.btn-bulk {
  padding: 10px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s ease;
}

.btn-bulk-reviewed {
  background-color: #38a169;
  color: white;
}

.btn-bulk-reviewed:hover {
  background-color: #2f855a;
  transform: translateY(-1px);
}

.btn-bulk-delete {
  background-color: #e53e3e;
  color: white;
}

.btn-bulk-delete:hover {
  background-color: #c53030;
  transform: translateY(-1px);
}

@media (max-width: 768px) {
  .bulk-actions {
    flex-direction: column;
    gap: 12px;
    align-items: stretch;
  }

  .bulk-buttons {
    justify-content: center;
  }
}
</style>
