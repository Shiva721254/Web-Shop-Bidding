<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div v-if="loading" class="flex items-center justify-center py-24">
      <p class="text-gray-500 text-lg">Loading auctions...</p>
    </div>

    <div v-else-if="error" class="flex flex-col items-center justify-center py-24 gap-4">
      <p class="text-red-600 font-medium">{{ error }}</p>
      <button
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
        @click="fetchAuctions"
      >
        Try Again
      </button>
    </div>

    <AuctionArchive
      v-else
      :auctions="auctions"
      @auction-click="(id) => router.push({ name: 'AuctionDetail', params: { id } })"
    />
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import AuctionArchive from '../../templates/AuctionArchive/AuctionArchive.vue'
import { get } from '../../../utils/api.js'

const router = useRouter()
const auctions = ref([])
const loading  = ref(true)
const error    = ref(null)

const fetchAuctions = async () => {
  loading.value = true
  error.value   = null
  try {
    const res  = await get('/auctions')
    const body = await res.json()
    if (!res.ok) throw new Error(body.error || 'Failed to fetch auctions')
    auctions.value = body.data ?? body
  } catch (err) {
    error.value    = err.message
    auctions.value = []
  } finally {
    loading.value = false
  }
}

onMounted(fetchAuctions)
</script>
