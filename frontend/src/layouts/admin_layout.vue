<template>
  <div class="admin-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
      <!-- Logo and Brand -->
      <div class="brand">
        <h1 class="brand-name">MOTION CYCLE</h1>
      </div>

      <!-- User Profile -->
      <div class="user-profile">
        <img :src="user.avatar || '/default-avatar.png'" :alt="user.name" class="avatar" />
        <p class="user-name">{{ user.name }}</p>
        <div class="user-actions">
          <button class="action-btn" title="Password">
            <i class="icon-lock"></i>
            <span>Password</span>
          </button>
          <button class="action-btn" title="My Profile">
            <i class="icon-user"></i>
            <span>My Profile</span>
          </button>
          <button class="action-btn" title="Logout">
            <i class="icon-logout"></i>
            <span>Logout</span>
          </button>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="nav-menu">
        <router-link to="/admin/dashboard" class="nav-item" active-class="active">
          <i class="icon-dashboard"></i>
          <span>Dashboard</span>
        </router-link>
        <router-link to="/admin/products" class="nav-item" active-class="active">
          <i class="icon-products"></i>
          <span>Products</span>
          <i class="icon-chevron"></i>
        </router-link>
        <router-link to="/admin/orders" class="nav-item" active-class="active">
          <i class="icon-orders"></i>
          <span>Orders</span>
          <i class="icon-chevron"></i>
        </router-link>
        <router-link to="/admin/customers" class="nav-item" active-class="active">
          <i class="icon-customers"></i>
          <span>Customers</span>
          <i class="icon-chevron"></i>
        </router-link>
        <router-link to="/admin/analytics" class="nav-item" active-class="active">
          <i class="icon-analytics"></i>
          <span>Revenue & Profit Analytics</span>
        </router-link>
      </nav>
    </aside>

    <!-- Main Content Area -->
    <main class="main-content">
      <!-- Top Header -->
      <header class="top-header">
        <div class="search-bar">
          <i class="icon-search"></i>
          <input type="text" placeholder="Search something..." v-model="searchQuery" />
        </div>
        <div class="header-actions">
          <button class="profile-btn">
            <img :src="user.avatar || '/default-avatar.png'" :alt="user.name" />
          </button>
        </div>
      </header>

      <!-- Page Content -->
      <div class="page-content">
        <router-view></router-view>
      </div>
    </main>
  </div>
</template>

<script>
export default {
  name: 'AdminLayout',
  data() {
    return {
      searchQuery: '',
      user: {
        name: 'Vuth Menghuor',
        avatar: '/path-to-avatar.jpg',
      },
    }
  },
}
</script>

<style scoped>
.admin-layout {
  display: flex;
  min-height: 100vh;
  background-color: #f5f5f5;
}

.sidebar {
  width: 250px;
  background: linear-gradient(180deg, #0d4d4d 0%, #0a3838 100%);
  color: white;
  padding: 20px;
  position: fixed;
  height: 100vh;
  overflow-y: auto;
}

.brand {
  text-align: center;
  margin-bottom: 30px;
}

.brand-name {
  font-size: 22px;
  font-weight: 600;
  margin: 0;
  margin-top: 4rem;
  font-family: 'Poppins', sans-serif;
}

.user-profile {
  text-align: center;
  padding: 20px 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  margin-bottom: 20px;
}

.avatar {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 10px;
  border: 3px solid rgba(255, 255, 255, 0.2);
}

.user-name {
  font-size: 16px;
  font-weight: 500;
  margin: 10px 0;
}

.user-actions {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-top: 15px;
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

.action-btn span {
  display: none;
}

.nav-menu {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.nav-item {
  display: flex;
  align-items: center;
  padding: 12px 15px;
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  border-radius: 8px;
  transition: all 0.3s;
  gap: 12px;
}

.nav-item:hover {
  background: rgba(255, 255, 255, 0.1);
  color: white;
}

.nav-item.active {
  background: rgba(255, 255, 255, 0.15);
  color: white;
}

.nav-item .icon-chevron {
  margin-left: auto;
}

.main-content {
  margin-left: 250px;
  flex: 1;
  display: flex;
  flex-direction: column;
}

.top-header {
  background: white;
  padding: 15px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 100;
}

.search-bar {
  display: flex;
  align-items: center;
  background: #f5f5f5;
  padding: 10px 15px;
  border-radius: 8px;
  width: 400px;
  gap: 10px;
}

.search-bar input {
  border: none;
  background: transparent;
  outline: none;
  flex: 1;
  font-size: 14px;
}

.profile-btn {
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
}

.profile-btn img {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.page-content {
  padding: 30px;
  flex: 1;
}

/* Icon placeholders - replace with actual icons */
i[class^='icon-'] {
  width: 20px;
  height: 20px;
  display: inline-block;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 3px;
}
</style>
