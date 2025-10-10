<script setup>
import BrandCard from './brand_card.vue'
import brand1 from '@/assets/images/brands/brand_1.png'
import brand2 from '@/assets/images/brands/brand_2.png'
import brand3 from '@/assets/images/brands/brand_3.png'
import brand4 from '@/assets/images/brands/brand_4.png'
import brand5 from '@/assets/images/brands/brand_5.png'
import brand6 from '@/assets/images/brands/brand_6.png'
import brand7 from '@/assets/images/brands/brand_7.png'
import brand8 from '@/assets/images/brands/brand_8.png'

const brands = [
  { id: 'cannondale', name: 'Cannondale', logo: brand1 },
  { id: 'trek', name: 'Trek', logo: brand2 },
  { id: 'bianchi', name: 'Bianchi', logo: brand3 },
  { id: 'giant', name: 'Giant', logo: brand4 },
  { id: 'cervelo', name: 'Cervelo', logo: brand5 },
  { id: 'specialized', name: 'Specialized', logo: brand6 },
  { id: 'shimano', name: 'Shimano', logo: brand7 },
  { id: 'colnago', name: 'Colnago', logo: brand8 },
]
</script>

<template>
  <div class="brand-carousel">
    <div class="brand-carousel__container">
      <div class="brand-carousel__track">
        <BrandCard v-for="brand in brands" :key="brand.name" :brand="brand" />
        <BrandCard v-for="brand in brands" :key="'dup-' + brand.name" :brand="brand" />
      </div>
    </div>
  </div>
</template>

<style scoped>
.brand-carousel {
  width: 100%;
  overflow: hidden;
  gap: 10px;
  border-radius: 16px;
  padding: 10px 0;
}

.brand-carousel__container {
  overflow: hidden;
}

.brand-carousel__track {
  display: flex;
  width: max-content;
  animation: scroll 20s linear infinite;
}

@keyframes scroll {
  0% {
    transform: translateX(0);
  }
  100% {
    transform: translateX(-50%);
  }
}
</style>
