<template>
  <div class="rating-row">
    <div class="rating-number">{{ rating }}</div>
    <div class="progress-bar">
      <div
        class="progress-fill"
        :style="{ width: percentage + '%' }"
        :class="`rating-${rating}`"
      ></div>
    </div>
    <div class="count">{{ count }}</div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  rating: { type: Number, required: true },
  count: { type: Number, required: true },
  total: { type: Number, required: true },
})

const percentage = computed(() => (props.total ? Math.round((props.count / props.total) * 100) : 0))
</script>

<style scoped>
.rating-row {
  display: flex;
  align-items: center;
  gap: 16px;
  font-family: 'Poppins', sans-serif;
}

.rating-number {
  font-size: 16px;
  font-weight: 500;
  color: #374151;
  width: 24px;
  text-align: center;
}

.progress-bar {
  flex: 1;
  height: 10px;
  background: #f1f5f9;
  border-radius: 6px;
  overflow: hidden;
  position: relative;
  flex: none;
  width: 100%;
}

.progress-fill {
  height: 100%;
  border-radius: 6px;
  transition: width 0.6s ease;
}

.rating-5,
.rating-4,
.rating-3 {
  background: #22c55e;
}

.rating-2 {
  background: #fbbf24;
}

.rating-1 {
  background: #ef4444;
}

.count {
  font-size: 16px;
  font-weight: 500;
  color: #374151;
  min-width: 32px;
  text-align: right;
}
</style>
