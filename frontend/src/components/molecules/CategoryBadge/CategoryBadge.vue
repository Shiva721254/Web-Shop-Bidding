<template>
  <Badge :variant="categoryVariant" size="sm">
    {{ category }}
  </Badge>
</template>

<script setup>
import { computed } from 'vue';
import Badge from '../../atoms/Badge/Badge.vue';

const props = defineProps({
  category: {
    type: String,
    required: true,
  },
});

const categoryVariant = computed(() => {
  const variantMap = {
    Electronics: 'primary',
    Sports: 'success',
    Home: 'warning',
    Fashion: 'secondary',
    Collectibles: 'default',
  };
  
  return variantMap[props.category] || 'default';
});
</script>
