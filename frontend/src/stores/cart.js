// src/stores/cart.js
import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCartStore = defineStore('cart', () => {
  // State: holds the array of products in the cart
  const cartItems = ref([])

  // Getters: computed properties based on state
  const count = computed(() => {
    // This now correctly reflects the total number of items in the cart
    return cartItems.value.reduce((total, item) => total + item.quantity, 0)
  })

  // Actions: methods to modify the state
  function addItem(product) {
    const existingItem = cartItems.value.find((item) => item.id === product.id)
    if (existingItem) {
      existingItem.quantity++
    } else {
      cartItems.value.push({
        ...product,
        // This is the missing line that populates the item name
        name: product.title,
        quantity: 1,
        price: calculateDiscountedPrice(product),
        originalPrice: product.price,
      })
    }
  }

  function removeItem(itemId) {
    cartItems.value = cartItems.value.filter((item) => item.id !== itemId)
  }

  function increaseQuantity(itemId) {
    const item = cartItems.value.find((i) => i.id === itemId)
    if (item) item.quantity++
  }

  function decreaseQuantity(itemId) {
    const item = cartItems.value.find((i) => i.id === itemId)
    if (item && item.quantity > 1) item.quantity--
  }

  // Private helper function to calculate the discounted price
  function calculateDiscountedPrice(product) {
    if (!product.discount) return product.price
    if (product.discount.type === 'percent') {
      return product.price - (product.price * product.discount.value) / 100
    } else {
      return product.price - product.discount.value
    }
  }

  // Expose the state and actions you want to use in your components
  return { cartItems, count, addItem, removeItem, increaseQuantity, decreaseQuantity }
})
