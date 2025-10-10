<template>
  <div class="review-submission">
    <h3 class="submission-title">Add Your Rating</h3>

    <div class="star-rating">
      <span
        v-for="i in 5"
        :key="i"
        class="star"
        :class="{ active: i <= selectedRating, hover: i <= hoverRating }"
        @click="selectRating(i)"
        @mouseover="hoverRating = i"
        @mouseleave="hoverRating = 0"
      >
        <Icon icon="ic:round-star" />
      </span>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label for="name" class="form-label">Name</label>
        <input
          id="name"
          v-model="formData.name"
          type="text"
          class="form-input"
          placeholder="Enter your name"
        />
      </div>

      <div class="form-group">
        <label for="email" class="form-label">Email</label>
        <input
          id="email"
          v-model="formData.email"
          type="email"
          class="form-input"
          placeholder="Enter your email"
        />
      </div>
    </div>

    <div class="form-group">
      <label for="review" class="form-label">Write your review here</label>
      <textarea
        id="review"
        v-model="formData.review"
        class="form-textarea"
        placeholder="Write something"
        rows="4"
      ></textarea>
    </div>

    <div class="form-actions">
      <button type="button" class="submit-button" @click="submitReview" :disabled="!isFormValid">
        Submit Review
      </button>
    </div>

    <div v-if="showSuccess" class="success-message">
      <Icon icon="qlementine-icons:success-16" />
      Your review has been submitted successfully!
    </div>
  </div>
</template>

<script setup>
import { Icon } from '@iconify/vue'
import { ref, computed } from 'vue'

const emit = defineEmits(['submit-review'])

const selectedRating = ref(0)
const hoverRating = ref(0)
const showSuccess = ref(false)
const formData = ref({
  name: '',
  email: '',
  review: '',
})

const selectRating = (rating) => {
  selectedRating.value = rating
}

const isFormValid = computed(() => {
  return (
    selectedRating.value > 0 &&
    formData.value.name.trim() &&
    formData.value.email.trim() &&
    formData.value.review.trim()
  )
})

const submitReview = () => {
  if (!isFormValid.value) return

  const reviewData = {
    user: formData.value.name,
    rating: selectedRating.value,
    comment: formData.value.review,
    email: formData.value.email,
    date: new Date().toISOString().split('T')[0],
  }

  emit('submit-review', reviewData)

  showSuccess.value = true
  setTimeout(() => (showSuccess.value = false), 3000)

  selectedRating.value = 0
  formData.value = { name: '', email: '', review: '' }
}
</script>

<style scoped>
.review-submission {
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 24px;
  margin-top: 16px;
  margin-bottom: 24px;
  background-color: #ffffff;
  font-family: 'Poppins', sans-serif;
}

.submission-title {
  font-size: 18px;
  font-weight: 500;
  color: #111827;
  margin: 0 0 16px 0;
}

.star-rating {
  display: flex;
  gap: 4px;
  margin-bottom: 20px;
}

.star {
  font-size: 24px;
  color: #d1d5db;
  cursor: pointer;
  transition: color 0.2s ease;
  user-select: none;
}

.star.active,
.star.hover {
  color: #fbbf24;
}

.star:hover {
  color: #fbbf24;
}

.form-row {
  display: flex;
  gap: 16px;
  margin-bottom: 16px;
}

.form-group {
  flex: 1;
}

.form-group:last-child {
  margin-bottom: 20px;
}

.form-label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: #6b7280;
  margin-bottom: 6px;
}

.form-input,
.form-textarea {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease;
  box-sizing: border-box;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #3b82f6;
}

.form-input::placeholder,
.form-textarea::placeholder {
  color: #9ca3af;
}

.form-textarea {
  resize: vertical;
  min-height: 100px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
}

.submit-button {
  margin-top: 8px;
  background-color: #3b82f6;
  color: white;
  border: none;
  border-radius: 8px;
  padding: 12px 24px;
  font-size: 14px;
  font-weight: 500;
  font-family: 'Poppins', sans-serif;
  cursor: pointer;
  transition:
    background-color 0.2s ease,
    opacity 0.2s ease;
}

.submit-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.submit-button:hover:not(:disabled) {
  background-color: #2563eb;
}

.success-message {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 16px;
  padding: 12px 16px;
  background-color: #d1fae5;
  color: #065f46;
  border: 1px solid #10b981;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  text-align: center;
  animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@media (max-width: 640px) {
  .review-submission {
    padding: 20px;
  }

  .form-row {
    flex-direction: column;
    gap: 16px;
  }

  .star {
    font-size: 20px;
  }

  .form-actions {
    justify-content: stretch;
  }

  .submit-button {
    width: 100%;
  }
}
</style>
