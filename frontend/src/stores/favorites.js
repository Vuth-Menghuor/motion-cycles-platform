import { defineStore } from 'pinia'

export const useFavoritesStore = defineStore('favorites', {
  // State for the store
  state: () => ({
    favorites: [],
  }),

  actions: {
    // Action to add a bike to favorites if not already added
    addFavorite(bike) {
      if (!this.favorites.some((fav) => fav.id === bike.id)) {
        this.favorites.push(bike)
      }
    },
    // Action to remove a bike from favorites
    removeFavorite(bikeId) {
      this.favorites = this.favorites.filter((fav) => fav.id !== bikeId)
    },
    // Action to toggle favorite status of a bike
    toggleFavorite(bike) {
      const exists = this.favorites.some((fav) => fav.id === bike.id)
      if (exists) {
        this.removeFavorite(bike.id)
      } else {
        this.addFavorite(bike)
      }
    },
    // Action to check if a bike is favorited
    isFavorited(bikeId) {
      return this.favorites.some((fav) => fav.id === bikeId)
    },
  },
})
