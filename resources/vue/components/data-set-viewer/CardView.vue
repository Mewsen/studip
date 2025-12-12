<template>
    <div class="card-view-container">
        <div v-for="(item, index) in data" :key="index" class="data-card">
            <slot name="card-layout-item" :item="item">
                <div class="card-view-item">
                    <h4>{{ item[titleKey] || titleKey }}</h4>
                    <p v-for="(value, key) in item" :key="key" class="default-row">
                        <strong>{{ key.charAt(0).toUpperCase() + key.slice(1) }}:</strong> {{ value }}
                    </p>
                </div>
            </slot>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    data: { type: Array, required: true },
    titleKey: { type: String, required: true },
});
</script>
<style scoped>
/* Minimales CSS für einen Card-Look, wie er in Stud.IP passen könnte */
.card-view-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  padding: 20px 0;
}

.data-card {
  border: 1px solid #e0e0e0;
  border-radius: 6px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  transition: transform 0.2s, box-shadow 0.2s;
  background-color: #ffffff;
  overflow: hidden;
  display: flex; /* Stellt sicher, dass der Inhalt den Platz ausfüllt */
  flex-direction: column;
}

.data-card:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transform: translateY(-2px);
}

.card-fallback {
  padding: 15px;
}

.card-title {
  margin-top: 0;
  margin-bottom: 5px;
  color: #333;
  font-size: 1.2em;
}

.card-separator {
  border: 0;
  height: 1px;
  background: #eee;
  margin: 10px 0;
}

.default-row {
  font-size: 0.9em;
  line-height: 1.4;
  margin-bottom: 4px;
}

.default-row strong {
  color: #555;
}
</style>