import { defineStore } from 'pinia'
import { cartApi } from '@/services/api'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    loading: false,
    count: 0,
  }),

  getters: {
    totalItems: (state) => state.items.reduce((total, item) => total + item.quantity, 0),
    totalPrice: (state) =>
      state.items.reduce((total, item) => total + item.product.pricing * item.quantity, 0),
  },

  actions: {
    async fetchCart() {
      this.loading = true
      try {
        const response = await cartApi.getCart()
        this.items = response.data
        this.updateCount()
      } catch (error) {
        console.error('Error fetching cart:', error)
      } finally {
        this.loading = false
      }
    },

    async addToCart(productId, quantity = 1) {
      try {
        const response = await cartApi.addToCart(productId, quantity)
        this.updateLocalCart(response.data)
        this.updateCount()
        this.fetchCartCount()
      } catch (error) {
        console.error('Error adding to cart:', error)
        throw error
      }
    },

    async updateCartItem(cartItemId, quantity) {
      try {
        await cartApi.updateCartItem(cartItemId, quantity)
        const item = this.items.find((item) => item.id === cartItemId)
        if (item) {
          item.quantity = quantity
        }
        this.updateCount()
        this.fetchCartCount()
      } catch (error) {
        console.error('Error updating cart item:', error)
        throw error
      }
    },

    async removeFromCart(cartItemId) {
      try {
        await cartApi.removeFromCart(cartItemId)
        this.items = this.items.filter((item) => item.id !== cartItemId)
        this.updateCount()
        this.fetchCartCount()
      } catch (error) {
        console.error('Error removing from cart:', error)
        throw error
      }
    },

    async clearCart() {
      try {
        await cartApi.clearCart()
        this.items = []
        this.count = 0
      } catch (error) {
        console.error('Error clearing cart:', error)
        throw error
      }
    },

    updateCount() {
      this.count = this.totalItems
    },

    async fetchCartCount() {
      try {
        const response = await cartApi.getCartCount()
        this.count = response.data.count
      } catch (error) {
        // If user is not authenticated, set count to 0
        if (error.response?.status === 401) {
          this.count = 0
        } else {
          console.error('Error fetching cart count:', error)
        }
      }
    },

    updateLocalCart(newItem) {
      const existingItem = this.items.find((item) => item.product_id === newItem.product_id)
      if (existingItem) {
        existingItem.quantity = newItem.quantity
      } else {
        this.items.push(newItem)
      }
    },
  },
})
