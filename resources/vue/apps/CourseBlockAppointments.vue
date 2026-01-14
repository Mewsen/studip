<template>
    <fieldset>
        <legend>{{ $gettext('Grunddaten') }}</legend>
        <section>
            <label class="col-2">
            {{ $gettext('Startdatum') }}
                <datepicker name="start_date"
                            v-model="start_date"></datepicker>
            </label>
            <label class="col-2">
            {{ $gettext('Enddatum') }}
                <datepicker name="end_date"
                            v-model="end_date"></datepicker>
            </label>
        </section>
        <section>
            <label class="col-2">
            {{ $gettext('Beginn') }}
                <timepicker name="start_time"
                            v-model="start_time_str"></timepicker>
            </label>
            <label class="col-2">
            {{ $gettext('Ende') }}
                <timepicker name="end_time"
                            v-model="end_time_str"></timepicker>
            </label>
        </section>
        <section id="block_appointment_days">
            <label>{{ $gettext('Die Termine finden an folgenden Tagen statt:') }}</label>
            <label class="col-2">
                <input type="checkbox" name="dow[]" v-model="all_days_selected" value="all"
                       :checked="all_days_selected">
                {{ $gettext('Jeden Tag') }}
            </label>
            <label class="col-2">
                <input type="checkbox" name="dow[]" v-model="mon_fri_selected" value="mon_fri"
                       :checked="mon_fri_selected">
                {{ $gettext('Montag - Freitag') }}
            </label>

            <label class="col-2">
                <input type="checkbox" name="dow[]" v-model="dow" :value="1"
                       :checked="dow.includes(1)">
                {{ $gettext('Montag') }}
            </label>
            <label class="col-2">
                <input type="checkbox" name="dow[]" v-model="dow" :value="2"
                       :checked="dow.includes(2)">
                {{ $gettext('Dienstag') }}
            </label>
            <label class="col-2">
                <input type="checkbox" name="dow[]" v-model="dow" :value="3"
                       :checked="dow.includes(3)">
                {{ $gettext('Mittwoch') }}
            </label>
            <label class="col-2">
                <input type="checkbox" name="dow[]" v-model="dow" :value="4"
                       :checked="dow.includes(4)">
                {{ $gettext('Donnerstag') }}
            </label>
            <label class="col-2">
                <input type="checkbox" name="dow[]" v-model="dow" :value="5"
                       :checked="dow.includes(5)">
                {{ $gettext('Freitag') }}
            </label>
            <label class="col-2">
                <input type="checkbox" name="dow[]" v-model="dow" :value="6"
                       :checked="dow.includes(6)">
                {{ $gettext('Samstag') }}
            </label>
            <label class="col-2">
                <input type="checkbox" name="dow[]" v-model="dow" :value="0"
                       :checked="dow.includes(0)">
                {{ $gettext('Sonntag') }}
            </label>
        </section>
        <section>
            <label>
                {{ $gettext('Anzahl der Termine') }}
                <input type="number" name="date_count"
                       min="1" :max="this.time_ranges.length"
                       v-model="this.date_count">
            </label>
            <studip-message-box v-if="this.date_count > 50"
                                type="info" :hideClose="true"
                                :hideDetails="false">
                {{ $gettextInterpolate(
                'Sie legen %{count} Termine an. Bitte kontrollieren Sie Ihre Eingaben.',
                {count: this.date_count}
            ) }}
            </studip-message-box>
        </section>
    </fieldset>
    <CourseDateRoomFieldset
        :time_ranges="time_ranges"
        :room_management_enabled="room_management_enabled"
        :initial_selected_room_option="'noroom'"
        :allow_multiple_room_bookings="allow_multiple_room_bookings"
        :initial_preparation_time="initial_preparation_time"
        :initial_subsequent_time="initial_subsequent_time"
        :max_preparation_time="max_preparation_time"
    ></CourseDateRoomFieldset>
    <fieldset>
    <legend>{{ $gettext('Weitere Angaben') }}</legend>
        <label>
            {{ $gettext('Termintyp') }}
            <select name="date_type"
                    v-model="selected_date_type">
                <option v-for="date_type in date_types" :value="date_type.id" :key="date_type.id">
                    {{ date_type.name}}
                </option>
            </select>
        </label>
        <label>
            {{ $gettext('Zugewiesene Lehrende') }}
            <multiselect name="assigned_lecturers[]"
                         :options="available_lecturer_options"
                         v-model="selected_lecturer_list"
                         :no_options_text="$gettext('Keine Lehrenden auswählbar')"
                         :value="selected_lecturer_list"
            ></multiselect>
            <input type="hidden" name="assigned_lecturers[]"
                   v-for="item in selected_lecturer_list"
                   v-bind:key="item" :value="item">
        </label>
    </fieldset>
</template>
<script>
import {$gettext} from "../../assets/javascripts/lib/gettext";
import CourseDateRoomFieldset from "../components/CourseDateRoomFieldset.vue";
import Timepicker from "../components/Timepicker.vue";
import Datepicker from "../components/Datepicker.vue";
import StudipMessageBox from "../components/StudipMessageBox.vue";
export default {
    name: 'CourseBlockAppointments',
    components: {StudipMessageBox, CourseDateRoomFieldset, Timepicker, Datepicker},
    props: {
        room_management_enabled: {
            type: Boolean,
            required: true,
            default: false
        },
        max_preparation_time: {
            type: Number,
            required: false,
            default: 999
        },
        initial_preparation_time: {
            type: Number,
            required: false,
            default: 0
        },
        initial_subsequent_time: {
            type: Number,
            required: false,
            default: 0
        },
        allow_multiple_room_bookings: {
            type: Boolean,
            required: false,
            default: false
        },
        date_types: {
            type: Array,
            required: true
        },
        available_lecturers: {
            type: Array,
            required: false,
            default: () => []
        },
        selected_lecturers: {
            type: Array,
            required: false,
            default: () => []
        }
    },
    methods: {
        $gettext,
    },
    data() {
        let now = new Date();
        //Use the next half hour as default:
        let start_date     = new Date(Math.ceil(now.getTime() / 1800000) * 1800000);
        let end_date       = new Date((Math.ceil(now.getTime() / 1800000)  * 1800000) + 1800000);
        let start_time_str = STUDIP.DateTime.pad(start_date.getHours()) + ':' + STUDIP.DateTime.pad(start_date.getMinutes());
        let end_time_str   = STUDIP.DateTime.pad(end_date.getHours()) + ':' + STUDIP.DateTime.pad(end_date.getMinutes());
        let selected_date_type = '';
        if (this.date_types.length > 0) {
            selected_date_type = this.date_types[0].id;
        }

        return {
            start_date: now.getTime() / 1000,
            end_date: now.getTime() / 1000,
            start_time_str,
            end_time_str,
            all_days_selected: true,
            mon_fri_selected: false,
            dow: [],
            date_count: 0,
            last_changed_start_date: new Date(),
            last_changed_end_date: new Date(),
            last_changed_start_time: new Date(),
            last_changed_end_time: new Date(),
            last_changed_dow: new Date(),
            available_lecturer_options: this.available_lecturers !== undefined ? this.available_lecturers : [],
            selected_lecturer_list:     this.selected_lecturers !== undefined ? this.selected_lecturers : [],
            selected_date_type
        }
    },
    computed: {
        time_ranges() {
            if (this.start_date > this.end_date) {
                //Invalid time range selection.
                return [];
            }
            let start_time_parts = this.start_time_str.split(':');
            let end_time_parts = this.end_time_str.split(':');
            if (start_time_parts.length !== 2 || end_time_parts.length !== 2) {
                //Invalid time format.
                return [];
            }
            let day_numbers = [];
            if (this.all_days_selected) {
                day_numbers = [0, 1, 2, 3, 4, 5, 6];
            } else if (this.mon_fri_selected) {
                day_numbers = [1, 2, 3, 4, 5];
            } else {
                day_numbers = this.dow;
            }
            if (day_numbers.length === 0) {
                //No days selected. Nothing to do.
                return [];
            }
            let current_start = new Date(this.start_date * 1000);
            current_start.setHours(parseInt(start_time_parts[0]), parseInt(start_time_parts[1]), 0, 0);
            let current_end   = new Date(this.end_date * 1000);
            current_end.setHours(parseInt(end_time_parts[0]), parseInt(end_time_parts[1]), 0, 0);

            let new_time_ranges = [];
            while (current_start < current_end) {
                let relevant_day = current_start.getDay();
                if (day_numbers.includes(relevant_day)) {
                    //Put the day into the time ranges.
                    let range_start = new Date(current_start.getTime());
                    let range_end = new Date(current_start.getTime());
                    range_end.setHours(parseInt(end_time_parts[0]), parseInt(end_time_parts[1]), 0, 0);
                    new_time_ranges.push({start: range_start, end: range_end});
                }
                current_start.setDate(current_start.getDate() + 1);
            }
            return new_time_ranges;
        }
    },
    watch: {
        time_ranges(newValue) {
            if (newValue === undefined) {
                this.date_count = 0;
            } else {
                this.date_count = newValue.length;
            }
        }
    }
}
</script>
