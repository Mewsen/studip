<template>
    <div class="image-comparator" :style="{ height: imageHeight + 'px' }" ref="comparator">
        <div class="images">
            <!-- Erstes Bild, linke Seite -->
            <img
                :src="image1"
                class="image"
                :style="{ clipPath: 'inset(0 ' + (100 - sliderValue) + '% 0 0)' }"
                @load="setImageHeight"
            />

            <!-- Zweites Bild, rechte Seite -->
            <img :src="image2" class="image" :style="{ clipPath: 'inset(0 0 0 ' + sliderValue + '%)' }" />
        </div>

        <!-- Slider zur Steuerung des Vergleichs -->
        <div class="slider-container" @mousedown="startDragging" @touchstart="startDragging">
            <div class="thumb" :class="{ dragging: isDragging}" :style="{ left: `${sliderValue}%`, height: imageHeight + 'px' }">
                <StudipIcon class="arrow-left" shape="arr_1left" :size="32" role="info_alt" draggable="false" />
                <StudipIcon class="arrow-right" shape="arr_1right" :size="32" role="info_alt" draggable="false" />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ImageComparator',
    data() {
        return {
            sliderValue: 50, // Anfangswert für den Slider (zeigt 50% von beiden Bildern)
            imageHeight: 0, // Höhe der Komponente
            isDragging: false, // Dragging-Zustand
        };
    },
    props: {
        image1: {
            type: String,
            required: true,
        },
        image2: {
            type: String,
            required: true,
        },
    },
    computed: {},
    methods: {
        setImageHeight(event) {
            const img = event.target;
            const ratio = img.naturalHeight / img.naturalWidth;
            this.imageHeight = this.$refs.comparator.clientWidth * ratio;
        },
        startDragging(event) {
            this.isDragging = true;
            this.updateSliderValue(event);
            window.addEventListener('mousemove', this.updateSliderValue);
            window.addEventListener('touchmove', this.updateSliderValue);
            window.addEventListener('mouseup', this.stopDragging);
            window.addEventListener('touchend', this.stopDragging);
        },
        stopDragging() {
            this.isDragging = false;
            window.removeEventListener('mousemove', this.updateSliderValue);
            window.removeEventListener('touchmove', this.updateSliderValue);
            window.removeEventListener('mouseup', this.stopDragging);
            window.removeEventListener('touchend', this.stopDragging);
        },
        updateSliderValue(event) {
            if (!this.isDragging) return;

            const rect = this.$el.getBoundingClientRect();
            const x = event.clientX - rect.left; // Position innerhalb des Containers
            const width = rect.width;
            const newValue = Math.min(Math.max((x / width) * 100, 0), 100); // Wert zwischen 0 und 100
            this.sliderValue = newValue; // Sliderwert aktualisieren
        },
    },
};
</script>

<style lang="scss">
.image-comparator {
    position: relative;
    width: 100%;
    margin: 0 auto;
    overflow: hidden;

    img {
        -webkit-touch-callout: none; /* iOS Safari */
        -webkit-user-select: none; /* Safari */
        -khtml-user-select: none; /* Konqueror HTML */
        -moz-user-select: none; /* Old versions of Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
        user-select: none; /* Non-prefixed version, currently supported by Chrome, Edge, Opera and Firefox */
    }

    .images {
        position: relative;
        display: flex;
        width: 100%;
        height: 100%;
        overflow: hidden;

        .image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: auto;
            object-fit: cover;
        }
    }
}

.slider-container {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    transform: translateY(-50%);
    display: flex;
    align-items: center;

    .thumb {
        position: absolute;
        top: 0;
        width: 4px;
        background: rgba(0, 0, 0, 0.2);
        cursor: grab;

        &.dragging {
            cursor: grabbing;
        }

        .arrow-left,
        .arrow-right {
            position: absolute;
            top: 50%;
            background-color: rgba(0, 0, 0, 0.2);
            padding: 8px 0px;
        }

        .arrow-left {
            left: -32px;
            border-top-left-radius: 8px;
            border-bottom-left-radius: 8px;
        }

        .arrow-right {
            right: -32px;
            border-top-right-radius: 8px;
            border-bottom-right-radius: 8px;
        }
    }
}
</style>
