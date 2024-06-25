<template>
    <form :action="storeUrl" method="post" class="default" :data-dialog="asDialog ? '' : null" @submit="validateInputs">
        <input type="hidden" :name="csrf.name" :value="csrf.value">
        <input v-for="id in responsibleGroups" type="hidden" name="responsibilities[statusgroup][]" :value="id" :key="`group-${id}`">
        <input v-for="id in responsibleInstitutes" type="hidden" name="responsibilities[institute][]" :value="id" :key="`institute-${id}`">
        <input v-for="id in responsibleUsers" type="hidden" name="responsibilities[user][]" :value="id" :key="`user-${id}`">

        <StudipMessageBox type="info" v-if="errors.length > 0">
            {{ $gettext('Folgende Angaben müssen korrigiert werden, um das Formular abschicken zu können:') }}

            <template #details>
                <ul>
                    <li v-for="(error, index) in errors" :key="`error-${index}`">
                        {{ error }}
                    </li>
                </ul>
            </template>
        </StudipMessageBox>

        <fieldset>
            <legend>{{ $gettext('Ort und Zeit') }}</legend>

            <label>
                <span class="required">{{ $gettext('Ort') }}</span>

                <input required type="text" name="room"
                       v-model="room"
                       :placeholder="$gettext('Ort')">
            </label>

            <label :class="{'col-3': !isSingleDay}">
                <span class="required">{{ $gettext('Intervall') }}</span>
                <select required name="interval" v-model.number="interval">
                    <option v-for="(label, value) in intervals" :key="value" :value="value">
                        {{ label }}
                    </option>
                </select>
            </label>

            <label class="col-3" v-if="!isSingleDay">
                <span class="required">{{ $gettext('Am Wochentag') }}</span>

                <select required name="day-of-week" @change="evt => dayOfWeek = parseInt(evt.target.value, 10)">
                    <option v-for="dow in daysOfTheWeek" :value="dow.key" :key="dow.key" :selected="dayOfWeek === dow.key">
                        {{ dow.label }}
                    </option>
                </select>
            </label>

            <label :class="{'col-3': !isSingleDay}">
                <span class="required">{{ isSingleDay ? $gettext('Datum') : $gettext('Beginn') }}</span>

                <Datepicker v-model="startDate"
                            name="start-date"
                            :disable-holidays="true"
                            :placeholder="$gettext('tt.mm.jjjj')"
                            mindate="today"
                            :emit-date="true"
                ></Datepicker>
            </label>

            <label class="col-3" v-if="!isSingleDay">
                <span class="required">{{ $gettext('Ende') }}</span>

                <Datepicker v-model="endDate"
                            name="end-date"
                            :disable-holidays="true"
                            :placeholder="$gettext('tt.mm.jjjj')"
                            :mindate="startDate"
                            :emit-date="true"
                ></Datepicker>
            </label>

            <label for="start-time" class="col-3">
                <span class="required">{{ $gettext('Von') }}</span>

                <Timepicker name="start-time"
                            v-model="startTime"
                            :maxtime="endTime"
                ></Timepicker>
            </label>

            <label for="ende_hour" class="col-3">
                <span class="required">{{ $gettext('Bis') }}</span>

                <Timepicker name="end-time"
                            v-model="endTime"
                            :mintime="startTime"
                ></Timepicker>
            </label>

            <label class="col-3">
                <span class="required">{{ $gettext('Dauer eines Termins in Minuten') }}</span>
                <input required type="number" name="duration" min="1"
                       v-model="duration">
            </label>

            <label class="col-3">
                {{ $gettext('Maximale Teilnehmerzahl') }}
                <StudipTooltipIcon :text="$gettext('Falls Sie mehrere Personen zulassen wollen (wie z.B. zu einer Klausureinsicht), so geben Sie hier die maximale Anzahl an Personen an, die sich anmelden dürfen.')"></StudipTooltipIcon>
                <input required type="text" name="size" id="size" min="1" max="50"
                       v-model="size">
            </label>

            <label>
                <input type="checkbox" name="pause" value="1"
                       v-model="pause">
                {{ $gettext('Pausen zwischen den Terminen einfügen?') }}
            </label>

            <label class="col-3" v-if="pause">
                {{ $gettext('Eine Pause nach wie vielen Minuten einfügen?') }}
                <input type="number" name="pause_time" min="1"
                       v-model="pauseTime">
            </label>

            <label class="col-3" v-if="pause">
                {{ $gettext('Dauer der Pause in Minuten') }}
                <input type="number" name="pause_duration" min="1"
                       v-model="pauseDuration">
            </label>

            <label>
                <input type="checkbox" name="lock" value="1"
                       v-model="lock">
                {{ $gettext('Termine für Buchungen sperren?') }}
            </label>

            <label v-if="lock">
                {{ $gettext('Wieviele Stunden vor Beginn des Blocks sollen die Termine für Buchungen gesperrt werden?') }}
                <input type="number" name="lock_time" min="1"
                       v-model="lockTime">
            </label>

            <label>
                <input type="checkbox" name="consecutive" value="1"
                       v-model="consecutive">
                {{ $gettext('Termine innerhalb der Blöcke nur fortlaufend vergeben') }}
            </label>

            <slot name="extension-point-1"></slot>
        </fieldset>

        <fieldset v-if="withResponsible">
            <legend>{{ $gettext('Durchführende Personen, Gruppen oder Einrichtungen') }}</legend>

            <template v-if="isInstitute">
                <p>
                    {{ $gettext('Bei Einrichtungen muss mindestens eine durchführende Person, Gruppe oder Einrichtung zugewiesen werden.') }}
                </p>
                <p>
                    {{ $gettext('Bitte beachten Sie, dass bei Zuweisungen von Statusgruppen alle Personen der Gruppe mit dem Status '
                        + '"tutor" und "dozent" als durchführende Personen zugewiesen werden und über alle Buchungen '
                        + 'informiert werden.') }}
                    {{ $gettext('Gleiches gilt für eine zugewiesene Einrichtung. Bitte achten Sie darauf, dass Sie Ihre hier '
                        + ' getroffene Auswahl in Absprache tätigen.') }}
                </p>
            </template>

            <label v-if="withResponsible.users">
                {{ $gettext('Durchführende Personen') }}
                <StudipSelect v-model="responsibleUsers"
                              :options="withResponsible.users"
                              :reduce="option => option.id"
                              multiple
                              :clearable="true"
                >
                    <template #open-indicator>
                        <span><studip-icon shape="arr_1down" :size="10" /></span>
                    </template>
                </StudipSelect>
            </label>

            <label v-if="withResponsible.groups">
                {{ $gettext('Durchführende Gruppen') }}
                <StudipSelect v-model="responsibleGroups"
                              :options="withResponsible.groups"
                              :reduce="option => option.id"
                              multiple
                              :clearable="true"
                >
                    <template #open-indicator>
                        <span><studip-icon shape="arr_1down" :size="10" /></span>
                    </template>
                </StudipSelect>
            </label>

            <label v-if="withResponsible.institutes">
                {{ $gettext('Durchführende Einrichtungen') }}
                <StudipSelect v-model="responsibleInstitutes"
                              :options="withResponsible.institutes"
                              :reduce="option => option.id"
                              multiple
                              :clearable="true"
                >
                    <template #open-indicator>
                        <span><studip-icon shape="arr_1down" :size="10" /></span>
                    </template>
                </StudipSelect>
            </label>
        </fieldset>

        <fieldset>
            <legend>{{ $gettext('Weitere Einstellungen') }}</legend>

            <label>
                {{ $gettext('Information zu den Terminen in diesem Block') }}
                <textarea name="note" v-model="note"></textarea>
            </label>

            <label>
                <input type="checkbox" name="calender-events" value="1"
                       v-model="calendarEvents">
                {{ $gettext('Die freien Termine auch im Kalender markieren') }}
            </label>

            <label v-if="isCourse">
                <input type="checkbox" name="mail-to-tutors" value="1"
                       v-model="mailToTutors">
                {{ $gettext('Tutor/innen beim Versand von Buchungsbenachrichtigungen berücksichtigen?') }}
            </label>

            <label>
                <input type="checkbox" name="show-participants" value="1"
                       v-model="showParticipants">
                {{ $gettext('Namen der buchenden Personen sind öffentlich sichtbar') }}
            </label>

            <label>{{ $gettext('Grund der Buchung abfragen') }}</label>
            <div class="hgroup">
                <label>
                    <input type="radio" name="require-reason" value="yes"
                           v-model="requireReason">
                    {{ $gettext('Ja, zwingend erforderlich') }}
                </label>

                <label>
                    <input type="radio" name="require-reason" value="optional"
                           v-model="requireReason">
                    {{ $gettext('Ja, optional') }}
                </label>

                <label>
                    <input type="radio" name="require-reason" value="no"
                           v-model="requireReason">
                    {{ $gettext('Nein') }}
                </label>
            </div>

            <label>
                {{ $gettext('Bestätigung für folgenden Text einholen') }}
                ({{ $gettext('optional') }})
                <StudipTooltipIcon :text="$gettext('Wird hier ein Text eingegeben, so müssen Buchende bestätigen, dass sie diesen Text gelesen haben.')"></StudipTooltipIcon>
                <textarea name="confirmation-text" v-model="confirmationText"></textarea>
            </label>

            <slot name="extension-point-2"></slot>
        </fieldset>

        <fieldset v-if="needsConfirmation">
            <legend>{{ $gettext('Bestätigung der Erstellung vieler Termine') }}</legend>

            <p>
                {{ $gettext('Sie erstellen eine sehr große Anzahl an Terminen.') }}
                {{ $gettext('Bitte bestätigen Sie diese Aktion.') }}
            </p>

            <label>
                <input type="checkbox" v-model="confirmed">
                {{ $gettextInterpolate(
                    $gettext('Ja, ich möchte wirklich %{ n } Termine erstellen.'),
                    { n: slotCount }
                ) }}
            </label>
        </fieldset>

        <footer data-dialog-button>
            <button class="accept button" :disabled="!confirmed">
                {{ $gettext('Termin speichern') }}
            </button>
            <a :href="cancelUrl" class="cancel button" @click="evt => closeCreator(evt)">
                {{ $gettext('Abbrechen') }}
            </a>
        </footer>
    </form>
</template>
<script>
import StudipTooltipIcon from './StudipTooltipIcon.vue';
import Datepicker from './Datepicker.vue';

import moment from 'moment';
import StudipSelect from './StudipSelect.vue';
import Timepicker from './Timepicker.vue';

export default {
    name: 'ConsultationCreator',
    components: {Datepicker, StudipSelect, StudipTooltipIcon, Timepicker},
    props: {
        asDialog: {
            type: Boolean,
            default: false,
        },
        cancelUrl: {
            type: String,
            required: true
        },
        defaultRoom: String,
        rangeType: {
            type: String,
            required: true,
        },
        slotCountThreshold: {
            type: Number,
            required: true,
        },
        storeUrl: {
            type: String,
            required: true
        },
        withResponsible: {
            type: [Boolean, Object],
            default: false,
        },
    },
    data() {
        return {
            calendarEvents: false,
            confirmationText: '',
            confirmed: false,
            consecutive: false,
            dayOfWeek: (new Date()).getDay(),
            duration: 15,
            endDate: moment().add(4, 'weeks').toDate(),
            endTime: '09:00',
            errors: [],
            interval: 1,
            lock: false,
            lockTime: 24,
            mailToTutors: true,
            note: '',
            pause: false,
            pauseDuration: 15,
            pauseTime: 45,
            requireReason: 'optional',
            responsibleGroups: [],
            responsibleInstitutes: [],
            responsibleUsers: [],
            room: this.defaultRoom,
            showParticipants: false,
            size: 1,

            slotCount: null,
            startDate: moment().add(1, 'weeks').toDate(),
            startTime: '08:00',
        }
    },
    computed: {
        csrf() {
            return STUDIP.CSRF_TOKEN;
        },
        daysOfTheWeek() {
            return [
                {key: 1, label:  this.$gettext('Montag')},
                {key: 2, label: this.$gettext('Dienstag')},
                {key: 3, label: this.$gettext('Mittwoch')},
                {key: 4, label: this.$gettext('Donnerstag')},
                {key: 5, label: this.$gettext('Freitag')},
                {key: 6, label: this.$gettext('Samstag')},
                {key: 0, label: this.$gettext('Sonntag')},
            ];
        },
        intervals() {
            return {
                0: this.$gettext('einmalig (ohne Wiederholung)'),
                1: this.$gettext('wöchentlich'),
                2: this.$gettext('zweiwöchentlich'),
                3: this.$gettext('dreiwöchentlich'),
                4: this.$gettext('monatlich'),
            };
        },
        isCourse() {
            return this.rangeType === 'Course';
        },
        isInstitute() {
            return this.rangeType === 'Institute';
        },
        isSingleDay() {
            return this.interval === 0;
        },
        needsConfirmation() {
            return this.slotCount > this.slotCountThreshold;
        },
        recalculationProperty() {
            return [
                this.startDate,
                this.startTime,
                this.endDate,
                this.endTime,
                this.dayOfWeek,
                this.interval,
                this.duration,
                this.pause,
                this.pauseTime,
                this.pauseDuration,
            ].join();
        },
    },
    methods: {
        closeCreator(event) {
            if (this.$el.closest('.studip-dialog')) {
                STUDIP.Dialog.close();
                event.preventDefault();
            }
        },
        validateInputs(event) {
            const errors = [];

            if (this.startTime > this.endTime) {
                errors.push(this.$gettext('Die Endzeit liegt vor der Startzeit!'));
            }

            if (this.interval > 0 && this.startDate > this.endDate) {
                errors.push(this.$gettext('Das Enddatum liegt vor dem Startdatum!'));
            }

            if (this.pauseTime && this.pauseTime < this.duration) {
                errors.push(this.$gettext('Die definierte Zeit bis zur Pause ist kleiner als die Dauer eines Termins.'));
            }

            if (
                this.isInstitute
                && this.responsibleGroups.length === 0
                && this.responsibleInstitutes.length === 0
                && this.responsibleUsers.length === 0
            ) {
                errors.push(this.$gettext('Es muss mindestens eine durchführende Person, Statusgruppe oder Einrichtung ausgewählt werden.'));
            }

            if (this.needsConfirmation && !this.confirmed) {
                errors.push(this.$gettext('Sie müssen bestätigen, dass sie eine große Anzahl von Terminen erstellen möchten.'));

            }

            if (errors.length > 0) {
                this.errors = errors;
                event.preventDefault();
            }
        },
        combineDateAndTime(date, time) {
            const [hour, minute] = time.split(':').map(item => parseInt(item, 10));
            const result = new Date(date);
            result.setHours(hour);
            result.setMinutes(minute);
            result.setSeconds(0);
            return result;
        }
    },
    watch: {
        interval(current) {
            if (current === 0) {
                this.endDate = new Date(this.startDate);
            }
        },
        recalculationProperty: {
            handler() {
                STUDIP.jsonapi.withPromises().GET('consultation-slots/count', {
                    data: {
                        start: this.combineDateAndTime(this.startDate, this.startTime).toISOString(),
                        end: this.combineDateAndTime(this.endDate, this.endTime).toISOString(),
                        dow: this.dayOfWeek,
                        interval: this.interval,
                        duration: this.duration,
                        pause_time: this.pause ? this.pauseTime : null,
                        pause_duration: this.pause ? this.pauseDuration : null,
                    }
                }).then((count) => {
                    this.slotCount = count;
                    this.confirmed = count <= this.slotCountThreshold;
                });
            },
            immediate: true
        },
        startDate(current) {
            this.dayOfWeek = current.getDay();
        },
    },
    beforeCreate() {
        STUDIP.Vue.emit('ConsultationCreatorWillCreate', this);
    }
}
</script>
<style scoped>
form.default label input[type="time"] {
    max-width: 48em;
}
</style>
