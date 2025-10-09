<template>
  <div class="feedback-list">
    <FeedbackCard
      v-for="feedback in feedbacks"
      :key="feedback.id"
      :feedback="feedback"
      @view="$emit('view', $event)"
      @respond="$emit('respond', $event)"
      @delete="$emit('delete', $event)"
      @toggle-select="$emit('toggle-select', $event)"
    />
  </div>
</template>

<script setup>
import FeedbackCard from './FeedbackCard.vue'

// Props
defineProps({
  feedbacks: {
    type: Array,
    required: true,
  },
})

// Emits
defineEmits(['view', 'respond', 'delete', 'toggle-select'])
</script>

<style scoped>
/* Feedback Cards List */
.feedback-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 20px;
}
</style>
