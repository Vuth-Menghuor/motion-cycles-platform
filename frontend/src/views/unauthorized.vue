<template>
  <div class="error-page">
    <div class="error-container">
      <!-- Error Code -->
      <h1 class="error-code">403</h1>

      <!-- Error Message -->
      <h2 class="error-title">Access Denied</h2>
      <p class="error-description">
        You don't have permission to access this page. This area is restricted to authorized
        administrators only.
      </p>

      <!-- Action Buttons -->
      <div class="error-actions">
        <button @click="goHome" class="btn btn-primary">
          <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
            <path
              d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"
            />
          </svg>
          Go to Home
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

      <!-- Contact Support -->
      <div class="support-section">
        <p class="support-text">
          If you believe this is a mistake, please contact your administrator.
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UnauthorizedPage',
  methods: {
    goHome() {
      this.$router.push('/')
    },
    goBack() {
      this.$router.go(-1)
    },
  },
  mounted() {
    document.title = 'Unauthorized Access - Motion Cycle'
  },
}
</script>

<style scoped>
.error-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f8fafc;
  position: relative;
  overflow: hidden;
  padding: 20px;
}

.error-container {
  background: white;
  border-radius: 20px;
  padding: 60px 40px;
  max-width: 600px;
  width: 100%;
  text-align: center;
  border: 2px solid #e5e7eb;
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

.error-code {
  font-size: 80px;
  font-weight: bold;
  color: #3b82f6;
  margin: 0 0 20px 0;
  line-height: 1;
}

.error-title {
  font-size: 32px;
  font-weight: 700;
  color: #2d3748;
  margin: 0 0 15px 0;
}

.error-description {
  font-size: 16px;
  color: #718096;
  line-height: 1.6;
  margin: 0 0 40px 0;
  max-width: 480px;
  margin-left: auto;
  margin-right: auto;
}

.error-actions {
  display: flex;
  gap: 15px;
  justify-content: center;
  flex-wrap: wrap;
  margin-bottom: 30px;
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
  background: #3b82f6;
  color: white;
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
}

.btn-secondary {
  background: #e2e8f0;
  color: #4a5568;
}

.btn-secondary:hover {
  background: #cbd5e0;
  transform: translateY(-2px);
}

.support-section {
  padding-top: 30px;
  border-top: 1px solid #e2e8f0;
}

.support-text {
  font-size: 14px;
  color: #a0aec0;
  margin: 0;
}

/* Responsive */
@media (max-width: 768px) {
  .error-container {
    padding: 40px 30px;
  }

  .error-code {
    font-size: 60px;
  }

  .error-title {
    font-size: 24px;
  }

  .error-description {
    font-size: 14px;
  }

  .icon-container {
    width: 100px;
    height: 100px;
  }

  .lock-icon {
    width: 50px;
    height: 50px;
  }

  .error-actions {
    flex-direction: column;
  }

  .btn {
    width: 100%;
    justify-content: center;
  }
}
</style>
