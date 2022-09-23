<template>
    <courseware-default-container
        :container="container"
        containerClass="cw-container-list"
        :canEdit="canEdit"
        :isTeacher="isTeacher"
        @storeContainer="storeContainer"
    >
        <template v-slot:containerContent>
            <ul class="cw-container-list-block-list">
                <li v-for="block in blocks" :key="block.id" class="cw-block-item">
                    <component :is="component(block)" :block="block" :canEdit="canEdit" :isTeacher="isTeacher" />
                </li>
                <li v-if="showEditMode && canEdit"><courseware-block-adder-area :container="container" :section="0" /></li>
            </ul>
            <draggable
                v-if="sortMode && canEdit"
                class="cw-container-list-block-list cw-container-list-sort-mode"
                tag="ul"
                v-model="blockList"
                v-bind="dragOptions"
                handle=".cw-sortable-handle"
                @start="isDragging = true"
                @end="isDragging = false"
            >
                <transition-group type="transition" name="flip-blocks">
                    <li v-for="block in blockList" :key="block.id" class="cw-block-item cw-block-item-sortable">
                        <component :is="component(block)" :block="block" :canEdit="canEdit" :isTeacher="isTeacher" />
                    </li>
                </transition-group>

            </draggable>
            <div v-if="sortMode && canEdit">
                <button class="button accept" @click="storeSort"><translate>Sortierung speichern</translate></button>
                <button class="button cancel"  @click="resetSort"><translate>Sortieren abbrechen</translate></button>
            </div>

        </template>
    </courseware-default-container>
</template>

<script>
import ContainerComponents from './container-components.js';
import containerMixin from '../../mixins/courseware/container.js';
import { mapGetters } from 'vuex';

export default {
    name: 'courseware-list-container',
    mixins: [containerMixin],
    components: ContainerComponents,
    props: {
        container: Object,
        canEdit: Boolean,
        isTeacher: Boolean,
    },
    data() {
        return {};
    },
    computed: {
        ...mapGetters({
            blockById: 'courseware-blocks/byId',
        }),
        blocks() {
            if (!this.container) {
                return [];
            }

            return this.container.relationships.blocks.data.map(({ id }) => this.blockById({ id })).filter(Boolean);
        },
        showEditMode() {
            return this.$store.getters.viewMode === 'edit';
        },
    },
    methods: {
        storeContainer(data) {
        },
        component(block) {
            if (block.attributes["block-type"] !== undefined) {
                return 'courseware-' + block.attributes["block-type"] + '-block';
            }
            return null;
        },
    },
    mounted() {},
};
</script>
