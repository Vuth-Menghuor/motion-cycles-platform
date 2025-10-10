<template>
  <div class="admin-layout">
    <!-- Sidebar Navigation -->
    <aside class="sidebar" :class="{ 'sidebar-open': sidebarOpen }">
      <!-- Brand Section -->
      <div class="brand">
        <h1 class="brand-name">MOTION CYCLE</h1>
      </div>

      <!-- User Profile Section -->
      <div class="user-profile">
        <img :src="user.avatar" :alt="user.name" class="avatar" />
        <p class="user-name">{{ user.name }}</p>
        <div class="user-actions">
          <div class="action-item">
            <button class="action-btn" title="Password" @click="showPasswordModal = true">
              <Icon icon="charm:key" class="action" />
            </button>
            <span class="action-label">Password</span>
          </div>
          <div class="action-item">
            <button class="action-btn" title="My Profile" @click="showProfileModal = true">
              <Icon icon="solar:user-linear" class="action" />
            </button>
            <span class="action-label">My Profile</span>
          </div>
          <div class="action-item">
            <button class="action-btn" title="Logout">
              <Icon icon="material-symbols:logout" class="action" />
            </button>
            <span class="action-label">Logout</span>
          </div>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="nav-menu">
        <router-link
          to="/admin/dashboard"
          class="nav-item"
          @click="handleDashboardClick"
          :class="{ active: activeNav === 'dashboard' }"
        >
          <Icon icon="material-symbols:dashboard-outline-rounded" class="nav-icon" />
          <span class="nav-label">Dashboard</span>
        </router-link>
        <div
          class="nav-item"
          @click="handleProductsClick"
          :class="{ active: activeNav === 'products' }"
        >
          <Icon icon="charm:package" class="nav-icon" />
          <span class="nav-label">Products</span>
          <Icon
            :icon="productsDropdownOpen ? 'charm:chevron-down' : 'charm:chevron-right'"
            class="icon-chevron"
          />
        </div>
        <div v-if="productsDropdownOpen" class="submenu">
          <router-link to="/admin/products/manage" class="submenu-item" active-class="active">
            Manage Product
          </router-link>
          <router-link to="/admin/products/discount" class="submenu-item" active-class="active">
            Manage Discount
          </router-link>
          <router-link to="/admin/products/feedback" class="submenu-item" active-class="active">
            Manage Feedback
          </router-link>
          <router-link to="/admin/products/stock" class="submenu-item" active-class="active">
            Manage Stock
          </router-link>
        </div>
        <router-link
          to="/admin/orders/list"
          class="nav-item"
          @click="handleOrdersClick"
          :class="{ active: activeNav === 'orders' }"
        >
          <Icon icon="material-symbols:orders-outline" class="nav-icon" />
          <span class="nav-label">Orders</span>
        </router-link>
        <router-link
          to="/admin/customers/list"
          class="nav-item"
          @click="handleCustomersClick"
          :class="{ active: activeNav === 'customers' }"
        >
          <Icon icon="hugeicons:user-ai" class="nav-icon" />
          <span class="nav-label">Customers</span>
        </router-link>
        <router-link
          to="/admin/analytics"
          class="nav-item"
          @click="handleAnalyticsClick"
          :class="{ active: activeNav === 'analytics' }"
        >
          <Icon icon="streamline-ultimate:money-bag-dollar" class="nav-icon" />
          <span class="nav-label">Revenue & Profit Analytics</span>
        </router-link>
      </nav>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content" :class="{ 'sidebar-open': sidebarOpen }">
      <!-- Top Header -->
      <header class="top-header">
        <div class="header-left">
          <button
            class="sidebar-toggle"
            @click="toggleSidebar"
            :aria-label="sidebarOpen ? 'Close sidebar' : 'Open sidebar'"
          >
            <Icon icon="charm:menu" />
          </button>
          <div class="search-bar">
            <input type="text" placeholder="Search something..." v-model="searchQuery" />
            <button class="search-btn">
              <Icon icon="charm:search" />
            </button>
          </div>
        </div>
        <div class="header-actions">
          <button class="profile-btn">
            <img :src="user.avatar" :alt="user.name" />
            <Icon icon="nrk:more" class="profile-icon" />
          </button>
        </div>
      </header>

      <!-- Navigation Tabs -->
      <div class="page-tabs">
        <div class="tabs">
          <router-link
            v-for="tab in availableTabs"
            :key="tab.path"
            :to="tab.path"
            class="tab"
            active-class="active"
          >
            <Icon :icon="tab.icon" class="tab-icon" />
            <span>{{ tab.label }}</span>
            <button
              v-if="openTabs.length > 1"
              class="tab-close"
              @click.prevent.stop="closeTab(tab.path)"
              title="Close tab"
            >
              <Icon icon="charm:cross" class="close-icon" />
            </button>
          </router-link>
        </div>
      </div>

      <!-- Page Content -->
      <div class="page-content">
        <router-view />
      </div>
    </main>

    <!-- Password Change Modal -->
    <div v-if="showPasswordModal" class="modal-overlay" @click="closePasswordModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Change Password</h3>
          <button class="modal-close" @click="closePasswordModal">
            <Icon icon="charm:cross" />
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="changePassword" class="password-form">
            <div class="form-group">
              <label for="oldPassword">Current Password</label>
              <input
                id="oldPassword"
                v-model="passwordForm.oldPassword"
                type="password"
                class="form-input"
                placeholder="Enter current password"
                required
              />
            </div>
            <div class="form-group">
              <label for="newPassword">New Password</label>
              <input
                id="newPassword"
                v-model="passwordForm.newPassword"
                type="password"
                class="form-input"
                placeholder="Enter new password"
                required
              />
            </div>
            <div class="form-group">
              <label for="confirmPassword">Confirm New Password</label>
              <input
                id="confirmPassword"
                v-model="passwordForm.confirmPassword"
                type="password"
                class="form-input"
                placeholder="Confirm new password"
                required
              />
            </div>
            <div class="form-actions">
              <button type="button" class="btn btn-secondary" @click="closePasswordModal">
                Cancel
              </button>
              <button type="submit" class="btn btn-primary">Change Password</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Profile Update Modal -->
    <div v-if="showProfileModal" class="modal-overlay" @click="closeProfileModal">
      <div class="modal-content" @click.stop>
        <div class="modal-header">
          <h3>Update Profile</h3>
          <button class="modal-close" @click="closeProfileModal">
            <Icon icon="charm:cross" />
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="updateProfile" class="profile-form">
            <div class="avatar-section">
              <div class="current-avatar">
                <img
                  :src="profileForm.avatar || user.avatar"
                  :alt="profileForm.name"
                  class="avatar-preview"
                />
              </div>
              <div class="avatar-upload">
                <input
                  id="avatarInput"
                  type="file"
                  accept="image/*"
                  @change="handleAvatarChange"
                  class="avatar-input"
                />
                <label for="avatarInput" class="avatar-label">
                  <Icon icon="material-symbols:edit" />
                  Change Avatar
                </label>
              </div>
            </div>

            <div class="form-group">
              <label for="accountName">Account Name</label>
              <input
                id="accountName"
                v-model="profileForm.name"
                type="text"
                class="form-input"
                placeholder="Enter account name"
                required
              />
            </div>
            <div class="form-group">
              <label for="username">Username</label>
              <input
                id="username"
                v-model="profileForm.username"
                type="text"
                class="form-input"
                placeholder="Enter username"
                required
              />
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input
                id="email"
                v-model="profileForm.email"
                type="email"
                class="form-input"
                placeholder="Enter email address"
                required
              />
            </div>
            <div class="form-group">
              <label for="contactAddress">Contact Address</label>
              <textarea
                id="contactAddress"
                v-model="profileForm.address"
                class="form-textarea"
                placeholder="Enter contact address"
                rows="3"
                required
              ></textarea>
            </div>
            <div class="form-actions">
              <button type="button" class="btn btn-secondary" @click="closeProfileModal">
                Cancel
              </button>
              <button type="submit" class="btn btn-primary">Update Profile</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import adminAvatar from '@/assets/images/admin/admin_avatar.png'
import { Icon } from '@iconify/vue'

// ===========================================
// CONFIGURATION CONSTANTS
// ===========================================

/**
 * Tab configuration object - defines all available admin tabs with their properties
 * Each tab has a unique key, path, icon, and display label
 */
const TAB_CONFIG = {
  dashboard: {
    path: '/admin/dashboard',
    icon: 'material-symbols:dashboard-outline-rounded',
    label: 'Dashboard',
  },
  manage: { path: '/admin/products/manage', icon: 'charm:package', label: 'Manage Product' },
  add: { path: '/admin/products/add', icon: 'material-symbols:add', label: 'Add Product' },
  edit: { path: '/admin/products/edit', icon: 'material-symbols:edit', label: 'Edit Product' },
  discount: {
    path: '/admin/products/discount',
    icon: 'ic:outline-discount',
    label: 'Manage Discount',
  },
  feedback: {
    path: '/admin/products/feedback',
    icon: 'material-symbols:feedback-outline',
    label: 'Manage Feedback',
  },
  stock: {
    path: '/admin/products/stock',
    icon: 'material-symbols:inventory',
    label: 'Manage Stock',
  },
  orderList: { path: '/admin/orders/list', icon: 'lets-icons:order', label: 'Order List' },
  viewOrder: {
    path: '/admin/orders/view',
    icon: 'material-symbols:visibility',
    label: 'View Order',
  },
  editOrder: { path: '/admin/orders/edit', icon: 'material-symbols:edit', label: 'Edit Order' },
  customerList: {
    path: '/admin/customers/list',
    icon: 'hugeicons:user-ai',
    label: 'Customer List',
  },
  viewCustomer: {
    path: '/admin/customers/view',
    icon: 'material-symbols:visibility',
    label: 'View Customer',
  },
  editCustomer: {
    path: '/admin/customers/edit',
    icon: 'material-symbols:edit',
    label: 'Edit Customer',
  },
  analytics: {
    path: '/admin/analytics',
    icon: 'streamline-ultimate:money-bag-dollar',
    label: 'Revenue & Profit Analytics',
  },
}

// ===========================================
// ROUTE TO TAB MAPPING
// ===========================================

/**
 * Maps route paths to tab configuration keys
 * Used for determining which tab to open when navigating
 */
const ROUTE_TO_TAB_MAP = {
  '/admin/dashboard': 'dashboard',
  '/admin/products/manage': 'manage',
  '/admin/products/add': 'add',
  '/admin/products/discount': 'discount',
  '/admin/products/feedback': 'feedback',
  '/admin/products/stock': 'stock',
  '/admin/orders/list': 'orderList',
  '/admin/orders/view': 'viewOrder',
  '/admin/customers/list': 'customerList',
  '/admin/customers/view': 'viewCustomer',
  '/admin/analytics': 'analytics',
}

// ===========================================
// REACTIVE STATE
// ===========================================

// Router and route instances
const router = useRouter()
const route = useRoute()

// UI state management
const searchQuery = ref('')
const sidebarOpen = ref(true)
const productsDropdownOpen = ref(false)
const activeNav = ref('dashboard')

// Modal visibility states
const showPasswordModal = ref(false)
const showProfileModal = ref(false)

// Form data with reactive objects
const passwordForm = ref({
  oldPassword: '',
  newPassword: '',
  confirmPassword: '',
})

const profileForm = ref({
  name: '',
  username: '',
  email: '',
  address: '',
  avatar: null,
})

// User information (in a real app, this would come from a store/API)
const user = ref({
  name: 'Admin User',
  avatar: adminAvatar,
})

// Tab management - tracks which tabs are currently open
const openTabs = ref(['dashboard'])

// ===========================================
// COMPUTED PROPERTIES
// ===========================================

/**
 * Available tabs computed property
 * Returns array of tab objects for currently open tabs
 * Filters out any undefined tabs and maps to configuration
 */
const availableTabs = computed(() => {
  return openTabs.value.map((tabKey) => TAB_CONFIG[tabKey]).filter(Boolean) // Remove any undefined tabs
})

// ===========================================
// WATCHERS
// ===========================================

/**
 * Route watcher - automatically manages tabs when route changes
 * Adds new tabs to openTabs array and updates active navigation
 */
watch(
  () => route.path,
  (newPath) => {
    // Determine which tab this route corresponds to
    const tabKey = getTabKeyFromRoute(newPath)

    // Add tab to open tabs if not already present
    if (tabKey && !openTabs.value.includes(tabKey)) {
      openTabs.value.push(tabKey)
    }

    // Update active navigation based on route
    updateActiveNavigation(newPath)
  },
)

// ===========================================
// HELPER FUNCTIONS
// ===========================================

/**
 * Maps a route path to its corresponding tab key
 * @param {string} path - The route path
 * @returns {string|null} The tab key or null if not found
 */
const getTabKeyFromRoute = (path) => {
  // Direct mapping for exact matches
  if (ROUTE_TO_TAB_MAP[path]) {
    return ROUTE_TO_TAB_MAP[path]
  }

  // Handle dynamic routes (edit pages)
  if (path.startsWith('/admin/products/edit/')) return 'edit'
  if (path.startsWith('/admin/orders/edit/')) return 'editOrder'
  if (path.startsWith('/admin/customers/edit/')) return 'editCustomer'

  return null
}

/**
 * Updates the active navigation state based on current route
 * @param {string} path - The current route path
 */
const updateActiveNavigation = (path) => {
  if (path.startsWith('/admin/products')) {
    activeNav.value = 'products'
  } else if (path.startsWith('/admin/orders')) {
    activeNav.value = 'orders'
  } else if (path.startsWith('/admin/customers')) {
    activeNav.value = 'customers'
  } else if (path === '/admin/dashboard') {
    activeNav.value = 'dashboard'
  } else if (path === '/admin/analytics') {
    activeNav.value = 'analytics'
  }
}

// ===========================================
// NAVIGATION METHODS
// ===========================================

/**
 * Toggles sidebar visibility
 */
const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

/**
 * Handles dashboard navigation click
 */
const handleDashboardClick = () => {
  resetNavigationState()
  activeNav.value = 'dashboard'
}

/**
 * Handles products navigation click - toggles dropdown
 */
const handleProductsClick = () => {
  productsDropdownOpen.value = !productsDropdownOpen.value
  activeNav.value = 'products'
}

/**
 * Handles orders navigation click
 */
const handleOrdersClick = () => {
  resetNavigationState()
  activeNav.value = 'orders'
}

/**
 * Handles customers navigation click
 */
const handleCustomersClick = () => {
  resetNavigationState()
  activeNav.value = 'customers'
}

/**
 * Handles analytics navigation click
 */
const handleAnalyticsClick = () => {
  resetNavigationState()
  activeNav.value = 'analytics'
}

/**
 * Resets navigation dropdown states
 */
const resetNavigationState = () => {
  productsDropdownOpen.value = false
}

// ===========================================
// TAB MANAGEMENT METHODS
// ===========================================

/**
 * Closes a specific tab and handles navigation to remaining tabs
 * @param {string} tabPath - The path of the tab to close
 */
const closeTab = (tabPath) => {
  const tabKey = getTabKeyFromRoute(tabPath)

  if (!tabKey) return

  const tabIndex = openTabs.value.indexOf(tabKey)

  if (tabIndex === -1) return

  // Remove tab from open tabs
  openTabs.value.splice(tabIndex, 1)

  // If we closed the currently active tab, navigate to another open tab
  const currentTabKey = getTabKeyFromRoute(route.path)
  if (currentTabKey === tabKey) {
    navigateToNextAvailableTab()
  }
}

/**
 * Navigates to the next available tab when current tab is closed
 */
const navigateToNextAvailableTab = () => {
  const nextTabKey = openTabs.value[0] || 'dashboard'
  const nextPath = TAB_CONFIG[nextTabKey]?.path || '/admin/dashboard'
  router.push(nextPath)
}

// ===========================================
// MODAL METHODS
// ===========================================

/**
 * Closes password change modal and resets form
 */
const closePasswordModal = () => {
  showPasswordModal.value = false
  resetPasswordForm()
}

/**
 * Closes profile update modal and resets form
 */
const closeProfileModal = () => {
  showProfileModal.value = false
  resetProfileForm()
}

/**
 * Resets password form to initial state
 */
const resetPasswordForm = () => {
  passwordForm.value = {
    oldPassword: '',
    newPassword: '',
    confirmPassword: '',
  }
}

/**
 * Resets profile form to current user data
 */
const resetProfileForm = () => {
  profileForm.value = {
    name: user.value.name,
    username: '',
    email: '',
    address: '',
    avatar: null,
  }
}

// ===========================================
// FORM HANDLING METHODS
// ===========================================

/**
 * Handles password change form submission with validation
 */
const changePassword = () => {
  // Validate password match
  if (passwordForm.value.newPassword !== passwordForm.value.confirmPassword) {
    alert('New passwords do not match!')
    return
  }

  // Validate password length
  if (passwordForm.value.newPassword.length < 6) {
    alert('New password must be at least 6 characters long!')
    return
  }

  // In a real application, this would make an API call
  console.log('Password change request:', {
    oldPassword: passwordForm.value.oldPassword,
    newPassword: passwordForm.value.newPassword,
  })

  alert('Password changed successfully!')
  closePasswordModal()
}

/**
 * Handles profile update form submission
 */
const updateProfile = () => {
  // In a real application, this would make an API call
  console.log('Profile update request:', profileForm.value)

  // Update local user data
  user.value.name = profileForm.value.name
  if (profileForm.value.avatar) {
    user.value.avatar = profileForm.value.avatar
  }

  alert('Profile updated successfully!')
  closeProfileModal()
}

/**
 * Handles avatar file selection with validation
 * @param {Event} event - File input change event
 */
const handleAvatarChange = (event) => {
  const file = event.target.files[0]

  if (!file) return

  // Validate file type
  if (!file.type.startsWith('image/')) {
    alert('Please select a valid image file!')
    return
  }

  // Validate file size (max 5MB)
  const maxSize = 5 * 1024 * 1024 // 5MB in bytes
  if (file.size > maxSize) {
    alert('File size must be less than 5MB!')
    return
  }

  // Create preview URL for the selected image
  const reader = new FileReader()
  reader.onload = (e) => {
    profileForm.value.avatar = e.target.result
  }
  reader.readAsDataURL(file)
}

// ===========================================
// LIFECYCLE HOOKS
// ===========================================

/**
 * Component mount hook - initializes form data
 */
onMounted(() => {
  // Initialize profile form with current user data
  resetProfileForm()
})
</script>

<style scoped>
/* ===========================================
   LAYOUT STRUCTURE
   =========================================== */

.admin-layout {
  display: flex;
  height: 100vh;
  flex-direction: row;
  background-color: rgba(230, 230, 230, 0.2);
  overflow: hidden;
}

/* ===========================================
   SIDEBAR STYLES
   =========================================== */

.admin-layout > .sidebar {
  width: 250px;
  background-color: #1e3a8a;
  color: white;
  padding: 20px;
  flex-shrink: 0;
  transition: width 0.3s ease;
}

.admin-layout > .main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  min-width: 0;
}

/* Brand Section */
.brand {
  text-align: center;
  margin-bottom: 30px;
}

.brand-name {
  font-size: 22px;
  font-weight: 600;
  margin: 0;
  margin-top: 2rem;
  font-family: 'Poppins', sans-serif;
}

/* User Profile Section */
.user-profile {
  text-align: center;
  padding: 20px 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  margin-bottom: 20px;
}

.avatar {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 10px;
  border: 3px solid rgba(255, 255, 255, 0.2);
}

.user-name {
  font-size: 14px;
  font-weight: 400;
  margin: 0 0 12px 0;
  font-family: 'Poppins', sans-serif;
}

.user-actions {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-top: 15px;
}

.action-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.action-btn {
  background: rgba(255, 255, 255, 0.1);
  border: none;
  color: white;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.3s;
}

.action-btn:hover {
  background: rgba(255, 255, 255, 0.2);
}

.action-label {
  font-size: 11px;
  font-weight: 400;
  color: rgba(255, 255, 255, 0.8);
  text-align: center;
  white-space: nowrap;
  font-family: 'Poppins', sans-serif;
}

/* Navigation Menu */
.nav-menu {
  display: flex;
  flex-direction: column;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 0 20px;
  height: 56px;
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  transition: all 0.3s;
  gap: 12px;
  font-family: 'Poppins', sans-serif;
  margin: 0 -22px;
  width: calc(100% + 10);
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #ff9934;
}

.nav-item:hover .nav-icon,
.nav-item:hover .icon-chevron {
  color: #ff9934;
}

.nav-item.active {
  background: rgba(255, 255, 255, 0.15);
  color: #ff9934;
}

.nav-item.active .nav-icon,
.nav-item.active .icon-chevron {
  color: #ff9934;
}

.nav-item .icon-chevron {
  margin-left: auto;
}

.nav-label {
  flex: 1;
  font-size: 12px;
  font-weight: 500;
}

.nav-icon {
  width: 20px;
  height: 20px;
  color: #d9d9d9;
}

/* Submenu Styles */
.submenu {
  display: flex;
  flex-direction: column;
  margin-left: 32px;
}

.submenu-item {
  display: flex;
  align-items: center;
  padding: 10px 20px;
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  font-size: 12px;
  font-weight: 500;
  transition: all 0.3s;
  font-family: 'Poppins', sans-serif;
  margin: 0 -22px;
  width: calc(100% + 10px);
}

.submenu-item:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #ff9934;
}

.submenu-item.active {
  background: rgba(255, 255, 255, 0.15);
  color: #ff9934;
}

/* ===========================================
   HEADER STYLES
   =========================================== */

.top-header {
  background: white;
  padding: 15px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 2px solid #eee;
  position: sticky;
  top: 0;
  z-index: 100;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 15px;
}

.sidebar-toggle {
  background: none;
  border: none;
  color: #666;
  font-size: 20px;
  cursor: pointer;
  padding: 8px;
  border-radius: 4px;
  transition: background 0.3s;
  display: none;
}

.sidebar-toggle:hover {
  background: #f5f5f5;
}

.search-bar {
  display: flex;
  align-items: center;
  background: #f5f5f5;
  padding: 10px 15px;
  border-radius: 8px;
  width: 600px;
  gap: 10px;
}

.search-bar input {
  border: none;
  background: transparent;
  outline: none;
  flex: 1;
  font-size: 12px;
  font-family: 'Poppins', sans-serif;
}

.search-btn {
  background: none;
  border: none;
  color: #666;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s;
}

.search-btn:hover {
  background: rgba(0, 0, 0, 0.1);
  color: #333;
}

.header-actions {
  display: flex;
  align-items: center;
}

.profile-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  display: flex;
  align-items: center;
  gap: 6px;
}

.profile-btn img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.profile-icon {
  width: 18px;
  height: 18px;
  color: #666;
}

/* ===========================================
   TABS STYLES
   =========================================== */

.page-tabs {
  background: white;
  border-bottom: 1px solid #e5e7eb;
}

.tabs {
  display: flex;
  background: #ffffff;
}

.tab {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  height: 42px;
  padding: 0 10px 0 16px;
  border-right: 1px solid #e5e7eb;
  cursor: pointer;
  font-size: 12px;
  transition: all 0.18s ease;
  color: grey;
  font-weight: 500;
  text-decoration: none;
  font-family: 'Poppins', sans-serif;
  position: relative;
}

.tab.active {
  background-color: #f7f7f7;
  color: #ff9934;
  box-shadow: 0 6px 20px rgba(14, 165, 233, 0.06);
}

.tab:hover {
  color: #ff9934;
}

.tab-icon {
  width: 18px;
  height: 18px;
  color: grey;
}

.tab:hover .tab-icon,
.tab.active .tab-icon {
  color: grey;
}

/* Tab Close Button */
.tab-close {
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
  border-radius: 2px;
  opacity: 0;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: 8px;
}

.tab:hover .tab-close {
  opacity: 1;
}

.tab-close:hover {
  background: rgba(0, 0, 0, 0.1);
}

.close-icon {
  width: 12px;
  height: 12px;
  color: #666;
}

.tab-close:hover .close-icon {
  color: #333;
}

/* ===========================================
   CONTENT STYLES
   =========================================== */

.page-content {
  padding: 16px;
  flex: 1;
  min-height: 0;
  /* overflow-y: auto; */
}

/* ===========================================
   BUTTON STYLES
   =========================================== */

.btn {
  padding: 0.5rem 1rem;
  border: none;
  border-radius: 4px;
  font-size: 0.9rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-primary {
  background: #14c9c9;
  color: white;
}

.btn-primary:hover {
  background: #0fa5a5;
}

.btn-secondary {
  background: #f8f9fa;
  color: #333;
  border: 1px solid #dee2e6;
}

/* ===========================================
   MODAL STYLES
   =========================================== */

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 8px;
  width: 90%;
  max-width: 500px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
  margin: 0;
  font-size: 18px;
  font-weight: 600;
  color: #1f2937;
  font-family: 'Poppins', sans-serif;
}

.modal-close {
  background: none;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 4px;
  border-radius: 4px;
  transition: background 0.2s;
}

.modal-close:hover {
  background: #f3f4f6;
  color: #374151;
}

.modal-body {
  padding: 24px;
}

/* Form Styles */
.password-form,
.profile-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-group label {
  font-size: 14px;
  font-weight: 500;
  color: #374151;
  font-family: 'Poppins', sans-serif;
}

.form-input,
.form-textarea {
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
  transition: border-color 0.2s;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #14c9c9;
  box-shadow: 0 0 0 3px rgba(20, 201, 201, 0.1);
}

.form-textarea {
  resize: vertical;
  min-height: 80px;
}

/* Avatar Section */
.avatar-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
  padding: 20px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  background: #f9fafb;
}

.current-avatar {
  position: relative;
}

.avatar-preview {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid #e5e7eb;
}

.avatar-upload {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.avatar-input {
  display: none;
}

.avatar-label {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 16px;
  background: #14c9c9;
  color: white;
  border-radius: 6px;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  transition: background 0.2s;
  font-family: 'Poppins', sans-serif;
}

.avatar-label:hover {
  background: #0fa5a5;
}

/* Form Actions */
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 20px;
  border-top: 1px solid #e5e7eb;
}

/* ===========================================
   RESPONSIVE STYLES
   =========================================== */

@media (max-width: 768px) {
  .admin-layout {
    flex-direction: column;
  }

  .admin-layout > .sidebar {
    width: 100%;
    height: auto;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
  }

  .admin-layout > .sidebar.sidebar-open {
    max-height: 500px;
  }

  .sidebar-toggle {
    display: block;
  }

  .search-bar {
    width: 250px;
  }

  .page-content {
    padding: 20px;
  }
}

@media (max-width: 480px) {
  .top-header {
    padding: 10px 15px;
  }

  .search-bar {
    width: 200px;
  }

  .page-content {
    padding: 15px;
  }
}

/* ===========================================
   UTILITY CLASSES
   =========================================== */

.action {
  width: 16px;
  height: 16px;
  color: #d9d9d9;
}
</style>
