<script setup>
import { Icon } from '@iconify/vue'
import Guest_button from './guest_button.vue'
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useRoute, useRouter } from 'vue-router'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const errorMessage = ref('')
const isLoading = ref(false)
const showPassword = ref(false)

const login = async () => {
  if (isLoading.value) return

  errorMessage.value = ''
  isLoading.value = true

  try {
    await authStore.login(email.value, password.value)
    router.push(route.query.redirect || '/')
  } catch (e) {
    const status = e.response?.status
    errorMessage.value =
      status === 401
        ? 'Invalid email or password. Please try again.'
        : status === 422
          ? 'Please check your email and password format.'
          : 'Login failed. Please try again later.'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-header">
        <h1 class="auth-title">Sign In</h1>
        <div class="auth-prompt">
          <span>Don't have an account?</span>
          <router-link to="/authentication/sign_up">Sign up</router-link>
        </div>
      </div>

      <div v-if="errorMessage" class="error-message">
        {{ errorMessage }}
      </div>

      <form @submit.prevent="login" action="auth-form">
        <div class="form-group">
          <input v-model="email" type="email" placeholder="Email" class="input-text" required />
          <Icon icon="ic:baseline-email" class="input-icon" />
        </div>
        <div class="form-group">
          <input
            v-model="password"
            :type="showPassword ? 'text' : 'password'"
            placeholder="Password"
            class="input-text"
            required
          />
          <button
            type="button"
            @click="showPassword = !showPassword"
            class="password-toggle"
            :title="showPassword ? 'Hide password' : 'Show password'"
          >
            <Icon :icon="showPassword ? 'mdi:eye-off' : 'mdi:eye'" class="toggle-icon" />
          </button>
        </div>
      </form>

      <div class="form-option">
        <div class="checkbox-wrapper">
          <input type="checkbox" id="remember" class="form-checkbox" />
          <label class="checkbox-label" for="remember">Remember me</label>
        </div>
        <a href="#" class="link">Forgot Password?</a>
      </div>

      <div class="button-wrapper">
        <button @click="login" type="submit" class="submit-button" :disabled="isLoading">
          <span v-if="isLoading">Signing In...</span>
          <span v-else>Sign In</span>
        </button>
        <Guest_button />
      </div>

      <div class="form-divider">
        <span class="divider-text">or sign in</span>
      </div>

      <div class="social-login">
        <button type="button" class="social-button">
          <Icon icon="flat-color-icons:google" class="soical-icon" />
          <span class="social-text">Google</span>
        </button>

        <button type="button" class="social-button">
          <Icon icon="logos:facebook" class="soical-icon" />
          <span class="social-text">Facebook</span>
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.auth-card {
  border: 1px solid rgb(202, 202, 202);
  border-radius: 20px;
  background-color: white;
  height: auto;
  width: 630px;
}

.auth-header {
  display: flex;
  justify-content: center;
  flex-direction: column;
  align-items: center;
  font-family: 'Poppins', sans-serif;
}

.auth-title {
  font-family: 'Poppins', sans-serif;
  font-weight: 400;
  font-size: 40px;
  padding: 0;
  margin: 40px 0 8px 0;
}

.auth-prompt {
  font-size: 14px;
}

.auth-prompt span {
  padding-right: 6px;
}

.auth-prompt a {
  color: #14c9c9;
  text-decoration: none;
  font-weight: 500;
}

.auth-prompt a:hover {
  text-decoration: underline;
}

.error-message {
  background-color: #fee;
  color: #c33;
  padding: 10px;
  margin: 10px 80px;
  border-radius: 4px;
  border: 1px solid #fcc;
  font-size: 14px;
  text-align: center;
  font-family: 'Poppins', sans-serif;
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
}

.input-text:focus {
  outline: none;
  background-color: transparent;
  border-bottom-color: #14c9c9;
}

.input-icon {
  position: relative;
  right: 20px;
  height: 20px;
  width: 20px;
  opacity: 70%;
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

.password-toggle:hover {
  opacity: 1;
}

.toggle-icon {
  width: 18px;
  height: 18px;
  color: #666;
}

.form-option {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 12px;
  margin-left: 84px;
  margin-right: 110px;
  font-family: 'Poppins', sans-serif;
}

.checkbox-wrapper {
  display: flex;
  align-items: center;
  gap: 6px;
}

.checkbox-label {
  font-size: 13px;
  font-weight: 300;
}

.form-checkbox {
  height: 14px;
  width: 14px;
}

.link {
  font-size: 12px;
  color: #14c9c9;
  text-decoration: none;
  font-weight: 500;
}

.link:hover {
  text-decoration: underline;
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
  border: none;
  font-weight: 500;
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

.submit-button:disabled {
  background-color: #ccc;
  cursor: not-allowed;
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

.social-login {
  display: flex;
  justify-content: space-around;
  margin: 40px 100px;
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

.soical-icon {
  height: 22px;
  width: 22px;
}

.social-text {
  font-weight: 500;
}
</style>
