<template>
  <header class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">

        <RouterLink to="/auctions" class="text-xl font-bold text-blue-600 hover:text-blue-700 transition-colors">
          Bid Shop
        </RouterLink>

        <nav class="hidden md:flex items-center space-x-8">
          <RouterLink to="/auctions" class="text-gray-700 hover:text-blue-600 transition-colors font-medium" active-class="text-blue-600">
            Auctions
          </RouterLink>
          <RouterLink to="/products" class="text-gray-700 hover:text-blue-600 transition-colors font-medium" active-class="text-blue-600">
            Products
          </RouterLink>
          <RouterLink to="/cart" class="relative text-gray-700 hover:text-blue-600 transition-colors font-medium" active-class="text-blue-600">
            Cart
            <span v-if="cartStore.itemCount > 0" class="absolute -top-2 -right-3 bg-blue-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
              {{ cartStore.itemCount }}
            </span>
          </RouterLink>
          <RouterLink v-if="authStore.isLoggedIn" to="/orders" class="text-gray-700 hover:text-blue-600 transition-colors font-medium" active-class="text-blue-600">
            My Orders
          </RouterLink>
          <RouterLink v-if="authStore.isAdmin" to="/admin" class="px-3 py-1 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition-colors" active-class="bg-purple-700">
            Admin
          </RouterLink>
          <RouterLink v-if="!authStore.isLoggedIn" to="/login" class="text-gray-700 hover:text-blue-600 transition-colors font-medium" active-class="text-blue-600">
            Login
          </RouterLink>
          <span v-if="authStore.isLoggedIn" class="text-sm text-gray-500">
            {{ authStore.user?.name }}
          </span>
          <button v-if="authStore.isLoggedIn" @click="logout" class="text-gray-700 hover:text-red-600 transition-colors font-medium">
            Logout
          </button>
        </nav>

        <button class="md:hidden text-gray-700 hover:text-blue-600" @click="mobileMenuOpen = !mobileMenuOpen" aria-label="Toggle menu">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

      <div v-if="mobileMenuOpen" class="md:hidden pb-4">
        <nav class="flex flex-col space-y-2">
          <RouterLink to="/auctions" class="text-gray-700 hover:text-blue-600 px-2 py-1" @click="mobileMenuOpen = false">Auctions</RouterLink>
          <RouterLink to="/products" class="text-gray-700 hover:text-blue-600 px-2 py-1" @click="mobileMenuOpen = false">Products</RouterLink>
          <RouterLink to="/cart" class="text-gray-700 hover:text-blue-600 px-2 py-1" @click="mobileMenuOpen = false">Cart ({{ cartStore.itemCount }})</RouterLink>
          <RouterLink v-if="authStore.isLoggedIn" to="/orders" class="text-gray-700 hover:text-blue-600 px-2 py-1" @click="mobileMenuOpen = false">My Orders</RouterLink>
          <RouterLink v-if="authStore.isAdmin" to="/admin" class="text-purple-600 font-semibold px-2 py-1" @click="mobileMenuOpen = false">Admin Panel</RouterLink>
          <RouterLink v-if="!authStore.isLoggedIn" to="/login" class="text-gray-700 hover:text-blue-600 px-2 py-1" @click="mobileMenuOpen = false">Login</RouterLink>
          <button v-if="authStore.isLoggedIn" @click="logout" class="text-left text-red-600 px-2 py-1">Logout</button>
        </nav>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useAuthStore } from '../../../stores/auth.js'
import { useCartStore } from '../../../stores/cart.js'

const router        = useRouter()
const authStore     = useAuthStore()
const cartStore     = useCartStore()
const mobileMenuOpen = ref(false)

function logout() {
  authStore.logout()
  mobileMenuOpen.value = false
  router.push('/login')
}
</script>
