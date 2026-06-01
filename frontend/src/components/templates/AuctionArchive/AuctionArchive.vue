<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <Header :navigation-links="navigationLinks" />
    
    <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
      <div class="mb-8">
        <Heading :level="1" size="3xl" class="mb-2">
          Active Auctions
        </Heading>
        <Text as="p" size="lg" color="muted">
          Browse listings and place your next bid
        </Text>
      </div>
      
      
      <!-- Auction Grid -->
      <div v-if="auctions && auctions.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <AuctionCard
          v-for="auction in auctions"
          :key="auction.id"
          :auction="auction"
          @click="handleAuctionClick"
        />
      </div>
      
      <!-- Empty State -->
      <div v-else class="text-center py-12">
        <Text as="p" size="lg" color="muted">
          No auctions found.
        </Text>
      </div>
      
    </main>
    
    <Footer 
      :quick-links="footerQuickLinks"
      :legal-links="footerLegalLinks"
    />
  </div>
</template>

<script setup>
import Header from '../../organisms/Header/Header.vue';
import Footer from '../../organisms/Footer/Footer.vue';
import AuctionCard from '../../organisms/AuctionCard/AuctionCard.vue';
import Heading from '../../atoms/Heading/Heading.vue';
import Text from '../../atoms/Text/Text.vue';

const props = defineProps({
  auctions: {
    type: Array,
    default: () => [],
  },
  navigationLinks: {
    type: Array,
    default: () => [
      { name: 'Home', href: '/' },
      { name: 'Auctions', href: '/auctions' },
      { name: 'About', href: '/about' },
      { name: 'Contact', href: '/contact' },
    ],
  },
  footerQuickLinks: {
    type: Array,
    default: () => [
      { name: 'Home', href: '/' },
      { name: 'Auctions', href: '/auctions' },
      { name: 'Categories', href: '/categories' },
      { name: 'About', href: '/about' },
    ],
  },
  footerLegalLinks: {
    type: Array,
    default: () => [
      { name: 'Privacy Policy', href: '/privacy' },
      { name: 'Terms of Service', href: '/terms' },
      { name: 'Cookie Policy', href: '/cookies' },
    ],
  },
  showFilters: {
    type: Boolean,
    default: false,
  },
  showPagination: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['auction-click']);

const handleAuctionClick = (auctionId) => {
  emit('auction-click', auctionId);
};
</script>
