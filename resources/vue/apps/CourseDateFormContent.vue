<template>
    <fieldset>
        <legend>{{ $gettext('Grunddaten') }}</legend>
        <label class="col-2">
            {{ $gettext('Datum') }}
            <datepicker name="date"
                        v-model="start_date"></datepicker>
        </label>
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
    </fieldset>
    <CourseDateRoomFieldset
        :time_ranges="time_ranges"
        :course_date_ids="course_date_ids"
        :show_nochange_option="course_date_ids.length > 0"
        :room_management_enabled="room_management_enabled"
        :initial_selected_rooms="selected_rooms"
        :initial_room_name="initial_room_name"
        :allow_multiple_room_bookings="allow_multiple_room_bookings"
        :initial_preparation_time="initial_preparation_time"
        :initial_subsequent_time="initial_subsequent_time"
    ></CourseDateRoomFieldset>

    <fieldset>
        <legend>{{ $gettext('Weitere Angaben') }}</legend>
        <label>
            {{ $gettext('Termintyp') }}
            <select name="date_type"
                    v-model="selected_date_type">
                <option v-for="date_type in date_types" :value="date_type.id" :key="date_type.id">
                    {{date_type.name}}
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
        <label>
            {{ $gettext('Beteiligte Gruppen') }}
            <multiselect name="assigned_groups[]"
                         :options="available_group_options"
                         v-model="selected_group_list"
                         :no_options_text="$gettext('Keine Gruppen auswählbar')"
                         :value="selected_group_list"
            ></multiselect>
            <input type="hidden" name="assigned_groups[]"
                   v-for="item in selected_group_list"
                   v-bind:key="item" :value="item">
        </label>
        <label v-if="enable_number_of_participants">
            {{ $gettext('Anzahl der Teilnehmenden') }}
            <input type="number" min="0"
                   name="number_of_participants">
        </label>
    </fieldset>
</template>
<script>
import {$gettext} from "../../assets/javascripts/lib/gettext";
import Datepicker from "../components/Datepicker.vue";
import Timepicker from "../components/Timepicker.vue";
import Multiselect from "../components/Multiselect.vue";
import CourseDateRoomFieldset from "../components/CourseDateRoomFieldset.vue";
import {datetime} from "../../assets/javascripts/lib/datetime";

export default {
    name: 'CourseDateFormContent',
    components: {CourseDateRoomFieldset, Multiselect, Timepicker, Datepicker},
    props: {
        course_date: {
            type: Object,
            required: false,
            default: null
        },
        date_types: {
            type: Array,
            required: true
        },
        room_management_enabled: {
            type: Boolean,
            required: true,
            default: false
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
        max_preparation_time: {
            type: Number,
            required: false,
            default: 999
        },
        allow_multiple_room_bookings: {
            type: Boolean,
            required: false,
            default: false
        },
        enable_number_of_participants: {
            type: Boolean,
            required: false,
            default: false
        },
        selected_rooms: {
            type: Array,
            required: false,
            default: () => []
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
        },
        available_groups: {
            type: Array,
            required: false,
            default: () => []
        },
        selected_groups: {
            type: Array,
            required: false,
            default: () => []
        },
    },
    data() {
        let selected_date_type = '';
        let course_date_ids = [];
        let start_date = null;
        let end_date   = null;
        let initial_room_name = '';
        if (this.course_date) {
            start_date = new Date(this.course_date.date * 1000);
            end_date   = new Date(this.course_date.end_time * 1000);
            selected_date_type = this.course_date.date_typ;
            course_date_ids.push(this.course_date.termin_id);
            initial_room_name = this.course_date.raum;
        } else {
            start_date = new Date();
            if (this.date_types[0] !== undefined) {
                selected_date_type = this.date_types[0].id;
            }
            //Round the time values to the next half hour:
            start_date = new Date(Math.ceil(start_date.getTime() / 1800000) * 1800000);
            end_date   = new Date((Math.ceil(start_date.getTime() / 1800000)  * 1800000) + 1800000);
        }
        let start_time_str = null;
        let end_time_str   = null;
        if (start_date && end_date) {
            start_time_str = datetime.pad(start_date.getHours()) + ':' + datetime.pad(start_date.getMinutes());
            end_time_str = datetime.pad(end_date.getHours()) + ':' + datetime.pad(end_date.getMinutes());
        }

        return {
            start_date,
            start_time_str,
            end_time_str,
            course_date_ids,
            selected_date_type,
            booking_selected: this.room_management_enabled && this.selected_rooms,
            separable_room_name: '',
            initial_room_name,
            last_changed_date: new Date(),
            last_changed_start_time: new Date(),
            last_changed_end_time: new Date(),
            available_lecturer_options: this.available_lecturers !== undefined ? this.available_lecturers : [],
            selected_lecturer_list: this.selected_lecturers !== undefined ? this.selected_lecturers : [],
            available_group_options: this.available_groups !== undefined ? this.available_groups : [],
            selected_group_list: this.selected_groups !== undefined ? this.selected_groups : []
        };
    },
    methods: {
        $gettext
    },
    computed: {
        time_ranges() {
            let start = new Date(this.start_date);
            let end = new Date(this.start_date);
            if (typeof(this.start_date) === 'number') {
                //The start date is not a date object but a timestamp.
                start = new Date(this.start_date * 1000);
                end   = new Date(this.start_date * 1000);
            }
            let start_time_parts = this.start_time_str.split(':');
            if (start_time_parts.length !== 2) {
                //Invalid time string.
                return [];
            }
            start.setHours(parseInt(start_time_parts[0]), parseInt(start_time_parts[1]), 0);

            let end_time_parts = this.end_time_str.split(':');
            if (end_time_parts.length !== 2) {
                //Invalid time string.
                return [];
            }
            end.setHours(parseInt(end_time_parts[0]), parseInt(end_time_parts[1]), 0);

            return [{start, end}];
        }
    }
}
</script>
