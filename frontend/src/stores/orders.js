import { defineStore } from 'pinia'
import { ordersApi } from '@/services/api'

export const useOrdersStore = defineStore('orders', {
  state: () => ({
    orders: [],
    currentOrder: null,
    loading: false,
    error: null,
  }),

  getters: {
    userOrders: (state) => state.orders,
    orderById: (state) => (id) => state.orders.find((order) => order.id === id),
    ordersCount: (state) => state.orders.length,
  },

  actions: {
    async createOrder(orderData) {
      this.loading = true
      this.error = null
      try {
        const response = await ordersApi.createOrder(orderData)
        this.currentOrder = response.data
        return response.data
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchUserOrders() {
      this.loading = true
      this.error = null
      try {
        const response = await ordersApi.listOrders()
        // Handle paginated response
        this.orders = response.data.orders.data || response.data.orders || []
        return this.orders
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    async fetchOrderById(orderId) {
      this.loading = true
      this.error = null
      try {
        const response = await ordersApi.getOrder(orderId)
        this.currentOrder = response.data.order
        return response.data.order
      } catch (error) {
        this.error = error.message
        throw error
      } finally {
        this.loading = false
      }
    },

    clearCurrentOrder() {
      this.currentOrder = null
    },

    clearOrders() {
      this.orders = []
      this.currentOrder = null
      this.error = null
    },
  },
})
