<template>
  <header class="header-slideshow">
    <!-- Background Slideshow -->
    <div class="slideshow-container">
      <div
        v-for="(image, index) in backgroundImages"
        :key="index"
        class="slide"
        :class="{ active: index === currentSlide }"
        :style="{ backgroundImage: `url(${image})` }"
      ></div>
    </div>

    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-content">
        <div class="hero-text">
          <h1 class="hero-title">{{ slideContent[currentSlide].title }}</h1>
          <p class="hero-subtitle">{{ slideContent[currentSlide].subtitle }}</p>
        </div>

        <!-- Pagination Navigation -->
        <div class="pagination-content">
          <div class="pagination-nav">
            <div class="pagination-dots">
              <span
                v-for="(dot, index) in backgroundImages"
                :key="index"
                class="dot"
                :class="{ active: index === currentSlide }"
                @click="goToSlide(index)"
              ></span>
            </div>
            <div class="pagination-arrows">
              <button class="arrow-btn prev" @click="prevSlide">
                <Icon icon="material-symbols:arrow-back" class="arrow-icon" />
              </button>
              <button class="arrow-btn next" @click="nextSlide">
                <Icon icon="material-symbols:arrow-forward" class="arrow-icon" />
              </button>
            </div>
          </div>
          <span class="pagination-text">{{ slideContent[currentSlide].description }}</span>
        </div>
      </div>
    </section>
  </header>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { Icon } from '@iconify/vue'

// --- Images ---
import banner1 from '@/assets/images/landing/banner_1.png'
import banner2 from '@/assets/images/landing/banner_2.png'
import banner3 from '@/assets/images/landing/banner_3.png'
import banner4 from '@/assets/images/landing/banner_4.png'
import banner5 from '@/assets/images/landing/banner_5.png'

// --- Slideshow Data ---
const backgroundImages = [banner1, banner2, banner3, banner4, banner5]

interface Slide {
  title: string
  subtitle: string
  description: string
}

const slideContent: Slide[] = [
  {
    title: '2022 Specialized Turbo Levo Expert Carbon',
    subtitle:
      'High-performance full-suspension electric mountain bike with lightweight carbon frame, advanced motor system, and trail-optimized geometry for tackling climbs, descents, and technical terrain with ease.',
    description: 'A premium e-MTB built for power, range, and confidence on any trail.',
  },
  {
    title: 'Specialized Stumpjumper 15 Comp Alloy',
    subtitle:
      'Alloy full-suspension trail bike with progressive geometry, advanced suspension design, and reliable components built to deliver efficiency, control, and confidence on climbs, descents, and technical singletrack.',
    description: 'A versatile trail bike engineered for balanced performance and durability.',
  },
  {
    title: 'Specialized Levo Comp Alloy G3',
    subtitle:
      'Durable full-suspension electric mountain bike with an alloy frame, powerful motor, long-range battery, and modern trail geometry designed to deliver balance, control, and versatility on technical terrain.',
    description: 'A rugged e-MTB built for performance and all-day trail adventures.',
  },
  {
    title: 'Specialized S-Works Epic World Cup Frameset',
    subtitle:
      'Ultra-lightweight carbon cross-country race frameset engineered with advanced suspension design, progressive geometry, and race-proven stiffness-to-weight ratio for uncompromising speed and efficiency on the world’s toughest XC courses.',
    description: 'A top-tier XC frameset built for pure speed and precision.',
  },
  {
    title: 'Specialized S-Works Levo SL carbon',
    subtitle:
      'Lightweight full-suspension electric trail bike with a premium carbon frame, advanced motor assist, and agile geometry designed to deliver a natural ride feel, exceptional handling, and confidence across diverse terrain.',
    description: 'A high-end e-MTB built for speed, agility, and all-day trail performance.',
  },
]

// --- Slideshow State ---
const currentSlide = ref(0)
let slideInterval: number | null = null

// --- Slideshow Controls ---
const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % backgroundImages.length
}

const prevSlide = () => {
  currentSlide.value =
    currentSlide.value === 0 ? backgroundImages.length - 1 : currentSlide.value - 1
}

const goToSlide = (index: number) => {
  currentSlide.value = index
  stopSlideshow()
  startSlideshow()
}

// --- Auto Slideshow ---
const startSlideshow = () => {
  slideInterval = window.setInterval(nextSlide, 4000)
}

const stopSlideshow = () => {
  if (slideInterval) {
    clearInterval(slideInterval)
    slideInterval = null
  }
}

// --- Lifecycle ---
onMounted(() => startSlideshow())
onUnmounted(() => stopSlideshow())
</script>

<style scoped>
.header-slideshow {
  position: relative;
  width: 100%;
  height: 100vh;
  overflow: hidden;
  color: white;
}

.slideshow-container {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: -1;
}

.slide {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  opacity: 0;
  transition: opacity 1s ease-in-out;
}

.slide.active {
  opacity: 1;
}

/* Overlay for text readability */
.header-slideshow::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.3);
  z-index: 0;
}

.hero-section {
  position: relative;
  z-index: 1;
  height: 100%;
}

.hero-content {
  display: flex;
  align-items: end;
  justify-content: space-between;
  margin: 0 80px;
  height: 92vh;
}

.hero-text {
  width: 44vw;
}

.hero-title {
  font-size: 2rem;
  font-weight: 600;
  margin-bottom: 1rem;
  line-height: 1.2;
  font-family: 'Poppins', sans-serif;
  width: 28rem;
  transition: opacity 0.5s ease;
}

.hero-subtitle {
  font-size: 14px;
  opacity: 0.9;
  line-height: 2;
  font-weight: 300;
  width: 36rem;
  font-family: 'Poppins', sans-serif;
  transition: opacity 0.5s ease;
}

.pagination-content {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.pagination-nav {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  justify-content: flex-end;
}

.pagination-dots {
  display: flex;
  gap: 0.5rem;
  align-items: center;
  justify-content: flex-end;
}

.dot {
  width: 50px;
  height: 4px;
  background: rgba(255, 255, 255, 0.3);
  cursor: pointer;
  transition: all 0.3s ease;
}

.dot.active {
  background: white;
  transform: scaleY(1.5);
}

.dot:hover:not(.active) {
  background: rgba(255, 255, 255, 0.5);
  transform: scaleY(1.2);
}

.pagination-arrows {
  display: flex;
  gap: 1rem;
}

.arrow-btn {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.3s ease;
  color: white;
}

.arrow-btn:hover {
  background: rgba(255, 255, 255, 0.2);
  border-color: rgba(255, 255, 255, 0.5);
  transform: translateY(-1px) scale(1.05);
}

.arrow-btn:active {
  transform: translateY(0) scale(0.95);
}

.arrow-icon {
  font-size: 18px;
}

.pagination-text {
  font-size: 12px;
  font-family: 'Poppins', sans-serif;
  padding-top: 16px;
  white-space: nowrap;
  text-overflow: ellipsis;
  overflow: hidden;
  max-width: 460px;
  display: block;
  transition: opacity 0.5s ease;
}

@keyframes pulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.1);
  }
  100% {
    transform: scale(1);
  }
}
</style>
