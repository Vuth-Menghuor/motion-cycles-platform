<script setup>
import { ref } from 'vue'
import { Icon } from '@iconify/vue'
import Navigation_sidebar from './sidebar/navigation_sidebar.vue'

const isSidebarOpen = ref(false)

const log = async () => {
  try {
    window.location.href = '/'
  } catch (error) {
    console.error('Navigation error:', error)
  }
}

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}

const closeSidebar = () => {
  isSidebarOpen.value = false
}
</script>

<template>
  <!-- Sidebar Component -->
  <Navigation_sidebar :isOpen="isSidebarOpen" @close="closeSidebar" />

  <header class="header-slideshow">
    <nav class="main-navigation">
      <div class="nav-container">
        <!-- Logo & Menu -->
        <div class="brand-logo-wrapper">
          <Icon icon="ic:outline-menu" class="menu-icon" @click="toggleSidebar" />
          <RouterLink to="/" class="brand-logo" @click="log('Logo clicked')">
            <span class="brand-text">MOTION CYCLE</span>
          </RouterLink>
        </div>
        <!-- Search Bar -->
        <div class="search-container">
          <input type="search" placeholder="Search Bicycle" class="search-input" />
          <button class="search-button">
            <Icon icon="ri:search-line" class="search-icon" />
          </button>
        </div>
        <!-- Shopping Cart -->
        <div class="cart-container">
          <button class="cart-button">
            <Icon icon="ion:cart" class="cart-icon" />
            <span class="cart-badge">3</span>
          </button>
          <div class="account-container">
            <router-link to="/authentication/sign_in">
              <button class="user-account">
                <Icon icon="mdi:user" class="account-icon" />
              </button>
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Brand Categories Navigation -->
    <nav class="brand-navigation">
      <div class="brand-nav-container">
        <ul class="brand-list">
          <li class="brand-item">
            <a href="/brands/cannondale" class="brand-link">Cannondale</a>
          </li>
          <li class="brand-item">
            <a href="/brands/trek" class="brand-link">Trek</a>
          </li>
          <li class="brand-item">
            <a href="/brands/bianchi" class="brand-link">Bianchi</a>
          </li>
          <li class="brand-item">
            <a href="/brands/giant" class="brand-link">Giant</a>
          </li>
          <li class="brand-item">
            <a href="/brands/cervelo" class="brand-link">Cervélo</a>
          </li>
          <li class="brand-item">
            <a href="/brands/specialized" class="brand-link">Specialized</a>
          </li>
          <li class="brand-item">
            <a href="/brands/shimano" class="brand-link">Shimano</a>
          </li>
          <li class="brand-item">
            <a href="/brands/colnago" class="brand-link">Colnago</a>
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

.menu-icon:hover {
  transform: scale(1.1);
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
</style>
