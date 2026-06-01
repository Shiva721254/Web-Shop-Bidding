<template>
  <footer class="bg-gray-900 text-gray-300 mt-auto">
    <div class="page-container py-12">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="lg:col-span-2">
          <p class="text-white font-bold text-xl mb-3">Bid Shop</p>
          <p class="text-gray-400 text-sm leading-relaxed max-w-sm">
            A web shop where you can buy products directly or bid in live auctions.
            Find unique items at the best prices.
          </p>
        </div>

        <div>
          <p class="text-white font-semibold text-sm mb-4 uppercase tracking-wide">Shop</p>
          <ul class="space-y-2 text-sm">
            <li><RouterLink to="/auctions" class="hover:text-white transition-colors">Auctions</RouterLink></li>
            <li><RouterLink to="/products" class="hover:text-white transition-colors">Products</RouterLink></li>
            <li><RouterLink to="/cart"     class="hover:text-white transition-colors">Cart</RouterLink></li>
          </ul>
        </div>

        <div>
          <p class="text-white font-semibold text-sm mb-4 uppercase tracking-wide">Account</p>
          <ul class="space-y-2 text-sm">
            <li><RouterLink to="/login"    class="hover:text-white transition-colors">Log In</RouterLink></li>
            <li><RouterLink to="/register" class="hover:text-white transition-colors">Register</RouterLink></li>
            <li><RouterLink to="/orders"   class="hover:text-white transition-colors">My Orders</RouterLink></li>
          </ul>
        </div>
      </div>

      <div class="mt-10 pt-6 border-t border-gray-800 flex flex-col sm:flex-row justify-between items-center gap-2 text-xs text-gray-500">
        <p>&copy; {{ year }} Bid Shop. All rights reserved.</p>
        <p>Built with Vue 3 · PHP 8.3 · MariaDB · Tailwind CSS</p>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { RouterLink } from 'vue-router'
const year = new Date().getFullYear()
</script>
