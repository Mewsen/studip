<template>
    <FullCalendar :options="calendar_options"></FullCalendar>
</template>
<script lang="ts">
import {defineComponent} from 'vue';
import {CalendarOptions, DateSelectionApi, EventClickArg, EventDropArg} from '@fullcalendar/core';
import FullCalendar from "@fullcalendar/vue3";
import interactionPlugin, {EventResizeDoneArg} from '@fullcalendar/interaction';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import resourceTimelinePlugin from '@fullcalendar/resource-timeline';
import Dialog from "../../assets/javascripts/lib/dialog.js";
import { jsonapi } from "../../assets/javascripts/lib/jsonapi";

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
        action_urls: {
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
        if (this.custom_event_handlers.view) {
            calendar_options.eventClick = this.custom_event_handlers.view;
        } else {
            calendar_options.eventClick = this.handleEventClick;
        }

        let holiday_cache = sessionStorage.getItem('fullcalendar_holidays');
        let vacation_cache = sessionStorage.getItem('fullcalendar_vacations');

        return {
            calendar_options: calendar_options as CalendarOptions,
            holiday_cache: holiday_cache ? JSON.parse(holiday_cache) : {},
            vacation_cache: vacation_cache ? JSON.parse(vacation_cache) : {}
        }
    },
    methods: {
        /*
        loadHolidays(year: number) {
            if (this.holiday_cache[year]) {
                return Promise.resolve(this.holiday_cache[year]);
            }
            return jsonapi.withPromises().GET('holidays', {
                data: { 'filter[year]': year }
            }).then(response => {
                const events = [];
                if (!response) {
                    return events;
                }

                for (const [date, data] of Object.entries(response)) {
                    const classNames = ['holiday'];
                    if (data.mandatory) {
                        classNames.push('official');
                    }

                    const day = new Date(date);
                    events.push({
                        // Note: Since allDay is set to true, the start and end time is ignored.
                        // See the documentation: https://fullcalendar.io/docs/v4/event-parsing
                        start:    day,
                        end:      day,
                        allDay:   true,
                        title:    data.holiday,
                        editable: false,

                        classNames,

                        // Note: Colours are set via SCSS.
                        textColor:   '',
                        color:       '',
                        borderColor: '',

                        rendering: 'background'
                    });
                }

                this.holiday_cache[year] = events;
                sessionStorage.setItem('fullcalendar_holidays', JSON.stringify(this.holiday_cache));
                return events;
            });
        },
         */
        handleSelection: function(selection: DateSelectionApi) {
            if (!this.calendar_options.editable || this.action_urls.length < 1) {
                //The calendar isn't editable.
                return;
            }
            if (this.action_urls['add']) {
                //Add the selected time range to the URL and load it
                //in a dialog:
                Dialog.fromURL(
                    this.action_urls['add'],
                    {
                        data: {
                            start:   (selection.start.getTime() / 1000).toString(),
                            end:     (selection.end.getTime() / 1000).toString(),
                            all_day: (selection.allDay ? '1' : '0')
                        },
                        size: this.dialog_size
                    }
                );
            }

            console.debug(selection);
        },
        handleEventClick: function(event_data: EventClickArg) {
            let show_url = event_data.event.extendedProps.studip_view_urls.show;
            if (show_url) {
                //Load the dialog:
                Dialog.fromURL(event_data.event.extendedProps.studip_view_urls.show,
                    {
                        size: this.dialog_size
                    }
                );
            }
        },
        handleEventDrop: function(dropped_event: EventDropArg) {
            if (!this.calendar_options.editable || !dropped_event.event.extendedProps)
            console.debug('drop');
            console.debug(dropped_event);
        },
        handleEventResize: function(event: EventResizeDoneArg) {
            console.debug('resize');
            console.debug(event);
        }
    }
})
</script>
<style scoped lang="scss">

</style>
