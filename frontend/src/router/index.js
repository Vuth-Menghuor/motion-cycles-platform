import { createRouter, createWebHistory } from 'vue-router'
import Authentication_page from '@/views/authentication_page.vue'
import Home_view from '@/views/home_view.vue'
import Brand_page from '@/views/brand_page.vue'
import Bike_detail from '@/views/bike_detail.vue'
import Default_layout from '@/layouts/Default_layout.vue'
import Gallery_image from '@/components/bike_detail/gallery_image.vue'
import Favorite_page from '@/views/favorite_page.vue'
import Checkout_cart from '@/views/checkout/checkout_cart.vue'
import Checkout_address from '@/views/checkout/checkout_address.vue'
import Checkout_payment from '@/views/checkout/checkout_payment.vue'
import Checkout_purchase from '@/views/checkout/checkout_purchase.vue'
import PaymentView from '@/views/PaymentView.vue'
import Purchase_summary from '@/views/checkout/purchase_summary.vue' // ✅ move to views

const routes = [
  {
    path: '/',
    component: Default_layout,
    children: [
      {
        path: '/',
        name: 'Home',
        component: Home_view,
      },
      {
        path: '/brands/:brandName',
        name: 'BrandPage',
        component: Brand_page,
        props: true,
      },
      {
        path: '/bike/:id',
        name: 'BikeDetail',
        component: Bike_detail,
        props: true,
      },
      {
        path: '/bike/:id/gallery',
        name: 'BikeGallery',
        component: Gallery_image,
        props: true,
      },
      {
        path: '/favorites',
        name: 'Favorites',
        component: Favorite_page,
      },
      {
        path: '/payment/khqr',
        name: 'KHQRPayment',
        component: PaymentView,
        meta: {
          title: 'KHQR Payment',
        },
      },
      {
        path: '/checkout/cart',
        name: 'ShoppingCart',
        component: Checkout_cart,
        meta: {
          title: 'Shopping Cart',
          step: 1,
          requiresCart: true,
        },
      },
      {
        path: '/checkout/address',
        name: 'CheckoutAddress',
        component: Checkout_address,
        meta: {
          title: 'Delivery Address',
          step: 2,
          requiresCart: true,
          requiresAuth: false,
        },
      },
      {
        path: '/checkout/payment',
        name: 'CheckoutPayment',
        component: Checkout_payment,
        meta: {
          title: 'Payment Method',
          step: 3,
          requiresCart: true,
          requiresAddress: true,
        },
      },
      {
        path: '/checkout/purchase',
        name: 'CheckoutPurchase',
        component: Checkout_purchase,
        meta: {
          title: 'Purchase',
          step: 4,
          requiresCart: true,
          requiresAddress: true,
          requiresPayment: true,
        },
      },
      {
        path: '/checkout/purchase/summary',
        name: 'PurchaseSummary',
        component: Purchase_summary,
        meta: {
          title: 'Purchase Summary',
          step: 4,
          requiresCart: true,
          requiresAddress: true,
          requiresPayment: true,
        },
      },
    ],
  },

  {
    path: '/authentication/:type',
    name: 'Authentication',
    component: Authentication_page,
    props: true,
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

export default router
