<template>
    <form class="default">
        <section>
            <label>
                {{ $gettext('Nachricht bei fehlgeschlagener Anmeldung') }}
                <textarea name="message" rows="4" cols="50" v-model="messageText"></textarea>
            </label>
        </section>
        <validity-time></validity-time>
        <section>
            <label>
                <input type="radio" v-model="theMode" :value="0">
                {{ $gettext('Mitgliedschaft ist in mindestens einer dieser Veranstaltungen notwendig') }}
            </label>
            <label>
                <input type="radio" v-model="theMode" :value="1">
                {{ $gettext('Mitgliedschaft ist in keiner dieser Veranstaltungen erlaubt') }}
            </label>
        </section>
        <section>
            <label for="csearch">
                {{ $gettext('Veranstaltung(en)') }}
            </label>
            <quicksearch v-if="courseSearch !== null"
                         :searchtype="courseSearch"
                         name="course"
                         :key="NaN"
                         @input="addCourse"
                         id="csearch"
                         ref="courseSearch"></quicksearch>
            <ul v-if="courseList.length > 0">
                <li v-for="(course, index) in courseList" :key="index">
                    {{ course.name }}
                </li>
            </ul>
        </section>
    </form>
</template>

<script>
import {AdmissionRuleMixin} from '../../mixins/AdmissionRuleMixin';
import ValidityTime from './ValidityTime.vue';
import quicksearch from '../Quicksearch.vue';

export default {
    name: 'CourseMemberAdmission',
    components: { ValidityTime, quicksearch },
    mixins: [AdmissionRuleMixin],
    data() {
        return {
            messageText: this.message || (
                this.theMode === 0
                    ? this.$gettext('Sie sind nicht in einer der gewählten Veranstaltungen eingetragen.')
                    : this.$gettext('Sie sind bereits in einer der gewählten Veranstaltungen eingetragen.')
            ),
            theMode: 0,
            courseList: [],
            courseSearch: null
        }
    },
    computed: {
        payload() {
            return {
                type: 'CourseMemberAdmission',
                payload: {
                    modus: this.theMode,
                    courses: this.courseList,
                    message: this.messageText
                }
            }
        }
    },
    methods: {
        addCourse(returnValue, inputValue) {
            if (!this.courseList.some(i => i.id === returnValue)) {
                this.courseList.push({id: returnValue, name: inputValue});
            }
        },
        setRuleData(data) {
            this.courseSearch = data.attributes.payload.search;
            this.courseList = data.attributes.payload.courses;
            this.theMode = data.attributes.payload.modus;
        },
    },
    validate() {
        if (this.courseList.length === 0) {
            this.invalidData.push(this.$gettext('Bitte geben Sie mindestens eine Veranstaltung an.'));
        }

        return this.invalidData.length === 0;
    },
    mounted() {
        // Get a new rule instance so we can use quicksearch.
        if (!this.id || this.id === '') {
            STUDIP.jsonapi.withPromises().post('admission-rules/CourseMemberAdmission', {
                data: {
                    data: {
                        attributes: {
                            payload: {
                                mode: 0,
                                courses: [],
                                message: ''
                            }
                        }
                    }
                }
            }).then(response => {
                this.courseSearch = response.data.attributes.payload.search;
            });
        }
    },
}
</script>
