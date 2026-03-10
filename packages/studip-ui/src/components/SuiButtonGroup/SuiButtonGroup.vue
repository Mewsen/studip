<template>
  <div class="button-group">
    <component 
      v-for="(vnode, index) in validButtons" 
      :is="vnode" 
      :key="index" 
    />
  </div>
</template>

<script setup>
import { computed, useSlots } from 'vue';
import { useSlotFilter } from './../../composables/';

const slots = useSlots();
const { getValidChildren } = useSlotFilter(slots);

const validButtons = computed(() => getValidChildren('SuiButton'));
</script>

<style scoped>
.button-group {
  display: flex;
  gap: 1rem;
}
</style>