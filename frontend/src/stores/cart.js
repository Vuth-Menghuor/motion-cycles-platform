import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

export const useCartStore = defineStore('cart', () => {
  // State: Array to hold cart items, each with product details and quantity
  const cartItems = ref([])

  // Getters: Computed properties based on state
  const count = computed(() => {
    // Calculates total number of items in cart by summing quantities
    return cartItems.value.reduce((total, item) => total + item.quantity, 0)
  })

  // Actions: Methods to modify the cart state

  // addItem: Adds a product to the cart or increases quantity if it already exists
  function addItem(product) {
    const existingItem = cartItems.value.find((item) => item.id === product.id)
    if (existingItem) {
      // If item exists, increase its quantity
      existingItem.quantity++
    } else {
      // If new item, add it with calculated price and default quantity
      const originalPrice = parseFloat(product.price) || 0
      cartItems.value.push({
        ...product,
        name: product.title, // Use product title as name
        quantity: 1,
        price: calculateDiscountedPrice({ ...product, price: originalPrice }), // Apply discount if any
        originalPrice: originalPrice,
      })
    }
  }

  // removeItem: Removes an item from the cart by its ID
  function removeItem(itemId) {
    cartItems.value = cartItems.value.filter((item) => item.id !== itemId)
  }

  // increaseQuantity: Increases the quantity of a specific item by 1
  function increaseQuantity(itemId) {
    const item = cartItems.value.find((i) => i.id === itemId)
    if (item) {
      item.quantity++
    }
  }

  // decreaseQuantity: Decreases the quantity of a specific item by 1, but not below 1
  function decreaseQuantity(itemId) {
    const item = cartItems.value.find((i) => i.id === itemId)
    if (item && item.quantity > 1) {
      item.quantity--
    }
  }

  // clearCart: Empties the entire cart
  function clearCart() {
    cartItems.value = []
  }

  // Private helper function: Calculates the discounted price for a product
  function calculateDiscountedPrice(product) {
    const price = parseFloat(product.price) || 0
    if (!product.discount || !Array.isArray(product.discount) || product.discount.length === 0) {
      // No discount, return original price
      return price
    }
    const discount = product.discount[0] // Take the first discount if it's an array
    if (discount.type === 'percent') {
      // Percentage discount
      return price - (price * discount.value) / 100
    } else {
      // Fixed amount discount
      return price - discount.value
    }
  }

  // Expose state, getters, and actions for use in components
  return { cartItems, count, addItem, removeItem, increaseQuantity, decreaseQuantity, clearCart }
})
