<template>
    <form v-if="!working" class="default">
        <section v-for="(type, index) in ruleTypes" :key="index">
            <admission-rule-config :type="type"
                                   :use-dialog="false"
                                   @submit="ruleData"></admission-rule-config>
        </section>
        <section>
            <label class="caption">
                {{ $gettext("Name für diese Anmelderegel") }}
                <input type="text" name="instant_course_set_name" size="70" v-model="name">
            </label>
        </section>
        <footer data-dialog-button>
            <button class="button accept" @click.prevent="triggerRules">
                {{ $gettext('Speichern') }}
            </button>
            <button class="button cancel" data-dialog-close>
                {{ $gettext('Abbrechen') }}
            </button>
        </footer>
    </form>
</template>

<script>
import AdmissionRuleConfig from '@/vue/components/admission/AdmissionRuleConfig';

export default {
    name: 'InstantCourseSet',
    components: { AdmissionRuleConfig },
    props: {
        ruleTypes: {
            type: Array,
            required: true
        },
        courseSetName: {
            type: String,
            required: true
        },
        courseId: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            name: this.courseSetName,
            rules: [],
            working: false
        }
    },
    methods: {
        ruleData(data) {
            this.working = true;
            this.rules.push(data);

            // Check if all rultTypes have some data. If yes, the whole courseset can be stored.
            let canStore = true;
            for (let i = 0 ; i < this.ruleTypes.length ; i++) {
                if (this.rules.filter(rule => rule.type === this.ruleTypes[i]).length === 0) {
                    canStore = false;
                }
            }

            if (canStore) {
                this.store();
            }
        },
        triggerRules() {
            STUDIP.eventBus.emit('getRuleConfiguration');
        },
        store() {
            const data = {
                data: {
                    attributes: {
                        name: this.name,
                        private: true,
                        infotext: '',
                        institutes: [],
                        courses: [ this.courseId ],
                        rules: this.rules.map((rule) => { return { attributes: rule } } ),
                        userlists: []
                    }
                }
            };

            STUDIP.jsonapi.withPromises().post(
                'course-sets',
                { data: data }
            ).then(() => {
                STUDIP.Report.success(this.$gettext('Die Zugangsberechtigungen wurden gespeichert.'));
                window.location = STUDIP.URLHelper.getURL('dispatch.php/course/admission', {cid: this.courseId});
            });
        }
    },
    created() {
        for (let i = 0 ; i < this.ruleTypes.length ; i++) {
            this.rules[this.ruleTypes[i]] = {};
        }
    }
}
</script>
