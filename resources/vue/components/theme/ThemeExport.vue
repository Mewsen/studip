<template>
    <div class="theme-export">
      <button class="button" @click="exportTheme" :disabled="!theme">
        {{ $gettext('Exportieren') }}
      </button>
    </div>
  </template>
  
  <script setup>
  
  const props = defineProps({
    theme: {
      type: Object,
      required: true,
    },
  });
  
  const exportTheme = () => {
    const themeData = {
      name: props.theme.attributes.name,
      description: props.theme.attributes.description || '',
      author: props.theme.attributes.author || '',
      version: props.theme.attributes.version || '1.0',
      type: props.theme.attributes.type || 'light',
      values: props.theme.attributes.values || {},
      studip_min_version: props.theme.attributes.studip_min_version || '6.1',
      studip_max_version: props.theme.attributes.studip_max_version || '7.0',
    };
  
    const blob = new Blob([JSON.stringify(themeData, null, 2)], { type: 'application/json' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `${props.theme.attributes.name || 'theme'}.json`;
    link.click();
  };
  </script>
  
  <style scoped>
  .theme-export button {
    padding: 0.5rem 1rem;
    margin-top: 2rem;
  }
  </style>
  