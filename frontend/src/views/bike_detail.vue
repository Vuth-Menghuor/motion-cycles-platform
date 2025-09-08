<template>
  <Navigation_header />
  <div class="bike-detail-container">
    <!-- Bike Details -->
    <div class="bike-detail">
      <Bread_crumb :brand="bike.subtitle" :product="bike.title" />
      <div class="detail-content">
        <Bike_image_gallery
          :image="bike.image"
          :title="bike.title"
          :additional-images="bike.additionalImages || []"
        />
        <Bike_info
          :bike="bike"
          :formatNumber="formatNumber"
          :getDiscountedPrice="getDiscountedPrice"
          :getSavings="getSavings"
          :getBrandDescription="getBrandDescription"
        />
        <div class="spec-card-wrapper">
          <div class="specifications-section">
            <Bike_specifications />
            <Reviews_page />
          </div>
          <Bike_card_sticky :bike="bike" :image="bike.image" class="sticky-card" />
        </div>
        <div class="recommend-section">
          <Bike_suggestion_card />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
// Import your bike images
import bike1 from '@/assets/images/product_card/mount_1.png'
import bike2 from '@/assets/images/product_card/mount_2.png'
import bike3 from '@/assets/images/product_card/mount_3.png'
import bike4 from '@/assets/images/product_card/road_1.png'
import bike5 from '@/assets/images/product_card/road_2.png'
import bike6 from '@/assets/images/product_card/road_3.png'
import Navigation_header from '@/components/navigation_header.vue'
import Bread_crumb from '@/components/bread_crumb.vue'
import Bike_image_gallery from '@/components/bike_detail/bike_image_gallery.vue'
import Bike_info from '@/components/bike_detail/bike_info.vue'
import Bike_specifications from '@/components/bike_detail/bike_specifications.vue'
import Reviews_page from '@/components/bike_detail/reviews/reviews_page.vue'
import Bike_card_sticky from '@/components/bike_detail/bike_card_sticky.vue'
import Bike_suggestion_card from '@/components/bike_detail/bike_suggestion_card.vue'

const route = useRoute()
const router = useRouter()

// Bike data (should be same as your main component or imported from a store)
const bikes = ref([
  {
    id: 1,
    title: 'Bianchi T-Tronik C Type - Sunrace (2023)',
    subtitle: 'Bianchi',
    price: 24400,
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
    specs: [
      {
        text: 'The 2023 Bianchi T-Tronik C-Type Sunrace is a refined Class 1 electric city bike designed for urban commuting and leisure rides. It features a Shimano E6100 250W mid-drive motor delivering 60Nm of torque, paired with a 417Wh Phylion battery offering up to 95 km of range.',
      },
    ],
    image: bike1,
  },
  {
    id: 2,
    title: 'Trek Slash 9.8 XT Carbon',
    subtitle: 'Trek',
    price: 44299,
    color: 'Orange',
    badge: {
      text: 'New',
      icon: 'material-symbols-light:new-releases',
      gradient: 'linear-gradient(135deg, #3491FA, #3491FA)',
    },
    discount: {
      type: 'fixed',
      value: 399,
    },
    rating: 2.4,
    reviewCount: 3221,
    specs: [
      {
        text: 'The Trek Slash 9.8 XT is a high-performance enduro mountain bike designed for aggressive trail riding and technical descents. It features a lightweight OCLV Mountain Carbon frame with 170mm of front and rear travel, utilizing a high-pivot suspension system with an idler pulley to enhance rear-wheel traction and control over rough terrain .',
      },
    ],
    image: bike2,
  },
  {
    id: 3,
    title: 'Santa Cruz Hightower CC X01',
    subtitle: 'Specialized',
    price: 29999,
    color: 'Grey',
    badge: {},
    discount: null,
    rating: 5,
    reviewCount: 3221,
    specs: [
      {
        text: "The Santa Cruz Hightower CC X01 is a versatile trail bike designed to excel on both climbs and descents. Featuring 150mm front and 140mm rear travel, it incorporates Santa Cruz's lower-link VPP suspension design, providing a nearly linear leverage curve for efficient pedaling and excellent bump absorption.",
      },
    ],
    image: bike3,
  },
  {
    id: 4,
    title: 'Giant TCR Advanced Pro 1',
    subtitle: 'Giant',
    price: 3799,
    color: 'Black',
    badge: {},
    discount: {
      type: 'percent',
      value: 30,
    },
    rating: 4.8,
    reviewCount: 1120,
    specs: [
      {
        text: 'Carbon fiber frame, lightweight and aerodynamic. Perfect for racing and long-distance rides on smooth roads.',
      },
    ],
    image: bike4,
  },
  {
    id: 5,
    title: 'Specialized Allez Sprint Comp',
    subtitle: 'Specialized',
    price: 3299,
    color: 'Blue',
    badge: {},
    discount: {
      type: 'fixed',
      value: 97,
    },
    rating: 4.7,
    reviewCount: 890,
    specs: [
      {
        text: 'High-modulus carbon fork, lightweight alloy frame. Optimized for sprinting and fast-paced group rides.',
      },
    ],
    image: bike5,
  },
  {
    id: 6,
    title: 'Cannondale Synapse Carbon Disc Ultegra',
    subtitle: 'Cannondale',
    price: 4199,
    color: 'Red',
    badge: {
      text: 'New',
      icon: 'material-symbols-light:new-releases',
      gradient: 'linear-gradient(135deg, #3491FA, #3491FA)',
    },
    discount: null,
    rating: 3.5,
    reviewCount: 650,
    specs: [
      {
        text: 'Comfort-oriented carbon frame with disc brakes. Designed for long endurance rides with smooth handling and stability.',
      },
    ],
    image: bike6,
  },
])

// Find the current bike
const bike = computed(() => {
  const bikeId = parseInt(route.params.id)
  return bikes.value.find((b) => b.id === bikeId)
})

// Utility functions
const formatNumber = (number) => {
  return number.toLocaleString()
}

const getDiscountedPrice = (bike) => {
  if (!bike.discount) return bike.price
  if (bike.discount.type === 'percent') {
    return bike.price - (bike.price * bike.discount.value) / 100
  } else {
    return bike.price - bike.discount.value
  }
}

const getSavings = (bike) => {
  return bike.price - getDiscountedPrice(bike)
}

const getBrandDescription = (brand) => {
  const descriptions = {
    Bianchi:
      'Founded in 1885, Bianchi is one of the oldest bicycle manufacturers in the world. Known for their distinctive celeste green color and Italian craftsmanship, Bianchi has been at the forefront of cycling innovation for over a century.',
    Trek: 'Trek Bicycle Corporation is an American bicycle and cycling product manufacturer and distributor. Trek is known for high-quality road, mountain, and electric bikes, with a commitment to advancing bicycle technology and sustainability.',
    Specialized:
      'Specialized is an American company that designs, manufactures, and markets bicycles, bicycle components, and related products. Founded in 1974, Specialized is known for innovation and performance-oriented cycling equipment.',
    Giant:
      "Giant Manufacturing Co. is a Taiwanese bicycle manufacturer. Founded in 1972, Giant is the world's largest bicycle manufacturer and is known for producing high-quality bikes at competitive prices.",
    Cannondale:
      'Cannondale Bicycle Corporation is an American division of Dutch conglomerate Pon Holdings. Founded in 1971, Cannondale is known for innovative designs and high-performance bicycles.',
  }
  return descriptions[brand] || 'A leading bicycle manufacturer known for quality and innovation.'
}

watch(
  bike,
  (newBike) => {
    if (!newBike) {
      router.push('/')
    }
  },
  { immediate: true },
)
</script>

<style scoped>
.recommend-title {
  font-family: 'Poppins', sans-serif;
  font-size: 24px;
  font-weight: 600;
  color: #111827;
}
.recommend-section {
  display: flex;
  flex-direction: column;
  font-family: 'Poppins', sans-serif;
  border-top: 1px solid #e2e8f0;
}
.specifications-section {
  margin-bottom: 14px;
  width: 100%;
}
.sticky-card {
  position: sticky;
  top: 10rem;
  align-self: flex-start;
  height: fit-content;
}

.spec-card-wrapper {
  display: flex;
  width: 100%;
  gap: 40px;
}

.bike-detail-container {
  min-height: 100vh;
  font-family: 'Poppins', sans-serif;
  margin-top: 14vh;
}

.bike-detail {
  max-width: 1400px;
  margin: 0 auto;
  padding: 20px;
}

.detail-content {
  display: flex;
  flex-direction: column;
}

@media (max-width: 768px) {
  .detail-content {
    gap: 32px;
  }

  .bike-title {
    font-size: 24px;
  }

  .features-grid {
    grid-template-columns: 1fr;
  }

  .action-buttons {
    flex-direction: column;
  }
}
</style>
