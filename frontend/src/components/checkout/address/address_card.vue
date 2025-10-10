<template>
  <div class="address-card" :class="{ selected: isSelected }" @click="selectAddress">
    <div class="radio-container">
      <input
        type="radio"
        :id="`address-${address.id}`"
        :name="radioGroupName"
        :checked="isSelected"
        @change="selectAddress"
        class="radio-input"
      />
      <label :for="`address-${address.id}`" class="radio-label"></label>
    </div>

    <div class="address-content">
      <div class="address-header">
        <h3 class="address-name">{{ address.name }}</h3>
        <div class="address-actions">
          <button @click.stop="editAddress" class="action-btn edit-btn">Edit</button>
          <button @click.stop="removeAddress" class="action-btn remove-btn">Remove</button>
        </div>
      </div>
      <p class="address-details">{{ address.fullAddress }}</p>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  address: { type: Object, required: true },
  isSelected: { type: Boolean, default: false },
  radioGroupName: { type: String, default: 'address-selection' },
})

const emit = defineEmits(['select', 'edit', 'remove'])

const selectAddress = () => emit('select', props.address)
const editAddress = () => emit('edit', props.address)
const removeAddress = () => emit('remove', props.address.id)
</script>

<style scoped>
.address-card {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  padding: 20px;
  border: 2px solid #e5e5e5;
  border-radius: 12px;
  background: white;
  cursor: pointer;
  transition: all 0.2s ease;
  margin-bottom: 16px;
}

.address-card:hover {
  border-color: #d1d1d1;
}

.address-card.selected {
  border-color: #14c9c9;
}

.radio-container {
  position: relative;
  flex-shrink: 0;
  margin-top: 2px;
}

.radio-input {
  appearance: none;
  width: 20px;
  height: 20px;
  border: 1px solid #d1d1d1;
  border-radius: 50%;
  background: white;
  cursor: pointer;
  transition: all 0.2s ease;
}

.radio-input:checked {
  border-color: #14c9c9;
  background: #14c9c9;
}

.radio-input:checked::before {
  content: '';
  position: absolute;
  top: 44.5%;
  left: 55%;
  transform: translate(-50%, -50%);
  width: 8px;
  height: 8px;
  background: white;
  border-radius: 50%;
}

.address-content {
  flex: 1;
  min-width: 0;
}

.address-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 8px;
}

.address-name {
  font-size: 16px;
  font-weight: 500;
  color: #2d2d2d;
  margin: 0;
}

.address-actions {
  display: flex;
  gap: 16px;
  flex-shrink: 0;
}

.action-btn {
  background: none;
  border: none;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  padding: 4px 0;
  transition: opacity 0.2s ease;
}

.action-btn:hover {
  opacity: 0.7;
}

.edit-btn {
  color: #3491fa;
}

.remove-btn {
  color: #ff6b6b;
}

.address-details {
  font-size: 14px;
  color: #8e8e8e;
  margin: 0;
  line-height: 1.4;
}

@media (max-width: 768px) {
  .address-card {
    padding: 16px;
  }

  .address-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }

  .address-actions {
    order: -1;
    align-self: flex-end;
  }
}
</style>
