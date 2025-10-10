// // src/main.js
// import { createApp } from 'vue'
// import App from './App.vue'
// import router from './router'
// import { createPinia } from 'pinia' // Import createPinia

// const app = createApp(App)
// const pinia = createPinia() // Create a Pinia instance

// app.use(router)
// app.use(pinia) // Use Pinia
// app.mount('#app')

// main.js
import { createApp } from 'vue'
import { createStore } from 'vuex' // Vuex
import { createPinia } from 'pinia' // Pinia
import App from './App.vue'
import router from './router'
import adminStore from './stores/admin'
import axios from 'axios'

// Configure axios base URL
axios.defaults.baseURL = 'http://localhost:8100'

// --- Vuex store setup ---
const store = createStore({
  modules: {
    admin: adminStore,
  },
})

// --- Create Vue app ---
const app = createApp(App)

// --- Use plugins ---
app.use(router)
app.use(store) // Vuex
app.use(createPinia()) // Pinia

// --- Global error handler ---
app.config.errorHandler = (err, instance, info) => {
  console.error('Global error:', err)
  console.error('Error info:', info)
}

// --- Mount app ---
app.mount('#app')

// --- Export for external use if needed ---
export { app, store, router }
