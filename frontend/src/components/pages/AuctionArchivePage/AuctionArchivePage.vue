<template>
  <div>
    <div class="gradient-brand text-white py-14 px-4">
      <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-4xl sm:text-5xl font-extrabold mb-4 text-balance">
          Find Your Next Great Deal
        </h1>
        <p class="text-blue-100 text-lg sm:text-xl max-w-2xl mx-auto">
          Browse live auctions and bid on unique items across electronics, sports, home goods, and more.
        </p>
      </div>
    </div>

    <div v-if="loading" class="flex items-center justify-center py-24">
      <p class="text-gray-400 text-lg">Loading auctions...</p>
    </div>

    <div v-else-if="error" class="flex flex-col items-center justify-center py-24 gap-4">
      <p class="text-red-600 font-medium">{{ error }}</p>
      <button class="btn btn-primary" @click="fetchAuctions">Try Again</button>
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

const router   = useRouter()
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
