<template>
  <div class="error-page">
    <div class="error-container">
      <!-- 404 Illustration -->
      <div class="illustration">
        <svg class="bike-icon" viewBox="0 0 200 120" fill="none">
          <!-- Bike Frame -->
          <path
            d="M60 80 L80 50 L110 50 L130 80"
            stroke="#42A5F5"
            stroke-width="4"
            stroke-linecap="round"
          />
          <path
            d="M80 50 L90 30 L110 30"
            stroke="#42A5F5"
            stroke-width="4"
            stroke-linecap="round"
          />
          <path d="M110 30 L120 50" stroke="#42A5F5" stroke-width="4" stroke-linecap="round" />

          <!-- Wheels -->
          <circle cx="60" cy="90" r="20" stroke="#42A5F5" stroke-width="4" fill="none" />
          <circle cx="60" cy="90" r="3" fill="#42A5F5" />
          <circle cx="130" cy="90" r="20" stroke="#42A5F5" stroke-width="4" fill="none" />
          <circle cx="130" cy="90" r="3" fill="#42A5F5" />

          <!-- Spokes -->
          <line x1="60" y1="90" x2="75" y2="75" stroke="#42A5F5" stroke-width="2" />
          <line x1="60" y1="90" x2="45" y2="75" stroke="#42A5F5" stroke-width="2" />
          <line x1="130" y1="90" x2="145" y2="75" stroke="#42A5F5" stroke-width="2" />
          <line x1="130" y1="90" x2="115" y2="75" stroke="#42A5F5" stroke-width="2" />

          <!-- Question Mark -->
          <text x="95" y="15" font-size="24" font-weight="bold" fill="#EF5350">?</text>
        </svg>
      </div>

      <!-- Error Code -->
      <h1 class="error-code">404</h1>

      <!-- Error Message -->
      <h2 class="error-title">Page Not Found</h2>
      <p class="error-description">
        Oops! It looks like you've taken a wrong turn. The page you're looking for doesn't exist or
        has been moved.
      </p>

      <!-- Search Bar -->
      <div class="search-section">
        <div class="search-bar">
          <svg class="search-icon" width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
            <path
              fill-rule="evenodd"
              d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
              clip-rule="evenodd"
            />
          </svg>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search for bikes, brands, or products..."
            @keyup.enter="handleSearch"
          />
          <button @click="handleSearch" class="search-btn">Search</button>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="error-actions">
        <button @click="goHome" class="btn btn-primary">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
            <path
              d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
            />
          </svg>
          Back to Home
        </button>
        <button @click="goBack" class="btn btn-secondary">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
            <path
              fill-rule="evenodd"
              d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
              clip-rule="evenodd"
            />
          </svg>
          Go Back
        </button>
      </div>

      <!-- Popular Links -->
      <div class="popular-links">
        <p class="links-title">Popular Pages</p>
        <div class="links-grid">
          <router-link to="/" class="link-item">
            <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
              <path
                d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
              />
            </svg>
            Home
          </router-link>
          <router-link to="/brands/trek" class="link-item">
            <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
              <path
                fill-rule="evenodd"
                d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                clip-rule="evenodd"
              />
            </svg>
            Brands
          </router-link>
          <router-link to="/favorites" class="link-item">
            <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
              <path
                fill-rule="evenodd"
                d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"
                clip-rule="evenodd"
              />
            </svg>
            Favorites
          </router-link>
          <router-link to="/checkout/cart" class="link-item">
            <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
              <path
                d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"
              />
            </svg>
            Cart
          </router-link>
        </div>
      </div>
    </div>

    <!-- Background Decoration -->
    <div class="decoration-lines">
      <div class="line line-1"></div>
      <div class="line line-2"></div>
      <div class="line line-3"></div>
      <div class="line line-4"></div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NotFoundPage',
  data() {
    return {
      searchQuery: '',
    }
  },
  methods: {
    goHome() {
      this.$router.push('/')
    },
    goBack() {
      this.$router.go(-1)
    },
    handleSearch() {
      if (this.searchQuery.trim()) {
        // Redirect to home with search query or your search page
        this.$router.push({
          path: '/',
          query: { search: this.searchQuery },
        })
      }
    },
  },
  mounted() {
    document.title = 'Page Not Found - Motion Cycle'
  },
}
</script>

<style scoped>
.error-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  position: relative;
  overflow: hidden;
  padding: 20px;
}

.error-container {
  background: white;
  border-radius: 20px;
  padding: 60px 40px;
  max-width: 700px;
  width: 100%;
  text-align: center;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  position: relative;
  z-index: 1;
  animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.illustration {
  margin-bottom: 30px;
}

.bike-icon {
  width: 200px;
  height: 120px;
  margin: 0 auto;
  animation: wobble 3s ease-in-out infinite;
}

@keyframes wobble {
  0%,
  100% {
    transform: rotate(-2deg);
  }
  50% {
    transform: rotate(2deg);
  }
}

.error-code {
  font-size: 100px;
  font-weight: bold;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin: 0 0 20px 0;
  line-height: 1;
}

.error-title {
  font-size: 36px;
  font-weight: 700;
  color: #2d3748;
  margin: 0 0 15px 0;
}

.error-description {
  font-size: 16px;
  color: #718096;
  line-height: 1.6;
  margin: 0 0 30px 0;
  max-width: 500px;
  margin-left: auto;
  margin-right: auto;
}

.search-section {
  margin-bottom: 30px;
}

.search-bar {
  display: flex;
  align-items: center;
  background: #f7fafc;
  border: 2px solid #e2e8f0;
  border-radius: 12px;
  padding: 5px;
  max-width: 500px;
  margin: 0 auto;
  transition: border-color 0.3s;
}

.search-bar:focus-within {
  border-color: #667eea;
}

.search-icon {
  margin-left: 12px;
  color: #a0aec0;
}

.search-bar input {
  flex: 1;
  border: none;
  background: transparent;
  padding: 12px;
  font-size: 15px;
  outline: none;
  color: #2d3748;
}

.search-bar input::placeholder {
  color: #a0aec0;
}

.search-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s;
}

.search-btn:hover {
  transform: scale(1.05);
}

.error-actions {
  display: flex;
  gap: 15px;
  justify-content: center;
  flex-wrap: wrap;
  margin-bottom: 40px;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border: none;
  border-radius: 10px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
}

.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
}

.btn-secondary {
  background: #e2e8f0;
  color: #4a5568;
}

.btn-secondary:hover {
  background: #cbd5e0;
  transform: translateY(-2px);
}

.popular-links {
  padding-top: 30px;
  border-top: 1px solid #e2e8f0;
}

.links-title {
  font-size: 14px;
  font-weight: 600;
  color: #4a5568;
  margin: 0 0 20px 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.links-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
  gap: 12px;
}

.link-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 16px;
  background: #f7fafc;
  border-radius: 8px;
  color: #4a5568;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.3s;
}

.link-item:hover {
  background: #667eea;
  color: white;
  transform: translateY(-2px);
}

/* Background Decoration */
.decoration-lines {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  overflow: hidden;
  z-index: 0;
}

.line {
  position: absolute;
  background: rgba(255, 255, 255, 0.1);
  transform: rotate(45deg);
  animation: slide 15s linear infinite;
}

.line-1 {
  width: 2px;
  height: 200%;
  left: 10%;
  animation-delay: 0s;
}

.line-2 {
  width: 2px;
  height: 200%;
  left: 40%;
  animation-delay: 5s;
}

.line-3 {
  width: 2px;
  height: 200%;
  left: 70%;
  animation-delay: 10s;
}

.line-4 {
  width: 2px;
  height: 200%;
  left: 90%;
  animation-delay: 2.5s;
}

@keyframes slide {
  from {
    transform: translateY(-100%) rotate(45deg);
  }
  to {
    transform: translateY(100%) rotate(45deg);
  }
}

/* Responsive */
@media (max-width: 768px) {
  .error-container {
    padding: 40px 30px;
  }

  .error-code {
    font-size: 70px;
  }

  .error-title {
    font-size: 28px;
  }

  .error-description {
    font-size: 14px;
  }

  .bike-icon {
    width: 150px;
    height: 90px;
  }

  .error-actions {
    flex-direction: column;
  }

  .btn {
    width: 100%;
    justify-content: center;
  }

  .search-bar input {
    font-size: 14px;
  }

  .links-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style>
