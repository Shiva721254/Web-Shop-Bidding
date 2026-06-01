<template>
  <article
    class="card card-clickable flex flex-col"
    @click="$emit('click', auction.id)"
    role="button"
    :aria-label="`View auction: ${auction.title}`"
  >
    <div class="p-6 flex flex-col flex-1">
      <div class="flex items-start justify-between mb-3">
        <CategoryBadge :category="auction.category" />
        <span :class="['badge', auction.status === 'open' ? 'badge-green' : 'badge-gray']">
          {{ auction.status === 'open' ? 'Open' : 'Closed' }}
        </span>
      </div>

      <h3 class="text-lg font-bold text-gray-900 mb-2 hover:text-blue-600 transition-colors line-clamp-2">
        {{ auction.title }}
      </h3>

      <p class="text-sm text-gray-500 mb-4 flex-1 line-clamp-3">
        {{ auction.description }}
      </p>

      <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="bg-gray-50 rounded-lg p-3">
          <p class="text-xs text-gray-400 mb-0.5">Starting price</p>
          <p class="font-semibold text-gray-700 text-sm">{{ formatPrice(auction.startingPrice) }}</p>
        </div>
        <div class="bg-blue-50 rounded-lg p-3">
          <p class="text-xs text-blue-500 mb-0.5">Current bid</p>
          <p class="font-bold text-blue-700">{{ formatPrice(auction.currentBid ?? auction.startingPrice) }}</p>
        </div>
      </div>

      <div class="flex items-center justify-between pt-3 border-t border-gray-100 text-xs text-gray-400">
        <span>Ends {{ formatDate(auction.endsAt) }}</span>
        <span class="btn btn-primary text-xs py-1.5 px-3">View Auction →</span>
      </div>
    </div>
  </article>
</template>

<script setup>
import CategoryBadge from '../../molecules/CategoryBadge/CategoryBadge.vue'

defineProps({
  auction: { type: Object, required: true },
})

defineEmits(['click'])

const formatPrice = (val) =>
  val != null
    ? new Intl.NumberFormat('nl-NL', { style: 'currency', currency: 'EUR' }).format(val)
    : '—'

const formatDate = (d) =>
  d ? new Date(d).toLocaleDateString('nl-NL', { day: 'numeric', month: 'short' }) : '—'
</script>
