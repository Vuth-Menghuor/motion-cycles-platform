import { defineStore } from 'pinia'
import { cartApi } from '@/services/api'

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: [],
    loading: false,
    count: 0
  }),

  getters: {
    totalItems: (state) => state.items.reduce((total, item) => total + item.quantity, 0),
    totalPrice: (state) => state.items.reduce((total, item) => total + (item.product.pricing * item.quantity), 0)
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
        // Update local state
        const existingItem = this.items.find(item => item.product_id === productId)
        if (existingItem) {
          existingItem.quantity = response.data.quantity
        } else {
          this.items.push(response.data)
        }
        this.updateCount()
        return response.data
      } catch (error) {
        console.error('Error adding to cart:', error)
        throw error
      }
    },

    async updateCartItem(cartId, quantity) {
      try {
        const response = await cartApi.updateCartItem(cartId, quantity)
        // Update local state
        const item = this.items.find(item => item.id === cartId)
        if (item) {
          item.quantity = quantity
        }
        return response.data
      } catch (error) {
        console.error('Error updating cart item:', error)
        throw error
      }
    },

    async removeFromCart(cartId) {
      try {
        await cartApi.removeFromCart(cartId)
        // Update local state
        this.items = this.items.filter(item => item.id !== cartId)
        this.updateCount()
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
    }
  }
})
