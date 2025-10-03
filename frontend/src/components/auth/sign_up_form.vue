<script setup>
import { Icon } from '@iconify/vue'
import Guest_button from './guest_button.vue'
import { useAuthStore } from '@/stores/auth'
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

const username = ref('')
const email = ref('')
const password = ref('')
// const password_confirmation = ref('')
const terms = ref(true)
const isLoading = ref(false)
const errorMessage = ref('')
const showPassword = ref(false)

const register = async () => {
  event.preventDefault() // Prevent form submission

  // Clear previous errors
  errorMessage.value = ''

  // Validation
  if (!username.value || !email.value || !password.value) {
    errorMessage.value = 'All fields are required'
    return
  }

  if (!terms.value) {
    errorMessage.value = 'You must agree to the Terms & Conditions'
    return
  }

  isLoading.value = true

  try {
    const result = await auth.register({
      username: username.value,
      name: username.value,
      email: email.value,
      password: password.value,
      password_confirmation: password.value,
      terms: terms.value ? 1 : 0, // Convert boolean to 1/0
    })

    // Success redirect to home or login
    console.log('Registration successful:', result)
    router.push('/authentication/sign_in')
  } catch (error) {
    console.error('Registration error:', error)

    // Optional: Handle different error type
    if (error.response) {
      // Server responded with error
      if (error.response.status === 422) {
        // Validation errors
        const errors = error.response.data.errors
        if (errors) {
          // Display first error message
          const firstError = Object.values(errors)[0]
          errorMessage.value = Array.isArray(firstError) ? firstError[0] : firstError
        } else {
          errorMessage.value = error.response.data.message || 'Validation failed'
        }
      } else if (error.response.status === 500) {
        errorMessage.value = 'Server error. Please try again later.'
      } else {
        errorMessage.value = error.response.data.message || 'Registration failed'
      }
    } else if (error.request) {
      // Request made but no response
      errorMessage.value = 'Network error. Please check your connection.'
    } else {
      // Something else happened
      errorMessage.value = 'An unexpected error occurred'
    }
  } finally {
    isLoading.value = false
  }
  // await auth.register({
  //   username: username.value,
  //   name: username.value,
  //   email: email.value,
  //   password: password.value,
  //   password_confirmation: password.value,
  //   terms: terms.value,
  // })
}
</script>

<template>
  <div class="auth-container">
    <div class="auth-card">
      <!-- Header -->
      <div class="auth-header">
        <h1 class="auth-title">Sign Up</h1>
        <div class="auth-prompt">
          <span>Already have an account?</span>
          <router-link to="/authentication/sign_in">Sign In</router-link>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="errorMessage" class="error-alert">
        <Icon icon="mdi:alert-circle" class="error-icon" />
        {{ errorMessage }}
      </div>

      <!-- Form Section -->
      <form @submit.prevent="register" class="auth-form">
        <div class="form-group">
          <input
            v-model="username"
            type="text"
            placeholder="Username"
            class="input-text"
            :disabled="isLoading"
            required
          />
          <Icon icon="mdi:user" class="input-icon" />
        </div>

        <div class="form-group">
          <input
            v-model="email"
            type="email"
            placeholder="Email"
            class="input-text"
            :disabled="isLoading"
            required
          />
          <Icon icon="ic:baseline-email" class="input-icon" />
        </div>

        <div class="form-group">
          <input
            v-model="password"
            :type="showPassword ? 'text' : 'password'"
            placeholder="Password (min 8 characters)"
            class="input-text"
            :disabled="isLoading"
            required
            minlength="8"
          />
          <button
            type="button"
            @click="showPassword = !showPassword"
            class="password-toggle"
            :title="showPassword ? 'Hide password' : 'Show password'"
            :disabled="isLoading"
          >
            <Icon :icon="showPassword ? 'mdi:eye-off' : 'mdi:eye'" class="toggle-icon" />
          </button>
        </div>

        <!-- Form Option -->
        <div class="form-option">
          <div class="checkbox-wrapper">
            <input
              v-model="terms"
              type="checkbox"
              id="terms"
              class="form-checkbox"
              :disabled="isLoading"
              required
            />
            <label class="checkbox-label" for="terms">I agree to the</label>
          </div>
          <a href="#" class="link">Terms & Conditions</a>
        </div>

        <!-- Submit Button -->
        <div class="button-wrapper">
          <button
            type="submit"
            class="submit-button"
            :disabled="isLoading"
            :class="{ loading: isLoading }"
          >
            <span v-if="!isLoading">Sign Up</span>
            <span v-else class="loading-text">
              <Icon icon="eos-icons:loading" class="loading-icon" />
              Creating account...
            </span>
          </button>
          <Guest_button />
        </div>
      </form>

      <!-- Divider -->
      <div class="form-divider">
        <span class="divider-text">or sign up with</span>
      </div>

      <!-- Social Section -->
      <div class="social-login">
        <button type="button" class="social-button">
          <Icon icon="flat-color-icons:google" class="social-icon" />
          <span class="social-text">Google</span>
        </button>

        <button type="button" class="social-button">
          <Icon icon="logos:facebook" class="social-icon" />
          <span class="social-text">Facebook</span>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.error-alert {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 20px;
  margin: 20px 80px 0 80px;
  background-color: #fee;
  border-left: 4px solid #f44;
  border-radius: 6px;
  color: #c33;
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
  animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.error-icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
}

.submit-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.loading-text {
  display: flex;
  align-items: center;
  gap: 8px;
}

.loading-icon {
  width: 20px;
  height: 20px;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.password-toggle {
  position: relative;
  right: 20px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0.7;
  transition: opacity 0.2s;
}

.password-toggle:hover:not(:disabled) {
  opacity: 1;
}

.password-toggle:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.toggle-icon {
  width: 18px;
  height: 18px;
  color: #666;
}

.social-icon {
  height: 22px;
  width: 22px;
}

.social-button {
  display: flex;
  justify-content: center;
  background-color: transparent;
  height: 40px;
  border: 1px solid rgb(212, 212, 212);
  border-radius: 6px;
  width: 185px;
  align-items: center;
  gap: 12px;
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
  cursor: pointer;
  transition: background-color 0.3s;
}

.social-button:hover {
  background-color: #f5f5f5;
}

.social-login {
  display: flex;
  justify-content: space-around;
  margin: 40px 100px;
}

.form-divider {
  position: relative;
  text-align: center;
  margin: 25px 0;
}

.form-divider::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 0;
  right: 0;
  height: 1px;
  margin: 0 106px 0 82px;
  background: #ddd;
}

.divider-text {
  background: white;
  padding: 0 15px;
  color: #666;
  font-size: 14px;
  position: relative;
  font-weight: 300;
  z-index: 1;
  font-family: 'Poppins', sans-serif;
}

.button-wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-top: 40px;
}

.submit-button {
  display: flex;
  justify-content: center;
  align-items: center;
  width: 440px;
  margin-right: 20px;
  padding: 12px 0;
  font-weight: 500;
  border: none;
  background-color: #14c9c9;
  color: white;
  font-size: 16px;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.submit-button:hover:not(:disabled) {
  background-color: #11b3b3;
}

.form-checkbox {
  height: 14px;
  width: 14px;
  cursor: pointer;
}

.form-option {
  display: flex;
  justify-content: start;
  align-items: center;
  gap: 4px;
  margin-top: 12px;
  margin-left: 84px;
  margin-right: 110px;
  font-family: 'Poppins', sans-serif;
}

.link {
  font-size: 12px;
  font-weight: 500;
  color: #14c9c9;
  text-decoration: none;
}

.link:hover {
  text-decoration: underline;
}

.checkbox-wrapper {
  display: flex;
  align-items: center;
  gap: 6px;
}

.checkbox-label {
  font-size: 13px;
  font-weight: 300;
  cursor: pointer;
}

.auth-prompt span {
  padding-right: 6px;
}

.auth-prompt {
  font-size: 14px;
}

.auth-prompt a {
  color: #14c9c9;
  text-decoration: none;
  font-weight: 500;
}

.auth-prompt a:hover {
  text-decoration: underline;
}

.form-group {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 35px;
  position: relative;
}

.input-text {
  font-size: 14px;
  font-family: 'Poppins', sans-serif;
  border: none;
  border-bottom: 1px solid grey;
  padding-right: 30px;
  padding-top: 10px;
  padding-bottom: 10px;
  width: 400px;
  background-color: transparent;
  transition: border-color 0.3s;
}

.input-text:focus {
  outline: none;
  background-color: transparent;
  border-bottom-color: #14c9c9;
}

.input-text:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.input-icon {
  position: relative;
  right: 20px;
  height: 20px;
  width: 20px;
  opacity: 70%;
}

.auth-title {
  font-family: 'Poppins', sans-serif;
  font-weight: 400;
  font-size: 40px;
  padding: 0;
  margin: 40px 0 8px 0;
}

.auth-header {
  display: flex;
  justify-content: center;
  flex-direction: column;
  align-items: center;
  font-family: 'Poppins', sans-serif;
}

.auth-card {
  border: 1px solid rgb(202, 202, 202);
  border-radius: 20px;
  background-color: white;
  height: auto;
  width: 630px;
}
</style>
