<template>
    <li class="cw-activity-item">
        <img class="cw-activity-avatar" :src="avatar" width="48" height="48" />
        <header class="cw-activity-title">
            <h3>
                <a :href="linkUrl" :title="elementTitle">{{ headerTitle }}</a>
            </h3>
            <p>
                <a :href="userUrl">{{ username }}</a>
            </p>
            <p><span v-html="content"></span></p>
        </header>
        <div class="cw-activity-buttons-wrapper"></div>
    </li>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
    name: 'courseware-activity-item',
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
            return (
                STUDIP.URLHelper.base_url +
                'dispatch.php/course/courseware/courseware/' +
                this.item.unitId +
                '?cid=' +
                this.item.contextId +
                '#/structural_element/' +
                this.item.elementId
            );
        },
        element() {
            return this.getStructuralElementById({ id: this.item.elementId });
        },
        elementTitle() {
            return this.element?.attributes?.title ?? this.$gettext('unbekannt');
        },
        unitTitle() {
            if (this.item.unit) {
                return this.getStructuralElementById({ id: this.item.unit.relationships['structural-element'].data.id })
                    .attributes.title;
            }

            return '-';
        },
        headerTitle() {
            return this.unitTitle === this.elementTitle ? this.unitTitle : this.unitTitle + ' | ' + this.elementTitle;
        },
    },
};
</script>
