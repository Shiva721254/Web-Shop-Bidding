<template>
  <div class="page-container">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="section-heading">Auction Monitor</h1>
        <p class="text-gray-500 mt-1">{{ total }} auction{{ total !== 1 ? 's' : '' }} total</p>
      </div>
      <div class="flex gap-2">
        <button
          v-for="f in filters"
          :key="f.value"
          @click="statusFilter = f.value; page = 1; fetchAuctions()"
          :class="['btn text-sm', statusFilter === f.value ? 'btn-primary' : 'btn-outline']"
        >
          {{ f.label }}
        </button>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center py-24">
      <p class="text-gray-400 text-lg">Loading auctions...</p>
    </div>

    <div v-else class="card overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
              <th class="text-left px-4 py-3 font-semibold text-gray-600">ID</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Title</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Category</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Starting Price</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Current Bid</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Ends At</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Bids</th>
              <th class="text-left px-4 py-3 font-semibold text-gray-600">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-if="auctions.length === 0">
              <td colspan="9" class="px-4 py-10 text-center text-gray-400">No auctions found.</td>
            </tr>
            <tr
              v-for="auction in auctions"
              :key="auction.id"
              class="hover:bg-gray-50 transition-colors"
            >
              <td class="px-4 py-3 text-gray-400">#{{ auction.id }}</td>
              <td class="px-4 py-3 font-medium text-gray-900 max-w-xs truncate">{{ auction.title }}</td>
              <td class="px-4 py-3 text-gray-500">{{ auction.category || '—' }}</td>
              <td class="px-4 py-3 text-gray-700">{{ formatPrice(auction.startingPrice) }}</td>
              <td class="px-4 py-3 font-semibold text-blue-700">
                {{ formatPrice(auction.currentBid ?? auction.startingPrice) }}
                <span v-if="auction.currentBid && auction.currentBid > auction.startingPrice"
                  class="ml-1 text-xs text-green-600 font-normal">
                  +{{ formatPrice(auction.currentBid - auction.startingPrice) }}
                </span>
              </td>
              <td class="px-4 py-3 text-gray-500 text-xs">
                <span :class="isExpired(auction.endsAt) ? 'text-red-500' : 'text-gray-600'">
                  {{ formatDate(auction.endsAt) }}
                </span>
              </td>
              <td class="px-4 py-3">
                <span :class="['badge', auction.status === 'open' ? 'badge-green' : 'badge-gray']">
                  {{ auction.status }}
                </span>
              </td>
              <td class="px-4 py-3">
                <span
                  v-if="bidCounts[auction.id] !== undefined"
                  class="font-semibold text-gray-700"
                >
                  {{ bidCounts[auction.id] }}
                </span>
                <span v-else class="text-gray-400">—</span>
              </td>
              <td class="px-4 py-3">
                <RouterLink
                  :to="{ name: 'AuctionDetail', params: { id: auction.id } }"
                  class="btn btn-outline text-xs py-1 px-3"
                >
                  View Bids
                </RouterLink>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="totalPages > 1" class="flex justify-center gap-2 p-4 border-t border-gray-100">
        <button
          v-for="p in totalPages"
          :key="p"
          @click="page = p; fetchAuctions()"
          :class="['px-3 py-1 rounded text-sm font-medium', p === page ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50']"
        >
          {{ p }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { get } from '../../../utils/api.js'

const auctions     = ref([])
const bidCounts    = ref({})
const loading      = ref(true)
const statusFilter = ref('')
const page         = ref(1)
const total        = ref(0)
const totalPages   = ref(0)
const limit        = 15

const filters = [
  { label: 'All',    value: '' },
  { label: 'Open',   value: 'open' },
  { label: 'Closed', value: 'closed' },
]

const formatPrice = (v) =>
  v != null ? new Intl.NumberFormat('nl-NL', { style: 'currency', currency: 'EUR' }).format(v) : '—'

const formatDate = (d) =>
  d ? new Date(d).toLocaleString('nl-NL', { dateStyle: 'medium', timeStyle: 'short' }) : '—'

const isExpired = (d) => d && new Date(d) < new Date()

async function fetchAuctions() {
  loading.value = true
  try {
    const params = new URLSearchParams({ page: page.value, limit })
    if (statusFilter.value) params.set('status', statusFilter.value)

    const res  = await get(`/auctions?${params}`)
    const body = await res.json()
    auctions.value   = body.data ?? body
    total.value      = body.total ?? auctions.value.length
    totalPages.value = Math.ceil(total.value / limit)

    fetchBidCounts()
  } finally {
    loading.value = false
  }
}

async function fetchBidCounts() {
  const counts = {}
  await Promise.all(
    auctions.value.map(async (auction) => {
      try {
        const res  = await get(`/auctions/${auction.id}/bids?limit=1`)
        const body = await res.json()
        counts[auction.id] = body.total ?? 0
      } catch {
        counts[auction.id] = 0
      }
    })
  )
  bidCounts.value = counts
}

onMounted(fetchAuctions)
</script>
