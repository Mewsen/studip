<template>
    <StudipDialog
        :title="$gettext('Peer-Review-Prozess anlegen')"
        :confirmText="$gettext('Anlegen')"
        :confirmDisabled="creating"
        :closeText="$gettext('Abbrechen')"
        @close="$emit('close')"
        @confirm="create"
        height="800"
        width="800"
    >
        <template #dialogContent>
            <div v-if="!creating" class="with-sidebar">
                <div>
                    <ul>
                        <li :class="{ active: selectedSlot === 'configuration' }">
                            <a href="#" @click.prevent="selectedSlot = 'configuration'">
                                {{ $gettext('Einstellungen') }}
                            </a>
                        </li>
                        <li :class="{ active: selectedSlot === 'assessment' }">
                            <a href="#" @click.prevent="selectedSlot = 'assessment'">
                                {{ $gettext('Bewertungssystem') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div v-if="selectedSlot === 'configuration'">
                    <ProcessCreateForm :configuration="configuration" @update="updateConfiguration" />
                </div>
                <div v-if="selectedSlot === 'assessment'">
                    <AssessmentTypeEditor :configuration="configuration" @update="updateConfiguration" />
                </div>
            </div>
            <div v-if="creating">
                <CompanionBox :msgCompanion="$gettext('Der Peer-Review-Prozess wird jetzt angelegt.')" />
            </div>
        </template>
    </StudipDialog>
</template>

<script>
import AssessmentTypeEditor from './AssessmentTypeEditor.vue';
import CompanionBox from '../../layouts/CoursewareCompanionBox.vue';
import ProcessCreateForm from './ProcessCreateForm.vue';
import StudipDialog from '../../../StudipDialog.vue';
import { defaultConfiguration, ProcessConfiguration } from './process-configuration';

export default {
    components: { AssessmentTypeEditor, CompanionBox, ProcessCreateForm, StudipDialog },
    props: ['taskGroup'],
    data: () => ({
        changed: false,
        configuration: defaultConfiguration(),
        creating: false,
        selectedSlot: 'configuration',
    }),
    methods: {
        create() {
            if (this.creating) {
                return;
            }
            this.creating = true;
            this.$emit('create', { ...this.configuration });
        },
        updateConfiguration(configuration) {
            this.changed = true;
            this.configuration = configuration;
        },
    },
};
</script>

<style scoped lang="scss">
.with-sidebar {
    display: flex;
    flex-wrap: wrap;
    gap: 1em;
}

.with-sidebar > :first-child {
    flex-grow: 1;
}

.with-sidebar > :last-child {
    flex-basis: 0;
    flex-grow: 999;
    min-inline-size: 50%;
}

.with-sidebar > :first-child {
    ul {
        list-style: none;
        padding: 0;
        width: 12em;

        > li:has(> a):not(:last-child) {
            border-bottom: solid thin var(--color--sidebar-divider);
        }

        > li {
            padding-block: 2px;
            padding-inline-start: 5px;

            a {
                display: block;
                line-height: 17px;
                padding-block: 4px;
                padding-inline: 0px;
                word-wrap: break-word;
            }

            &.active {
                background-color: var(--color--sidebar-active);
                border-left: solid 4px var(--color--sidebar-marker-active);
                margin-left: -4px;
                padding-left: 1px;

                a {
                    color: var(--black);
                    padding-left: 4px;
                }
            }
        }

        > li.active {
            background-color: var(--color--sidebar-active);
        }
    }
}
</style>
