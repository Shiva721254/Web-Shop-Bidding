<template>
  <div class="page-container">
    <div class="mb-8">
      <h1 class="section-heading">Orders</h1>
      <p class="text-gray-500 mt-1">{{ total }} order{{ total !== 1 ? 's' : '' }} total</p>
    </div>

    <div v-if="loading" class="flex justify-center py-24">
      <p class="text-gray-400 text-lg">Loading orders...</p>
    </div>

    <div v-else class="card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Order ID</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Customer</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Items</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Total</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Date</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-if="orders.length === 0">
              <td colspan="6" class="px-4 py-10 text-center text-gray-400">No orders found.</td>
            </tr>
            <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3 font-medium text-gray-900">#{{ order.id }}</td>
              <td class="px-4 py-3 text-gray-500">User #{{ order.userId }}</td>
              <td class="px-4 py-3 text-gray-500">{{ order.items?.length ?? 0 }} item{{ order.items?.length !== 1 ? 's' : '' }}</td>
              <td class="px-4 py-3 font-semibold text-gray-800">€{{ order.total?.toFixed(2) }}</td>
              <td class="px-4 py-3 text-gray-400 text-xs">{{ formatDate(order.createdAt) }}</td>
              <td class="px-4 py-3">
                <select
                  :value="order.status"
                  @change="updateStatus(order, $event.target.value)"
                  :class="['text-xs font-semibold rounded-lg px-2 py-1 border-0 cursor-pointer focus:ring-2 focus:ring-blue-500 outline-none', statusClass(order.status)]"
                >
                  <option value="pending">pending</option>
                  <option value="confirmed">confirmed</option>
                  <option value="cancelled">cancelled</option>
                </select>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="totalPages > 1" class="flex justify-center gap-2 p-4 border-t border-gray-100">
        <button v-for="p in totalPages" :key="p" @click="page = p; fetchOrders()"
          :class="['px-3 py-1 rounded text-sm font-medium', p === page ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50']">
          {{ p }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { get, put } from '../../../utils/api.js'

const orders     = ref([])
const loading    = ref(true)
const page       = ref(1)
const total      = ref(0)
const totalPages = ref(0)
const limit      = 20

const formatDate  = (d) => d ? new Date(d).toLocaleDateString('nl-NL', { dateStyle: 'medium' }) : '—'
const statusClass = (s) => ({
  pending:   'bg-yellow-100 text-yellow-700',
  confirmed: 'bg-green-100 text-green-700',
  cancelled: 'bg-red-100 text-red-700',
}[s] ?? 'bg-gray-100 text-gray-600')

async function fetchOrders() {
  loading.value = true
  try {
    const res  = await get(`/orders?page=${page.value}&limit=${limit}`)
    const body = await res.json()
    orders.value     = body.data ?? body
    total.value      = body.total ?? orders.value.length
    totalPages.value = Math.ceil(total.value / limit)
  } finally {
    loading.value = false
  }
}

async function updateStatus(order, status) {
  try {
    const res  = await put(`/orders/${order.id}/status`, { status })
    const body = await res.json()
    if (!res.ok) throw new Error(body.error)
    order.status = status
  } catch (err) {
    alert(`Failed to update status: ${err.message}`)
  }
}

onMounted(fetchOrders)
</script>
