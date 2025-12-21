<template>
  <div>
    <Navigation_header />
    <Landing_page />
    <div>
      <label class="brand-list-title">Brand Lists</label>
      <Brand_carousel />
    </div>
    <div class="content-title">
      <label class="section-title">Popular Products</label>
    </div>
    <Popular_card />
    <div class="content-title">
      <label class="section-title">Product Listing</label>
    </div>
    <Product_card :search-query="searchQuery" />
  </div>
</template>

<script setup>
import Brand_carousel from '@/components/brand/brand_carousel.vue'
import Landing_page from '@/components/landing_page.vue'
import Navigation_header from '@/components/navigation_header.vue'
import Popular_card from '@/components/popular_card.vue'
import Product_card from '@/components/product_card.vue'
import { computed } from 'vue'
import { useRoute } from 'vue-router'

// Get route to read query parameters
const route = useRoute()
const searchQuery = computed(() => route.query.search || '')

console.log('HomeView initialized, route.query:', route.query)
console.log('Initial searchQuery:', searchQuery.value)
</script>
<style scoped>
.content-title {
  display: flex;
  justify-content: flex-start;
  margin: 50px 160px 20px;
  box-sizing: border-box;
}

.section-title {
  font-family: 'Poppins', sans-serif;
  font-size: 1.8rem;
  font-weight: 600;
}

.brand-list-title {
  margin: 50px 0;
  display: flex;
  justify-content: center;
  font-family: 'Poppins', sans-serif;
  font-size: 1.8rem;
}
</style>
