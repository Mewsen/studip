<template>
    <FullCalendar :options="calendar_options"></FullCalendar>
</template>
<script lang="ts">
import {defineComponent} from 'vue';
import {CalendarOptions, EventClickArg} from '@fullcalendar/core';
import FullCalendar from "@fullcalendar/vue3";
import interactionPlugin from '@fullcalendar/interaction';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import resourceTimelinePlugin from '@fullcalendar/resource-timeline';
import Dialog from "../../assets/javascripts/lib/dialog.js";

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
        },
        custom_event_handlers: {
            type: Object,
            required: false,
            default: () => ({})
        }
    },
    data() {
        //Make sure that defaults are set for the calendar:
        let calendar_options = this.config;
        //Add the plugins here so that users of this component
        //do not need to add them separately.
        //TODO: load on demand
        calendar_options.plugins = [dayGridPlugin, timeGridPlugin, resourceTimelinePlugin, interactionPlugin];
        calendar_options.schedulerLicenseKey = 'GPL-My-Project-Is-Open-Source';

        if (!calendar_options.initialView) {
            calendar_options.initialView = 'timeGridWeek';
        }
        console.debug(calendar_options);
        //Set the event handlers, if needed.
        if (calendar_options.editable) {
            calendar_options.eventDrop   = this.handleEventDrop;
            calendar_options.eventResize = this.handleEventResize;

            if (calendar_options.selectable) {
                calendar_options.select = this.handleSelection;
            }
        }
        if (this.dialog_urls.view) {
            calendar_options.eventClick = this.handleEventClick;
        } else if (this.custom_event_handlers.view) {
            calendar_options.eventClick = this.custom_event_handlers.view;
        }

        return {
            calendar_options: calendar_options as CalendarOptions,
        }
    },
    methods: {
        handleSelection: function(selection: object) {
            console.debug(selection);
        },
        handleEventClick: function(event_data: EventClickArg) {
            let show_url = event_data.event.extendedProps.studip_view_urls.show;
            console.debug(show_url);
            if (show_url) {
                //Load the dialog:
                Dialog.fromURL(event_data.event.extendedProps.studip_view_urls.show);
            }
        },
        handleEventDrop: function(event: object) {
            console.debug(event);
        },
        handleEventResize: function(event: object) {
            console.debug(event);
        }
    }
})
</script>
<style scoped lang="scss">

</style>
