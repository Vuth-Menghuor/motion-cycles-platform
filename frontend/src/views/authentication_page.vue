<template>
  <div class="container">
    <!-- Left Side -->
    <div class="left-side">
      <!-- Logo on Top -->
      <div class="logo">
        <!-- <img src="@/assets/images/black_logo.png" alt="Logo" /> -->
        <label class="logo-name">MOTION CYCLE</label>
      </div>

      <!-- Illustration in Middle -->
      <div class="illustration">
        <img src="@/assets/images/auth/poster_login.png" alt="Cycling Illustration" />
      </div>

      <!-- Content Below -->
      <div class="left-content">
        <h2>Welcome to Motion Cycle</h2>
        <span>
          Discover the best bikes and gear for your next ride. Join our community of passionate
          cyclists today!
        </span>
      </div>
    </div>

    <!-- Right Side -->
    <div class="right-side">
      <div class="form-wrapper">
        <component :is="currentForm" />
      </div>
    </div>
  </div>
</template>

<script>
import SignInForm from '@/components/auth/sign_in_form.vue'
import SignUpForm from '@/components/auth/sign_up_form.vue'

export default {
  name: 'AuthenticationPage',
  props: ['type'],
  components: {
    SignInForm,
    SignUpForm,
  },
  computed: {
    currentForm() {
      return this.type === 'sign_in' ? 'SignInForm' : 'SignUpForm'
    },
  },
}
</script>

<style scoped>
.logo-name {
  font-size: 22px;
  font-weight: 600;
  font-family: 'Poppins', sans-serif;
  color: black;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}
.container {
  display: flex;
}

.left-side {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: center;
  background-color: #14c9c9;
  height: 97vh;
  margin: 14px;
  border-radius: 10px;
  color: white;
  text-align: center;
}
.logo {
  display: flex;
  align-self: flex-start;
  align-items: center;
  gap: 20px;
  background-color: white;
  clip-path: polygon(0 0, 100% 0, 90% 100%, 0 100%);
  padding: 10px 50px 14px 10px;
}
.logo img {
  width: 60px;
  height: auto;
}

.illustration img {
  max-width: 70%;
  border-radius: 20px;
}

.left-content {
  position: relative;
  bottom: 30px;
  max-width: 80%;
  font-family: 'Poppins', sans-serif;
}

.left-content h2 {
  font-size: 26px;
  font-weight: 600;
}

.left-content span {
  font-size: 16px;
  font-weight: 400;
}

.right-side {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 40px;
  background-color: #ffffff;
  position: relative;
}

.guest-btn {
  position: relative;
  align-self: flex-end;
  bottom: 100px;
}

.form-wrapper {
  display: flex;
  justify-content: center;
}
</style>
