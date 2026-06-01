<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <button @click="router.back()" class="mb-6 text-blue-600 hover:text-blue-700 flex items-center gap-1 font-medium">
      &larr; Back to Auctions
    </button>

    <div v-if="loading" class="flex justify-center py-24">
      <p class="text-gray-500 text-lg">Loading auction...</p>
    </div>

    <div v-else-if="error" class="text-center py-24">
      <p class="text-red-600 font-medium">{{ error }}</p>
    </div>

    <div v-else-if="auction">
      <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 mb-6">
        <div class="flex items-start justify-between mb-4">
          <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full">
            {{ auction.category }}
          </span>
          <span :class="['px-3 py-1 text-sm font-semibold rounded-full', auction.status === 'open' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600']">
            {{ auction.status === 'open' ? 'Open' : 'Closed' }}
          </span>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ auction.title }}</h1>
        <p class="text-gray-600 mb-6 leading-relaxed">{{ auction.description }}</p>

        <div class="grid grid-cols-2 gap-6 mb-6">
          <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-sm text-gray-500 mb-1">Starting Price</p>
            <p class="text-2xl font-bold text-gray-900">€{{ auction.startingPrice?.toFixed(2) }}</p>
          </div>
          <div class="bg-blue-50 rounded-xl p-4">
            <p class="text-sm text-blue-600 mb-1">Current Bid</p>
            <p class="text-2xl font-bold text-blue-700">€{{ (auction.currentBid ?? auction.startingPrice)?.toFixed(2) }}</p>
          </div>
        </div>

        <p class="text-sm text-gray-500 mb-6">
          Ends: <span class="font-medium text-gray-800">{{ formatDate(auction.endsAt) }}</span>
        </p>

        <div v-if="auction.status === 'open'" class="border-t border-gray-100 pt-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-3">Place a Bid</h2>
          <div v-if="!isLoggedIn" class="text-center py-4">
            <p class="text-gray-600 mb-3">You must be logged in to place a bid.</p>
            <RouterLink to="/login" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
              Log In
            </RouterLink>
          </div>
          <form v-else @submit.prevent="placeBid" class="flex gap-3">
            <input
              v-model.number="bidAmount"
              type="number"
              step="0.01"
              :min="minBid"
              :placeholder="`Min bid: €${minBid.toFixed(2)}`"
              class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <button
              type="submit"
              :disabled="bidding"
              class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-colors font-medium"
            >
              {{ bidding ? 'Placing...' : 'Place Bid' }}
            </button>
          </form>
          <p v-if="bidError" class="mt-2 text-red-600 text-sm">{{ bidError }}</p>
          <p v-if="bidSuccess" class="mt-2 text-green-600 text-sm font-medium">{{ bidSuccess }}</p>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Bid History ({{ bids.length }})</h2>
        <div v-if="bids.length === 0" class="text-gray-500 text-sm py-4 text-center">No bids yet.</div>
        <ul v-else class="divide-y divide-gray-100">
          <li v-for="bid in bids" :key="bid.id" class="py-3 flex justify-between items-center">
            <span class="text-gray-600 text-sm">Bidder #{{ bid.userId }}</span>
            <span class="font-semibold text-blue-700">€{{ bid.amount?.toFixed(2) }}</span>
            <span class="text-gray-400 text-xs">{{ formatDate(bid.createdAt) }}</span>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { get, post } from '../../../utils/api.js'

const route  = useRoute()
const router = useRouter()

const auction    = ref(null)
const bids       = ref([])
const loading    = ref(true)
const error      = ref(null)
const bidAmount  = ref(0)
const bidding    = ref(false)
const bidError   = ref(null)
const bidSuccess = ref(null)

const isLoggedIn = computed(() => !!localStorage.getItem('token'))
const minBid     = computed(() => {
  const current = auction.value?.currentBid ?? auction.value?.startingPrice ?? 0
  return parseFloat((current + 0.01).toFixed(2))
})

const formatDate = (dateStr) =>
  dateStr ? new Date(dateStr).toLocaleString('nl-NL', { dateStyle: 'medium', timeStyle: 'short' }) : '—'

async function fetchAuction() {
  loading.value = true
  error.value   = null
  try {
    const [auctionRes, bidsRes] = await Promise.all([
      get(`/auctions/${route.params.id}`),
      get(`/auctions/${route.params.id}/bids`),
    ])
    const auctionBody = await auctionRes.json()
    const bidsBody    = await bidsRes.json()
    if (!auctionRes.ok) throw new Error(auctionBody.error || 'Auction not found')
    auction.value = auctionBody
    bids.value    = bidsBody.data ?? bidsBody
  } catch (err) {
    error.value = err.message
  } finally {
    loading.value = false
  }
}

async function placeBid() {
  bidError.value   = null
  bidSuccess.value = null
  bidding.value    = true
  try {
    const res  = await post(`/auctions/${route.params.id}/bids`, { amount: bidAmount.value })
    const body = await res.json()
    if (!res.ok) throw new Error(body.error || 'Bid failed')
    bidSuccess.value       = `Bid of €${bidAmount.value.toFixed(2)} placed successfully!`
    auction.value.currentBid = bidAmount.value
    bids.value.unshift(body)
    bidAmount.value = 0
  } catch (err) {
    bidError.value = err.message
  } finally {
    bidding.value = false
  }
}

onMounted(fetchAuction)
</script>
