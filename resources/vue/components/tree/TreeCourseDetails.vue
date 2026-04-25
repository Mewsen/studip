<template>
    <div v-if="details" class="course-details">
        <div class="semester">
            ({{ details.semester }})
        </div>
        <div class="admission-state" v-if="details.admissionstate">
            <studip-icon :shape="details.admissionstate.icon"
                         :role="details.admissionstate.role"
                         :alt="details.admissionstate.info"></studip-icon>
        </div>
        <div class="course-lecturers">
            <span v-for="(lecturer, index) in details.lecturers" :key="index">
                <a :href="profileUrl(lecturer.username)"
                   :title="$gettext(
                       'Zum Profil von %{ user }',
                       { user: lecturer.name },
                       true
                   )"
                   tabindex="0">
                    {{ lecturer.name }}
                </a><template v-if="details.lecturers.length > 1 && index < details.lecturers.length - 1">, </template>
            </span>
        </div>
        <Teleport :to="'#course-dates-' + course">
            <div v-html="details.dates" v-collapsible-list></div>
        </Teleport>
    </div>
</template>

<script>
import { TreeMixin } from '@/vue/mixins/TreeMixin';
import CollapsibleListDirective from '@/vue/directives/collapsible-list';

export default {
    name: 'TreeCourseDetails',
    mixins: [ TreeMixin ],
    directives: {
        'collapsible-list': CollapsibleListDirective
    },
    props: {
        course: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            details: null
        }
    },
    async mounted() {
        this.details = await STUDIP.jsonapi.withPromises().GET(`tree-node/course/details/${this.course}`);
    }
}
</script>
