<template>
    <courseware-default-container
        :container="container"
        containerClass="cw-container-list"
        :canEdit="canEdit"
        :isTeacher="isTeacher"
        @storeContainer="storeContainer"
        @sortBlocks="enableSort"
    >
        <template v-slot:containerContent>
            <ul v-if="!showEditMode || currentElementisLink"  class="cw-container-list-block-list">
                <li v-for="block in blocks" :key="block.id" class="cw-block-item">
                    <component :is="component(block)" :block="block" :canEdit="canEdit" :isTeacher="isTeacher" />
                </li>
                <li v-if="showEditMode && canEdit && canAddElements"><courseware-block-adder-area :container="container" :section="0" /></li>
            </ul>
            <template v-else>
                <template v-if="!processing">
                    <span aria-live="assertive" class="assistive-text">{{ assistiveLive }}</span>
                    <span id="operation" class="assistive-text">
                        {{$gettext('Drücken Sie die Leertaste, um neu anzuordnen.')}}
                    </span>
                    <draggable
                        v-if="showEditMode && canEdit"
                        class="cw-container-list-block-list cw-container-list-sort-mode"
                        tag="ol"
                        role="listbox"
                        v-model="blockList"
                        v-bind="dragOptions"
                        handle=".cw-sortable-handle"
                        group="blocks"
                        @start="isDragging = true"
                        @end="dropBlock"
                        ref="sortables"
                        :containerId="container.id"
                        sectionId="0"
                    >
                        <li
                            v-for="block in blockList"
                            :key="block.id"
                            class="cw-block-item cw-block-item-sortable"
                        >
                            <span
                                :class="{ 'cw-sortable-handle-dragging': isDragging }"
                                class="cw-sortable-handle"
                                tabindex="0"
                                role="option"
                                aria-describedby="operation"
                                :ref="'sortableHandle' + block.id"
                                @keydown="keyHandler($event, block.id)"
                            ></span>
                            <component
                                :is="component(block)"
                                :block="block"
                                :canEdit="canEdit"
                                :isTeacher="isTeacher"
                                :class="{ 'cw-block-item-selected': keyboardSelected === block.id}"
                                :blockId="block.id"
                            />
                        </li>
                    </draggable>
                    <courseware-block-adder-area :container="container" :section="0" />
                </template>
                <div v-else class="progress-wrapper" :style="{ height: contentHeight + 'px' }">
                    <studip-progress-indicator :description="$gettext('Vorgang wird bearbeitet...')" />
                </div>
            </template>
        </template>
    </courseware-default-container>
</template>

<script>
import ContainerComponents from './container-components.js';
import containerMixin from '../../mixins/courseware/container.js';
import draggable from 'vuedraggable';
import { mapActions, mapGetters } from 'vuex';

export default {
    name: 'courseware-list-container',
    mixins: [containerMixin],
    components: Object.assign(ContainerComponents, {
        draggable
    }),
    props: {
        container: Object,
        canEdit: Boolean,
        isTeacher: Boolean,
        canAddElements: Boolean,
    },
    data() {
        return {
            sortMode: false,
            isDragging: false,
            dragOptions: {
                animation: 0,
                group: this.container.id,
                disabled: false,
                ghostClass: "block-ghost"
            },
            blockList: [],
        };
    },
    computed: {
        ...mapGetters({
            blockById: 'courseware-blocks/byId',
            containerById: 'courseware-containers/byId',
            viewMode: 'viewMode',
            currentElementisLink: 'currentElementisLink'
        }),
        blocks() {
            if (!this.container) {
                return [];
            }
            let containerBlocks = this.container.relationships.blocks.data.map(({ id }) => this.blockById({ id })).filter(Boolean);
            let unallocated = new Set(containerBlocks.map(({ id }) => id));
            let sortedBlocks = this.container.attributes.payload.sections[0].blocks.map((id) => this.blockById({ id })).filter(Boolean);
            sortedBlocks.forEach(({ id }) => unallocated.delete(id));
            let unallocatedBlocks = [...unallocated].map((id) => this.blockById({ id }));

            return sortedBlocks.concat(unallocatedBlocks);
        },
        showEditMode() {
            return this.$store.getters.viewMode === 'edit';
        },
    },
    methods: {
        ...mapActions({
            updateContainer: 'updateContainer',
            lockObject: 'lockObject',
            unlockObject: 'unlockObject',
        }),
        storeContainer(data) {
        },
        initCurrentData() {
            this.blockList = this.blocks;
        },
        enableSort() {
            this.initCurrentData();
            this.sortMode = true;
        },
        async storeSort() {
            this.sortMode = false;

            let currentContainer = this.container;
            currentContainer.attributes.payload.sections[0].blocks = this.blockList.map(block => {return block.id});
            await this.updateContainer({
                container: currentContainer,
                structuralElementId: currentContainer.relationships['structural-element'].data.id,
            });
            await this.unlockObject({ id: this.container.id, type: 'courseware-containers' });
            this.initCurrentData();
        },
        async resetSort() {
            await this.unlockObject({ id: this.container.id, type: 'courseware-containers' });
            this.sortMode = false;
            this.blockList = this.blocks;
        },
        component(block) {
            if (block.attributes["block-type"] !== undefined) {
                return 'courseware-' + block.attributes["block-type"] + '-block';
            }
            return null;
        },
    },
    mounted() {
        this.initCurrentData();
    },
};
</script>
