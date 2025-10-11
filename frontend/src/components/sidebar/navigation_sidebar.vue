<template>
  <div class="sidebar-overlay" :class="{ 'sidebar-overlay--open': isOpen }" @click="closeSidebar">
    <div class="app-sidebar" :class="{ 'app-sidebar--open': isOpen }" @click.stop>
      <div class="app-sidebar__header">
        <div class="app-sidebar__logo">
          <span class="app-sidebar__logo-text">MOTION CYCLE</span>
        </div>
        <button class="app-sidebar__close-btn" @click="closeSidebar">
          <Icon icon="mdi:close" />
        </button>
      </div>

      <nav class="app-sidebar__nav">
        <div class="app-sidebar__section">
          <h3 class="app-sidebar__section-title">MAIN</h3>
          <ul class="app-sidebar__menu">
            <li class="app-sidebar__menu-item">
              <a href="#" class="app-sidebar__menu-link" @click="goToHome">
                <Icon icon="mdi:home" class="app-sidebar__menu-icon" />
                Home
              </a>
            </li>
            <li class="app-sidebar__menu-item">
              <a href="#" class="app-sidebar__menu-link" @click="goToFav">
                <Icon icon="solar:heart-bold" class="app-sidebar__menu-icon" />
                Favorites
                <span class="count-fav">{{ favoritesStore.favorites.length }}</span>
              </a>
            </li>
            <li class="app-sidebar__menu-item">
              <a href="#" class="app-sidebar__menu-link" @click="goToProducts">
                <Icon icon="mdi:shopping" class="app-sidebar__menu-icon" />
                Products
              </a>
            </li>
          </ul>
        </div>

        <!-- SETTINGS -->
        <div class="app-sidebar__section">
          <h3 class="app-sidebar__section-title">SETTINGS</h3>
          <ul class="app-sidebar__menu">
            <li class="app-sidebar__menu-item">
              <a href="#" class="app-sidebar__menu-link" @click="goToAppearance">
                <Icon icon="mdi:palette" class="app-sidebar__menu-icon" />
                Appearance
              </a>
            </li>
            <li class="app-sidebar__menu-item">
              <a href="#" class="app-sidebar__menu-link" @click="goToNotification">
                <Icon icon="mdi:bell" class="app-sidebar__menu-icon" />
                Notification
              </a>
            </li>
            <li class="app-sidebar__menu-item">
              <a href="#" class="app-sidebar__menu-link" @click="goToHelp">
                <Icon icon="mdi:help-circle" class="app-sidebar__menu-icon" />
                Help & Support
              </a>
            </li>
          </ul>
        </div>
      </nav>

      <!-- FOOTER -->
      <div class="app-sidebar__footer">
        <div class="app-sidebar__user">
          <div class="app-sidebar__user-avatar">
            <Icon icon="mdi:account-circle" />
          </div>
          <div class="app-sidebar__user-info">
            <div class="app-sidebar__user-name">Vuth Menghuor</div>
            <div class="app-sidebar__user-email">vuthmenghuor@gmail.com</div>
          </div>
          <button class="app-sidebar__user-menu">
            <Icon icon="mdi:dots-vertical" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useFavoritesStore } from '@/stores/favorites'
import { Icon } from '@iconify/vue'
import { useRouter } from 'vue-router'

// Define the props for the component
defineProps({ isOpen: { type: Boolean, default: false } })

// Get the favorites store
const favoritesStore = useFavoritesStore()

// Define the events the component can emit
const emit = defineEmits(['close'])

// Get the router instance
const router = useRouter()

// Function to close the sidebar
const closeSidebar = () => emit('close')

// Function to navigate to a path and close sidebar
const navigate = (path) => {
  router.push(path).then(() => {
    setTimeout(() => window.scrollTo(0, 0), 100)
    closeSidebar()
  })
}

// Function to go to home page
const goToHome = () => navigate('/')

// Function to go to favorites page
const goToFav = () => navigate('/favorites')
</script>

<style scoped>
/* Count Fav */
.count-fav {
  margin-left: 5.5rem;
  color: #3491fa;
  font-weight: 500;
  padding: 2px 10px;
  border: 1px solid #3491fa;
  border-radius: 10px;
}

.fav-product-count {
  display: flex;
}

/* Sidebar Overlay */
.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  z-index: 1000;
  opacity: 0;
  visibility: hidden;
  transition: all 0.3s ease;
}

.sidebar-overlay--open {
  opacity: 1;
  visibility: visible;
}

/* App Sidebar */
.app-sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 280px;
  height: 100vh;
  background: #ffffff;
  border-right: 2px solid #e5e7eb;
  transform: translateX(-100%);
  transition: transform 0.3s ease;
  display: flex;
  flex-direction: column;
  z-index: 1001;
}

.app-sidebar--open {
  transform: translateX(0);
}

/* Sidebar Header */
.app-sidebar__header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.5rem;
  border-bottom: 2px solid #e5e7eb;
  background: #f8fafc;
}

.app-sidebar__logo {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.app-sidebar__logo-text {
  font-family: 'Poppins', sans-serif;
  font-weight: 600;
  font-size: 18px;
  color: #3491fa;
}

.app-sidebar__close-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 0.375rem;
  transition: background-color 0.2s ease;
  color: #6b7280;
}

.app-sidebar__close-btn:hover {
  background: #e5e7eb;
  color: #374151;
}

/* Sidebar Nav */
.app-sidebar__nav {
  flex: 1;
  overflow-y: auto;
  padding: 1rem 0;
}

.app-sidebar__section {
  margin-bottom: 2rem;
}

.app-sidebar__section-title {
  font-family: 'Poppins', sans-serif;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  color: #6b7280;
  margin: 0 0 0.75rem 1.5rem;
  letter-spacing: 0.05em;
}

.app-sidebar__menu {
  list-style: none;
  padding: 0;
  margin: 0;
}

.app-sidebar__menu-item {
  margin: 0;
}

.app-sidebar__menu-link {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.75rem 1.5rem;
  color: #374151;
  text-decoration: none;
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  transition: all 0.2s ease;
  border-left: 3px solid transparent;
}

.app-sidebar__menu-link:hover {
  background: #eff6ff;
  color: #3491fa;
  border-left-color: #3491fa;
}

.app-sidebar__menu-item--active .app-sidebar__menu-link {
  background: #eff6ff;
  color: #3491fa;
  border-left-color: #3491fa;
  font-weight: 500;
}

.app-sidebar__menu-icon {
  font-size: 18px;
  flex-shrink: 0;
  text-align: center;
}

/* Sidebar Footer */
.app-sidebar__footer {
  border-top: 2px solid #e5e7eb;
  padding: 1rem;
  background: #f8fafc;
}

.app-sidebar__user {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.5rem;
  border-radius: 0.5rem;
  transition: background-color 0.2s ease;
}

.app-sidebar__user:hover {
  background: #e5e7eb;
}

.app-sidebar__user-avatar {
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6b7280;
  font-size: 32px;
}

.app-sidebar__user-info {
  flex: 1;
  min-width: 0;
}

.app-sidebar__user-name {
  font-family: 'Poppins', sans-serif;
  font-size: 14px;
  font-weight: 500;
  color: #1f2937;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.app-sidebar__user-email {
  font-family: 'Poppins', sans-serif;
  font-size: 12px;
  color: #6b7280;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.app-sidebar__user-menu {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0.25rem;
  color: #6b7280;
  border-radius: 0.25rem;
  transition: background-color 0.2s ease;
}

.app-sidebar__user-menu:hover {
  background: #d1d5db;
  color: #374151;
}

/* Responsive */
@media (max-width: 768px) {
  .app-sidebar {
    width: 100%;
    max-width: 320px;
  }
}
</style>
