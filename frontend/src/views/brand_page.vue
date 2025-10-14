<script setup>
import Brand_carousel from '@/components/brand/brand_carousel.vue'
import Brand_product_showcase from '@/components/brand_showcase/brand_product_showcase.vue'
import Product_card from '@/components/product_card.vue'
import Back_btn from '@/components/back_btn.vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const brandName = route.params.brandName

defineProps({
  id: String,
})
</script>

<template>
  <div class="brand-page">
    <Back_btn :brandName="brandName" />
    <Brand_product_showcase />
    <Brand_carousel />
    <Product_card :brand="brandName" />
  </div>
</template>
