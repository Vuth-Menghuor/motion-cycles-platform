// main.js - Application entry point
import { createApp } from 'vue'
import { createPinia } from 'pinia' // Pinia for state management
import App from './App.vue'
import router from './router'
import axios from 'axios'

// Configure axios base URL for API calls
axios.defaults.baseURL = 'http://localhost:8100'

// Create Vue application instance
const app = createApp(App)

// Use plugins: router and Pinia
app.use(router)
app.use(createPinia()) // Pinia

// Set up global error handler
app.config.errorHandler = (err, instance, info) => {
  console.error('Global error:', err)
  console.error('Error info:', info)
}

// Mount the app to the DOM
app.mount('#app')

// Export for external use if needed
export { app, router }
