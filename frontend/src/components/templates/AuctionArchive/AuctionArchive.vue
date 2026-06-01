<template>
  <div class="page-container">
    <div class="mb-8">
      <h1 class="section-heading mb-2">Active Auctions</h1>
      <p class="text-gray-500 text-lg">Browse listings and place your next bid</p>
    </div>

    <div v-if="auctions && auctions.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <AuctionCard
        v-for="auction in auctions"
        :key="auction.id"
        :auction="auction"
        @click="$emit('auction-click', $event)"
      />
    </div>

    <div v-else class="text-center py-20">
      <p class="text-gray-400 text-lg">No auctions found.</p>
    </div>
  </div>
</template>

<script setup>
import AuctionCard from '../../organisms/AuctionCard/AuctionCard.vue'

defineProps({
  auctions: { type: Array, default: () => [] },
})

defineEmits(['auction-click'])
</script>
