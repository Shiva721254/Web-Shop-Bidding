<script setup>
import { RouterView } from 'vue-router'
import Header from './components/organisms/Header/Header.vue'
import Footer from './components/organisms/Footer/Footer.vue'
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <Header />
    <main class="flex-1">
      <RouterView />
    </main>
    <Footer />
  </div>
</template>
