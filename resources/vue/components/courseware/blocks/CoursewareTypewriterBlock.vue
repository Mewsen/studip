<template>
    <div class="cw-block cw-block-typewriter">
        <courseware-default-block
            :block="block"
            :canEdit="canEdit"
            :isTeacher="isTeacher"
            :preview="true"
            @showEdit="initCurrentData"
            @storeEdit="storeText"
            @closeEdit="initCurrentData"
        >
            <template #content>
                <div class="cw-typewriter-content" :class="[currentFont, currentSize]">
                    <span class="typewriter-text">{{ typedText }}</span>
                </div>
            </template>
            <template v-if="canEdit" #edit>
                <form class="default" @submit.prevent="">
                    <label class="col-4">
                        {{ $gettext('Text') }}
                        <textarea v-model="currentText" />
                    </label>
                    <br>
                    <label class="col-1">
                        {{ $gettext('Geschwindigkeit') }}
                        <select v-model="currentSpeed" @change="restartTyping">
                            <option value="0">{{ $gettext('Langsam') }}</option>
                            <option value="1">{{ $gettext('Normal') }}</option>
                            <option value="2">{{ $gettext('Schnell') }}</option>
                            <option value="3">{{ $gettext('Sehr schnell') }}</option>
                        </select>
                    </label>
                    <label class="col-1">
                        {{ $gettext('Schriftart') }}
                        <select v-model="currentFont">
                            <option value="font-default">{{ $gettext('Standard') }}</option>
                            <option value="font-typewriter">Lucida Sans Typewriter</option>
                            <option value="font-trebuchet">Trebuchet MS</option>
                            <option value="font-tahoma">Tahoma</option>
                            <option value="font-georgia">Georgia</option>
                            <option value="font-narrow">Arial Narrow</option>
                        </select>
                    </label>
                    <label class="col-1">
                        {{ $gettext('Schriftgröße') }}
                        <select v-model="currentSize">
                            <option value="size-default">100%</option>
                            <option value="size-tall">125%</option>
                            <option value="size-grande">150%</option>
                            <option value="size-huge">200%</option>
                        </select>
                    </label>
                </form>
            </template>
            <template #info>
                <p>{{ $gettext('Informationen zum Schreibmaschinen-Block') }}</p>
            </template>
        </courseware-default-block>
    </div>
</template>

<script>
import BlockComponents from './block-components.js';
import blockMixin from '@/vue/mixins/courseware/block.js';
import { mapActions } from 'vuex';

export default {
    name: 'courseware-typewriter-block',
    mixins: [blockMixin],
    components: Object.assign(BlockComponents, {}),
    props: {
        block: Object,
        canEdit: Boolean,
        isTeacher: Boolean,
    },
    data() {
        return {
            speeds: [200, 100, 50, 25],
            typing: false,
            currentText: ' ',
            currentSpeed: '',
            currentFont: '',
            currentSize: '',
            typedText: '',
            currentIndex: 0,
        };
    },
    computed: {
        text() {
            return this.block?.attributes?.payload?.text;
        },
        speed() {
            return this.block?.attributes?.payload?.speed;
        },
        typeDelay() {
            return this.speeds[this.currentSpeed];
        },
        font() {
            return this.block?.attributes?.payload?.font;
        },
        size() {
            return this.block?.attributes?.payload?.size;
        }
    },
    watch: {
        currentText(newText) {
            this.startTyping(newText);
        }
    },
    mounted() {
        this.initCurrentData();
    },
    methods: {
        ...mapActions({
            updateBlock: 'updateBlockInContainer',
        }),
        initCurrentData() {
            this.currentText = this.text;
            this.currentSpeed = this.speed;
            this.currentFont = this.font;
            this.currentSize = this.size;
            this.startTyping(this.currentText);
        },
        startTyping(text) {
            this.currentIndex = 0;
            this.typedText = '';
            this.typingEffect(text);
        },
        typingEffect(text) {
            if (this.currentIndex < text.length) {
                this.typedText += text.charAt(this.currentIndex);
                this.currentIndex++;
                setTimeout(() => this.typingEffect(text), this.typeDelay);
            }
        },
        restartTyping() {
            let text = this.currentText;
            this.currentText = ' ';
            this.$nextTick(() => {
                this.currentText = text;
            });
        },
        storeText() {
            let attributes = {};
            attributes.payload = {};
            attributes.payload.text = this.currentText;
            attributes.payload.speed = this.currentSpeed;
            attributes.payload.font = this.currentFont;
            attributes.payload.size = this.currentSize;

            this.updateBlock({
                attributes: attributes,
                blockId: this.block.id,
                containerId: this.block.relationships.container.data.id,
            });
        }
    },
};
</script>

<style scoped lang="scss">
@import '../../../../assets/stylesheets/scss/courseware/blocks/typewriter';

</style>
