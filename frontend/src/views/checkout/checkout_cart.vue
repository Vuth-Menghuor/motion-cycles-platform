<template>
  <Navigation_header
    :disableAnimation="true"
    :colors="{
      headerBg: 'white',
      boxShadowHeader: '0 4px 20px rgba(0, 0, 0, 0.1)',
      menuIcon: 'black',
      logoName: 'black',
      searchBorder: 'rgba(0, 0, 0, 0.2)',
      searchBg: 'white',
      cartIcon: 'black',
      userIcon: 'black',
      userBorderBtn: 'rgba(0, 0, 0, 0.2)',
      userBgBtn: 'white',
      brandName: 'black',
      brandBorder: '#e5e5e5',
      brandBg: 'white',
    }"
  />
  <div class="cart-container">
    <div class="cart-content">
      <div class="cart-items-section">
        <Indecator_process />
        <Cart_item_card
          v-for="item in cartItems"
          :key="item.id"
          :item="item"
          @increase-quantity="increaseQuantity"
          @decrease-quantity="decreaseQuantity"
          @remove-item="removeItem"
        />
        <Bike_suggestion_card :products="allBikes" @add-to-cart="addToCart" />
      </div>
      <Checkout_summary
        :cart-items="cartItems"
        :promo-code="promoCode"
        @update:promo-code="updatePromoCode"
        @proceed-to-checkout="proceedToCheckout"
        class="checkout-summary-sticky"
      />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useCartStore } from '@/stores/cart'
import { storeToRefs } from 'pinia'
import Navigation_header from '@/components/navigation_header.vue'
import Indecator_process from '@/components/checkout/cart/indecator_process.vue'
import Cart_item_card from '@/components/checkout/cart/cart_item_card.vue'
import Bike_suggestion_card from '@/ui/bike_suggestion_card.vue'
import Checkout_summary from '@/components/checkout/cart/checkout_summary.vue'

const router = useRouter()
const promoCode = ref('')
const cartStore = useCartStore()
const { cartItems } = storeToRefs(cartStore)

const allBikes = ref([
  {
    id: 1,
    title: 'Bianchi T-Tronik C Type - Sunrace (2023)',
    subtitle: 'Bianchi',
    price: 8.99,
    color: 'Pink',
    badge: {
      text: 'Hot',
      icon: 'mdi:hot',
      gradient: 'linear-gradient(135deg, rgb(255, 107, 107), rgb(255, 82, 82))',
    },
    discount: {
      type: 'percent',
      value: 10,
    },
    rating: 4.8,
    reviewCount: 3221,
    image: '/src/assets/images/product_card/mount_1.png',
  },
  {
    id: 2,
    title: 'Trek Slash 9.8 XT Carbon',
    subtitle: 'Trek',
    price: 9.99,
    color: 'Orange',
    badge: {
      text: 'New',
      icon: 'material-symbols-light:new-releases',
      gradient: 'linear-gradient(135deg, #3491FA, #3491FA)',
    },
    discount: {
      type: 'fixed',
      value: 1.5,
    },
    rating: 2.4,
    reviewCount: 3221,
    image: '/src/assets/images/product_card/mount_2/mount_2.png',
  },
  {
    id: 3,
    title: 'Santa Cruz Hightower CC X01',
    subtitle: 'Specialized',
    price: 7.49,
    color: 'Grey',
    badge: {},
    discount: null,
    rating: 5,
    reviewCount: 3221,
    image: '/src/assets/images/product_card/mount_3.png',
  },
  {
    id: 4,
    title: 'Giant TCR Advanced Pro 1',
    subtitle: 'Giant',
    price: 5.99,
    color: 'Black',
    badge: {},
    discount: {
      type: 'percent',
      value: 15,
    },
    rating: 4.8,
    reviewCount: 1120,
    image: '/src/assets/images/product_card/road_1.png',
  },
  {
    id: 5,
    title: 'Specialized Allez Sprint Comp',
    subtitle: 'Specialized',
    price: 6.79,
    color: 'Blue',
    badge: {},
    discount: {
      type: 'fixed',
      value: 0.8,
    },
    rating: 4.7,
    reviewCount: 890,
    image: '/src/assets/images/product_card/road_2.png',
  },
  {
    id: 6,
    title: 'Cannondale Synapse Carbon Disc Ultegra',
    subtitle: 'Cannondale',
    price: 8.49,
    color: 'Red',
    badge: {
      text: 'New',
      icon: 'material-symbols-light:new-releases',
      gradient: 'linear-gradient(135deg, #3491FA, #3491FA)',
    },
    discount: null,
    rating: 3.5,
    reviewCount: 650,
    image: '/src/assets/images/product_card/road_3.png',
  },
])

const increaseQuantity = (item) => {
  cartStore.increaseQuantity(item.id)
}

const decreaseQuantity = (item) => {
  cartStore.decreaseQuantity(item.id)
}

const removeItem = (itemId) => {
  cartStore.removeItem(itemId)
}

const addToCart = (products) => {
  cartStore.addItem(products)
}

const updatePromoCode = (newCode) => {
  promoCode.value = newCode
}

const proceedToCheckout = () => {
  router.push('/checkout/address')
}
</script>

<style scoped>
.cart-container {
  max-width: 1350px;
  margin: 0 auto;
  padding: 20px;
  margin-top: 150px;
  font-family: 'Poppins', sans-serif;
}

.cart-content {
  display: grid;
  grid-template-columns: 1fr 390px;
  gap: 30px;
}

.cart-items-section {
  display: flex;
  flex-direction: column;
  max-width: 879px;
  border-right: 1px solid #d9d9d9;
  padding-right: 50px;
}
.checkout-summary-sticky {
  position: sticky;
  top: 10rem;
  align-self: flex-start;
  height: fit-content;
}
@media (max-width: 768px) {
  .cart-content {
    grid-template-columns: 1fr;
  }
}
</style>
