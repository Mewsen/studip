<template>
    <div class="cw-welcome-screen">
        <courseware-companion-box :msgCompanion="this.$gettext('Es wurden bisher noch keine Inhalte eingepflegt.')">
            <template v-slot:companionActions>
                <button v-if="canEdit && noContainers" class="button" @click="addNewContainer">
                    {{ $gettext('Einen Abschnitt hinzufügen') }}
                </button>
            </template>
        </courseware-companion-box>
    </div>
</template>

<script>
import CoursewareCompanionBox from '../layouts/CoursewareCompanionBox.vue';
import containerMixin from '@/vue/mixins/courseware/container';

export default {
    name: 'courseware-empty-element-box',
    components: {
        CoursewareCompanionBox,
    },
    mixins: [containerMixin],
    props: {
        canEdit: Boolean,
        noContainers: Boolean,
    },
    methods: {
        addNewContainer() {
            this.addContainer({
                type: 'list',
                colspan: 'full',
                sections: {
                    firstSection: ''
                },
                newPosition: null
            });
        },
    },
};
</script>
