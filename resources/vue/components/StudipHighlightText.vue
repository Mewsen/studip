<template>
  <span>
    <template v-for="(part, index) in computedParts" :key="index">
      {{ part }}<span v-if="searchTerm && index < computedParts.length - 1" class="filter-match">{{ searchTerm }}</span>
    </template>
  </span>
</template>

<script>
export default {
  props: {
    text: {
      type: String,
      default: ''
    },
    searchTerm: {
      type: String,
      default: ''
    }
  },
  computed: {
    computedParts() {
      if (!this.searchTerm) {
        return [this.text || ''];
      }

      console.log(this.text, (this.text || '').split(this.searchTerm));
      return (this.text || '').split(this.searchTerm);
    }
  }
};
</script>
