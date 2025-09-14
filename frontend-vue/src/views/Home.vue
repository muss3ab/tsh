<script setup lang="ts">
// Home page - could show featured products or categories
</script>

<template>
  <div class="text-center">
    <h1 class="text-4xl font-bold text-gray-900 mb-4">Welcome to Our Store</h1>
    <p class="text-lg text-gray-600 mb-8">Discover amazing products at great prices</p>
    <RouterLink
      to="/products"
      class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors"
    >
      Shop Now
    </RouterLink>
  </div>
</template>
