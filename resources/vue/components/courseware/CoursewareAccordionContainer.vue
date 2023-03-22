<template>
    <courseware-default-container
        :container="container"
        containerClass="cw-container-accordion"
        :canEdit="canEdit"
        :isTeacher="isTeacher"
        @showEdit="setShowEdit"
        @storeContainer="storeContainer"
        @closeEdit="initCurrentData"
        @sortBlocks="enableSort"
    >
        <template v-slot:containerContent>
            <template v-if="showEditMode && canEdit && !currentElementisLink">
                <span aria-live="assertive" class="assistive-text">{{ assistiveLive }}</span>
                <span id="operation" class="assistive-text">
                    {{$gettext('Drücken Sie die Leertaste, um neu anzuordnen.')}}
                </span>
            </template>
            <courseware-collapsible-box
                v-for="(section, index) in currentSections"
                :key="index"
                :title="section.name"
                :icon="section.icon"
                :open="index === 0"
            >
                <ul v-if="!showEditMode || currentElementisLink" class="cw-container-accordion-block-list">
                    <li v-for="block in section.blocks" :key="block.id" class="cw-block-item">
                        <component
                            :is="component(block)"
                            :block="block"
                            :canEdit="canEdit"
                            :isTeacher="isTeacher"
                        />
                    </li>
                    <li v-if="showEditMode && canAddElements">
                        <courseware-block-adder-area :container="container" :section="index" @updateContainerContent="updateContent"/>
                    </li>
                </ul>
                <template v-else>
                    <template v-if="!processing">
                        <draggable
                            v-if="canEdit"
                            class="cw-container-list-block-list cw-container-list-sort-mode"
                            :class="[section.blocks.length === 0 ? 'cw-container-list-sort-mode-empty' : '']"
                            tag="ol"
                            role="listbox"
                            v-model="section.blocks"
                            v-bind="dragOptions"
                            handle=".cw-sortable-handle"
                            group="blocks"
                            @start="isDragging = true"
                            @end="dropBlock"
                            :containerId="container.id"
                            :sectionId="index"
                        >
                            <li v-for="block in section.blocks" :key="block.id" class="cw-block-item cw-block-item-sortable">
                                <span
                                    :class="{ 'cw-sortable-handle-dragging': isDragging }"
                                    class="cw-sortable-handle"
                                    tabindex="0"
                                    role="option"
                                    aria-describedby="operation"
                                    :ref="'sortableHandle' + block.id"
                                    @keydown="keyHandler($event, block.id, index)"
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
                        <template v-if="canAddElements">
                            <courseware-block-adder-area :container="container" :section="index" @updateContainerContent="updateContent"/>
                        </template>
                    </template>
                    <div v-else class="progress-wrapper">
                        <studip-progress-indicator :description="$gettext('Vorgang wird bearbeitet...')" />
                    </div>
                </template>
            </courseware-collapsible-box>
            <div v-if="sortMode && canEdit">
                <button class="button accept" @click="storeSort"><translate>Sortierung speichern</translate></button>
                <button class="button cancel"  @click="resetSort"><translate>Sortieren abbrechen</translate></button>
            </div>
        </template>
        <template v-slot:containerEditDialog>
            <form class="default cw-container-dialog-edit-form" @submit.prevent="">
                <fieldset v-for="(section, index) in currentContainer.attributes.payload.sections.filter(section => !section.locked)" :key="index">
                    <label>
                        <translate>Title</translate>
                        <input type="text" v-model="section.name" />
                    </label>
                    <label>
                        <translate>Icon</translate>
                        <studip-select :options="icons" v-model="section.icon">
                            <template #open-indicator="selectAttributes">
                                <span v-bind="selectAttributes"><studip-icon shape="arr_1down" size="10"/></span>
                            </template>
                            <template #no-options="{ search, searching, loading }">
                                <translate>Es steht keine Auswahl zur Verfügung.</translate>
                            </template>
                            <template #selected-option="option">
                                <studip-icon :shape="option.label"/> <span class="vs__option-with-icon">{{option.label}}</span>
                            </template>
                            <template #option="option">
                                <studip-icon :shape="option.label"/> <span class="vs__option-with-icon">{{option.label}}</span>
                            </template>
                        </studip-select>
                    </label>
                    <label
                        class="cw-container-section-delete"
                        v-if="currentContainer.attributes.payload.sections.length > 1"
                    >
                    <button class="button trash" @click="deleteSection(index)"><translate>Fach löschen</translate></button>
                    </label>
                </fieldset>
            </form>
            <button class="button add" @click="addSection"><translate>Fach hinzufügen</translate></button>
        </template>
    </courseware-default-container>
</template>

<script>
import ContainerComponents from './container-components.js';
import containerMixin from '../../mixins/courseware/container.js';
import contentIcons from './content-icons.js';
import CoursewareCollapsibleBox from './CoursewareCollapsibleBox.vue';
import StudipIcon from './../StudipIcon.vue';

import { mapGetters, mapActions } from 'vuex';

export default {
    name: 'courseware-accordion-container',
    mixins: [containerMixin],
    components: Object.assign(ContainerComponents, {
        CoursewareCollapsibleBox,
        StudipIcon,
    }),
    props: {
        container: Object,
        canEdit: Boolean,
        isTeacher: Boolean,
        canAddElements: Boolean,
    },
    data() {
        return {
            showEdit: false,
            currentContainer: {},
            currentSections: [],
            unallocatedBlocks: [],
            sortMode: false,
            isDragging: false,
            dragOptions: {
                animation: 0,
                group: this.container.id,
                disabled: false,
                ghostClass: "block-ghost"
            },
        };
    },
    computed: {
        ...mapGetters({
            blockById: 'courseware-blocks/byId',
            viewMode: 'viewMode',
            currentElementisLink: 'currentElementisLink'
        }),
        blocks() {
            if (!this.container) {
                return [];
            }

            return this.container.relationships.blocks.data.map(({ id }) => this.blockById({ id })).filter((a) => a);
        },
        showEditMode() {
            return this.$store.getters.viewMode === 'edit';
        },
        icons() {
            return contentIcons;
        },
    },
    mounted() {
        this.initCurrentData();
    },
    methods: {
        ...mapActions({
            updateContainer: 'updateContainer',
            lockObject: 'lockObject',
            unlockObject: 'unlockObject',
        }),
        initCurrentData() {
            this.currentContainer = _.cloneDeep(this.container);

            let view = this;
            let sections = this.currentContainer.attributes.payload.sections;

            const unallocated = new Set(this.blocks.map(({ id }) => id));

            for (let section of sections) {
                section.locked = false;
                section.blocks = section.blocks.map((id) =>  view.blockById({id})).filter(Boolean);
                for (let sectionBlock of section.blocks) {
                    if (sectionBlock?.id && unallocated.has(sectionBlock.id)) {
                        unallocated.delete(sectionBlock.id);
                    }
                }
            }

            if (unallocated.size > 0) {
                this.unallocatedBlocks = [...unallocated].map((id) => view.blockById({ id }));
                sections.push({
                    blocks: this.unallocatedBlocks,
                    name: this.$gettext('nicht zugewiesene Inhalte'),
                    icon: 'decline',
                    locked: true
                });
            }

            this.currentSections = sections;
        },
        setShowEdit(state) {
            this.showEdit = state;
        },
        addSection() {
            this.currentContainer.attributes.payload.sections.push({ name: '', icon: '', blocks: [] });
        },
        deleteSection(index) {
            if (this.currentContainer.attributes.payload.sections.length === 1) {
                return;
            }
            if (this.currentContainer.attributes.payload.sections[index].blocks.length > 0) {
                if (index === 0) {
                    this.currentContainer.attributes.payload.sections[
                        index + 1
                    ].blocks = this.currentContainer.attributes.payload.sections[index + 1].blocks.concat(
                        this.currentContainer.attributes.payload.sections[index].blocks
                    );
                } else {
                    this.currentContainer.attributes.payload.sections[
                        index - 1
                    ].blocks = this.currentContainer.attributes.payload.sections[index - 1].blocks.concat(
                        this.currentContainer.attributes.payload.sections[index].blocks
                    );
                }
            }
            this.currentContainer.attributes.payload.sections.splice(index, 1);
        },
        async storeContainer() {
            this.currentContainer.attributes.payload.sections = this.currentContainer.attributes.payload.sections.filter(section => !section.locked);
            this.currentContainer.attributes.payload.sections.forEach(section => {
                section.blocks = section.blocks.map((block) => {return block.id;});
                delete section.locked;
            });
            await this.updateContainer({
                container: this.currentContainer,
                structuralElementId: this.currentContainer.relationships['structural-element'].data.id,
            });
            await this.unlockObject({ id: this.currentContainer.id, type: 'courseware-containers' });
            this.initCurrentData();
        },
        enableSort() {
            this.sortMode = true;
        },
        async storeSort() {
            this.sortMode = false;
            this.storeContainer();
        },
        async resetSort() {
            await this.unlockObject({ id: this.currentContainer.id, type: 'courseware-containers' });
            this.sortMode = false;
            this.initCurrentData();
        },
        component(block) {
            if (block.attributes) {
                return 'courseware-' + block.attributes["block-type"] + '-block';
            }
            return null;
        },
        updateContent(blockAdder) {
            if(blockAdder.hasOwnProperty('container') && blockAdder.container.id === this.container.id) {
                this.initCurrentData();
            }
        }
    },
    watch: {
        blocks(newBlocks, oldBlocks) {
            if (!this.showEdit && !this.checkSimpleArrayEquality(newBlocks, oldBlocks)) {
                this.$nextTick(() => {
                    setTimeout(() =>  this.initCurrentData(), 250);
                });
            }
        }
    }
};
</script>
