<template>
  <div id="app">
    <!-- Main application container with router view for page navigation -->
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  name: 'App',
}
</script>

<style>
#app {
  min-height: 100vh;
}
</style>
