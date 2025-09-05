<template>
    <FullCalendar :options="calendar_options"></FullCalendar>
</template>
<script lang="ts">
import {defineComponent} from 'vue'
import FullCalendar from "@fullcalendar/vue3";
//import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid';

export default defineComponent({
    name: "StudipCalendar",
    components: {
        FullCalendar
    },
    props: {
        config: {
            type: Object,
            required: true,
            default: () => {}
        }
    },
    data() {
        return {
            calendar_options: {
                plugins: [timeGridPlugin],
                initialView: this.config.initialView ? this.config.initialView : 'timeGridWeek'
            }
        }
    },
    computed: {

    }
})
</script>
<style scoped lang="scss">

</style>
