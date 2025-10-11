import axios from 'axios'
import { defineStore } from 'pinia'

export const useAuthStore = defineStore('auth', () => {
  // Function to log in a user
  async function login(email, password) {
    try {
      const result = await axios.post('/api/auth/login', { email, password })
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

  // Function to register a new user
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

  // Function to log out the current user
  function logout() {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    localStorage.removeItem('userRole')
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
