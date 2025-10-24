<template>
    <StudipCalendar
        :config="calendar_config"
        @eventReceived="handleColourDrop"
    ></StudipCalendar>
</template>
<script lang="ts">
import {defineComponent} from 'vue'
import StudipCalendar from "./StudipCalendar.vue";
import {EventReceiveArg} from "@fullcalendar/interaction";

export default defineComponent({
    name: "StudipTintableCalendar",
    components: {StudipCalendar},
    props: {
        config: {
            type: Object,
            required: true,
            default: () => ({
                editable:    false,
                selectable:  false,
                slotMinTime: '08:00',
                slotMaxTime: '20:00',
                initialDate: new Date(),
                allDaySlot:  false,
                allDayText:  '',
                weekNumbers: true,
                headerToolbar: {
                    left:  ['dayGridMonth', 'timeGridWeek', 'timeGridDay'],
                    right: ['prev', 'next']
                }
            })
        },
    },
    data() {
        return {
            calendar_config: this.config
        };
    },
    methods: {
        handleColourDrop: function(arg: EventReceiveArg) {
            console.debug(arg);
            //test
        }
    }
})
</script>
