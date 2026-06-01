<template>
  <footer class="bg-gray-50 border-t border-gray-200 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="col-span-1 md:col-span-2">
          <Heading :level="3" size="lg" class="mb-4">Bid Shop</Heading>
          <Text as="p" size="sm" color="muted">
            Browse current auctions and discover your next purchase.
          </Text>
        </div>

        <div>
          <Heading :level="4" size="md" class="mb-4">Quick Links</Heading>
          <ul class="space-y-2">
            <li v-for="link in quickLinks" :key="link.name">
              <a :href="link.href" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">
                {{ link.name }}
              </a>
            </li>
          </ul>
        </div>

        <div>
          <Heading :level="4" size="md" class="mb-4">Legal</Heading>
          <ul class="space-y-2">
            <li v-for="link in legalLinks" :key="link.name">
              <a :href="link.href" class="text-gray-600 hover:text-blue-600 transition-colors text-sm">
                {{ link.name }}
              </a>
            </li>
          </ul>
        </div>
      </div>

      <div class="mt-8 pt-8 border-t border-gray-200">
        <Text as="p" size="sm" color="muted" class="text-center">
          &copy; {{ currentYear }} Bid Shop. All rights reserved.
        </Text>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { computed } from "vue";
import Heading from "../../atoms/Heading/Heading.vue";
import Text from "../../atoms/Text/Text.vue";

defineProps({
  quickLinks: {
    type: Array,
    default: () => [
      { name: "Home", href: "/" },
      { name: "Auctions", href: "/auctions" },
      { name: "Categories", href: "/categories" },
      { name: "About", href: "/about" },
    ],
  },
  legalLinks: {
    type: Array,
    default: () => [
      { name: "Privacy Policy", href: "/privacy" },
      { name: "Terms of Service", href: "/terms" },
      { name: "Cookie Policy", href: "/cookies" },
    ],
  },
});

const currentYear = computed(() => new Date().getFullYear());
</script>
