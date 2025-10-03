import axios from 'axios'
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', () => {
  // Login function
  async function login(email, password) {
    try {
      const result = await axios.post('/api/auth/login', { email, password }) // Changed this line
      const token = result.data.data.token
      const user = result.data.data.user

      localStorage.setItem('token', token)
      localStorage.setItem('user', JSON.stringify(user))
      localStorage.setItem('userRole', user.role || 'user')

      console.log('Login Successful:', result)
      return result.data
    } catch (e) {
      console.error('Auth store login error:', e)
      throw e
    }
  }

  // Register function
  async function register(payload) {
    try {
      const result = await axios.post(`/api/auth/register`, payload)
      console.log('Register Successful:', result)
      return result.data
    } catch (e) {
      console.error('Auth store register error:', e)
      throw e
    }
  }

  // Logout function
  function logout() {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    localStorage.removeItem('userRole')
  }

  // Getters
  function getUser() {
    return JSON.parse(localStorage.getItem('user'))
  }

  function isLoggedIn() {
    return !!localStorage.getItem('token')
  }

  function getRole() {
    return localStorage.getItem('userRole') || 'user'
  }

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
