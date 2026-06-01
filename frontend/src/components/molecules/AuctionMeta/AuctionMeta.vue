<template>
  <div class="flex items-center gap-2 text-sm text-gray-600">
    <Text as="span" size="sm" color="muted">Seller: {{ seller }}</Text>
    <span class="text-gray-400">|</span>
    <Text as="span" size="sm" color="muted">Ends:</Text>
    <DateDisplay :date="endsAt" format="short" />
  </div>
</template>

<script setup>
import Text from "../../atoms/Text/Text.vue";
import DateDisplay from "../../atoms/DateDisplay/DateDisplay.vue";

defineProps({
  seller: {
    type: String,
    required: true,
  },
  endsAt: {
    type: String,
    required: true,
  },
});
</script>
