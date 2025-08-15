<template>
    <div ref="mount"></div>
</template>
<script setup>
import {nextTick, ref, watch} from "vue";
import {ready} from "@/assets/javascripts/lib/ready";

const props = defineProps({
    html: {
        type: String,
        required: true,
    },
});

const mount = ref(null);

let initedForVersion = -1;
let version = 0;

async function renderAndInit(currentVersion) {
    if (
        currentVersion !== version
        || !mount.value
    ) {
        return;
    }

    mount.value.innerHTML = props.html;

    if (initedForVersion === currentVersion) {
        return;
    }

    ready.trigger(mount.value);

    initedForVersion = currentVersion;
}

async function update() {
    version++;
    const current = version;

    await nextTick();

    mount.value.innerHTML = '';

    renderAndInit(current);
}

watch(() => props.html, update, {immediate: true});

</script>
