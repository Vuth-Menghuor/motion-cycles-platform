<template>
  <div class="feedback-card" :class="{ selected: feedback.selected }" @click="handleCardClick">
    <div class="card-header">
      <div class="card-checkbox">
        <input
          type="checkbox"
          :checked="feedback.selected"
          @change.stop="$emit('toggle-select', feedback.id)"
          class="checkbox"
        />
      </div>

      <div class="card-info">
        <div class="feedback-id">{{ feedback.id }}</div>
        <div class="feedback-date">{{ formatDate(feedback.date) }}</div>
      </div>

      <div class="card-status">
        <span
          class="status-badge"
          :class="`status-${feedback.status.toLowerCase().replace(' ', '-')}`"
        >
          {{ feedback.status }}
        </span>
      </div>
    </div>

    <div class="card-content">
      <div class="customer-info">
        <div class="customer-name">
          <img
            :src="feedback.customerAvatar"
            :alt="feedback.customerName"
            class="customer-avatar"
          />
          {{ feedback.customerName }}
        </div>
        <div class="product-name">
          <Icon icon="mdi:bike" />
          {{ feedback.productName }}
        </div>
      </div>

      <div class="rating-section">
        <div class="rating-stars">
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
      </div>

      <div class="comment-section">
        <div class="comment-text">"{{ feedback.comment }}"</div>
      </div>
    </div>

    <div class="card-actions">
      <button @click="$emit('view', feedback.id)" class="btn-action btn-view" title="View Feedback">
        <Icon icon="mdi:eye" />
        View
      </button>
      <button
        @click="$emit('respond', feedback.id)"
        class="btn-action btn-respond"
        title="Respond to Feedback"
      >
        <Icon icon="mdi:reply" />
        Respond
      </button>
      <button
        @click="$emit('delete', feedback.id)"
        class="btn-action btn-delete"
        title="Delete Feedback"
      >
        <Icon icon="mdi:delete" />
        Delete
      </button>
    </div>
  </div>
</template>

<script setup>
import { Icon } from '@iconify/vue'

// Props
const props = defineProps({
  feedback: {
    type: Object,
    required: true,
  },
})

// Emits
const emit = defineEmits(['view', 'respond', 'delete', 'toggle-select'])

// Methods
const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  })
}

const handleCardClick = (event) => {
  // Don't toggle if clicking on buttons or checkbox
  if (event.target.closest('.card-actions') || event.target.closest('.card-checkbox')) {
    return
  }
  emit('toggle-select', props.feedback.id)
}
</script>

<style scoped>
/* Feedback Card */
.feedback-card {
  background: #ffffff;
  border-radius: 6px;
  border: 1px solid #e2e8f0;
  overflow: hidden;
  cursor: pointer;
  transition: all 0.2s ease;
}

.feedback-card:hover {
  border-color: #cbd5e0;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.feedback-card.selected {
  border-color: #4299e1;
  box-shadow: 0 0 0 2px rgba(66, 153, 225, 0.2);
}

/* Card Header */
.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  background-color: #f7fafc;
  border-bottom: 1px solid #e2e8f0;
}

.card-checkbox {
  flex-shrink: 0;
}

.card-info {
  flex: 1;
  margin-left: 12px;
}

.feedback-id {
  font-family: 'Monaco', 'Menlo', monospace;
  font-weight: 600;
  color: #2b6cb0;
  font-size: 14px;
}

.feedback-date {
  font-size: 12px;
  color: #718096;
  margin-top: 2px;
}

.card-status {
  flex-shrink: 0;
}

/* Card Content */
.card-content {
  padding: 20px;
}

.customer-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  flex-wrap: wrap;
  gap: 12px;
}

.customer-name,
.product-name {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  color: #4a5568;
  font-weight: 500;
}

.customer-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #e2e8f0;
  flex-shrink: 0;
}

.customer-name svg,
.product-name svg {
  color: #718096;
  flex-shrink: 0;
}

/* Rating Section */
.rating-section {
  margin-bottom: 16px;
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

/* Comment Section */
.comment-section {
  margin-bottom: 16px;
}

.comment-text {
  font-size: 15px;
  line-height: 1.5;
  color: #2d3748;
  font-style: italic;
  background-color: #f7fafc;
  padding: 12px 16px;
  border-radius: 4px;
  border-left: 4px solid #4299e1;
}

/* Card Actions */
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

.btn-view {
  background-color: #4299e1;
  color: white;
}

.btn-view:hover {
  background-color: #3182ce;
  transform: translateY(-1px);
}

.btn-respond {
  background-color: #38a169;
  color: white;
}

.btn-respond:hover {
  background-color: #2f855a;
  transform: translateY(-1px);
}

.btn-delete {
  background-color: #e53e3e;
  color: white;
}

.btn-delete:hover {
  background-color: #c53030;
  transform: translateY(-1px);
}

/* Status Badges */
.status-badge {
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  text-transform: uppercase;
}

.status-pending {
  background-color: #fef5e7;
  color: #f59e0b;
}

.status-reviewed {
  background-color: #e6fffa;
  color: #38b2ac;
}

.status-responded {
  background-color: #ebf8ff;
  color: #4299e1;
}

/* Responsive Design */
@media (max-width: 768px) {
  .card-header {
    padding: 12px 16px;
    flex-direction: column;
    gap: 12px;
    align-items: flex-start;
  }

  .card-info {
    margin-left: 0;
  }

  .card-content {
    padding: 16px;
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
  .card-header {
    padding: 10px 12px;
  }

  .card-content {
    padding: 12px;
  }

  .card-actions {
    padding: 10px 12px;
  }

  .comment-text {
    font-size: 14px;
    padding: 10px 12px;
  }

  .rating-stars .star {
    font-size: 16px;
  }

  .customer-name,
  .product-name {
    font-size: 13px;
  }
}
</style>
