<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">My Orders</h1>

    <div v-if="loading" class="flex justify-center py-24">
      <p class="text-gray-500 text-lg">Loading orders...</p>
    </div>

    <div v-else-if="error" class="text-center py-16">
      <p class="text-red-600">{{ error }}</p>
    </div>

    <div v-else-if="orders.length === 0" class="text-center py-16 text-gray-500">
      <p class="text-lg">You have no orders yet.</p>
      <RouterLink to="/products" class="mt-4 inline-block px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
        Browse Products
      </RouterLink>
    </div>

    <div v-else class="space-y-4">
      <div
        v-for="order in orders"
        :key="order.id"
        class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6"
      >
        <div class="flex justify-between items-start mb-4">
          <div>
            <p class="text-sm text-gray-500">Order #{{ order.id }}</p>
            <p class="text-sm text-gray-500">{{ formatDate(order.createdAt) }}</p>
          </div>
          <div class="text-right">
            <span :class="['px-3 py-1 text-sm font-semibold rounded-full', statusClass(order.status)]">
              {{ order.status }}
            </span>
            <p class="text-xl font-bold text-gray-900 mt-1">€{{ order.total?.toFixed(2) }}</p>
          </div>
        </div>

        <div class="border-t border-gray-100 pt-4">
          <p class="text-sm font-medium text-gray-700 mb-2">Items ({{ order.items?.length ?? 0 }})</p>
          <ul class="space-y-2">
            <li
              v-for="item in order.items"
              :key="item.id"
              class="flex justify-between text-sm text-gray-600"
            >
              <span>Product #{{ item.productId }} × {{ item.quantity }}</span>
              <span class="font-medium">€{{ (item.priceAtPurchase * item.quantity).toFixed(2) }}</span>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { get } from '../../../utils/api.js'

const orders  = ref([])
const loading = ref(true)
const error   = ref(null)

const formatDate = (d) => d ? new Date(d).toLocaleDateString('nl-NL', { dateStyle: 'medium' }) : '—'

const statusClass = (status) => ({
  pending:   'bg-yellow-100 text-yellow-700',
  confirmed: 'bg-green-100 text-green-700',
  cancelled: 'bg-red-100 text-red-700',
}[status] ?? 'bg-gray-100 text-gray-600')

async function fetchOrders() {
  loading.value = true
  error.value   = null
  try {
    const user = JSON.parse(localStorage.getItem('user') || '{}')
    if (!user.id) throw new Error('User not found')
    const res  = await get(`/users/${user.id}/orders`)
    const body = await res.json()
    if (!res.ok) throw new Error(body.error || 'Failed to fetch orders')
    orders.value = body.data ?? body
  } catch (err) {
    error.value  = err.message
    orders.value = []
  } finally {
    loading.value = false
  }
}

onMounted(fetchOrders)
</script>
