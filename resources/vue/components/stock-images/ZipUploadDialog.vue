<template>
    <studip-dialog
        v-if="show"
        height="270"
        width="540"
        :title="$gettext('Bildersammlung importieren')"
        @close="onCancel"
        closeClass="cancel"
        :closeText="$gettext('Abbrechen')"
    >
        <template #dialogContent>
            <form id="stock-images-zip-upload-form" class="default" @submit.prevent="onSubmit">
                <label>
                    {{ $gettext('Bildersammlung') }}
                    <input ref="upload_zip" type="file" accept=".zip" name="zip" class="cw-file-input" @change="checkUploadFile">
                </label>
            </form>
        </template>
        <template #dialogButtons>
            <button form="stock-images-zip-upload-form" type="submit" class="button accept" :disabled="!hasFile">
                {{ $gettext('Importieren') }}
            </button>
        </template>
    </studip-dialog>
</template>

<script>
export default {
    name: 'ZipUploadDialog',
    emits: ['cancel', 'confirm'],
    props: {
        show: {
            type: Boolean,
            required: true,
        },
    },
    data() {
        return {
            file: null,
        };
    },
    computed: {
        hasFile() {
            return this.file !== null;
        },
    },
    methods: {
        onSubmit() {
            this.$emit('confirm', { file: this.file });
            this.file = null;
        },
        onCancel() {
            this.$emit('cancel');
        },
        checkUploadFile() {
            this.file = this.$refs?.upload_zip?.files[0];
        }
    },
};
</script>
<style lang="scss">
@import url('./../../../assets/stylesheets/scss/courseware/layouts/input-file.scss');

</style>
