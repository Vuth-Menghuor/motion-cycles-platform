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
      // Determine which form to show based on the type prop
      if (this.type === 'sign_in') {
        return 'SignInForm'
      } else {
        return 'SignUpForm'
      }
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
  min-height: 100vh;
  padding: 14px;
  box-sizing: border-box;
}

.left-side {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  align-items: center;
  background-color: #14c9c9;
  min-height: calc(100vh - 28px);
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
  padding: 40px clamp(20px, 5vw, 72px);
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
  width: 100%;
}

@media (max-width: 900px) {
  .left-side { flex: 0.8; }
  .illustration img { max-width: 82%; }
}

@media (max-width: 700px) {
  .container { display: block; padding: 0; }
  .left-side { min-height: 185px; margin: 0; border-radius: 0 0 20px 20px; justify-content: flex-start; }
  .logo { padding: 9px 38px 12px 12px; }
  .illustration { display: none; }
  .left-content { bottom: auto; max-width: min(88%, 460px); margin: 22px 0; }
  .left-content h2 { margin: 0 0 6px; font-size: clamp(21px, 6vw, 26px); }
  .left-content span { display: block; font-size: 14px; line-height: 1.5; }
  .right-side { padding: 28px 16px 40px; }
}
</style>
