<template>
  <div>
    <div v-if="loading" class="min-h-screen flex items-center justify-center">
      <p class="text-gray-600">Loading auctions...</p>
    </div>

    <div v-else-if="error" class="min-h-screen flex items-center justify-center">
      <div class="text-center max-w-md">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Error Loading Auctions</h2>
        <p class="text-gray-600 mb-4">{{ error }}</p>
        <button
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
          @click="fetchAuctions"
        >
          Try Again
        </button>
      </div>
    </div>

    <AuctionArchive
      v-else
      :auctions="auctions"
      @auction-click="handleAuctionClick"
    />
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import AuctionArchive from "../../templates/AuctionArchive/AuctionArchive.vue";
import { get } from "../../../utils/api.js";

const auctions = ref([]);
const loading = ref(true);
const error = ref(null);

const fetchAuctions = async () => {
  loading.value = true;
  error.value = null;

  try {
    const response = await get("/auctions");
    if (!response.ok) {
      throw new Error(`Failed to fetch auctions: ${response.status} ${response.statusText}`);
    }
    auctions.value = await response.json();
  } catch (err) {
    console.error("Error fetching auctions:", err);
    error.value = err.message || "Failed to load auctions. Please try again later.";
    auctions.value = [];
  } finally {
    loading.value = false;
  }
};

const handleAuctionClick = (auctionId) => {
  console.log("Auction clicked:", auctionId);
};

onMounted(fetchAuctions);
</script>
