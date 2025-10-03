import { defineStore } from 'pinia'
// import bike1 from '@/assets/images/product_card/mount_1.png'
// import bike2 from '@/assets/images/product_card/mount_2/mount_2.png'
// import bike3 from '@/assets/images/product_card/mount_3.png'

export const useFavoritesStore = defineStore('favorites', {
  state: () => ({
    favorites: [
      // {
      //   id: 101,
      //   title: 'Bianchi T-Tronik C Type - Sunrace (2023)',
      //   subtitle: 'Bianchi',
      //   price: 24400,
      //   image: bike1,
      //   color: 'Pink',
      //   rating: 4.8,
      //   reviewCount: 3221,
      //   badge: {
      //     text: 'Hot',
      //     icon: 'mdi:fire',
      //     gradient: 'linear-gradient(135deg, rgb(255, 107, 107), rgb(255, 82, 82))',
      //   },
      //   discount: {
      //     type: 'percent',
      //     value: 10,
      //   },
      // },
      // {
      //   id: 102,
      //   title: 'Trek Slash 9.8 XT Carbon',
      //   subtitle: 'Trek',
      //   price: 44299,
      //   image: bike2,
      //   color: 'Orange',
      //   rating: 2.4,
      //   reviewCount: 3221,
      //   badge: {
      //     text: 'New',
      //     icon: 'material-symbols-light:new-releases',
      //     gradient: 'linear-gradient(135deg, #3491FA, #3491FA)',
      //   },
      //   discount: {
      //     type: 'fixed',
      //     value: 399,
      //   },
      // },
      // {
      //   id: 103,
      //   title: 'Santa Cruz Hightower CC X01',
      //   subtitle: 'Santa Cruz',
      //   price: 29999,
      //   image: bike3,
      //   color: 'Grey',
      //   rating: 5,
      //   reviewCount: 3221,
      //   badge: null, // No badge
      //   discount: null, // No discount
      // },
    ],
  }),

  actions: {
    addFavorite(bike) {
      if (!this.favorites.find((fav) => fav.id === bike.id)) {
        this.favorites.push(bike)
      }
    },
    removeFavorite(bikeId) {
      this.favorites = this.favorites.filter((fav) => fav.id !== bikeId)
    },
    toggleFavorite(bike) {
      if (this.favorites.find((fav) => fav.id === bike.id)) {
        this.removeFavorite(bike.id)
      } else {
        this.addFavorite(bike)
      }
    },
    isFavorited(bikeId) {
      return this.favorites.some((fav) => fav.id === bikeId)
    },
  },
})
