<template>
    <li class="cw-activity-item">
        <img class="cw-activity-avatar" :src="avatar" width="48" height="48">
        <header class="cw-activity-title">
            <h3><a :href="linkUrl" :title="elementTitle">{{ headerTitle }}</a></h3>
            <p><a :href="userUrl">{{ username }}</a></p>
            <p><span v-html="content"></span></p>
        </header>
        <div class="cw-activity-buttons-wrapper">
        </div>



        <!-- <p v-if="item.username" class="cw-activity-item-user">
            <a :href="userUrl"><studip-icon role="inactive" shape="headache" />{{ username }}</a>
        </p>
        <p v-if="item.readableDate" class="cw-activity-item-date">
            <studip-icon role="inactive" shape="timetable" />{{ item.readableDate }}
        </p>
        <p class="cw-activity-item-element">
            <a :href="linkUrl" :title="elementTitle"><studip-icon role="inactive" shape="content2" />{{ unitTitle }} | {{ breadcrumb }}</a>
        </p>
        <p v-if="content" class="cw-activity-item-content">
            <studip-icon role="inactive" :shape="shape" /><span v-html="content"></span>
        </p> -->
    </li>
</template>

<script>
import StudipIcon from './../StudipIcon.vue';

import { mapGetters } from 'vuex';

export default {
    name: 'courseware-activity-item',
    components: {
        StudipIcon,
    },
    props: {
        item: Object,
    },
    computed: {
        ...mapGetters({
            context: 'context',
            getStructuralElementById: 'courseware-structural-elements/byId',
        }),
        content() {
            if (this.item.content == null || this.item.content == '') {
                return this.item.title;
            }

            return this.item.content;
        },
        user() {
            return this.item.user;
        },
        userUrl() {
            return STUDIP.URLHelper.base_url + 'dispatch.php/profile?username=' + this.username;
        },
        username() {
            return this.user.attributes['formatted-name'];
        },
        avatar() {
            return this.user.meta.avatar.small;
        },
        linkUrl() {
            return STUDIP.URLHelper.base_url + 'dispatch.php/course/courseware/courseware/' + this.item.unitId + '?cid=' + this.item.contextId + '#/structural_element/' + this.item.elementId;
        },
        // shape() {
        //     switch (this.item.type) {
        //         case 'interacted':
        //             return 'item';
        //         case 'answered':
        //             return 'support';
        //         case 'created':
        //             return 'add';
        //         case 'edited':
        //             return 'edit';
        //         default:
        //             return 'question-circle-full';
        //     }
        // },
        // breadcrumb() {
        //     let breadcrumb = this.element.attributes.title;
        //     let currentStructuralElement = this.element;
        //     let i = 1; //max breadcrumb navigation depth check
        //     while (currentStructuralElement.relationships.parent.data !== null) {
        //             let parentId = currentStructuralElement.relationships.parent.data.id;
        //             currentStructuralElement = this.getStructuralElementById({ id: parentId });
        //             if (currentStructuralElement === undefined) {
        //                 break;
        //             }
        //             if (++i <= 3) {
        //                 breadcrumb = currentStructuralElement.attributes.title + '/' + breadcrumb;
        //                 if (currentStructuralElement.relationships.parent.data !== null && i === 3) {
        //                     breadcrumb = '.../' + breadcrumb;
        //                 }
        //             }
        //         }

        //     return breadcrumb;
        // },
        element() {
            return this.getStructuralElementById({ id: this.item.elementId });
        },
        elementTitle() {
            return this.element?.attributes?.title ?? this.$gettext('unbekannt');
        },
        unitTitle() {
            if (this.item.unit) {
                return this.getStructuralElementById({id: this.item.unit.relationships['structural-element'].data.id }).attributes.title;
            }

            return '-';
        },
        headerTitle() {
            return this.unitTitle === this.elementTitle ? this.unitTitle : this.unitTitle  + ' | ' + this.elementTitle;
        }
    },
};
</script>
