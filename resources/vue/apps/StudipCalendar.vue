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
            default: () => ({})
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
