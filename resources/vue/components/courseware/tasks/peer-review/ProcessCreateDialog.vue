<template>
    <WizardDialog
        :title="$gettext('Peer-Review-Prozess anlegen')"
        :confirmText="$gettext('Anlegen')"
        :closeText="$gettext('Abbrechen')"
        @close="$emit('close')"
        @confirm="create"
        height="800"
        width="800"
        :lastRequiredSlotId="0"
        :requirements="requirements"
        :slots="wizardSlots"
    >
        <template v-slot:configuration>
            <ProcessCreateForm :configuration="configuration" @update="updateConfiguration" />
        </template>

        <template v-slot:assessment>
            <AssessmentTypeEditor :configuration="configuration" />
        </template>
    </WizardDialog>
</template>

<script>
import AssessmentTypeEditor from './AssessmentTypeEditor.vue';
import ProcessCreateForm from './ProcessCreateForm.vue';
import WizardDialog from '../../../StudipWizardDialog.vue';
import { defaultConfiguration, ProcessConfiguration } from './process-configuration';
import { $gettext, $gettextInterpolate } from '../../../../../assets/javascripts/lib/gettext';

const getSlots = () => {
    return [
        {
            id: 1,
            valid: true,
            name: 'configuration',
            title: $gettext('Konfiguration'),
            icon: 'courseware',
            description: $gettext(
                'Es gibt im Moment in diese Mannschaft, oh, einige Spieler vergessen ihnen Profi was sie sind. Ich lese nicht sehr viele Zeitungen, aber ich habe gehört viele Situationen. Erstens: wir haben nicht offensiv gespielt.'
            ),
        },
        {
            id: 2,
            valid: true,
            name: 'assessment',
            title: $gettext('Bewertungssystem'),
            icon: 'content2',
            description: $gettext(
                'Es gibt keine deutsche Mannschaft spielt offensiv und die Name offensiv wie Bayern. Letzte Spiel hatten wir in Platz drei Spitzen: Elber, Jancka und dann Zickler. Wir müssen nicht vergessen Zickler. Zickler ist eine Spitzen mehr, Mehmet eh mehr Basler.'
            ),
        },
    ];
};

export default {
    components: { AssessmentTypeEditor, ProcessCreateForm, WizardDialog },
    props: ['taskGroup'],
    data: () => ({
        changed: false,
        configuration: defaultConfiguration(),
        requirements: [],
        wizardSlots: getSlots(),
    }),
    methods: {
        create() {
            this.$emit('create', { ...this.configuration });
        },
        updateConfiguration(configuration) {
            this.changed = true;
            this.configuration = configuration;
        },
    },
};
</script>
