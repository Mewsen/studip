<template>
    <div class="cw-block-comments-overview-wrapper">
        <ul>
            <li v-for="block in filteredBlocks" :key="block.id">
                <StudipIcon class="cw-comments-icon" :shape="block.attributes.icon" role="info" :size="32" />
                <a class="cw-comments-title" :href="block.elementURL">
                    <h3>{{ block.element.attributes.title }}</h3>
                    <p>{{ block.unitName }}</p>
                </a>
                <div class="cw-comments-buttons-wrapper">
                    <a href="#" @click.prevent="enableCommentsDialog(block)">{{block.comments.length}} <StudipIcon shape="chat" /></a>
                    <a v-if="block.feedbacks.length > 0" href="#" @click.prevent="enableFeedbackDialog(block)">{{block.feedbacks.length}} <StudipIcon shape="exclaim-circle" /></a>
                </div>
            </li>
        </ul>
        <courseware-comments-overview-dialog
            v-if="showCommentsDialog"
            item-type="block"
            com-type="comment"
            :item="currentDialogBlock"
            @close="closeCommentsDialog"
        />
        <courseware-comments-overview-dialog
            v-if="showFeedbackDialog"
            item-type="block"
            com-type="feedback"
            :item="currentDialogBlock"
            @close="closeFeedbackDialog"
        />
    </div>
</template>

<script>
import CoursewareCommentsOverviewDialog from '../CoursewareCommentsOverviewDialog.vue';
import commentsOverviewMixin from '@/vue/mixins/courseware/comments-overview-helper.js';

import { mapGetters } from 'vuex';
import StudipIcon from '../../StudipIcon.vue';

export default {
    name: 'courseware-block-comments-overview',
    components: {
        CoursewareCommentsOverviewDialog,
    },
    mixins: [commentsOverviewMixin],
    data() {
        return {
            blocksWithRelations: [],
            currentDialogBlock: null,
            showCommentsDialog: false,
            showFeedbackDialog: false,
            sortBy: 'units',
            sortASC: true,
        };
    },
    computed: {
        ...mapGetters({
            units: 'courseware-units/all',
            elements: 'courseware-structural-elements/all',
            containers: 'courseware-containers/all',
            blocks: 'courseware-blocks/all',
            blockComments: 'courseware-block-comments/all',
            blockFeedbacks: 'courseware-block-feedback/all',
            elementComments: 'courseware-structural-element-comments/all',
            elementFeedbacks: 'courseware-structural-element-feedback/all',
            containerById: 'courseware-containers/byId',
            elementById: 'courseware-structural-elements/byId',
            unitById: 'courseware-units/byId',
            context: 'context',
        }),

        filteredBlocks() {
            let filteredBlocks = this.blocksWithRelations;
            // if (this.unitFilter !== 'all') {
            //     filteredBlocks = filteredBlocks.filter(block => block.unit.id === this.unitFilter);
            // }
            // if (this.createdFilter !== 'all') {
            //     filteredBlocks = filteredBlocks.filter(block => block.comments[this.createdFilter] > 0);
            // }

            return this.sortBlocks(filteredBlocks);
        },
    },
    methods: {
        collectBlockRelations() {
            this.blocksWithRelations = _.cloneDeep(this.blocks);
            this.blocksWithRelations.forEach((block) => {
                block.container = this.containerById({ id: block.relationships.container.data.id });
                block.element = this.elementById({ id: block.container.relationships['structural-element'].data.id });
                block.unit = this.unitById({ id: block.element.relationships.unit.data.id });
                const unitRoot = this.elementById({ id: block.unit.relationships['structural-element'].data.id });
                block.unitName = unitRoot.attributes.title;
                block.elementURL = STUDIP.URLHelper.getURL(
                    `dispatch.php/course/courseware/courseware/${block.unit.id}?cid=${this.context.id}#/structural_element/${block.element.id}`
                );
                block.comments = this.blockComments.filter(
                    (comment) => comment.relationships.block.data.id === block.id
                );
                block.comments.oneDay = 0;
                block.comments.oneWeek = 0;
                block.comments.forEach((comment) => {
                    comment.created = this.calcCreated(comment.attributes.mkdate);
                    if (comment.created.oneDay) {
                        block.comments.oneDay++;
                    }
                    if (comment.created.oneWeek) {
                        block.comments.oneWeek++;
                    }
                });
                block.feedbacks = this.blockFeedbacks.filter(
                    (feedback) => feedback.relationships.block.data.id === block.id
                );
                block.feedbacks.forEach((feedback) => {
                    feedback.created = this.calcCreated(feedback.attributes.mkdate);
                });
            });
        },
        enableCommentsDialog(block) {
            this.currentDialogBlock = block;
            this.showCommentsDialog = true;
        },
        closeCommentsDialog() {
            this.collectBlockRelations();
            this.showCommentsDialog = false;
            this.currentDialogBlock = null;
        },
        enableFeedbackDialog(block) {
            this.currentDialogBlock = block;
            this.showFeedbackDialog = true;
        },
        closeFeedbackDialog() {
            this.collectBlockRelations();
            this.showFeedbackDialog = false;
            this.currentDialogBlock = null;
        },
        getMenuItems(block) {
            let menuItems = [];
            menuItems.push({
                id: 1,
                label: this.$gettext('Kommentare anzeigen'),
                icon: 'comment2',
                emit: 'showComments',
            });
            if (block.element.attributes['can-edit']) {
                menuItems.push({
                    id: 2,
                    label: this.$gettext('Anmerkungen anzeigen'),
                    icon: 'comment2',
                    emit: 'showFeedback',
                });
            }

            return menuItems;
        },
        sort(sortBy) {
            if (this.sortBy === sortBy) {
                this.sortASC = !this.sortASC;
            } else {
                this.sortBy = sortBy;
            }
        },
        getSortClass(col) {
            if (col === this.sortBy) {
                return this.sortASC ? 'sortasc' : 'sortdesc';
            }
        },
        sortBlocks(blocks) {
            switch (this.sortBy) {
                case 'units':
                    blocks = blocks.sort((a, b) => {
                        if (this.sortASC) {
                            return a.unitName < b.unitName ? -1 : 1;
                        } else {
                            return a.unitName > b.unitName ? -1 : 1;
                        }
                    });
                    break;
                case 'structural-elements':
                    blocks = blocks.sort((a, b) => {
                        if (this.sortASC) {
                            return a.element.attributes.title < b.element.attributes.title ? -1 : 1;
                        } else {
                            return a.element.attributes.title > b.element.attributes.title ? -1 : 1;
                        }
                    });
                    break;
                case 'blocks':
                    blocks = blocks.sort((a, b) => {
                        if (this.sortASC) {
                            return a.attributes.title < b.attributes.title ? -1 : 1;
                        } else {
                            return a.attributes.title > b.attributes.title ? -1 : 1;
                        }
                    });
                    break;
                case 'comments':
                    blocks = blocks.sort((a, b) => {
                        if (this.sortASC) {
                            return a.comments.length - b.comments.length;
                        } else {
                            return b.comments.length - a.comments.length;
                        }
                    });
                    break;
                case 'feedback':
                    blocks = blocks.sort((a, b) => {
                        if (this.sortASC) {
                            return a.feedbacks.length - b.feedbacks.length;
                        } else {
                            return b.feedbacks.length - a.feedbacks.length;
                        }
                    });
                    break;
            }

            return blocks;
        },
    },
    mounted() {
        this.$nextTick(() => {
            this.collectBlockRelations();
        });
    },
};
</script>
