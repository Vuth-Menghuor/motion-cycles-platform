<template>
  <div class="brand-page-container">
    <div class="brand-page-wrapper">
      <!-- Brand Content -->
      <div class="brand-content">
        <!-- Full-width red background -->
        <div class="brand-showcase-background" :style="{ background: currentBrand.bgColor }">
          <div class="brand-showcase-inner">
            <h1 class="brand-name-display text-center" :style="{ color: currentBrand.textColor }">
              {{ currentBrand.featuredProduct.name.toUpperCase() }}
            </h1>
            <div class="brand-bike-showcase showcase-section">
              <img
                :src="currentBrand.featuredProduct.image"
                :alt="currentBrand.featuredProduct.name"
                class="brand-product-image"
              />
            </div>
          </div>
        </div>

        <!-- Rest of content -->
        <div class="brand-hero-section">
          <!-- Product Variants List -->
          <div class="product-variants-list text-center">
            <a
              v-for="variant in currentBrand.productVariants"
              :key="variant.id"
              :href="`/products/${variant.slug}`"
              class="variant-link"
            >
              {{ variant.name }}
            </a>
          </div>

          <!-- Featured Model Info -->
          <div class="text-center">
            <p class="brand-model-subtitle">{{ currentBrand.featuredProduct.category }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import bike1 from '@/assets/images/product_showcase/image_1.png'
import bike2 from '@/assets/images/product_showcase/image_2.png'
import bike3 from '@/assets/images/product_showcase/image_3.png'
import bike4 from '@/assets/images/product_showcase/image_4.png'
import bike5 from '@/assets/images/product_showcase/image_5.png'
import bike6 from '@/assets/images/product_showcase/image_6.png'
import bike7 from '@/assets/images/product_showcase/image_7.png'
import bike8 from '@/assets/images/product_showcase/image_8.png'
import { Icon } from '@iconify/vue'

const route = useRoute()
const router = useRouter()

// Reactive data
const brandName = ref(route.params.brandName)

const brandConfig = {
  cannondale: {
    bgColor: 'linear-gradient(0deg, rgba(46, 32, 5, 0.7) 0%, rgba(255, 255, 255, 0.7) 50%)',
    textColor: '#2E2005',
    featuredProduct: {
      name: 'CAAD OPTIMO 105',
      category: 'Modern Road',
      image: bike1,
    },
    productVariants: [
      { id: 1, name: 'F-SI Carbon 2', slug: 'f-si-carbon-2' },
      { id: 2, name: 'F-SI Carbon 5', slug: 'f-si-carbon-5' },
      { id: 3, name: 'F-SI Hi-MOD 1', slug: 'f-si-hi-mod-1' },
    ],
  },
  trek: {
    bgColor: 'linear-gradient(0deg, rgba(28, 43, 87, 0.7) 0%, rgba(255, 255, 255, 0.7) 50%)',
    textColor: '#1C2B57',
    featuredProduct: {
      name: 'Domane SL 6',
      category: 'Endurance Road',
      image: bike2,
    },
    productVariants: [
      { id: 1, name: 'Emonda SL 7', slug: 'emonda-sl-7' },
      { id: 2, name: 'Checkpoint ALR 5', slug: 'checkpoint-alr-5' },
    ],
  },
  bianchi: {
    bgColor: 'linear-gradient(0deg, rgba(61, 58, 52, 0.7) 0%, rgba(255, 255, 255, 0.7) 50%)',
    textColor: '#3D3A34',
    featuredProduct: {
      name: 'Oltre XR4',
      category: 'Aero Road',
      image: bike3,
    },
    productVariants: [
      { id: 1, name: 'Specialissima', slug: 'specialissima' },
      { id: 2, name: 'Infinito CV', slug: 'infinito-cv' },
    ],
  },
  giant: {
    bgColor: 'linear-gradient(0deg, rgba(53, 53, 55, 0.7) 0%, rgba(255, 255, 255, 0.7) 50%)',
    textColor: '#353537',
    featuredProduct: {
      name: 'TCR Advanced Pro',
      category: 'Climbing Road',
      image: bike4,
    },
    productVariants: [
      { id: 1, name: 'Defy Advanced 1', slug: 'defy-advanced-1' },
      { id: 2, name: 'Propel Advanced 2', slug: 'propel-advanced-2' },
    ],
  },
  cervelo: {
    bgColor: 'linear-gradient(0deg, rgba(28, 39, 48, 0.7) 0%, rgba(255, 255, 255, 0.7) 50%)',
    textColor: '#1C2730',
    featuredProduct: {
      name: 'S5',
      category: 'Aero Road',
      image: bike5,
    },
    productVariants: [
      { id: 1, name: 'R5', slug: 'r5' },
      { id: 2, name: 'Caledonia-5', slug: 'caledonia-5' },
    ],
  },
  specialized: {
    bgColor: 'linear-gradient(0deg, rgba(106, 0, 8, 0.7) 0%, rgba(255, 255, 255, 0.7) 50%)',
    textColor: '#6A0008',
    featuredProduct: {
      name: 'Tarmac SL7',
      category: 'All-Rounder',
      image: bike6,
    },
    productVariants: [
      { id: 1, name: 'Roubaix Pro', slug: 'roubaix-pro' },
      { id: 2, name: 'Aethos Expert', slug: 'aethos-expert' },
    ],
  },
  shimano: {
    bgColor: 'linear-gradient(0deg, rgba(81, 50, 63, 0.7) 0%, rgba(255, 255, 255, 0.7) 50%)',
    textColor: '#51323F',
    featuredProduct: {
      name: 'Dura-Ace R9200',
      category: 'Groupset',
      image: bike7,
    },
    productVariants: [
      { id: 1, name: 'Ultegra R8100', slug: 'ultegra-r8100' },
      { id: 2, name: '105 R7100', slug: '105-r7100' },
    ],
  },
  colnago: {
    bgColor: 'linear-gradient(0deg, rgba(86, 108, 98, 0.7) 0%, rgba(255, 255, 255, 0.7) 50%)',
    textColor: '#566C62',
    featuredProduct: {
      name: 'V4Rs',
      category: 'Pro Racing',
      image: bike8,
    },
    productVariants: [
      { id: 1, name: 'C68', slug: 'c68' },
      { id: 2, name: 'V3 Disc', slug: 'v3-disc' },
    ],
  },
}

const goBack = () => {
  router.push('/').then(() => {
    window.scrollTo(0, 0)
  })
}

const currentBrand = computed(() => {
  return (
    brandConfig[brandName.value?.toLowerCase()] || {
      bgColor: '#ccc',
      featuredProduct: { name: 'Unknown', category: 'Unknown', image: bike1 },
      productVariants: [],
    }
  )
})
</script>

<style scoped>
.brand-page-container {
  background-color: #f8f9fa;
}

.brand-page-wrapper {
  margin: 0 auto;
}

.brand-showcase-background {
  width: 100%;
  background: #3e4f62;
  background: linear-gradient(0deg, rgba(62, 79, 98, 1) 0%, rgba(255, 255, 255, 1) 50%);
  padding: 40px 0;
  display: flex;
  justify-content: center;
}

.brand-showcase-inner {
  max-width: 800px;
  height: 38rem;
  width: 100%;
  text-align: center;
}

.brand-hero-section {
  background: white;
  border-radius: 10px;
  padding: 20px 40px;
}

.brand-showcase {
  max-width: 800px;
  margin: 0 auto;
}

.brand-name-display {
  font-size: 5rem;
  font-weight: bold;
  color: #333;
  font-family: '911Porscha', sans-serif;
  margin: 0;
}

.showcase-section {
  position: absolute;
  display: flex;
  justify-content: center;
  align-items: center;
  max-width: 800px;
  top: 30.8rem;
  z-index: 100;
  transform: translateY(-50%);
}

.brand-product-image {
  max-width: 140%;
  width: auto;
  height: auto;
  object-fit: contain;
}

.product-variants-list {
  margin: 20px 0 30px 0;
  display: flex;
  justify-content: center;
  gap: 20px;
  flex-wrap: wrap;
}

.variant-link {
  font-family: 'Poppins', sans-serif;
  color: #666;
  padding: 8px 12px;
  font-weight: 500;
  border-bottom: 1px solid transparent;
  transition: all 0.3s ease;
}

.variant-link:hover {
  color: #3491fa;
  border-bottom-color: #3491fa;
}

.brand-model-subtitle {
  text-align: center;
  padding: 10px;
  background-color: #d9d9d9;
  width: 14vw;
  border-radius: 6px;
  font-size: 1rem;
  font-family: 'Poppins', sans-serif;
  color: #666;
  margin: 0 auto;
}

/* Layout utilities */
.text-center {
  text-align: center;
}

/* Responsive */
@media (max-width: 768px) {
  .brand-name-display {
    font-size: 2rem;
  }

  .brand-featured-model {
    font-size: 1.5rem;
  }

  .brand-hero-section {
    padding: 40px 20px;
  }

  .product-variants-list {
    flex-direction: column;
    align-items: center;
  }
}
</style>
