import { ref, computed } from 'vue'
import { defineStore } from 'pinia'

export const useCounterStore = defineStore('counter', () => {
  // A reactive variable to hold the count, starting at 0
  const count = ref(0)

  // A computed property that doubles the count value
  const doubleCount = computed(() => count.value * 2)

  // Function to increase the count by 1
  function increment() {
    count.value++
  }

  // Return the state and functions for use in components
  return { count, doubleCount, increment }
})
