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
import Purchase_summary from '@/views/checkout/purchase_summary.vue'
import Admin_layout from '@/layouts/admin_layout.vue'
import Dashboard_page from '@/views/admin/dashboard_page.vue'
import Unauthorized from '@/views/unauthorized.vue'
import Not_found from '@/views/not_found.vue'
import { useAuthStore } from '@/stores/auth'
import Product_page from '@/views/admin/product_page.vue'
import Order_page from '@/views/admin/order_page.vue'
import Customer_page from '@/views/admin/customer_page.vue'
import Analytics_page from '@/views/admin/analytics_page.vue'

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
          requiresAuth: true,
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

  // Admin Routes
  {
    path: '/admin',
    component: Admin_layout,
    meta: {
      requiresAuth: true,
      requiresRole: 'admin',
    },
    children: [
      {
        path: '',
        redirect: '/admin/dashboard',
      },
      {
        path: 'dashboard',
        name: 'AdminDashboard',
        component: Dashboard_page,
        meta: {
          title: 'Dashboard',
          requiresAuth: true,
          requiresRole: 'admin',
        },
      },
      {
        path: 'products',
        name: 'AdminProducts',
        component: Product_page,
        meta: {
          title: 'Products',
          requiresAuth: true,
          requiresRole: 'admin',
        },
      },
      // {
      //   path: 'products/add',
      //   name: 'AddProduct',
      //   // component: () => import('@/views/admin/add_product.vue'),
      //   meta: {
      //     title: 'Add Product',
      //     requiresAuth: true,
      //     requiresRole: 'admin',
      //   },
      // },
      // {
      //   path: 'products/:id/edit',
      //   name: 'EditProduct',
      //   // component: () => import('@/views/admin/edit_product.vue'),
      //   props: true,
      //   meta: {
      //     title: 'Edit Product',
      //     requiresAuth: true,
      //     requiresRole: 'admin',
      //   },
      // },
      {
        path: 'orders',
        name: 'AdminOrders',
        component: Order_page,
        meta: {
          title: 'Orders',
          requiresAuth: true,
          requiresRole: 'admin',
        },
      },
      // {
      //   path: 'orders/:id',
      //   name: 'OrderDetail',
      //   component: () => import('@/views/admin/order_detail.vue'),
      //   props: true,
      //   meta: {
      //     title: 'Order Details',
      //     requiresAuth: true,
      //     requiresRole: 'admin',
      //   },
      // },
      {
        path: 'customers',
        name: 'AdminCustomers',
        component: Customer_page,
        meta: {
          title: 'Customers',
          requiresAuth: true,
          requiresRole: 'admin',
        },
      },
      {
        path: 'analytics',
        name: 'Analytics',
        component: Analytics_page,
        meta: {
          title: 'Revenue & Profit Analytics',
          requiresAuth: true,
          requiresRole: 'admin',
        },
      },
    ],
  },

  // Authentication Routes
  {
    path: '/authentication/:type',
    name: 'Authentication',
    component: Authentication_page,
    props: true,
  },

  // Unauthorized Access
  {
    path: '/unauthorized',
    name: 'Unauthorized',
    component: Unauthorized,
    meta: {
      title: 'Unauthorized Access',
    },
  },

  // 404 Not Found - Must be last
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: Not_found,
    meta: {
      title: 'Page Not Found',
    },
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

router.beforeEach((to, from) => {
  const auth = useAuthStore()
  const token = localStorage.getItem('token')

  // Requires authentication
  if (to.meta.requiresAuth && !token) {
    return { path: '/authentication/sign_in', query: { redirect: to.fullPath } }
  }

  // Requires specific role
  if (to.meta.requiresRole && auth.getRole() !== to.meta.requiresRole) {
    return {
      path: '/unauthorized',
    }
  }
})

export default router
