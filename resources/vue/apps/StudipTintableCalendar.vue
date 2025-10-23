<template>
    <StudipCalendar
        :config="config"
        @eventDropped="handleColourDrop"
    ></StudipCalendar>
</template>
<script lang="ts">
import {defineComponent} from 'vue'
import StudipCalendar from "./StudipCalendar.vue";

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
                header:      {
                    start:  ['dayGridYear', 'dayGridMonth', 'timeGridWeek', 'timeGridDay'],
                    center: ['title'],
                    end:    ['prev', 'today', 'next']
                }
            })
        },
    },
    methods: {
        handleColourDrop: function(event: any) {
            console.debug(event);
        }
    }
})
</script>
