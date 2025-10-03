// stores/admin.js
export default {
  namespaced: true,

  state: {
    dashboardStats: {
      totalProducts: 14000,
      totalRevenue: 14000,
      totalCustomers: 1300,
    },

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

    categoryDistribution: [
      { name: 'Mountain Bikes', percentage: 40, color: '#42A5F5' },
      { name: 'Road Bikes', percentage: 60, color: '#EF5350' },
    ],

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

    stockStatus: [
      { name: 'FULL', percentage: 40, color: '#42A5F5' },
      { name: 'Normal', percentage: 30, color: '#FFA726' },
      { name: 'LOW', percentage: 30, color: '#EF5350' },
    ],

    user: {
      name: 'Vuth Menghuor',
      email: 'vuth@example.com',
      avatar: '/path-to-avatar.jpg',
      role: 'admin',
    },
  },

  getters: {
    getDashboardStats: (state) => state.dashboardStats,
    getRevenueData: (state) => state.revenueData,
    getCategoryDistribution: (state) => state.categoryDistribution,
    getStockAlerts: (state) => state.stockAlerts,
    getStockStatus: (state) => state.stockStatus,
    getCurrentUser: (state) => state.user,
  },

  mutations: {
    SET_DASHBOARD_STATS(state, stats) {
      state.dashboardStats = { ...state.dashboardStats, ...stats }
    },

    SET_REVENUE_DATA(state, data) {
      state.revenueData = data
    },

    SET_CATEGORY_DISTRIBUTION(state, categories) {
      state.categoryDistribution = categories
    },

    SET_STOCK_ALERTS(state, alerts) {
      state.stockAlerts = alerts
    },

    SET_STOCK_STATUS(state, status) {
      state.stockStatus = status
    },

    SET_USER(state, user) {
      state.user = { ...state.user, ...user }
    },
  },

  actions: {
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
