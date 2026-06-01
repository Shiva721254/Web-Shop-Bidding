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

      <!-- Winner banner -->
      <div v-if="auction.status === 'closed' && isWinner"
        class="mb-6 bg-green-50 border border-green-200 rounded-2xl p-5 flex items-center gap-4">
        <span class="text-3xl">🏆</span>
        <div>
          <p class="font-bold text-green-800 text-lg">You won this auction!</p>
          <p class="text-green-700 text-sm">Congratulations! The admin will contact you to arrange payment and delivery.</p>
        </div>
      </div>

      <!-- Auction closed, not winner -->
      <div v-else-if="auction.status === 'closed' && authStore.isLoggedIn && !isWinner"
        class="mb-6 bg-gray-50 border border-gray-200 rounded-2xl p-5 flex items-center gap-4">
        <span class="text-3xl">⏰</span>
        <div>
          <p class="font-bold text-gray-700 text-lg">This auction has ended</p>
          <p class="text-gray-500 text-sm">Better luck next time! Browse other open auctions below.</p>
        </div>
      </div>

      <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 mb-6">
        <div class="flex items-start justify-between mb-4">
          <span class="badge badge-blue">{{ auction.category }}</span>
          <span :class="['badge', auction.status === 'open' ? 'badge-green' : 'badge-gray']">
            {{ auction.status === 'open' ? 'Open' : 'Closed' }}
          </span>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-3">{{ auction.title }}</h1>
        <p class="text-gray-600 mb-6 leading-relaxed">{{ auction.description }}</p>

        <div class="grid grid-cols-2 gap-6 mb-6">
          <div class="bg-gray-50 rounded-xl p-4">
            <p class="text-xs text-gray-400 mb-1">Starting Price</p>
            <p class="text-2xl font-bold text-gray-900">{{ formatPrice(auction.startingPrice) }}</p>
          </div>
          <div class="bg-blue-50 rounded-xl p-4">
            <p class="text-xs text-blue-500 mb-1">{{ auction.status === 'open' ? 'Current Bid' : 'Final Bid' }}</p>
            <p class="text-2xl font-bold text-blue-700">
              {{ formatPrice(auction.currentBid ?? auction.startingPrice) }}
            </p>
          </div>
        </div>

        <!-- Countdown timer -->
        <div class="mb-6">
          <div v-if="auction.status === 'open'" class="bg-amber-50 border border-amber-200 rounded-xl p-4">
            <p class="text-xs text-amber-600 font-medium mb-2 uppercase tracking-wide">Time Remaining</p>
            <div v-if="countdown.expired" class="text-red-600 font-bold text-lg">
              Calculating final result...
            </div>
            <div v-else class="grid grid-cols-4 gap-3 text-center">
              <div v-for="unit in countdown.units" :key="unit.label">
                <p class="text-2xl font-bold text-amber-700">{{ unit.value }}</p>
                <p class="text-xs text-amber-500 uppercase tracking-wide">{{ unit.label }}</p>
              </div>
            </div>
          </div>
          <div v-else class="text-sm text-gray-500">
            Ended: <span class="font-medium text-gray-700">{{ formatDate(auction.endsAt) }}</span>
          </div>
        </div>

        <!-- Bid form -->
        <div v-if="auction.status === 'open' && !countdown.expired" class="border-t border-gray-100 pt-6">
          <h2 class="text-lg font-semibold text-gray-900 mb-3">Place a Bid</h2>
          <div v-if="!authStore.isLoggedIn" class="text-center py-4">
            <p class="text-gray-600 mb-3">You must be logged in to place a bid.</p>
            <RouterLink to="/login" class="btn btn-primary">Log In to Bid</RouterLink>
          </div>
          <form v-else @submit.prevent="placeBid" class="flex gap-3">
            <input
              v-model.number="bidAmount"
              type="number"
              step="0.01"
              :min="minBid"
              :placeholder="`Min bid: ${formatPrice(minBid)}`"
              class="input flex-1"
            />
            <button type="submit" :disabled="bidding" class="btn btn-primary">
              {{ bidding ? 'Placing...' : 'Place Bid' }}
            </button>
          </form>
          <p v-if="bidError"   class="mt-2 text-red-600 text-sm">{{ bidError }}</p>
          <p v-if="bidSuccess" class="mt-2 text-green-600 text-sm font-medium">{{ bidSuccess }}</p>
        </div>
      </div>

      <!-- Bid history -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">
          Bid History <span class="text-gray-400 font-normal text-base">({{ bids.length }})</span>
        </h2>
        <div v-if="bids.length === 0" class="text-gray-400 text-sm py-4 text-center">No bids placed yet.</div>
        <ul v-else class="divide-y divide-gray-100">
          <li v-for="(bid, index) in bids" :key="bid.id"
            class="py-3 flex justify-between items-center"
            :class="index === 0 && auction.status === 'closed' ? 'bg-green-50 rounded-lg px-3' : ''">
            <div class="flex items-center gap-2">
              <span v-if="index === 0 && auction.status === 'closed'" class="text-sm">🏆</span>
              <span class="text-gray-600 text-sm">Bidder #{{ bid.userId }}</span>
              <span v-if="index === 0" class="badge badge-blue text-xs">Highest</span>
            </div>
            <span class="font-bold text-blue-700">{{ formatPrice(bid.amount) }}</span>
            <span class="text-gray-400 text-xs">{{ formatDate(bid.createdAt) }}</span>
          </li>
        </ul>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter, RouterLink } from 'vue-router'
import { useAuthStore } from '../../../stores/auth.js'
import { get, post } from '../../../utils/api.js'

const route     = useRoute()
const router    = useRouter()
const authStore = useAuthStore()

const auction    = ref(null)
const bids       = ref([])
const loading    = ref(true)
const error      = ref(null)
const bidAmount  = ref(0)
const bidding    = ref(false)
const bidError   = ref(null)
const bidSuccess = ref(null)
let   timerRef   = null

const isWinner = computed(() =>
  auction.value?.winnerId && authStore.user?.id === auction.value.winnerId
)

const minBid = computed(() => {
  const current = auction.value?.currentBid ?? auction.value?.startingPrice ?? 0
  return parseFloat((current + 0.01).toFixed(2))
})

const countdown = ref({ units: [], expired: false })

function updateCountdown() {
  if (!auction.value) return
  const diff = new Date(auction.value.endsAt) - new Date()

  if (diff <= 0) {
    countdown.value = { expired: true, units: [] }
    clearInterval(timerRef)
    // Re-fetch so backend closes the auction and sets winner
    setTimeout(fetchAuction, 1500)
    return
  }

  const days    = Math.floor(diff / 86400000)
  const hours   = Math.floor((diff % 86400000) / 3600000)
  const minutes = Math.floor((diff % 3600000)  / 60000)
  const seconds = Math.floor((diff % 60000)    / 1000)

  countdown.value = {
    expired: false,
    units: [
      { label: 'Days',    value: String(days).padStart(2, '0') },
      { label: 'Hours',   value: String(hours).padStart(2, '0') },
      { label: 'Minutes', value: String(minutes).padStart(2, '0') },
      { label: 'Seconds', value: String(seconds).padStart(2, '0') },
    ],
  }
}

const formatPrice = (v) =>
  v != null ? new Intl.NumberFormat('nl-NL', { style: 'currency', currency: 'EUR' }).format(v) : '—'

const formatDate = (d) =>
  d ? new Date(d).toLocaleString('nl-NL', { dateStyle: 'medium', timeStyle: 'short' }) : '—'

async function fetchAuction() {
  loading.value = true
  error.value   = null
  try {
    const [ar, br] = await Promise.all([
      get(`/auctions/${route.params.id}`),
      get(`/auctions/${route.params.id}/bids`),
    ])
    const aBody = await ar.json()
    const bBody = await br.json()
    if (!ar.ok) throw new Error(aBody.error || 'Auction not found')
    auction.value = aBody
    bids.value    = bBody.data ?? bBody

    clearInterval(timerRef)
    if (auction.value.status === 'open') {
      updateCountdown()
      timerRef = setInterval(updateCountdown, 1000)
    }
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
    bidSuccess.value         = `Bid of ${formatPrice(bidAmount.value)} placed successfully!`
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
onUnmounted(() => clearInterval(timerRef))
</script>
