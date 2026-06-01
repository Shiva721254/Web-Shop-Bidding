<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

    <div v-if="cartStore.items.length === 0" class="text-center py-16">
      <p class="text-gray-500 text-lg mb-4">Your cart is empty.</p>
      <RouterLink to="/products" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
        Browse Products
      </RouterLink>
    </div>

    <div v-else>
      <div class="bg-white rounded-2xl shadow-sm border border-gray-200 divide-y divide-gray-100 mb-6">
        <div v-for="item in cartStore.items" :key="item.productId" class="p-4 flex items-center gap-4">
          <div class="flex-1">
            <p class="font-semibold text-gray-900">{{ item.title }}</p>
            <p class="text-sm text-gray-500">€{{ item.price.toFixed(2) }} each</p>
          </div>
          <div class="flex items-center gap-2">
            <button @click="cartStore.updateQuantity(item.productId, item.quantity - 1)"
              class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-gray-700 font-bold">
              −
            </button>
            <span class="w-8 text-center font-medium">{{ item.quantity }}</span>
            <button @click="cartStore.updateQuantity(item.productId, item.quantity + 1)"
              class="w-8 h-8 flex items-center justify-center border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-gray-700 font-bold">
              +
            </button>
          </div>
          <p class="w-24 text-right font-semibold text-gray-900">€{{ (item.price * item.quantity).toFixed(2) }}</p>
          <button @click="cartStore.removeItem(item.productId)" class="text-red-500 hover:text-red-700 transition-colors ml-2" aria-label="Remove">
            ✕
          </button>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-6">
          <span class="text-xl font-bold text-gray-900">Total</span>
          <span class="text-2xl font-bold text-blue-700">€{{ cartStore.total.toFixed(2) }}</span>
        </div>

        <div v-if="!authStore.isLoggedIn" class="text-center">
          <p class="text-gray-600 mb-3">Please log in to place an order.</p>
          <RouterLink to="/login" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
            Log In
          </RouterLink>
        </div>

        <div v-else>
          <p v-if="orderError"   class="mb-3 text-red-600 text-sm">{{ orderError }}</p>
          <p v-if="orderSuccess" class="mb-3 text-green-600 text-sm font-medium">{{ orderSuccess }}</p>
          <button @click="placeOrder" :disabled="ordering"
            class="w-full py-3 bg-green-600 text-white rounded-xl hover:bg-green-700 disabled:opacity-50 transition-colors font-semibold text-lg">
            {{ ordering ? 'Placing Order...' : 'Place Order' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { useCartStore } from '../../../stores/cart.js'
import { useAuthStore } from '../../../stores/auth.js'
import { post } from '../../../utils/api.js'

const cartStore   = useCartStore()
const authStore   = useAuthStore()
const router      = useRouter()
const ordering    = ref(false)
const orderError  = ref(null)
const orderSuccess = ref(null)

async function placeOrder() {
  ordering.value    = true
  orderError.value  = null
  orderSuccess.value = null
  try {
    const items = cartStore.items.map(i => ({ productId: i.productId, quantity: i.quantity }))
    const res   = await post('/orders', { items })
    const body  = await res.json()
    if (!res.ok) throw new Error(body.error || 'Order failed')
    cartStore.clear()
    orderSuccess.value = `Order #${body.id} placed successfully!`
    setTimeout(() => router.push('/orders'), 1500)
  } catch (err) {
    orderError.value = err.message
  } finally {
    ordering.value = false
  }
}
</script>
