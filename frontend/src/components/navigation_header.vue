<template>
  <Navigation_sidebar :isOpen="isSidebarOpen" @close="closeSidebar" />

  <header
    ref="headerRef"
    class="header-slideshow"
    :style="{ backgroundColor: colors.headerBg || '', boxShadow: colors.boxShadowHeader || '' }"
  >
    <nav class="main-navigation">
      <div class="nav-container">
        <div class="brand-logo-wrapper">
          <Icon
            ref="menuIconRef"
            icon="ic:outline-menu"
            class="menu-icon"
            :style="{ color: colors.menuIcon || '' }"
            :class="{ 'is-open': isSidebarOpen }"
            @click="toggleSidebar"
          />
          <div class="brand-logo" @click="goToHome">
            <span class="brand-text" :style="{ color: colors.logoName || '' }">MOTION CYCLE</span>
          </div>
        </div>

        <div class="search-container">
          <input
            type="search"
            placeholder="Search Bicycle"
            class="search-input"
            :style="{
              borderColor: colors.searchBorder || '',
              backgroundColor: colors.searchBg || '',
            }"
          />
          <button class="search-button">
            <Icon icon="ri:search-line" class="search-icon" />
          </button>
        </div>

        <div class="user-actions">
          <button
            class="cart-button action-button"
            @click="goToCart"
            :style="{ borderColor: colors.userBorderBtn }"
          >
            <Icon
              icon="ion:cart"
              class="cart-icon action-icon"
              :style="{ color: colors.cartIcon }"
            />
            <span class="cart-badge" v-if="count > 0">{{ count }}</span>
          </button>

          <button
            class="user-account action-button"
            :style="{
              backgroundColor: colors.userBgBtn || '',
              borderColor: colors.userBorderBtn,
            }"
            @click="goToAuth"
          >
            <Icon
              icon="mdi:user"
              class="action-icon user-icon"
              :style="{ color: colors.userIcon }"
            />
          </button>
        </div>
      </div>
    </nav>

    <nav
      class="brand-navigation"
      :style="{
        backgroundColor: colors.brandBg || '',
        borderColor: colors.brandBorder,
      }"
    >
      <div class="brand-nav-container">
        <ul class="brand-list">
          <li
            class="brand-item"
            v-for="brand in [
              'Cannondale',
              'Trek',
              'Bianchi',
              'Giant',
              'Cervélo',
              'Specialized',
              'Shimano',
              'Colnago',
            ]"
            :key="brand"
          >
            <router-link
              :to="{ name: 'BrandPage', params: { id: brand.toLowerCase(), brandName: brand } }"
              class="brand-link"
              :style="{ color: colors.brandName }"
            >
              {{ brand }}
            </router-link>
          </li>
        </ul>
      </div>
    </nav>
  </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Icon } from '@iconify/vue'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import Navigation_sidebar from './sidebar/navigation_sidebar.vue'
import { useCartStore } from '@/stores/cart'
import { storeToRefs } from 'pinia'
import router from '@/router'

gsap.registerPlugin(ScrollTrigger)

// Define props for the component
const props = defineProps({
  disableAnimation: Boolean,
  colors: {
    type: Object,
    default: () => ({
      headerBg: '',
      boxShadowHeader: '',
      menuIcon: '',
      logoName: '',
      searchBorder: '',
      cartIcon: '',
      userIcon: '',
      userBgBtn: '',
      brandName: '',
      brandBorder: '',
      brandBg: '',
    }),
  },
})

// Get cart store and count
const cartStore = useCartStore()
const { count } = storeToRefs(cartStore)

// Reactive data for sidebar state
const isSidebarOpen = ref(false)
const headerRef = ref(null)
const menuIconRef = ref(null)

let menuTl = null

// Function to toggle sidebar
const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
  if (menuTl?.reversed()) {
    menuTl.play()
  } else {
    menuTl.reverse()
  }
}

// Function to close sidebar
const closeSidebar = () => {
  isSidebarOpen.value = false
  menuTl?.reverse()
}

// Function to navigate to a path
const navigate = (path) => {
  router.push(path).then(() => window.scrollTo(0, 0))
}

// Navigation functions
const goToCart = () => navigate('/checkout/cart')
const goToHome = () => navigate('/')
const goToAuth = () => navigate('/authentication/sign_in')

// Function to handle click outside to close sidebar
const handleClickOutside = (event) => {
  if (!headerRef.value?.contains(event.target)) closeSidebar()
}

// Lifecycle hook for mounting
onMounted(() => {
  if (props.disableAnimation) return // Skip animations if disabled

  // Create menu timeline
  menuTl = gsap.timeline({ reversed: true })
  menuTl.to('.sidebar-content', { x: 0, duration: 0.3, ease: 'power2.out' })

  // Scroll trigger for header
  gsap.to(headerRef.value, {
    scrollTrigger: {
      trigger: 'body',
      start: '840 top',
      toggleClass: { targets: headerRef.value, className: 'is-scrolled' },
    },
  })

  // Animation for brand items
  gsap.fromTo(
    '.brand-item',
    { y: -50, opacity: 0 },
    { y: 0, opacity: 1, duration: 0.6, stagger: 0.1, ease: 'power2.out' },
  )

  // Add click outside listener
  document.addEventListener('click', handleClickOutside)
})

// Lifecycle hook for unmounting
onUnmounted(() => {
  ScrollTrigger.getAll().forEach((trigger) => trigger.kill()) // Kill all scroll triggers
  document.removeEventListener('click', handleClickOutside) // Remove event listener
})
</script>

<style scoped>
.header-slideshow {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 999;
  pointer-events: auto;
  transition: all 0.3s ease;
}

/* ================================
   NAVIGATION CONTAINERS
   ================================ */
.main-navigation,
.brand-navigation {
  width: 100%;
  backdrop-filter: blur(5px);
}

.nav-container {
  display: flex;
  align-items: center;
  justify-content: space-around;
  padding: 1rem 2rem;
  gap: 1rem;
  flex-wrap: wrap;
}

.brand-nav-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
}

/* ================================
   BRAND LOGO SECTION
   ================================ */
.brand-logo-wrapper {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.menu-icon {
  font-size: 36px;
  cursor: pointer;
  transition: transform 0.3s ease;
  color: white;
}

.brand-logo {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  text-decoration: none;
  color: black;
  cursor: pointer;
}

.brand-text {
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  color: white;
  font-size: 20px;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

/* ================================
   SEARCH SECTION
   ================================ */
.search-container {
  flex: 1 1 500px;
  min-width: 200px;
  max-width: 600px;
  position: relative;
}

.search-input {
  width: 100%;
  height: 36px;
  padding: 0.5rem 1rem;
  padding-right: 3rem;
  border-radius: 25px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  margin-right: 100px;
  outline: none;
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.9);
  backdrop-filter: blur(10px);
}

.search-input:focus {
  border-color: rgba(255, 255, 255, 0.6);
  background: rgba(255, 255, 255, 0.95);
  box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.2);
}

.search-button {
  position: absolute;
  top: 50%;
  right: 0.5rem;
  transform: translateY(-50%);
  background: none;
  border: none;
  cursor: pointer;
  color: #666;
  transition: color 0.3s ease;
}

.search-button:hover {
  color: #333;
}

.search-icon {
  font-size: 18px;
}

/* ================================
   USER ACTIONS SECTION
   ================================ */
.user-actions {
  display: flex;
  align-items: center;
  gap: 14px;
  margin-left: 0.5rem;
}

.action-button {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 42px;
  height: 42px;
  border: 1px solid rgba(255, 255, 255, 0.3);
  background: rgba(255, 255, 255, 0.1);
  border-radius: 2px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.action-icon {
  font-size: 1.5rem;
  color: white;
}

/* Cart specific styles */
.cart-button {
  position: relative;
  border: 1px solid rgba(255, 255, 255, 0.3);
}

.cart-badge {
  position: absolute;
  top: -8px;
  right: -8px;
  background: #ef4444;
  color: white;
  width: 18px;
  height: 18px;
  border-radius: 30%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.64rem;
  font-weight: 600;
  animation: pulse 2s infinite;
}

/* ================================
   BRAND NAVIGATION
   ================================ */
.brand-navigation {
  background: rgba(0, 0, 0, 0.15);
  border-bottom: 1px solid #b9b1b1;
  border-top: 1px solid #b9b1b1;
  backdrop-filter: blur(5px);
}

.brand-list {
  display: flex;
  list-style: none;
  margin: 0;
  padding: 0;
  gap: 0;
}

.brand-item {
  flex: 1;
}

.brand-link {
  display: block;
  padding: 1rem 1.5rem;
  color: white;
  text-decoration: none;
  text-align: center;
  font-weight: 400;
  transition: all 0.3s ease;
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
  border-bottom: 2px solid transparent;
}

.brand-link:hover {
  background: rgba(255, 255, 255, 0.1);
  border-bottom-color: #b9b1b1;
  transform: translateY(-1px);
}

/* ================================
   SCROLL STATE STYLES
   ================================ */
.header-slideshow.is-scrolled {
  background-color: rgba(255, 255, 255, 0.95);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.header-slideshow.is-scrolled .main-navigation {
  background: rgba(255, 255, 255, 0.9);
}

.header-slideshow.is-scrolled .brand-navigation {
  background: rgba(255, 255, 255, 0.85);
  border-color: #e5e5e5;
}

/* Scrolled state element color changes */
.header-slideshow.is-scrolled .menu-icon,
.header-slideshow.is-scrolled .brand-text,
.header-slideshow.is-scrolled .brand-link,
.header-slideshow.is-scrolled .action-icon {
  color: #333333;
  text-shadow: none;
}

.header-slideshow.is-scrolled .brand-link:hover {
  background: rgba(0, 0, 0, 0.05);
  border-bottom-color: #333333;
}

.header-slideshow.is-scrolled .action-button {
  background: white;
  border-color: rgba(0, 0, 0, 0.2);
  color: #333333;
}

.header-slideshow.is-scrolled .search-input {
  border-color: rgba(0, 0, 0, 0.2);
  background: rgba(255, 255, 255, 0.95);
}

.header-slideshow.is-scrolled .search-input:focus {
  border-color: grey;
  box-shadow: 0 0 0 2px rgba(51, 51, 51, 0.1);
}

/* ================================
   RESPONSIVE DESIGN
   ================================ */
@media (max-width: 768px) {
  .nav-container {
    flex-direction: column;
    gap: 1rem;
  }

  .brand-logo-wrapper {
    width: 100%;
    justify-content: space-between;
  }

  .search-container {
    width: 100%;
    max-width: none;
  }

  .brand-list {
    flex-wrap: wrap;
  }

  .brand-item {
    flex: 1 1 25%;
    min-width: 120px;
  }

  .user-actions {
    gap: 0.5rem;
  }
}

@media (max-width: 480px) {
  .brand-item {
    flex: 1 1 50%;
  }

  .action-button {
    width: 38px;
    height: 38px;
  }

  .action-icon {
    font-size: 1.25rem;
  }
}
</style>
