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
            <button class="action-btn" title="Password">
              <Icon icon="charm:key" class="action" />
            </button>
            <span class="action-label">Password</span>
          </div>
          <div class="action-item">
            <button class="action-btn" title="My Profile">
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
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import adminAvatar from '@/assets/images/admin/admin_avatar.png'
import { Icon } from '@iconify/vue'

// ===========================================
// CONSTANTS & CONFIGURATION
// ===========================================

/**
 * Tab configuration - defines available tabs with their properties
 */
const TAB_CONFIG = {
  dashboard: {
    path: '/admin/dashboard',
    icon: 'material-symbols:dashboard-outline-rounded',
    label: 'Dashboard',
  },
  products: {
    path: '/admin/products',
    icon: 'charm:package',
    label: 'Products',
  },
  manage: {
    path: '/admin/products/manage',
    icon: 'charm:package',
    label: 'Manage Product',
  },
  add: {
    path: '/admin/products/add',
    icon: 'material-symbols:add',
    label: 'Add Product',
  },
  edit: {
    path: '/admin/products/edit',
    icon: 'material-symbols:edit',
    label: 'Edit Product',
  },
  discount: {
    path: '/admin/products/discount',
    icon: 'material-symbols:discount',
    label: 'Manage Discount',
  },
  feedback: {
    path: '/admin/products/feedback',
    icon: 'material-symbols:feedback',
    label: 'Manage Feedback',
  },
  stock: {
    path: '/admin/products/stock',
    icon: 'material-symbols:inventory',
    label: 'Manage Stock',
  },
  orders: {
    path: '/admin/orders',
    icon: 'material-symbols:orders-outline',
    label: 'Orders',
  },
  orderList: {
    path: '/admin/orders/list',
    icon: 'material-symbols:list-alt',
    label: 'Order List',
  },
  viewOrder: {
    path: '/admin/orders/view',
    icon: 'material-symbols:visibility',
    label: 'View Order',
  },
  editOrder: {
    path: '/admin/orders/edit',
    icon: 'material-symbols:edit',
    label: 'Edit Order',
  },
  customers: {
    path: '/admin/customers/list',
    icon: 'hugeicons:user-ai',
    label: 'Customers',
  },
  customerList: {
    path: '/admin/customers/list',
    icon: 'material-symbols:list-alt',
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
// REACTIVE DATA
// ===========================================

// Router instances
const router = useRouter()
const route = useRoute()

// UI state
const searchQuery = ref('')
const sidebarOpen = ref(true)
const productsDropdownOpen = ref(false)
const activeNav = ref('dashboard')

// User information
const user = ref({
  name: 'Admin User',
  avatar: adminAvatar,
})

// Tab management - starts with dashboard only
const openTabs = ref(['dashboard'])

// ===========================================
// COMPUTED PROPERTIES
// ===========================================

/**
 * Get available tabs based on currently open tabs
 * Filters tab config to only show tabs that are open
 */
const availableTabs = computed(() => {
  return openTabs.value.map((tabKey) => TAB_CONFIG[tabKey]).filter(Boolean) // Remove any undefined tabs
})

// ===========================================
// WATCHERS
// ===========================================

/**
 * Watch for route changes to automatically add new tabs
 * When user navigates to a new admin page, add it to open tabs
 */
watch(
  () => route.path,
  (newPath) => {
    let page = ''

    // Handle different route patterns
    if (newPath === '/admin/dashboard') {
      page = 'dashboard'
    } else if (newPath === '/admin/products/manage') {
      page = 'manage'
    } else if (newPath === '/admin/products/add') {
      page = 'add'
    } else if (newPath.startsWith('/admin/products/edit/')) {
      page = 'edit'
    } else if (newPath === '/admin/products/discount') {
      page = 'discount'
    } else if (newPath === '/admin/products/feedback') {
      page = 'feedback'
    } else if (newPath === '/admin/products/stock') {
      page = 'stock'
    } else if (newPath === '/admin/orders/list') {
      page = 'orderList'
    } else if (newPath === '/admin/orders/view') {
      page = 'viewOrder'
    } else if (newPath.startsWith('/admin/orders/edit/')) {
      page = 'editOrder'
    } else if (newPath === '/admin/customers') {
      page = 'customers'
    } else if (newPath === '/admin/customers/list') {
      page = 'customerList'
    } else if (newPath === '/admin/customers/view') {
      page = 'viewCustomer'
    } else if (newPath.startsWith('/admin/customers/edit/')) {
      page = 'editCustomer'
    } else if (newPath === '/admin/analytics') {
      page = 'analytics'
    }

    if (page && !openTabs.value.includes(page)) {
      openTabs.value.push(page)
    }

    // Set active nav based on route
    if (newPath.startsWith('/admin/products')) {
      activeNav.value = 'products'
    } else if (newPath.startsWith('/admin/orders')) {
      activeNav.value = 'orders'
    } else if (newPath.startsWith('/admin/customers')) {
      activeNav.value = 'customers'
    } else if (newPath === '/admin/dashboard') {
      activeNav.value = 'dashboard'
    } else if (newPath === '/admin/analytics') {
      activeNav.value = 'analytics'
    }
  },
)

// ===========================================
// METHODS
// ===========================================

/**
 * Toggle sidebar visibility
 */
const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value
}

/**
 * Handle navigation click for dashboard
 */
const handleDashboardClick = () => {
  productsDropdownOpen.value = false
  activeNav.value = 'dashboard'
}

/**
 * Handle navigation click for products
 */
const handleProductsClick = () => {
  productsDropdownOpen.value = !productsDropdownOpen.value
  activeNav.value = 'products'
}

/**
 * Handle navigation click for orders
 */
const handleOrdersClick = () => {
  productsDropdownOpen.value = false
  activeNav.value = 'orders'
}

/**
 * Handle navigation click for customers
 */
const handleCustomersClick = () => {
  productsDropdownOpen.value = false
  activeNav.value = 'customers'
}

/**
 * Handle navigation click for analytics
 */
const handleAnalyticsClick = () => {
  productsDropdownOpen.value = false
  activeNav.value = 'analytics'
}

/**
 * Close a specific tab
 * @param {string} tabPath - The path of the tab to close
 */
const closeTab = (tabPath) => {
  let page = ''

  // Handle different route patterns for closing tabs
  if (tabPath === '/admin/dashboard') {
    page = 'dashboard'
  } else if (tabPath === '/admin/products/manage') {
    page = 'manage'
  } else if (tabPath === '/admin/products/add') {
    page = 'add'
  } else if (tabPath.startsWith('/admin/products/edit')) {
    page = 'edit'
  } else if (tabPath === '/admin/products/discount') {
    page = 'discount'
  } else if (tabPath === '/admin/products/feedback') {
    page = 'feedback'
  } else if (tabPath === '/admin/products/stock') {
    page = 'stock'
  } else if (tabPath === '/admin/orders') {
    page = 'orders'
  } else if (tabPath === '/admin/orders/list') {
    page = 'orderList'
  } else if (tabPath === '/admin/orders/view') {
    page = 'viewOrder'
  } else if (tabPath.startsWith('/admin/orders/edit')) {
    page = 'editOrder'
  } else if (tabPath === '/admin/customers') {
    page = 'customers'
  } else if (tabPath === '/admin/customers/list') {
    page = 'customerList'
  } else if (tabPath === '/admin/customers/view') {
    page = 'viewCustomer'
  } else if (tabPath.startsWith('/admin/customers/edit')) {
    page = 'editCustomer'
  } else if (tabPath === '/admin/analytics') {
    page = 'analytics'
  }

  const tabIndex = openTabs.value.indexOf(page)

  if (tabIndex > -1) {
    // Remove tab from open tabs
    openTabs.value.splice(tabIndex, 1)

    // If we closed the currently active tab, navigate to another
    let currentPage = ''
    if (route.path === '/admin/dashboard') {
      currentPage = 'dashboard'
    } else if (route.path === '/admin/products/manage') {
      currentPage = 'manage'
    } else if (route.path === '/admin/products/add') {
      currentPage = 'add'
    } else if (route.path.startsWith('/admin/products/edit/')) {
      currentPage = 'edit'
    } else if (route.path === '/admin/products/discount') {
      currentPage = 'discount'
    } else if (route.path === '/admin/products/feedback') {
      currentPage = 'feedback'
    } else if (route.path === '/admin/products/stock') {
      currentPage = 'stock'
    } else if (route.path === '/admin/orders/list') {
      currentPage = 'orderList'
    } else if (route.path === '/admin/orders/view') {
      currentPage = 'viewOrder'
    } else if (route.path.startsWith('/admin/orders/edit/')) {
      currentPage = 'editOrder'
    } else if (route.path === '/admin/customers') {
      currentPage = 'customers'
    } else if (route.path === '/admin/analytics') {
      currentPage = 'analytics'
    }

    if (currentPage === page) {
      const nextTab = openTabs.value[0] || 'dashboard'
      let nextPath = nextTab
      if (nextTab === 'manage') {
        nextPath = 'products/manage'
      } else if (nextTab === 'orderList') {
        nextPath = 'orders/list'
      }
      router.push(`/admin/${nextPath}`)
    }
  }
}

// ===========================================
// LIFECYCLE HOOKS
// ===========================================

onMounted(() => {
  // Component initialization logic can go here if needed
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
  overflow-y: auto;
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
