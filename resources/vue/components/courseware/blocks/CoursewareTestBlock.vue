<template>
    <div class="cw-block cw-block-test">
        <courseware-default-block
            :block="block"
            :canEdit="canEdit"
            :isTeacher="isTeacher"
            :defaultGrade="false"
            @storeEdit="storeBlock"
            @closeEdit="initCurrentData"
        >
            <template #content>
                <div class="cw-block-title cw-exercise-header" v-if="assignment">
                    <template v-if="exercises.length > 1">
                        <button class="as-link" @click="prevExercise" :title="$gettext('Zurück')">
                            <studip-icon shape="arr_1left" size="20"/>
                        </button>
                        <span>
                            {{ $gettextInterpolate(
                                $gettext('%{title}, Aufgabe %{num} von %{length}'),
                                { title: assignment.title, num: exercise_pos + 1, length: exercises.length }
                            ) }}
                        </span>
                        <button class="as-link" @click="nextExercise" :title="$gettext('Weiter')">
                            <studip-icon shape="arr_1right" size="20"/>
                        </button>
                    </template>
                    <span v-else>
                        {{assignment.title}}
                    </span>
                </div>
                <template v-for="(exercise, index) in exercises" :key="exercise.id">
                    <div v-show="index === exercise_pos">
                        <form class="default" autocomplete="off" :exercise="exercise.id">
                            <fieldset class="cw-exercise-fieldset" v-html="exercise.template" ref="content">
                            </fieldset>
                            <footer v-show="exercise.item_count && (assignment.reset_allowed || !exercise.show_solution)">
                                <button
                                    v-show="!exercise.show_solution"
                                    class="button accept"
                                    @click.prevent="submitSolution"
                                >
                                    {{ $gettext('Speichern') }}
                                </button>
                                <button
                                    v-show="exercise.show_solution && assignment.reset_allowed"
                                    class="button reset"
                                    @click.prevent="resetDialogHandler"
                                >
                                    {{ $gettext('Lösung dieser Aufgabe löschen') }}
                                </button>
                                <a
                                    v-if="canEdit && $store.getters.viewMode === 'edit'"
                                    class="button"
                                    :href="vips_url('sheets/edit_assignment', { assignment_id: assignment.id })"
                                >
                                    {{ $gettext('Aufgabenblatt bearbeiten') }}
                                </a>
                            </footer>
                        </form>
                    </div>
                </template>
                <courseware-companion-box
                    :msgCompanion="errorMessage" mood="sad"
                    v-if="errorMessage !== null"
                />
            </template>
            <template v-if="canEdit" #edit>
                <form class="default" @submit.prevent="">
                    <label>
                        {{ $gettext('Aufgabenblatt') }}
                        <studip-select
                            :options="assignments"
                            label="title"
                            :reduce="assignment => assignment.id"
                            :clearable="false"
                            v-model="assignment_id"
                            class="cw-vs-select"
                        >
                            <template #open-indicator="{ attributes }">
                                <span v-bind="attributes"><studip-icon shape="arr_1down" :size="10"/></span>
                            </template>
                            <template #no-options="{}">
                                {{ $gettext('Es steht keine Auswahl zur Verfügung') }}
                            </template>
                            <template #selected-option="{title, icon, start, end}">
                                <studip-icon :shape="icon" role="info"/>
                                {{title}} ({{start}} - {{end}})
                            </template>
                            <template #option="{title, icon, start, end, block}">
                                <studip-icon :shape="icon" role="info"/>
                                {{ block ? block + ' / ' + title : title }}<br>
                                <small>{{start}} - {{end}}</small>
                            </template>
                        </studip-select>
                    </label>
                </form>
            </template>
        </courseware-default-block>
        <studip-dialog
            v-if="exerciseResetId"
            :title="$gettext('Bitte bestätigen Sie die Aktion')"
            :question="$gettext('Wollen Sie die Lösung dieser Aufgabe wirklich löschen?')"
            height="180"
            @confirm="resetSolution"
            @close="exerciseResetId = null"
        ></studip-dialog>
    </div>
</template>

<script>
import CoursewareDefaultBlock from './CoursewareDefaultBlock.vue';
import CoursewareCompanionBox from '../layouts/CoursewareCompanionBox.vue'

export default {
    name: 'courseware-test-block',
    components: { CoursewareDefaultBlock, CoursewareCompanionBox },
    props: {
        block: Object,
        canEdit: Boolean,
        isTeacher: Boolean
    },
    data() {
        return {
            assignments: [],
            assignment_id: '',
            assignment: null,
            errorMessage: null,
            exercises: [],
            exercise_pos: 0,
            exerciseResetId: null
        }
    },
    methods: {
        storeBlock() {
            const attributes = { payload: { assignment: this.assignment_id } };
            const container = this.$store.getters['courseware-containers/related']({
                parent: this.block,
                relationship: 'container',
            });

            return this.$store.dispatch('updateBlockInContainer', {
                attributes,
                blockId: this.block.id,
                containerId: container.id,
            });
        },
        initCurrentData() {
            this.assignment_id = this.block.attributes.payload.assignment;
            this.loadSelectedAssignment();
        },
        prevExercise() {
            if (this.exercise_pos === 0) {
                this.exercise_pos = this.exercises.length - 1;
            } else {
                this.exercise_pos = this.exercise_pos - 1;
            }
        },
        nextExercise() {
            if (this.exercise_pos === this.exercises.length - 1) {
                this.exercise_pos = 0;
            } else {
                this.exercise_pos = this.exercise_pos + 1;
            }
        },
        loadAssignments() {
            // axios is this.$store.getters.httpClient
            $.get(this.vips_url('api/assignments/' + this.$store.getters.context.id))
                .done(response => {
                    this.assignments = response;
                });
        },
        loadSelectedAssignment() {
            if (this.assignment_id === '') {
                this.errorMessage = this.$gettext('Es wurde noch kein Aufgabenblatt ausgewählt.');
                return;
            }

            this.assignment = null;
            this.errorMessage = null;
            this.exercises = [];
            $.get(this.vips_url('api/assignment/' + this.assignment_id))
                .done(response => {
                    this.assignment = response;
                    this.exercises = response.exercises;
                    this.$nextTick(() => {
                        this.loadMathjax();
                        STUDIP.Vips.vips_post_render(this.$refs.content);
                    });
                })
                .fail(xhr => {
                    this.errorMessage = xhr.responseJSON ? xhr.responseJSON.message : xhr.statusText;
                });
        },
        reloadExercise(exercise_id) {
            $.get(this.vips_url('api/exercise/' + this.assignment.id + '/' + exercise_id))
                .done(response => {
                    this.exercises[this.exercise_pos] = response;
                    this.$nextTick(() => {
                        this.loadMathjax();
                        STUDIP.Vips.vips_post_render(this.$refs.content);
                    });
                });
        },
        loadMathjax() {
            STUDIP.loadChunk('mathjax').then(({ Hub }) => {
                Hub.Queue(['Typeset', Hub, this.$refs.content]);
            });
        },
        vips_url(url, param_object) {
            return STUDIP.URLHelper.getURL('dispatch.php/vips/' + url, param_object);
        },
        submitSolution(event) {
            let exercise_id = event.currentTarget.form.getAttribute('exercise');
            let data = new FormData(event.currentTarget.form);
            data.set('assignment_id', this.assignment.id);
            data.set('block_id', this.block.id);

            $.ajax({
                type: 'POST',
                url: this.vips_url('api/solution/' + this.assignment.id + '/' + exercise_id),
                data: data,
                enctype: 'multipart/form-data',
                processData: false,
                contentType: false
            })
            .fail(xhr => {
                let info = xhr.responseJSON ? xhr.responseJSON.message : xhr.statusText;

                if (xhr.status === 422) {
                    info = this.$gettext('Ihre Lösung ist leer und wurde nicht gespeichert.');
                }
                this.$store.dispatch('companionError', { info: info });
            })
            .done(() => {
                this.$store.dispatch('companionSuccess', {
                    info: this.$gettext('Ihre Lösung zur Aufgabe wurde gespeichert.'),
                });
                this.reloadExercise(exercise_id);
            });
        },
        resetDialogHandler(event) {
            this.exerciseResetId = event.currentTarget.form.getAttribute('exercise');
        },
        resetSolution() {
            $.ajax({
                type: 'DELETE',
                url: this.vips_url('api/solution/' + this.assignment.id + '/' + this.exerciseResetId, { block_id: this.block.id })
            })
            .fail(xhr => {
                let info = xhr.responseJSON ? xhr.responseJSON.message : xhr.statusText;
                this.$store.dispatch('companionError', { info: info });
                this.exerciseResetId = null;
            })
            .done(() => {
                this.reloadExercise(this.exerciseResetId);
                this.exerciseResetId = null;
            });
        }
    },
    created() {
        this.initCurrentData();
        if (this.canEdit) {
            this.loadAssignments();
        }
    }
};
</script>
