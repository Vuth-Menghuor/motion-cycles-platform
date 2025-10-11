<template>
  <div v-if="visible" class="confirm-overlay">
    <div class="confirm-box">
      <h3 class="confirm-title">{{ title }}</h3>
      <p class="confirm-message">{{ message }}</p>
      <div class="confirm-actions">
        <button class="btn btn-secondary" @click="$emit('cancel')">Cancel</button>
        <button class="btn btn-danger" @click="$emit('confirm')">Yes, Remove</button>
      </div>
    </div>
  </div>
</template>

<script setup>
// Define the props for the confirmation dialog
defineProps({
  visible: Boolean,
  title: String,
  message: String,
})
</script>

<style scoped>
/* Overlay */
.confirm-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2000;
}

/* Dialog Box */
.confirm-box {
  background: white;
  padding: 24px;
  border-radius: 10px;
  max-width: 400px;
  width: 100%;
  text-align: center;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}

/* Title */
.confirm-title {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 12px;
}

/* Message */
.confirm-message {
  font-size: 14px;
  margin-bottom: 20px;
  color: #444;
}

/* Actions */
.confirm-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
}

/* Buttons */
.btn {
  padding: 6px 14px;
  border-radius: 4px;
  outline: none;
  font-size: 12px;
  font-family: 'Poppins', sans-serif;
  border: 1px solid grey;
}

.btn-danger {
  background: #ff4d4f;
  color: white;
  border: none;
}

.btn-danger:hover {
  background: #e63946;
}
</style>
