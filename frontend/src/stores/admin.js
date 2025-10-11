// stores/admin.js
export default {
  namespaced: true,

  // State object containing all admin-related data
  state: {
    // Dashboard statistics
    dashboardStats: {
      totalProducts: 14000,
      totalRevenue: 14000,
      totalCustomers: 1300,
    },

    // Revenue data for charts
    revenueData: {
      labels: [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December',
      ],
      values: [65, 70, 68, 80, 75, 68, 90, 85, 78, 88, 72, 82],
    },

    // Category distribution data
    categoryDistribution: [
      { name: 'Mountain Bikes', percentage: 40, color: '#42A5F5' },
      { name: 'Road Bikes', percentage: 60, color: '#EF5350' },
    ],

    // Stock alerts data
    stockAlerts: [
      {
        status: 'LOW',
        count: 10,
        totalValue: 43300.0,
        percentage: 12.5,
        action: 'Restock Needed',
      },
      {
        status: 'FULL',
        count: 10,
        totalValue: 43300.0,
        percentage: 12.5,
        action: 'Well Stock',
      },
      {
        status: 'Normal',
        count: 10,
        totalValue: 43300.0,
        percentage: 12.5,
        action: 'Monitor',
      },
    ],

    // Stock status data
    stockStatus: [
      { name: 'FULL', percentage: 40, color: '#42A5F5' },
      { name: 'Normal', percentage: 30, color: '#FFA726' },
      { name: 'LOW', percentage: 30, color: '#EF5350' },
    ],

    // Current user information
    user: {
      name: 'Vuth Menghuor',
      email: 'vuth@example.com',
      avatar: '/path-to-avatar.jpg',
      role: 'admin',
    },
  },

  // Getters to access state data
  getters: {
    // Get dashboard statistics
    getDashboardStats: (state) => state.dashboardStats,
    // Get revenue data
    getRevenueData: (state) => state.revenueData,
    // Get category distribution
    getCategoryDistribution: (state) => state.categoryDistribution,
    // Get stock alerts
    getStockAlerts: (state) => state.stockAlerts,
    // Get stock status
    getStockStatus: (state) => state.stockStatus,
    // Get current user
    getCurrentUser: (state) => state.user,
  },

  // Mutations to update state
  mutations: {
    // Set dashboard statistics
    SET_DASHBOARD_STATS(state, stats) {
      state.dashboardStats = { ...state.dashboardStats, ...stats }
    },

    // Set revenue data
    SET_REVENUE_DATA(state, data) {
      state.revenueData = data
    },

    // Set category distribution
    SET_CATEGORY_DISTRIBUTION(state, categories) {
      state.categoryDistribution = categories
    },

    // Set stock alerts
    SET_STOCK_ALERTS(state, alerts) {
      state.stockAlerts = alerts
    },

    // Set stock status
    SET_STOCK_STATUS(state, status) {
      state.stockStatus = status
    },

    // Set user information
    SET_USER(state, user) {
      state.user = { ...state.user, ...user }
    },
  },

  // Actions for async operations
  actions: {
    // Fetch dashboard statistics
    async fetchDashboardStats({ commit }) {
      try {
        // Replace with actual API call
        // const response = await axios.get('/api/admin/dashboard/stats')
        // commit('SET_DASHBOARD_STATS', response.data)

        // Simulated data for now
        commit('SET_DASHBOARD_STATS', {
          totalProducts: 14000,
          totalRevenue: 14000,
          totalCustomers: 1300,
        })
      } catch (error) {
        console.error('Error fetching dashboard stats:', error)
      }
    },

    // Fetch revenue data
    async fetchRevenueData({ commit }, period = 'monthly') {
      try {
        // Replace with actual API call
        // const response = await axios.get(`/api/admin/revenue?period=${period}`)
        // commit('SET_REVENUE_DATA', response.data)

        // Simulated data for now
        const data = {
          labels: [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
          ],
          values: [65, 70, 68, 80, 75, 68, 90, 85, 78, 88, 72, 82],
        }
        commit('SET_REVENUE_DATA', data)
      } catch (error) {
        console.error('Error fetching revenue data:', error)
      }
    },

    // Fetch category distribution
    async fetchCategoryDistribution({ commit }) {
      try {
        // Replace with actual API call
        // const response = await axios.get('/api/admin/categories/distribution')
        // commit('SET_CATEGORY_DISTRIBUTION', response.data)

        // Simulated data for now
        const categories = [
          { name: 'Mountain Bikes', percentage: 40, color: '#42A5F5' },
          { name: 'Road Bikes', percentage: 60, color: '#EF5350' },
        ]
        commit('SET_CATEGORY_DISTRIBUTION', categories)
      } catch (error) {
        console.error('Error fetching category distribution:', error)
      }
    },

    // Fetch stock alerts
    async fetchStockAlerts({ commit }) {
      try {
        // Replace with actual API call
        // const response = await axios.get('/api/admin/stock/alerts')
        // commit('SET_STOCK_ALERTS', response.data)

        // Simulated data for now
        const alerts = [
          {
            status: 'LOW',
            count: 10,
            totalValue: 43300.0,
            percentage: 12.5,
            action: 'Restock Needed',
          },
          {
            status: 'FULL',
            count: 10,
            totalValue: 43300.0,
            percentage: 12.5,
            action: 'Well Stock',
          },
          {
            status: 'Normal',
            count: 10,
            totalValue: 43300.0,
            percentage: 12.5,
            action: 'Monitor',
          },
        ]
        commit('SET_STOCK_ALERTS', alerts)
      } catch (error) {
        console.error('Error fetching stock alerts:', error)
      }
    },

    // Fetch stock status
    async fetchStockStatus({ commit }) {
      try {
        // Replace with actual API call
        // const response = await axios.get('/api/admin/stock/status')
        // commit('SET_STOCK_STATUS', response.data)

        // Simulated data for now
        const status = [
          { name: 'FULL', percentage: 40, color: '#42A5F5' },
          { name: 'Normal', percentage: 30, color: '#FFA726' },
          { name: 'LOW', percentage: 30, color: '#EF5350' },
        ]
        commit('SET_STOCK_STATUS', status)
      } catch (error) {
        console.error('Error fetching stock status:', error)
      }
    },

    // Update user profile
    async updateUserProfile({ commit }, userData) {
      try {
        // Replace with actual API call
        // const response = await axios.put('/api/admin/profile', userData)
        // commit('SET_USER', response.data)

        commit('SET_USER', userData)
      } catch (error) {
        console.error('Error updating user profile:', error)
      }
    },
  },
}
