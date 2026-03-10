<template>
    <button 
    class="sui-btn" 
    :class="variantClass"
    :disabled="disabled"
  >
  <SuiIcon 
      v-if="icon && !iconRight" 
      :name="icon" 
      aria-hidden="true"
      class="sui-btn__icon"
    />
    
    <span v-if="label" class="sui-button__label">
      {{ label }}
    </span>

    <SuiIcon 
      v-if="icon && iconRight" 
      :name="icon" 
      aria-hidden="true"
      class="sui-btn__icon"
    />
  </button>
</template>
<script setup>
import { computed } from 'vue';
import SuiIcon from './../SuiIcon/SuiIcon.vue';

const props = defineProps({
    label: { type: String, default: '' },
    icon: { type: String, default: null },
    iconRight: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    variant: { type: String, default: 'primary' }
});

const variantClass = computed(() => `sui-btn--${props.variant}`);
</script>