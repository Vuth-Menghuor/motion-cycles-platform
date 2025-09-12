<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Icon } from '@iconify/vue'
import { gsap } from 'gsap'
import { ScrollTrigger } from 'gsap/ScrollTrigger'
import Navigation_sidebar from './sidebar/navigation_sidebar.vue'
import { useCartStore } from '@/stores/cart' // Import the cart store
import { storeToRefs } from 'pinia' // Import storeToRefs

gsap.registerPlugin(ScrollTrigger)

const props = defineProps({
  disableAnimation: {
    type: Boolean,
    default: false,
  },
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

// Access the cart store and its state
const cartStore = useCartStore()
const { count } = storeToRefs(cartStore) // Get the reactive count from the store

const isSidebarOpen = ref(false)
const headerRef = ref(null)
const menuIconRef = ref(null)

let menuTl = null

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
  if (menuTl) {
    menuTl.reversed() ? menuTl.play() : menuTl.reverse()
  }
}

const closeSidebar = () => {
  isSidebarOpen.value = false
  if (menuTl) {
    menuTl.reverse()
  }
}

const handleClickOutside = (event) => {
  if (!headerRef.value?.contains(event.target)) {
    closeSidebar()
  }
}

onMounted(() => {
  if (!props.disableAnimation) {
    menuTl = gsap.timeline({ reversed: true })

    menuTl.to('.sidebar-content', {
      x: 0,
      duration: 0.3,
      ease: 'power2.out',
    })

    gsap.to(headerRef.value, {
      scrollTrigger: {
        trigger: 'body',
        start: '840 top',
        toggleClass: { targets: headerRef.value, className: 'is-scrolled' },
      },
    })

    gsap.fromTo(
      '.brand-item',
      { y: -50, opacity: 0 },
      { y: 0, opacity: 1, duration: 0.6, stagger: 0.1, ease: 'power2.out' },
    )

    document.addEventListener('click', handleClickOutside)
  }
})

onUnmounted(() => {
  ScrollTrigger.getAll().forEach((trigger) => trigger.kill())
  document.removeEventListener('click', handleClickOutside)
})
</script>

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
          <RouterLink to="/" class="brand-logo" @click="log">
            <span class="brand-text" :style="{ color: colors.logoName || '' }">MOTION CYCLE</span>
          </RouterLink>
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
        <div class="cart-container">
          <button class="cart-button">
            <Icon icon="ion:cart" class="cart-icon" :style="{ color: colors.cartIcon }" />
            <span class="cart-badge" v-if="count > 0">{{ count }}</span>
          </button>
          <div class="account-container">
            <router-link to="/authentication/sign_in">
              <button
                class="user-account"
                :style="{
                  backgroundColor: colors.userBgBtn || '',
                  borderColor: colors.userBorderBtn,
                }"
              >
                <Icon icon="mdi:user" class="account-icon" :style="{ color: colors.userIcon }" />
              </button>
            </router-link>
          </div>
        </div>
      </div>
    </nav>
    <nav
      class="brand-navigation"
      :style="{ backgroundColor: colors.brandBg || '', borderColor: colors.brandBorder }"
    >
      <div class="brand-nav-container">
        <ul class="brand-list">
          <li class="brand-item">
            <a href="/brands/cannondale" class="brand-link" :style="{ color: colors.brandName }"
              >Cannondale</a
            >
          </li>
          <li class="brand-item">
            <a href="/brands/trek" class="brand-link" :style="{ color: colors.brandName }">Trek</a>
          </li>
          <li class="brand-item">
            <a href="/brands/bianchi" class="brand-link" :style="{ color: colors.brandName }"
              >Bianchi</a
            >
          </li>
          <li class="brand-item">
            <a href="/brands/giant" class="brand-link" :style="{ color: colors.brandName }"
              >Giant</a
            >
          </li>
          <li class="brand-item">
            <a href="/brands/cervelo" class="brand-link" :style="{ color: colors.brandName }"
              >Cervélo</a
            >
          </li>
          <li class="brand-item">
            <a href="/brands/specialized" class="brand-link" :style="{ color: colors.brandName }"
              >Specialized</a
            >
          </li>
          <li class="brand-item">
            <a href="/brands/shimano" class="brand-link" :style="{ color: colors.brandName }"
              >Shimano</a
            >
          </li>
          <li class="brand-item">
            <a href="/brands/colnago" class="brand-link" :style="{ color: colors.brandName }"
              >Colnago</a
            >
          </li>
        </ul>
      </div>
    </nav>
  </header>
</template>

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

/* Menu icon color change on scroll */
.header-slideshow.is-scrolled .menu-icon {
  color: #333333;
}

/* Brand text color change on scroll */
.header-slideshow.is-scrolled .brand-text {
  color: #333333;
  text-shadow: none;
}

/* Brand links color change on scroll */
.header-slideshow.is-scrolled .brand-link {
  color: #333333;
}

.header-slideshow.is-scrolled .brand-link:hover {
  background: rgba(0, 0, 0, 0.05);
  border-bottom-color: #333333;
}

/* Cart icon color change on scroll */
.header-slideshow.is-scrolled .cart-icon {
  color: #333333;
}

/* User account icon color change on scroll */
.header-slideshow.is-scrolled .user-account {
  background: white;
  border-color: rgba(0, 0, 0, 0.2);
  color: #333333;
}

/* Search input border change on scroll */
.header-slideshow.is-scrolled .search-input {
  border-color: rgba(0, 0, 0, 0.2);
  background: rgba(255, 255, 255, 0.95);
}

.header-slideshow.is-scrolled .search-input:focus {
  border-color: grey;
  box-shadow: 0 0 0 2px rgba(51, 51, 51, 0.1);
}

.account-container {
  display: flex;
  align-items: center;
  justify-content: center;
}

.user-account {
  background: rgba(255, 255, 255, 0.1);
  border: 1px solid rgba(255, 255, 255, 0.3);
  width: 42px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: white;
}

.account-icon {
  font-size: 22px;
}

.brand-navigation {
  background: rgba(0, 0, 0, 0.15);
  border-bottom: 1px solid #b9b1b1;
  border-top: 1px solid #b9b1b1;
  backdrop-filter: blur(5px);
}

.brand-nav-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 2rem;
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
}

.logo-image {
  width: 45px;
  height: auto;
}

.brand-text {
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  color: white;
  font-size: 20px;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}

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

.cart-container {
  position: relative;
  margin-left: 0.5rem;
  display: flex;
  align-items: center;
  gap: 2rem;
}

.cart-button {
  background: none;
  border: none;
  cursor: pointer;
  position: relative;
  padding-top: 0.5rem;
  transition: transform 0.3s ease;
}

.cart-icon {
  width: 30px;
  height: auto;
  color: white;
}

.cart-badge {
  position: absolute;
  top: -6px;
  right: -6px;
  background: #ef4444;
  color: white;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.75rem;
  font-weight: 600;
  animation: pulse 2s infinite;
}
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
}
</style>
