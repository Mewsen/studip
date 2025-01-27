<template>
    <studip-dialog
        :title="$gettext('Rechte und Sichtbarkeit')"
        :confirm-text="$gettext('Wechseln')"
        confirm-class="accept"
        :close-text="$gettext('Abbrechen')"
        close-class="cancel"
        :question="$gettext('Sie haben bereits die Rechte und Sichtbarkeit für einzelne Seiten eingestellt. Möchten Sie die Rechte für das gesamte Lernmaterial anpassen? Achtung: Die bereits an den einzelnen Seiten festgelegten Rechte werden überschrieben.')"
        height="260"
        @close="$emit('close')"
        @confirm="switchPermissionScope"
    >
    </studip-dialog>
</template>
<script>
import { mapActions } from 'vuex';

export default {
    name: 'courseware-unit-item-dialog-permission-scope',
    emits: ['close', 'switch'],
    props: {
        unit: {
            type: Object,
            required: true,
        },
    },
    methods: {
        ...mapActions({
            updateUnit: 'courseware-units/update',
            loadUnit: 'courseware-units/loadById',
        }),
        async switchPermissionScope() {
            const unit = {
                id: this.unit.id,
                type: 'courseware-units',
                attributes: {
                    'permission-scope': 'unit',
                },
            };
            await this.updateUnit(unit);
            this.$emit('switch');
        },
    }
};
</script>
