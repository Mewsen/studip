<template>
    <FullCalendar :options="calendar_options"></FullCalendar>
</template>
<script lang="ts">
import {defineComponent} from 'vue';
import {
    CalendarOptions,
    DateSelectionApi,
    EventClickArg,
    EventDropArg,
    EventSourceFuncArg
} from '@fullcalendar/core';
import FullCalendar from "@fullcalendar/vue3";
import interactionPlugin, {EventResizeDoneArg} from '@fullcalendar/interaction';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import resourceTimelinePlugin from '@fullcalendar/resource-timeline';
import Dialog from "../../assets/javascripts/lib/dialog.js";
import { jsonapi } from "../../assets/javascripts/lib/jsonapi";
import {getLocale} from "../../assets/javascripts/lib/gettext";

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
        },
        display_holidays: {
            type: Boolean,
            required: false,
            default: true
        },
        display_vacations: {
            type: Boolean,
            required: false,
            default: true
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
        calendar_options.firstDay = 1;

        //Fullcalendar needs a short version of the locale:
        let short_locale: string = getLocale();
        if (short_locale) {
            let underscore = short_locale.indexOf('_');
            short_locale = short_locale.substring(0, underscore);
        } else {
            short_locale = 'de';
        }
        calendar_options.locale = short_locale;

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

        //Build the event sources:
        if (!calendar_options.eventSources) {
            calendar_options.eventSources = [];
        }
        if (this.display_holidays) {
            let holiday_source = function(arg: EventSourceFuncArg, successCallback: (events: object) => void, failureCallback: () => void) {
                const startYear = arg.start.getFullYear();
                const endYear   = arg.end.getFullYear();
                const requests: any = [];
                let holiday_cache_str = sessionStorage.getItem('fullcalendar_holidays');
                let holiday_cache: any = {};
                if (holiday_cache_str != null) {
                    holiday_cache = JSON.parse(holiday_cache_str);
                }
                for (let year = startYear; year <= endYear; year++) {
                    if (holiday_cache[year.toString()]) {
                        return Promise.resolve(holiday_cache[year.toString()]);
                    }
                    let request = jsonapi.withPromises().GET('holidays', {
                        data: { 'filter[year]': year }
                    }).then(response => {
                        const events: object[] = [];
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
                                textColor:       '',
                                backgroundColor: '',
                                borderColor:     '',

                                display: 'background'
                            });
                        }

                        if (holiday_cache != null) {
                            holiday_cache[year.toString()] = events;
                        }
                        sessionStorage.setItem('fullcalendar_holidays', JSON.stringify(holiday_cache));
                        return events;
                    });
                    requests.push(request);
                }
                Promise.all(requests).then(results => {
                    const events = [].concat(...results);
                    successCallback(events);
                    return results;
                }).catch(failureCallback);
            };
            calendar_options.eventSources.push(holiday_source);
        }
        if (this.display_vacations) {
            let vacation_source = function(arg: EventSourceFuncArg, successCallback: (events: object) => void, failureCallback: () => void) {
                const startYear = arg.start.getFullYear();
                const endYear = arg.end.getFullYear();
                const requests: any = [];
                let vacation_cache_str = sessionStorage.getItem('fullcalendar_vacations');
                let vacation_cache: any = {};
                if (vacation_cache_str != null) {
                    vacation_cache = JSON.parse(vacation_cache_str);
                }
                for (let year = startYear; year <= endYear; year++) {
                    if (vacation_cache[year]) {
                        return Promise.resolve(vacation_cache[year.toString()]);
                    }
                    let request = jsonapi.withPromises().get('vacations', {
                        data: {'filter[year]': year}
                    }).then(response => {
                        if (!response) {
                            return [];
                        }

                        const items: object[] = [];

                        for (const vacation_data of Object.values(response)) {
                            const start = new Date(parseInt(vacation_data.start) * 1000);
                            const end = new Date(parseInt(vacation_data.end) * 1000);
                            items.push({
                                start,
                                end,
                                allDay: true,
                                title: vacation_data.name,
                                editable: false,
                                classNames: ['holiday'],

                                // Note: Colours are set via SCSS.
                                textColor: '',
                                color: '',
                                borderColor: '',

                                display: 'background'
                            });
                        }

                        if (vacation_cache != null) {
                            vacation_cache[year.toString()] = items;
                        }
                        sessionStorage.setItem('fullcalendar_vacations', JSON.stringify(vacation_cache));
                        return items;
                    });
                    requests.push(request);
                }
                Promise.all(requests).then(results => {
                    const events = [].concat(...results);
                    successCallback(events);
                    return results;
                }).catch(failureCallback);
            };
            calendar_options.eventSources.push(vacation_source);
        }

        return {
            calendar_options: calendar_options as CalendarOptions,
            holiday_cache: holiday_cache ? JSON.parse(holiday_cache) : {},
            vacation_cache: vacation_cache ? JSON.parse(vacation_cache) : {}
        }
    },
    methods: {
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
                            start:   selection.startStr,
                            end:     selection.endStr,
                            all_day: (selection.allDay ? '1' : '0')
                        },
                        size: this.dialog_size
                    }
                );
            }
        },
        handleEventClick: function(event_data: EventClickArg) {
            if (!event_data.event.extendedProps.studip_view_urls
                || !event_data.event.extendedProps.studip_view_urls.show) {
                //Nothing to do.
                return;
            }
            //Load the dialog:
            Dialog.fromURL(
                event_data.event.extendedProps.studip_view_urls.show,
                {
                    size: this.dialog_size
                }
            );
        },
        handleEventDrop: function(drop_arg: any) {
            let event = drop_arg.event;
            console.debug(event);
            if (!this.calendar_options.editable || !event.startEditable
                || !event.startStr || !event.endStr) {
                //Nothing to do.
                return;
            }
            if (event.extendedProps.studip_api_urls.move) {
                let data : any = {
                    begin: event.startStr,
                    end:   event.endStr
                };
                if (event.newResource) {
                    data.resource_id = event.newResource.id;
                }

                //Call the move URL as HTTP POST:
                $.post({
                    async: false,
                    url: event.extendedProps.studip_api_urls.move,
                    data
                }).fail(drop_arg.revert);
            } else if (event.extendedProps.studip_view_urls.move_dialog) {
                //Show the move dialog:
                Dialog.fromURL(
                    event.extendedProps.studip_api_urls.move_dialog,
                    {
                        data: {
                            start:   event.startStr,
                            end:     event.endStr,
                            all_day: (event.allDay ? '1' : '0')
                        },
                        size: this.dialog_size
                    }
                );
            }
        },
        handleEventResize: function(resize_arg: EventResizeDoneArg) {
            if (!this.calendar_options.editable
            || !resize_arg.event.extendedProps.studip_api_urls.resize_dialog
            || !resize_arg.event.startStr || !resize_arg.event.endStr) {
                //Nothing to do.
                return;
            }
            Dialog.fromURL(
                resize_arg.event.extendedProps.studip_api_urls.resize_dialog,
                {
                    data: {
                        start:   resize_arg.event.startStr,
                        end:     resize_arg.event.endStr,
                        all_day: (resize_arg.event.allDay ? '1' : '0')
                    },
                    size: this.dialog_size
                }
            );
        }
    }
})
</script>
<style lang="scss">
@import '../../assets/stylesheets/scss/buttons';

.fc {
    .fc-toolbar.fc-header-toolbar {
        margin-bottom: 0.5em;
    }

    .fc-button-group {
        height: 30px;

        .fc-button {
            @include button;
            margin-top: 0;
            margin-bottom: 0;
            padding: 0;

            &:last-of-type {
                margin-right: 0;
            }

            .fc-icon {
                /* Unset rules that are set in the fullcalendar default stylesheet: */
                line-height: unset;
                height: unset;
            }
        }
    }
}
</style>
