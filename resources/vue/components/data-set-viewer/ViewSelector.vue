<template>
    <div class="view-selector-group" role="group" aria-label="Ansicht auswählen">
        <button
            v-for="viewType in views"
            :key="viewType"
            :class="{ active: viewType === activeView }"
            :aria-pressed="viewType === activeView"
            @click="selectView(viewType)"
        >
            {{ formatViewName(viewType) }}
        </button>
    </div>
</template>

<script setup>
const props = defineProps({
    views: {
        type: Array,
        required: true,
    },
    activeView: {
        type: String,
        required: true,
    },
});
const emit = defineEmits(['update:activeView']);
const selectView = (viewType) => {
    if (viewType !== props.activeView) {
        emit('update:activeView', viewType);
    }
};

const formatViewName = (name) => {
    const mapping = {
        table: 'Tabelle',
        card: 'Karten',
        list: 'Liste',
    };
    return mapping[name] || name.charAt(0).toUpperCase() + name.slice(1);
};
</script>
<style scoped>
.view-selector-group {
  display: inline-flex;
  border-radius: 4px;
  overflow: hidden;
}

.view-selector-group button {
  padding: 8px 15px;
  border: 1px solid #ccc;
  background-color: #f8f9fa;
  cursor: pointer;
  margin: 0; 
  border-left: none;
}

.view-selector-group button:first-child {
  border-left: 1px solid #ccc;
  border-top-left-radius: 4px;
  border-bottom-left-radius: 4px;
}

.view-selector-group button:last-child {
  border-top-right-radius: 4px;
  border-bottom-right-radius: 4px;
}

.view-selector-group button.active {
  background-color: #007bff;
  color: white;
  border-color: #007bff;
  z-index: 1; 
}

.view-selector-group button:hover:not(.active) {
  background-color: #e2e6ea;
}
</style>