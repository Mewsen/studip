<template>
    <div>
        <studip-message-box type="info" :hideClose="false">
            {{ $gettext('Die folgenden Daten können automatisch erfasst werden. Achtung: Diese Daten können zu einer Deanonymisierung der Teilnehmenden führen. Die Teilnehmenden sehen einen Hinweis, dass und welche Daten von ihnen erfasst werden.') }}
        </studip-message-box>
        <div class="automated-data-checkbox-list">
            <label>
                <input v-autofocus type="checkbox" v-model="val_clone.geschlecht" true-value="1" false-value="0">
                {{ $gettext('Geschlecht') }}
            </label>
            <label>
                <input type="checkbox" true-value="1" v-model="val_clone.studienfach" false-value="0">
                {{$gettext('Studienfach')}}
            </label>
            <label>
                <input type="checkbox" true-value="1" v-model="val_clone.studiengang" false-value="0">
                {{$gettext('Studiengang')}}
            </label>
            <label>
                <input type="checkbox" true-value="1" v-model="val_clone.studiengangfachsemester" false-value="0">
                {{$gettext('Studiengang-Fachsemester')}}
            </label>
            <label v-for="datafield in datafields" :key="datafield.datafield_id">
                <input type="checkbox" v-model="val_clone.datafields" :value="datafield.datafield_id">
                {{ datafield.name }}
            </label>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { QuestionnaireComponent } from '../../mixins/QuestionnaireComponent';

export default {
    name: 'automated-data-edit',
    mixins: [ QuestionnaireComponent ],
    data() {
        return {
            datafields: []
        }
    },
    created() {
        this.setDefaultValues({
            geschlecht: 0,
            studienfach: 0,
            studiengang: 0,
            studiengangfachsemester: 0,
            datafields: []
        });
        axios.get(STUDIP.URLHelper.getURL(`jsonapi.php/v1/datafields?filter[object_type]=user`)).then((response) => {
            for (let studiengang of response.data.data) {
                this.datafields.push({
                    datafield_id: studiengang.id,
                    name: studiengang.attributes.name
                });
            }
        });
    }
}
</script>
