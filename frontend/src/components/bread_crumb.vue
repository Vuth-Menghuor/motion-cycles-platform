<template>
  <nav class="breadcrumb">
    <ul>
      <li>
        <router-link to="/">Home</router-link>
      </li>
      <li v-if="brand">
        <router-link :to="`/brands/${brand}`">{{ brand }}</router-link>
      </li>
      <li v-if="product">{{ product }}</li>
    </ul>
  </nav>
</template>

<script setup>
defineProps({
  brand: { type: String, required: false },
  product: { type: String, required: false },
})
</script>

<style scoped>
/* Breadcrumb */
.breadcrumb ul {
  display: flex;
  list-style: none;
  padding: 0;
  margin: 0 0 16px 0;
  gap: 5px;
  font-size: 16px;
}

.breadcrumb li::after {
  content: '>';
  margin: 0 5px;
  transition: color 0.3s ease;
}

.breadcrumb li:last-child::after {
  content: '';
}

.breadcrumb a {
  color: grey;
  text-decoration: none;
  transition: all 0.3s ease;
}

.breadcrumb a:hover {
  font-weight: 500;
  color: #3491fa;
}

.breadcrumb li {
  font-weight: 500;
  color: grey;
  transition: all 0.3s ease;
}

.breadcrumb li:hover {
  font-weight: 500;
  color: #3491fa;
}
</style>
