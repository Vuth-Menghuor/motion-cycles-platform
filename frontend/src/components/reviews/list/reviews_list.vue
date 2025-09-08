<template>
  <div class="reviews-list">
    <Review_card v-for="(review, index) in reviews" :key="index" :review="review" />
  </div>
</template>

<script setup>
import Review_card from './review_card.vue'

// eslint-disable-next-line no-unused-vars
const props = defineProps({
  reviews: {
    type: Array,
    required: true,
  },
})
</script>

<style scoped>
.reviews-list {
  max-height: 600px;
  overflow-y: auto;
  padding-right: 8px;
}
</style>
