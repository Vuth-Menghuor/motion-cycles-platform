import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import { favoritesApi } from '@/services/api'

/**
 * Favorites Store - Manages user favorites with backend persistence
 *
 * Features:
 * - Add/remove products to/from favorites
 * - Fetch user's favorites from backend
 * - Check if product is favorited
 * - Toggle favorite status
 * - Sync with backend API
 *
 * Usage:
 * ```js
 * import { useFavoritesStore } from '@/stores/favorites'
 *
 * const favoritesStore = useFavoritesStore()
 *
 * // Initialize favorites on app load
 * await favoritesStore.fetchFavorites()
 *
 * // Toggle favorite status
 * await favoritesStore.toggleFavorite(productId)
 *
 * // Check if favorited
 * const isFav = favoritesStore.isFavorited(productId)
 * ```
 */
export const useFavoritesStore = defineStore('favorites', () => {
  // State
  const favorites = ref([])
  const loading = ref(false)
  const error = ref(null)

  // Getters
  const favoriteProductIds = computed(() => {
    return favorites.value.map((fav) => fav.product_id)
  })

  const favoriteCount = computed(() => {
    return favorites.value.length
  })

  // Actions

  /**
   * Fetch user's favorites from backend
   */
  async function fetchFavorites() {
    // Check if user is authenticated
    const token = localStorage.getItem('token')
    if (!token) {
      console.log('User not authenticated, skipping favorites fetch')
      return []
    }

    loading.value = true
    error.value = null

    try {
      const response = await favoritesApi.getFavorites()
      favorites.value = response.data.data || []
      return favorites.value
    } catch (err) {
      // If unauthorized, just clear favorites and don't show error
      if (err.response?.status === 401) {
        console.log('User not authenticated for favorites')
        favorites.value = []
        return []
      }

      error.value = err.response?.data?.message || 'Failed to fetch favorites'
      console.error('Error fetching favorites:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Add product to favorites
   */
  async function addToFavorites(productId) {
    // Check if user is authenticated
    const token = localStorage.getItem('token')
    if (!token) {
      throw new Error('Please log in to add favorites')
    }

    loading.value = true
    error.value = null

    try {
      const response = await favoritesApi.addToFavorites(productId)

      // Add to local state
      if (!favorites.value.some((fav) => fav.product_id === productId)) {
        favorites.value.push({
          id: response.data.data.id,
          product_id: productId,
          product: response.data.data.product,
          created_at: response.data.data.created_at,
        })
      }

      return response.data
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to add to favorites'
      console.error('Error adding to favorites:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Remove product from favorites
   */
  async function removeFromFavorites(productId) {
    // Check if user is authenticated
    const token = localStorage.getItem('token')
    if (!token) {
      throw new Error('Please log in to manage favorites')
    }

    loading.value = true
    error.value = null

    try {
      await favoritesApi.removeFromFavorites(productId)

      // Remove from local state
      favorites.value = favorites.value.filter((fav) => fav.product_id !== productId)

      return true
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to remove from favorites'
      console.error('Error removing from favorites:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  /**
   * Toggle favorite status for a product
   */
  async function toggleFavorite(productId) {
    // Check if user is authenticated
    const token = localStorage.getItem('token')
    if (!token) {
      throw new Error('Please log in to manage favorites')
    }

    if (isFavorited(productId)) {
      await removeFromFavorites(productId)
      return false // Now not favorited
    } else {
      await addToFavorites(productId)
      return true // Now favorited
    }
  }

  /**
   * Check if a product is favorited
   */
  function isFavorited(productId) {
    return favoriteProductIds.value.includes(productId)
  }

  /**
   * Get favorite by product ID
   */
  function getFavoriteByProductId(productId) {
    return favorites.value.find((fav) => fav.product_id === productId)
  }

  /**
   * Clear all favorites (useful for logout)
   */
  function clearFavorites() {
    favorites.value = []
    error.value = null
  }

  /**
   * Clear error state
   */
  function clearError() {
    error.value = null
  }

  return {
    // State
    favorites,
    loading,
    error,

    // Getters
    favoriteProductIds,
    favoriteCount,

    // Actions
    fetchFavorites,
    addToFavorites,
    removeFromFavorites,
    toggleFavorite,
    isFavorited,
    getFavoriteByProductId,
    clearFavorites,
    clearError,
  }
})
