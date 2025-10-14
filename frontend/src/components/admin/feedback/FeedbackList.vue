<template>
  <div class="feedback-list">
    <FeedbackCard
      v-for="feedback in feedbacks"
      :key="feedback.id"
      :feedback="feedback"
      @view="$emit('view', $event)"
      @respond="$emit('respond', $event)"
      @update-review="$emit('update-review', $event)"
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
defineEmits(['view', 'respond', 'update-review', 'delete', 'toggle-select'])
</script>

<style scoped>
.feedback-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 20px;
}
</style>
