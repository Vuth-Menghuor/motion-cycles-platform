// src/main.js
import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import { createPinia } from 'pinia' // Import createPinia

const app = createApp(App)
const pinia = createPinia() // Create a Pinia instance

app.use(router)
app.use(pinia) // Use Pinia
app.mount('#app')
