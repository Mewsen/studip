<template>
    <FullCalendar :options="calendar_options"></FullCalendar>
</template>
<script lang="ts">
import {defineComponent} from 'vue';
import FullCalendar from "@fullcalendar/vue3";
//import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid';
import resourceTimelinePlugin from '@fullcalendar/resource-timeline';

export default defineComponent({
    name: "StudipCalendar",
    components: {
        FullCalendar
    },
    props: {
        config: {
            type: Object,
            required: true,
            default: () => ({
                editable:    false,
                selectable:  false,
                studip_urls: [],
                dialog_size: 'auto',
                slotMinTime: '08:00',
                slotMaxTime: '20:00',
                initialDate: new Date(),
                allDaySlot:  false,
                allDayText:  '',
                weekNumbers: true,
                header:      {
                    start:  ['dayGridYear', 'dayGridMonth', 'timeGridWeek', 'timeGridDay'],
                    center: ['title'],
                    end:    ['prev', 'today', 'next']
                }
            })
        },
        dialog_urls: {
            type: Object,
            required: false,
            default: () => ({})
        },
        dialog_size: {
            type: String,
            required: false,
            default: 'auto'
        }
    },
    data() {
        //Make sure that defaults are set for the calendar:
        let calendar_options = this.config;
        calendar_options.plugins = [timeGridPlugin, resourceTimelinePlugin];
        calendar_options.schedulerLicenseKey = 'GPL-My-Project-Is-Open-Source';

        if (!calendar_options.initialView) {
            calendar_options.initialView = 'timeGridWeek';
        }
        return {
            calendar_options: calendar_options
        }
    },
    computed: {

    }
})
</script>
<style scoped lang="scss">

</style>
