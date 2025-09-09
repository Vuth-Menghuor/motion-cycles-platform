import { createRouter, createWebHistory } from 'vue-router'
import Authentication_page from '@/views/authentication_page.vue'
import Home_view from '@/views/home_view.vue'
import Brand_page from '@/views/brand_page.vue'
import Bike_detail from '@/views/bike_detail.vue'
import Default_layout from '@/layouts/Default_layout.vue'
import { chai } from 'globals'

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
        path: '/brands/:id',
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
