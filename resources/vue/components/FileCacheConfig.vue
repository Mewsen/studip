<template>
    <label class="col-4">
        <span class="required">
            {{ $gettext('Dateipfad') }}
        </span>
        <input required type="text" name="path" v-model="thePath">
    </label>
</template>

<script>
export default {
    name: 'FileCacheConfig',
    emits: ['is-valid'],
    props: {
        path: {
            type: String,
            default: ''
        }
    },
    data () {
        return {
            thePath: this.path
        };
    },
    watch: {
        thePath: {
            handler (current) {
                this.$emit('is-valid', current.trim().length > 0);
            },
            immediate: true
        }
    }
}
</script>
