<script setup>
import {computed, ref} from "vue";
import StudipIcon from "./StudipIcon.vue";
import {$gettext} from "../../assets/javascripts/lib/gettext";

const props = defineProps({
    links: {
        type: Array,
        required: true
    }
});

const currentIndex = ref(0);

const currentLink = computed(() => props.links[currentIndex.value]);
</script>

<template>
    <div class="links-preview">
        <div
            v-if="links.length > 1"
            class="links-preview__controls"
            role="group"
            :aria-label="$gettext('Link-Vorschau Navigation')"
        >
            <button type="button" @click="--currentIndex" :disabled="currentIndex === 0" :title="$gettext('Vorherige Link-Vorschau')" :aria-label="$gettext('Vorherige Link-Vorschau')">
                <StudipIcon shape="arr_1left" :size="20" aria-hidden="true" />
            </button>
            <button type="button" @click="++currentIndex" :disabled="currentIndex === links.length - 1" :title="$gettext('Nächste Link-Vorschau')" :aria-label="$gettext('Nächste Link-Vorschau')">
                <StudipIcon shape="arr_1right" :size="20" aria-hidden="true" />
            </button>
        </div>
        <div class="links-preview__item" :aria-label="currentLink.title || $gettext('Link-Vorschau')">
            <a
                class="og-preview"
                :href="currentLink.url"
                target="_blank"
                :title="$gettext('Vorschau von %{title}', {title: currentLink.title})"
                :aria-label="$gettext('Vorschau von %{title}', {title: currentLink.title})"
            >
                <div class="og-preview__image-container" v-if="currentLink.image">
                    <img :src="currentLink.image" :alt="currentLink.title" />
                </div>
                <div class="og-preview__details">
                    <h4 class="og-preview__title">{{ currentLink.title }}</h4>
                    <p class="og-preview__description">{{ currentLink.description }}</p>
                </div>
            </a>
        </div>
    </div>
</template>
