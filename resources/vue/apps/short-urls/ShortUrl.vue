<template>

    <form class="default" v-if="store.editShortUrl">
        <label>
            {{ $gettext('URL') }}
            <input type="text" readonly name="path" v-model="store.editShortUrl.attributes.path"/>
        </label>

        <label class="studiprequired">
            <span class="textlabel">{{ $gettext('Bezeichnung') }}</span>
            <span class="asterisk" :title="$gettext('Dies ist ein Pflichtfeld')" aria-hidden="true">*</span>

            <input
                type="text"
                name="alias"
                v-model="store.editShortUrl.attributes.alias"
                @input="validateAlias"
                maxlength="256"
            />
        </label>

        <footer data-dialog-button v-if="props.isNew">
            <button class="button accept" @click.prevent="save">
                {{ $gettext('Speichern') }}
            </button>
        </footer>
    </form>
</template>

<script setup>
import {onMounted, ref} from "vue";
import {$gettext} from '../../../assets/javascripts/lib/gettext';
import {useShortUrlsStore} from '../../store/pinia/shortUrlsStore';

const props = defineProps({
    path: {
        type: String,
        required: false
    },
    isNew: {
        type: Boolean,
        required: true
    },
})

const store = useShortUrlsStore();


function validateAlias() {
    const pattern = /[^a-zA-Z0-9-]/g;
    store.editShortUrl.attributes.alias = store.editShortUrl.attributes.alias.replace(pattern, '').slice(0, 256);
}

async function save() {
    await store.saveShortUrl();
}

onMounted(() => {
    if (props.isNew) {
        store.startEditShortUrl(null, props.path);
    }
});

</script>
