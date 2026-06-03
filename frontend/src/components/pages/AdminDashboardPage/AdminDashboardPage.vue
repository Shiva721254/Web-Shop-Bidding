<template>
  <div class="page-container">
    <div class="mb-8 flex items-center justify-between">
      <div>
        <h1 class="section-heading">Admin Dashboard</h1>
        <p class="text-gray-500 mt-1">Overview of shop activity</p>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
      <div v-for="stat in stats" :key="stat.label" class="card p-6">
        <p class="text-sm text-gray-500 mb-1">{{ stat.label }}</p>
        <p class="text-3xl font-bold" :class="stat.color">
          {{ loading ? '—' : stat.value }}
        </p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
      <RouterLink to="/admin/products"
        class="card p-6 flex items-start gap-4 hover:border-blue-200 border border-transparent transition-colors group">
        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-2xl flex-shrink-0 group-hover:bg-blue-200 transition-colors">📦</div>
        <div>
          <p class="font-bold text-gray-900 mb-1">Manage Products</p>
          <p class="text-sm text-gray-500">Create, edit and delete products. Set buy-now prices or starting bids.</p>
        </div>
      </RouterLink>

      <RouterLink to="/admin/orders"
        class="card p-6 flex items-start gap-4 hover:border-green-200 border border-transparent transition-colors group">
        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-2xl flex-shrink-0 group-hover:bg-green-200 transition-colors">🧾</div>
        <div>
          <p class="font-bold text-gray-900 mb-1">Manage Orders</p>
          <p class="text-sm text-gray-500">View all customer orders and update their status.</p>
        </div>
      </RouterLink>

      <RouterLink to="/admin/auctions"
        class="card p-6 flex items-start gap-4 hover:border-purple-200 border border-transparent transition-colors group">
        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-2xl flex-shrink-0 group-hover:bg-purple-200 transition-colors">🔨</div>
        <div>
          <p class="font-bold text-gray-900 mb-1">Monitor Auctions</p>
          <p class="text-sm text-gray-500">Track all auctions, current bids, bid counts and auction status.</p>
        </div>
      </RouterLink>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { get } from '../../../utils/api.js'

const loading = ref(true)
const stats   = ref([
  { label: 'Total Products', value: 0, color: 'text-blue-600' },
  { label: 'Active Auctions', value: 0, color: 'text-purple-600' },
  { label: 'Total Orders', value: 0, color: 'text-green-600' },
  { label: 'Open Bids', value: 0, color: 'text-orange-500' },
])

onMounted(async () => {
  try {
    const [pRes, aRes, oRes] = await Promise.all([
      get('/products?limit=1'),
      get('/auctions?status=open&limit=1'),
      get('/orders?limit=1'),
    ])
    const [pBody, aBody, oBody] = await Promise.all([pRes.json(), aRes.json(), oRes.json()])
    stats.value[0].value = pBody.total ?? 0
    stats.value[1].value = aBody.total ?? 0
    stats.value[2].value = oBody.total ?? 0
  } catch {
    // stats stay 0
  } finally {
    loading.value = false
  }
})
</script>
