<template>
    <StudipDialog
        :title="title"
        :confirmText="$gettext('Speichern')"
        confirmClass="accept"
        :confirmDisabled="!changed"
        :closeText="$gettext('Schließen')"
        closeClass="cancel"
        height="600"
        width="800"
        @close="$emit('close')"
        @confirm="confirm"
    >
        <template #dialogContent>
            <ProcessCreateForm :configuration="process.attributes.configuration" custom @update="updateConfiguration" />
        </template>
    </StudipDialog>
</template>

<script lang="ts">
import Vue from 'vue';
import { mapGetters } from 'vuex';
import { $gettext, $gettextInterpolate } from '../../../../../assets/javascripts/lib/gettext';
import StudipDialog from '../../../StudipDialog.vue';
import ProcessCreateForm from './ProcessCreateForm.vue';
import { defaultConfiguration, ProcessConfiguration } from './process-configuration';

export default Vue.extend({
    components: { ProcessCreateForm, StudipDialog },
    props: ['process'],
    data: () => ({
        changed: false,
        configuration: defaultConfiguration(),
    }),
    computed: {
        ...mapGetters({
            relatedTaskGroups: 'courseware-task-groups/related',
        }),
        title() {
            const taskGroup = this.relatedTaskGroups({ parent: this.process, relationship: 'task-group' });
            return $gettextInterpolate($gettext('Peer-Review-Prozess konfigurieren zur Aufgabe "%{title}"'), {
                title: taskGroup.attributes.title,
            });
        },
    },
    methods: {
        confirm() {
            this.$emit('update', {
                process: this.process,
                configuration: { ...this.configuration },
            });
        },
        updateConfiguration(configuration: ProcessConfiguration) {
            this.changed = true;
            this.configuration = configuration;
        },
    },
});
</script>

<style scoped>
header {
    margin-block-end: 2rem;
}
</style>
