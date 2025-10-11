// main.js - Application entry point
import { createApp } from 'vue'
import { createStore } from 'vuex' // Vuex for state management
import { createPinia } from 'pinia' // Pinia for state management
import App from './App.vue'
import router from './router'
import adminStore from './stores/admin'
import axios from 'axios'

// Configure axios base URL for API calls
axios.defaults.baseURL = 'http://localhost:8100'

// Create Vuex store with admin module
const store = createStore({
  modules: {
    admin: adminStore,
  },
})

// Create Vue application instance
const app = createApp(App)

// Use plugins: router, Vuex store, and Pinia
app.use(router)
app.use(store) // Vuex
app.use(createPinia()) // Pinia

// Set up global error handler
app.config.errorHandler = (err, instance, info) => {
  console.error('Global error:', err)
  console.error('Error info:', info)
}

// Mount the app to the DOM
app.mount('#app')

// Export for external use if needed
export { app, store, router }
