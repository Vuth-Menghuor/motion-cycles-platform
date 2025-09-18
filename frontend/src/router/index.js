import { createRouter, createWebHistory } from 'vue-router'
import Authentication_page from '@/views/authentication_page.vue'
import Home_view from '@/views/home_view.vue'
import Brand_page from '@/views/brand_page.vue'
import Bike_detail from '@/views/bike_detail.vue'
import Default_layout from '@/layouts/Default_layout.vue'
import Gallery_image from '@/components/bike_detail/gallery_image.vue'
import Favorite_page from '@/views/favorite_page.vue'
import Shopping_cart from '@/views/shopping_cart.vue'

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
        path: '/',
        children: [
          {
            path: '/favorites',
            name: 'Favorites',
            component: Favorite_page,
          },
          // Cart Routes
          {
            path: '/cart',
            name: 'ShoppingCart',
            component: Shopping_cart,
            meta: {
              title: 'Shopping Cart',
              step: 1,
              requiresCart: true,
            },
          },
          // Checkout Process Routes
          // {
          //   path: '/checkout',
          //   redirect: '/checkout/address',
          //   component: { template: '<router-view />' }, // A placeholder component for nested routes
          //   children: [
          //     {
          //       path: 'address',
          //       name: 'CheckoutAddress',
          //       component: CheckoutAddress,
          //       meta: {
          //         title: 'Delivery Address',
          //         step: 2,
          //         requiresCart: true,
          //         requiresAuth: true,
          //       },
          //     },
          //     {
          //       path: 'payment',
          //       name: 'CheckoutPayment',
          //       component: CheckoutPayment,
          //       meta: {
          //         title: 'Payment Method',
          //         step: 3,
          //         requiresCart: true,
          //         requiresAuth: true,
          //         requiresAddress: true,
          //       },
          //     },
          //   ],
          // },
          // {
          //   path: '/order-confirmation/:orderId?',
          //   name: 'OrderConfirmation',
          //   component: OrderConfirmation,
          //   props: true,
          //   meta: {
          //     title: 'Order Confirmed',
          //     requiresAuth: true,
          //   },
          // },
          // ... (existing routes)
        ],
      },
      // Catch-all 404 route (if needed)
      // {
      //   path: '/:pathMatch(.*)*',
      //   name: 'NotFound',
      //   component: () => import('@/views/NotFound.vue'),
      // },
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
