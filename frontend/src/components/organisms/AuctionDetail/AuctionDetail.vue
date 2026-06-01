<template>
  <article class="max-w-4xl mx-auto">
    <header class="mb-6">
      <div class="mb-4">
        <CategoryBadge :category="auction.category" />
      </div>
      
      <Heading :level="1" size="3xl" class="mb-4">
        {{ auction.title }}
      </Heading>
      
      <AuctionMeta 
        :seller="auction.seller" 
        :endsAt="auction.endsAt" 
        class="mb-6"
      />
    </header>
    
    <div class="prose prose-lg max-w-none">
      <Text 
        as="div" 
        size="base" 
        color="default"
        class="whitespace-pre-line leading-relaxed"
      >
        {{ auction.description }}
      </Text>
    </div>
  </article>
</template>

<script setup>
import Heading from '../../atoms/Heading/Heading.vue';
import Text from '../../atoms/Text/Text.vue';
import AuctionMeta from '../../molecules/AuctionMeta/AuctionMeta.vue';
import CategoryBadge from '../../molecules/CategoryBadge/CategoryBadge.vue';

defineProps({
  auction: {
    type: Object,
    required: true,
    validator: (value) => {
      return value.id && value.title && value.seller && value.category && value.endsAt && value.description;
    },
  },
});
</script>

<style scoped>
.prose {
  color: #374151;
}

.prose p {
  margin-bottom: 1.25em;
}
</style>
