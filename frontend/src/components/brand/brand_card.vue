<script setup>
import { useRouter } from 'vue-router'

// eslint-disable-next-line no-unused-vars
const props = defineProps({ brand: Object })
const router = useRouter()

const goToBrand = (id, brandName) => {
  router.push({ name: 'BrandPage', params: { id, brandName } }).then(() => {
    setTimeout(() => window.scrollTo(0, 0), 100)
  })
}
</script>

<template>
  <div class="brand-card" @click="goToBrand(brand.id, brand.name)">
    <img :src="brand.logo" :alt="brand.name" class="brand-card__logo" />
  </div>
</template>

<style scoped>
.brand-card {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 230px;
  padding: 30px;
  height: 50px;
  border: 1px solid #dee2e6;
  margin: 0 6px;
  flex-shrink: 0;
  cursor: pointer;
}

.brand-card__logo {
  max-width: 100%;
  height: auto;
}
</style>
