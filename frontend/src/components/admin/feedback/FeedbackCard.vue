<template>
  <div class="feedback-card" :class="{ selected: feedback.selected }" @click="handleCardClick">
    <div class="card-content">
      <div class="top-row">
        <div class="customer-info">
          <div class="customer-avatar">
            {{ getInitials(feedback.customerName) }}
          </div>
          <div class="customer-name">
            {{ feedback.customerName }}
          </div>
        </div>

        <div class="feedback-date">{{ formatDate(feedback.date) }}</div>
      </div>

      <div class="rating-section">
        <div v-if="!isEditing" class="rating-stars">
          <span
            v-for="star in 5"
            :key="star"
            class="star"
            :class="{ filled: star <= feedback.rating }"
          >
            ★
          </span>
          <span class="rating-number">({{ feedback.rating }}/5)</span>
        </div>
        <div v-else class="rating-input">
          <label class="rating-label">Rating:</label>
          <select v-model="editRating" class="rating-select">
            <option value="1">1 ★</option>
            <option value="2">2 ★★</option>
            <option value="3">3 ★★★</option>
            <option value="4">4 ★★★★</option>
            <option value="5">5 ★★★★★</option>
          </select>
        </div>
      </div>

      <div class="comment-section">
        <div v-if="!isEditing" class="comment-text">"{{ feedback.comment }}"</div>
        <div v-else class="comment-input">
          <label class="comment-label">Comment:</label>
          <textarea
            v-model="editComment"
            class="comment-textarea"
            placeholder="Enter your review comment..."
            rows="3"
          ></textarea>
        </div>
      </div>
    </div>

    <div class="card-actions">
      <template v-if="!isEditing">
        <button @click="startEdit" class="btn-action btn-update" title="Update Feedback">
          <Icon icon="mdi:pencil" />
          Update
        </button>
        <button
          @click="$emit('delete', feedback.id)"
          class="btn-action btn-delete"
          title="Delete Feedback"
        >
          <Icon icon="mdi:delete" />
          Delete
        </button>
      </template>
      <template v-else>
        <button @click="cancelEdit" class="btn-action btn-cancel" title="Cancel Edit">
          <Icon icon="mdi:close" />
          Cancel
        </button>
        <button
          @click="saveEdit"
          class="btn-action btn-save"
          title="Save Changes"
          :disabled="saving"
        >
          <Icon v-if="!saving" icon="mdi:check" />
          <Icon v-else icon="mdi:loading" class="spin" />
          {{ saving ? 'Saving...' : 'Save' }}
        </button>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Icon } from '@iconify/vue'

// Props
const props = defineProps({
  feedback: {
    type: Object,
    required: true,
  },
})

// Emits
const emit = defineEmits(['delete', 'toggle-select', 'update-review'])

// Reactive state for editing
const isEditing = ref(false)
const editRating = ref(5)
const editComment = ref('')
const saving = ref(false)

// Methods
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const getInitials = (name) => {
  if (!name) return 'U'
  return name
    .split(' ')
    .map((word) => word.charAt(0).toUpperCase())
    .join('')
    .slice(0, 2)
}

const handleCardClick = (event) => {
  // Don't toggle if clicking on buttons
  if (event.target.closest('.card-actions')) {
    return
  }
  emit('toggle-select', props.feedback.id)
}

const startEdit = () => {
  isEditing.value = true
  editRating.value = props.feedback.rating
  editComment.value = props.feedback.comment
}

const cancelEdit = () => {
  isEditing.value = false
  editRating.value = props.feedback.rating
  editComment.value = props.feedback.comment
}

const saveEdit = async () => {
  if (!editComment.value.trim()) {
    alert('Comment cannot be empty')
    return
  }

  saving.value = true
  try {
    await emit('update-review', {
      id: props.feedback.id,
      rating: parseInt(editRating.value),
      comment: editComment.value.trim(),
    })
    isEditing.value = false
  } catch (error) {
    console.error('Failed to update review:', error)
    alert('Failed to update review. Please try again.')
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.feedback-card {
  background: #ffffff;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s ease;
}

.feedback-card.selected {
  border-color: #4299e1;
  box-shadow: 0 0 0 2px rgba(66, 153, 225, 0.2);
}

.card-content {
  padding: 16px 20px;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.top-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.customer-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.customer-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background-color: #3b82f6;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 600;
}

.customer-name {
  font-size: 14px;
  font-weight: 500;
  color: #374151;
}

.feedback-date {
  font-size: 12px;
  color: #6b7280;
  flex-shrink: 0;
}

.rating-section {
  margin-top: 12px;
  width: 100%;
}

.comment-section {
  margin-top: 12px;
  width: 100%;
}

.rating-stars {
  display: flex;
  align-items: center;
  gap: 4px;
}

.star {
  color: #e2e8f0;
  font-size: 18px;
  line-height: 1;
}

.star.filled {
  color: #fbbf24;
}

.rating-number {
  font-size: 14px;
  color: #718096;
  margin-left: 8px;
  font-weight: 500;
}

.comment-text {
  font-size: 14px;
  line-height: 1.5;
  color: #374151;
  background-color: #f9fafb;
  border: 1px solid #e5e7eb;
  border-radius: 4px;
  padding: 8px 12px;
}

.card-actions {
  display: flex;
  gap: 12px;
  padding: 16px 20px;
  background-color: #f7fafc;
  border-top: 1px solid #e2e8f0;
  justify-content: flex-end;
}

.btn-action {
  padding: 8px 16px;
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
  text-decoration: none;
}

.btn-update {
  background-color: #38a169;
  color: white;
}

.btn-delete {
  background-color: #e53e3e;
  color: white;
}

.btn-cancel {
  background-color: #718096;
  color: white;
}

.btn-save {
  background-color: #38a169;
  color: white;
}

.btn-save:disabled {
  background-color: #a0aec0;
  cursor: not-allowed;
  transform: none;
}

.rating-input {
  display: flex;
  align-items: center;
  gap: 12px;
}

.rating-label {
  font-size: 14px;
  font-weight: 500;
  color: #2d3748;
  min-width: 50px;
}

.rating-select {
  padding: 6px 12px;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  font-size: 14px;
  background-color: white;
  cursor: pointer;
  min-width: 120px;
}

.rating-select:focus {
  outline: none;
  border-color: #4299e1;
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

.comment-input {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.comment-label {
  font-size: 14px;
  font-weight: 500;
  color: #2d3748;
}

.comment-textarea {
  padding: 12px 16px;
  border: 1px solid #e2e8f0;
  border-radius: 4px;
  font-size: 14px;
  line-height: 1.5;
  font-family: 'Poppins', sans-serif;
  resize: vertical;
  min-height: 80px;
}

.comment-textarea:focus {
  outline: none;
  border-color: #4299e1;
  box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.1);
}

.comment-textarea::placeholder {
  color: #a0aec0;
}

.spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 768px) {
  .card-content {
    padding: 16px;
    gap: 10px;
  }

  .top-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }

  .card-actions {
    padding: 12px 16px;
    flex-direction: column;
    gap: 8px;
  }

  .btn-action {
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .card-content {
    padding: 12px;
    gap: 8px;
  }

  .top-row {
    flex-direction: column;
    align-items: flex-start;
    gap: 6px;
  }

  .card-actions {
    padding: 10px 12px;
  }

  .comment-text {
    font-size: 13px;
    padding: 6px 10px;
  }

  .rating-stars .star {
    font-size: 16px;
  }

  .customer-name,
  .product-name {
    font-size: 13px;
  }

  .feedback-date {
    font-size: 10px;
  }
}
</style>
