import { defineStore } from 'pinia'
import api from '@/services/api.js'
import { useFavoritesStore } from './favorites'
import { useCartStore } from './cart'
import { useOrdersStore } from './orders'
import { useRouter } from 'vue-router'

export const useAuthStore = defineStore('auth', () => {
  const router = useRouter()

  // Function to log in a user
  async function login(email, password) {
    try {
      const result = await api.post('/auth/login', { email, password })
      const token = result.data.data.token
      const user = result.data.data.user

      localStorage.setItem('token', token)
      localStorage.setItem('user', JSON.stringify(user))
      localStorage.setItem('userRole', user.role || 'user')

      // Initialize user data after login
      const favoritesStore = useFavoritesStore()
      const cartStore = useCartStore()
      const ordersStore = useOrdersStore()

      // Clear any previous user data and load current user's data
      ordersStore.clearOrders()
      try {
        await favoritesStore.fetchFavorites()
        await cartStore.fetchCart()
        await ordersStore.fetchUserOrders()
      } catch (error) {
        console.warn('Failed to load user data after login:', error)
      }

      // Redirect based on user role
      if (user.role === 'admin') {
        router.push('/admin')
      } else {
        // Redirect to home for regular users
        router.push('/home')
      }

      return result.data
    } catch (e) {
      console.error('Auth store login error:', e)
      throw e
    }
  }

  // Function to register a new user
  async function register(payload) {
    try {
      const result = await api.post('/auth/register', payload)
      return result.data
    } catch (e) {
      console.error('Auth store register error:', e)
      throw e
    }
  }

  // Function to log out the current user
  function logout() {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    localStorage.removeItem('userRole')

    // Clear user data from stores
    const favoritesStore = useFavoritesStore()
    const cartStore = useCartStore()
    const ordersStore = useOrdersStore()

    favoritesStore.clearFavorites()
    cartStore.clearCart()
    ordersStore.clearOrders()
  }

  // Getter to get the current user
  function getUser() {
    return JSON.parse(localStorage.getItem('user'))
  }

  // Getter to check if user is logged in
  function isLoggedIn() {
    return !!localStorage.getItem('token')
  }

  // Getter to get the user's role
  function getRole() {
    return localStorage.getItem('userRole') || 'user'
  }

  // Getter to check if user is admin
  function isAdmin() {
    return getRole() === 'admin'
  }

  return {
    login,
    register,
    logout,
    getUser,
    getRole,
    isLoggedIn,
    isAdmin,
  }
})
