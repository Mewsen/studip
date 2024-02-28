<template>
    <label id="stock-images-upload-box-drag-area">
        <div class="holder" :class="{dragging: dragging}">
            <div class="box-centered">
                <div class="icon-upload">
                    <studip-icon shape="upload" :size="100" alt="" :role="dragging ? 'info_alt' : 'clickable' "/>
                </div>
                <strong>{{ $gettext('Bild auswählen oder per Drag & Drop hierher ziehen') }}</strong>
                <div class="upload-button-holder">
                    <input type="file" name="file" tabindex="-1" accept="image/*" ref="upload"
                           @change="onUpload"
                           @dragenter="setDragging(true)"
                           @dragleave="setDragging(false)"
                    />
                </div>
            </div>
        </div>
    </label>
</template>

<script>
export default {
    data: () => ({
        dragging: false,
    }),
    methods: {
        onUpload() {
            const files = this.$refs.upload.files;
            const file = files[0];
            this.$emit('upload', { file });
        },
        setDragging(state) {
            this.dragging = state;
        },
    },
};
</script>

<style scoped lang="scss">
#stock-images-upload-box-drag-area {
    background-color: var(--content-color-20);
    height: 100%;
    margin: -15px;
    padding: 18px 15px 10px;
    text-align: center;
}
.holder {
    align-items: center;
    border-color: var(--content-color-60);
    border-radius: 0.5em;
    border-style: dashed;
    border-width: 1px;
    box-sizing: border-box;
    display: flex;
    height: 100%;
    justify-content: center;
    padding: 0;
    position: relative;

    &.dragging {
        background-color: var(--base-color);

        .icon-upload + strong {
            color: var(--white);
        }
    }
}

.box-centered {
    height: auto;
    width: 100%;
    max-height: 100%;
}

.icon-upload + strong {
    color: var(--base-color);
    font-size: 1.5em;
    line-height: 1.2;
    display: block;
    font-weight: 500;
    text-align: center;
    margin: 0 2em 14px;
}

.upload-button-holder input[type='file'] {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;

    opacity: 0;
    width: 100%;
    height: 100%;
    padding: 0;
}
</style>
