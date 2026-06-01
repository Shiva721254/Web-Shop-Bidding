<template>
  <article class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 overflow-hidden">
    <div class="p-6">
      <CategoryBadge :category="auction.category" class="mb-3" />

      <Heading :level="3" size="xl" class="mb-3">
        <a
          :href="`#/auctions/${auction.id}`"
          class="hover:text-blue-600 transition-colors"
          @click.prevent="$emit('click', auction.id)"
        >
          {{ auction.title }}
        </a>
      </Heading>

      <Text as="p" size="sm" color="muted" class="mb-4 line-clamp-3">
        {{ truncatedDescription }}
      </Text>

      <div class="mb-4">
        <Text as="p" size="xs" color="muted">Current bid</Text>
        <Text as="p" size="lg" weight="bold" color="primary">
          {{ formattedCurrentBid }}
        </Text>
      </div>

      <AuctionMeta :seller="auction.seller" :ends-at="auction.endsAt" />
    </div>
  </article>
</template>

<script setup>
import { computed } from "vue";
import Heading from "../../atoms/Heading/Heading.vue";
import Text from "../../atoms/Text/Text.vue";
import AuctionMeta from "../../molecules/AuctionMeta/AuctionMeta.vue";
import CategoryBadge from "../../molecules/CategoryBadge/CategoryBadge.vue";

const props = defineProps({
  auction: {
    type: Object,
    required: true,
    validator: (value) =>
      value.id &&
      value.title &&
      value.seller &&
      value.category &&
      value.endsAt &&
      value.description &&
      Number.isFinite(value.currentBid),
  },
});

defineEmits(["click"]);

const truncatedDescription = computed(() => {
  const maxLength = 150;
  return props.auction.description.length <= maxLength
    ? props.auction.description
    : `${props.auction.description.substring(0, maxLength)}...`;
});

const formattedCurrentBid = computed(() =>
  new Intl.NumberFormat("en-US", {
    style: "currency",
    currency: "EUR",
  }).format(props.auction.currentBid),
);
</script>

<style scoped>
.line-clamp-3 {
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
