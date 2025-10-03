<script setup>
import { Icon } from '@iconify/vue'
import { ref, onMounted, onUnmounted } from 'vue'
import bike1 from '@/assets/images/popular_card/image_1.png'
import bike2 from '@/assets/images/popular_card/image_2.png'
import bike3 from '@/assets/images/popular_card/image_3.png'

const bikes = ref([
  {
    id: 1,
    title: 'Bianchi T-Tronik C Type - Sunrace (2023)',
    subtitle: 'Mountain Bike',
    price: '$14,400.00',
    badge: {
      text: 'Hot Deal',
      icon: 'mdi:hot',
      gradient: 'linear-gradient(135deg, #ff6b6b, #ff5252)',
    },
    bgBtn: '#3E4F62',
    bgGradient: 'linear-gradient(135deg, #ffffff, #3E4F62)',
    bgPrice: 'linear-gradient(272deg, #3E4F62 100%, rgba(255, 255, 255, 1) 100%)',
    specs: [
      { label: 'Travel', text: '160mm / 170mm', icon: 'mdi:bike' },
      { label: 'Wheels', text: '29\" DT Swiss EX1700', icon: 'mdi:wheel' },
      { label: 'Weight', text: '33.3 lb (15.1 kg)', icon: 'mdi:scale-bathroom' },
    ],
    image: bike1,
  },
  {
    id: 2,
    title: 'Trek Slash 9.8 XT Carbon',
    subtitle: 'Enduro Bike',
    price: '$6,299.99',
    badge: {
      text: 'Best Seller',
      icon: 'mdi:star',
      gradient: 'linear-gradient(135deg, #ffd700, #ffb300)',
    },
    bgBtn: '#2E2005',
    bgGradient: 'linear-gradient(135deg, #ffffff, #2E2005)',
    bgPrice: 'linear-gradient(272deg, #2E2005 100%, rgba(255, 255, 255, 1) 100%)',
    specs: [
      { label: 'Travel', text: '150mm / 160mm', icon: 'mdi:bike' },
      { label: 'Wheels', text: '29\" Bontrager Line Comp', icon: 'mdi:wheel' },
      { label: 'Weight', text: '31.2 lb (14.1 kg)', icon: 'mdi:scale-bathroom' },
    ],
    image: bike2,
  },
  {
    id: 3,
    title: 'Santa Cruz Hightower CC X01',
    subtitle: 'Trail Bike',
    price: '$8,999.00',
    badge: {
      text: 'New Arrival',
      icon: 'mdi:new-box',
      gradient: 'linear-gradient(135deg, #4ecdc4, #44a08d)',
    },
    bgBtn: '#6A0008',
    bgGradient: 'linear-gradient(135deg, #ffffff, #6A0008)',
    bgPrice: 'linear-gradient(272deg, #6A0008 100%, rgba(255, 255, 255, 1) 100%)',
    specs: [
      { label: 'Travel', text: '140mm / 150mm', icon: 'mdi:bike' },
      { label: 'Wheels', text: '29\" Reserve 30', icon: 'mdi:wheel' },
      { label: 'Weight', text: '29.8 lb (13.5 kg)', icon: 'mdi:scale-bathroom' },
    ],
    image: bike3,
  },
])

// const favorites = ref(new Set())
const visibleBikes = ref(new Set()) // track which bikes are in view

// const toggleFavorite = (bikeId) => {
//   if (favorites.value.has(bikeId)) {
//     favorites.value.delete(bikeId)
//   } else {
//     favorites.value.add(bikeId)
//   }
//   favorites.value = new Set(favorites.value) // Trigger reactivity
// }

// const isFavorited = (bikeId) => {
//   return favorites.value.has(bikeId)
// }

let observer

onMounted(() => {
  observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        const id = Number(entry.target.dataset.id)
        if (entry.isIntersecting) {
          visibleBikes.value.add(id)
        } else {
          visibleBikes.value.delete(id)
        }
        visibleBikes.value = new Set(visibleBikes.value)
      })
    },
    { threshold: 0.5 }, // 50% of card visible
  )

  const cards = document.querySelectorAll('.popular-product-card')
  cards.forEach((card) => observer.observe(card))
})

onUnmounted(() => {
  if (observer) observer.disconnect()
})
</script>

<template>
  <div class="bikes-container">
    <div v-for="bike in bikes" :key="bike.id" class="popular-product-card" :data-id="bike.id">
      <!-- sale badge -->
      <div class="sale-badge" :style="{ background: bike.badge.gradient }">
        <Icon :icon="bike.badge.icon" class="sale-icon" />
        <span>{{ bike.badge.text }}</span>
      </div>

      <div class="card-header">
        <div class="product-info">
          <label class="product-title">{{ bike.title }}<br /></label>
          <span class="product-subtitle">{{ bike.subtitle }}</span>
        </div>
        <!-- <button
          class="favorite-btn"
          :class="{ favorited: isFavorited(bike.id) }"
          @click="toggleFavorite(bike.id)"
        >
          <Icon
            :icon="isFavorited(bike.id) ? 'solar:heart-bold' : 'solar:heart-linear'"
            class="fav-icon"
          />
        </button> -->
      </div>

      <div class="product-image-container">
        <!-- Gradient background behind image -->
        <div class="bg-popular" :style="{ background: bike.bgGradient }"></div>

        <!-- Product Image with slide in/out -->
        <img
          :src="bike.image"
          alt="bikes-image-transparent"
          class="product-image"
          :class="{
            'slide-in': visibleBikes.has(bike.id),
            'slide-out': !visibleBikes.has(bike.id),
          }"
        />
      </div>

      <div class="product-specs">
        <div v-for="spec in bike.specs" :key="spec.label" class="spec-item">
          <Icon :icon="spec.icon" class="item-icon" />
          <div class="spec-content">
            <span class="spec-label">{{ spec.label }}</span>
            <span class="spec-text">{{ spec.text }}</span>
          </div>
        </div>
      </div>

      <div class="card-footer">
        <div class="price-section">
          <span class="price" :style="{ background: bike.bgPrice }">{{ bike.price }}</span>
        </div>
        <button class="quick-buy-btn" :style="{ background: bike.bgBtn }">
          <Icon icon="fa7-solid:cart-arrow-down" />
          <span>Quick Buy</span>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.bikes-container {
  display: flex;
  gap: 44px;
  flex-wrap: wrap;
  justify-content: center;
  padding: 20px;
}

.spec-content {
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.spec-label {
  font-size: 11px;
  color: #718096;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
.popular-product-card:hover .product-image.slide-in {
  transform: scale(1.05) rotate(2deg);
}
.sale-icon {
  font-size: 22px;
}
.sale-badge span {
  font-size: 12px;
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
}
.sale-badge {
  position: absolute;
  top: 90px;
  left: -8px;
  color: white;
  padding: 6px 12px 6px 14px;
  display: flex;
  align-items: center;
  clip-path: polygon(0 0, 95% 0, 100% 20%, 100% 80%, 95% 100%, 0 100%, 0% 80%, 0% 20%);
  gap: 4px;
  z-index: 10;
}
.popular-product-card {
  max-width: 520px;
  background-color: white;
  border: 1px solid #dee2e6;
  border-radius: 10px;
  object-fit: contain;
  overflow: hidden;
  position: relative;
}
.card-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  padding: 20px 24px 0 24px;
}
.product-info {
  flex: 1;
  font-family: 'Poppins', sans-serif;
  line-height: 1.6;
}
/* .favorite-btn {
  display: flex;
  align-items: center;
  border: 1px solid #dee2e6;
  cursor: pointer;
  padding: 6px;
  border-radius: 50%;
  color: grey;
  background-color: white;
  transition: all 0.3s ease;
}
.favorite-btn.favorited {
  background-color: #ffebef;
  border-color: #ff4d6d;
  color: #ff4d6d;
}

.favorite-btn.favorited .fav-icon {
  color: #ff4d6d;
} */

.product-title {
  font-size: 16px;
  font-weight: 600;
  color: #333;
  margin: 0 0 4px 0;
}
.product-subtitle {
  font-size: 14px;
  color: grey;
  font-weight: 400;
  margin: 0;
}
/* .fav-icon {
  font-size: 20px;
} */
.product-image-container {
  position: relative;
  padding: 20px;
  min-height: 200px;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

/* Gradient background */
.bg-popular {
  position: absolute;
  height: 200px;
  width: 1000px;
  top: 120px;
  rotate: -20deg;
  z-index: 1;
}

.product-image {
  width: 100%;
  max-height: 200px;
  object-fit: contain;
  z-index: 2;
  position: relative;
  opacity: 0;
  transform: translateY(50px);
  transition:
    transform 0.6s ease,
    opacity 0.6s ease;
}

.product-image.slide-in {
  opacity: 1;
  transform: translateY(0);
}

.product-image.slide-out {
  opacity: 0;
  transform: translateY(50px);
}

.card-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px;
  gap: 16px;
}

.product-specs {
  display: flex;
  justify-content: space-around;
  font-family: 'Poppins', sans-serif;
  padding: 10px 20px;
  gap: 16px;
  position: relative;
  z-index: 3;
}

.spec-item {
  display: flex;
  align-items: center;
  gap: 6px;
  flex: 1;
  justify-content: center;
  color: white;
}

.spec-text {
  font-size: 12px;
  color: #333;
  font-weight: 500;
}

.item-icon {
  font-size: 26px;
  color: black;
}
.price-section {
  display: flex;
  align-items: center;
  z-index: 4;
}

.price {
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  font-weight: 500;
  color: white;
  clip-path: polygon(5% 0, 95% 0, 100% 20%, 100% 80%, 95% 100%, 5% 100%, 0 80%, 0 20%);
  padding: 10px 50px;
}

.quick-buy-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  font-family: 'Poppins', sans-serif;
  background: #428fc0;
  color: white;
  border: none;
  padding: 10px 50px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  clip-path: polygon(5% 0, 95% 0, 100% 20%, 100% 80%, 95% 100%, 5% 100%, 0 80%, 0 20%);
  transition: background-color 0.3s ease;
  flex-shrink: 0;
}
</style>
