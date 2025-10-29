<template>
    <FullCalendar :options="calendar_options">
        <template v-slot:eventContent='arg'>
            <section v-if="arg.event.display === 'auto' && ['timeGridDay', 'timeGridWeek'].includes(arg.view.type)"
                     :title="arg.event.title">
                <span v-if="!arg.event.allDay"
                      class="fc-event-time">
                    {{ arg.timeText }}
                </span>
                <span class="fc-event-title-container">
                    <span class="debug">
                    {{ JSON.stringify(arg.event.icons) }}
                    </span>
                    <span v-if="arg.event.extendedProps['icons']" class="icons">
                        <StudipIcon v-for="icon of arg.event.extendedProps['icons']" :shape="icon" v-bind:key="icon"
                                    class="text-bottom"></StudipIcon>
                    </span>
                    <span class="fc-event-title">
                    {{ arg.event.title }}
                    </span>
                </span>
            </section>
            <div v-if="arg.event.display === 'auto' && ['dayGridMonth', 'dayGridYear', 'resourceTimelineWeek', 'resourceTimelineDay'].includes(arg.view.type)"
                 :style="{color: arg.event.textColor, backgroundColor: arg.event.backgroundColor, borderColor: arg.event.borderColor}"
                 :title="arg.event.title">
                <span v-if="['dayGridMonth', 'dayGridYear'].includes(arg.view.type)"
                      class="fc-event-time">
                    {{ arg.timeText }}
                </span>
                <span class="fc-event-title-container">
                    <span v-if="arg.event.extendedProps['icons']" class="icons">
                        <StudipIcon v-for="icon of arg.event.extendedProps['icons']" :shape="icon" v-bind:key="icon"
                                    class="text-bottom"></StudipIcon>
                    </span>
                    <span class="fc-event-title">
                    {{ arg.event.title }}
                    </span>
                </span>
            </div>
            <div v-if="arg.event.display === 'background'"
                 :title="arg.event.title">
                <div v-if="arg.event.extendedProps['generate-title']"
                     class="title">
                    {{ arg.event.title }}
                </div>
            </div>
        </template>
        <template v-slot:dayHeaderContent="arg: DayHeaderContentArg">
            <section>
                <div>{{ arg.text }}</div>
                <div>(Test-Feiertag)</div>
            </section>
        </template>
    </FullCalendar>
</template>
<script lang="ts">
import {defineComponent} from 'vue';
import FullCalendar from "@fullcalendar/vue3";
import {
    CalendarOptions,
    DateSelectionApi, DayHeaderContentArg,
    EventClickArg, EventDropArg, EventMountArg,
    EventSourceFuncArg
} from '@fullcalendar/core';
import interactionPlugin, {EventReceiveArg, EventResizeDoneArg} from '@fullcalendar/interaction';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import resourceTimelinePlugin from '@fullcalendar/resource-timeline';
import locale_de from '@fullcalendar/core/locales/de';
import locale_en_gb from '@fullcalendar/core/locales/en-gb';

import ColumnHeaderEvent from "../../assets/javascripts/lib/calendar";
import Dialog from "../../assets/javascripts/lib/dialog.js";
import { jsonapi } from "../../assets/javascripts/lib/jsonapi";
import {getLocale} from "../../assets/javascripts/lib/gettext";
import StudipIcon from "../components/StudipIcon.vue";

export default defineComponent({
    name: "StudipCalendar",
    components: {
        StudipIcon,
        FullCalendar
    },
    props: {
        config: {
            type: Object,
            required: true,
            default: () => ({})
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
    emits: {
        eventDropped: (payload: EventDropArg) => payload,
        eventReceived: (payload: EventReceiveArg) => payload
    },
    data() {
        //Make sure that defaults are set for the calendar:
        let calendar_options = this.config;
        //Add the plugins here so that users of this component
        //do not need to add them separately.
        //TODO: load plugins on demand, if possible
        calendar_options.plugins = [dayGridPlugin, timeGridPlugin, resourceTimelinePlugin, interactionPlugin];
        calendar_options.schedulerLicenseKey = 'GPL-My-Project-Is-Open-Source';
        //Fullcalendar needs a short version of the locale:
        let short_locale: string = getLocale();
        if (short_locale) {
            short_locale = short_locale.replace('_', '-');
        } else {
            short_locale = 'de-DE';
        }
        calendar_options.locales = [locale_de, locale_en_gb];
        calendar_options.locale = short_locale;

        //Set other fixed options:
        calendar_options.firstDay = 1;
        calendar_options.weekNumberCalculation = 'ISO';
        calendar_options.height = 'auto';
        calendar_options.contentHeight = 'auto';

        //Provide defaults for options that can be altered:
        if (!calendar_options.timeFormat) {
            calendar_options.timeFormat = 'H:mm';
        }
        if (!calendar_options.nowIndicator) {
            calendar_options.nowIndicator = true;
        }
        if (!calendar_options.slotMinTime) {
            calendar_options.slotMinTime = '08:00';
        }
        if (!calendar_options.slotMaxTime) {
            calendar_options.slotMaxTime = '20:00';
        }
        if (!calendar_options.initialDate) {
            calendar_options.initialDate = new Date();
        }
        if (!calendar_options.initialView) {
            calendar_options.initialView = 'timeGridWeek';
        }
        if (calendar_options.allDaySlot === undefined) {
            calendar_options.allDaySlot = false;
        }
        if (calendar_options.allDayText === undefined) {
            calendar_options.allDayText = '';
        }
        if (calendar_options.weekNumbers === undefined) {
            calendar_options.weekNumbers = true;
        }
        if (!calendar_options.header) {
            calendar_options.header = {
                start:  ['dayGridYear', 'dayGridMonth', 'timeGridWeek', 'timeGridDay'],
                center: ['title'],
                end:    ['prev', 'today', 'next']
            };
        }

        //Set the event handlers, if needed.
        if (calendar_options.editable) {
            calendar_options.eventDrop    = this.handleEventDrop;
            calendar_options.eventResize  = this.handleEventResize;
            if (calendar_options.selectable) {
                calendar_options.select = this.handleSelection;
            }
        }
        calendar_options.eventDidMount = this.handleEventMount;
        calendar_options.eventClick    = this.handleEventClick;
        calendar_options.eventReceive  = this.handleEventReceive;

        //Build the event sources:
        if (!calendar_options.eventSources) {
            calendar_options.eventSources = [];
        }
        if (this.display_holidays) {
            let holiday_source = function(arg: EventSourceFuncArg, successCallback: (events: Array<ColumnHeaderEvent>) => void, failureCallback: () => void) {
                const startYear = arg.start.getFullYear();
                const endYear   = arg.end.getFullYear();
                const requests: Array<Promise<Array<ColumnHeaderEvent>>> = [];
                let holiday_cache_str = sessionStorage.getItem('fullcalendar_holidays');
                let holiday_cache     = new Map<number, Array<object>>();
                if (holiday_cache_str != null) {
                    holiday_cache = new Map<number, Array<object>>(JSON.parse(holiday_cache_str));
                }
                for (let year = startYear; year <= endYear; year++) {
                    let existing_cache = holiday_cache.get(year);
                    if (existing_cache) {
                        return Promise.resolve(existing_cache);
                    }
                    let request = jsonapi.withPromises().GET('holidays', {
                        data: { 'filter[year]': year }
                    }).then(response => {
                        const events: Array<ColumnHeaderEvent> = [];
                        if (!response) {
                            return events;
                        }

                        for (const [date, data] of Object.entries(response)) {
                            const day = new Date(date);
                            events.push(new ColumnHeaderEvent(day, day, data.holiday, ['holiday', 'official']));
                        }

                        if (holiday_cache != null) {
                            holiday_cache.set(year, events);
                            sessionStorage.setItem('fullcalendar_holidays', JSON.stringify(Array.from(holiday_cache.entries())));
                        }

                        return events;
                    });
                    requests.push(request);
                }
                Promise.all(requests).then(results => {
                    let events: Array<ColumnHeaderEvent> = [];
                    for (let result of results) {
                        events = events.concat(result);
                    }
                    successCallback(events);
                    return results;
                }).catch(failureCallback);
            };
            calendar_options.eventSources.push(holiday_source);
        }
        if (this.display_vacations) {
            let vacation_source = function(arg: EventSourceFuncArg, successCallback: (events: Array<ColumnHeaderEvent>) => void, failureCallback: () => void) {
                const startYear = arg.start.getFullYear();
                const endYear = arg.end.getFullYear();
                const requests: Array<Promise<Array<ColumnHeaderEvent>>> = [];
                let vacation_cache_str = sessionStorage.getItem('fullcalendar_vacations');
                let vacation_cache = new Map<number, Array<object>>();
                if (vacation_cache_str != null) {
                    vacation_cache = new Map<number, Array<object>>(JSON.parse(vacation_cache_str));
                }
                for (let year = startYear; year <= endYear; year++) {
                    let existing_cache = vacation_cache.get(year);
                    if (existing_cache) {
                        return Promise.resolve(existing_cache);
                    }
                    let request = jsonapi.withPromises().get('vacations', {
                        data: {'filter[year]': year}
                    }).then(response => {
                        const events: Array<ColumnHeaderEvent> = [];
                        if (!response) {
                            return events;
                        }

                        for (const vacation_data of Object.values(response)) {
                            const start = new Date(parseInt(vacation_data.start) * 1000);
                            const end = new Date(parseInt(vacation_data.end) * 1000);
                            events.push(new ColumnHeaderEvent(start, end, vacation_data.name, ['holiday']));
                        }

                        if (vacation_cache != null) {
                            vacation_cache.set(year, events);
                            sessionStorage.setItem('fullcalendar_vacations', JSON.stringify(Array.from(vacation_cache.entries())));
                        }

                        return events;
                    });
                    requests.push(request);
                }
                Promise.all(requests).then(results => {
                    let events: Array<ColumnHeaderEvent> = [];
                    for (let result of results) {
                        events = events.concat(result);
                    }
                    successCallback(events);
                    return results;
                }).catch(failureCallback);
            };
            calendar_options.eventSources.push(vacation_source);
        }

        return {
            calendar_options: calendar_options as CalendarOptions
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
            this.$emit('eventDropped', drop_arg as EventDropArg);
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
                        all_day: resize_arg.event.allDay ? '1' : '0'
                    },
                    size: this.dialog_size
                }
            );
        },
        handleEventReceive(receive_arg: EventReceiveArg) {
            console.debug(receive_arg);
            if (receive_arg.event.startStr && receive_arg.event.endStr) {
                if (receive_arg.event.extendedProps.studip_api_urls.receive_dialog) {
                    Dialog.fromURL(
                        receive_arg.event.extendedProps.studip_api_urls.receive_dialog,
                        {
                            data: {
                                start:   receive_arg.event.startStr,
                                end:     receive_arg.event.endStr,
                                all_day: receive_arg.event.allDay ? '1' : '0'
                            }
                        }
                    );
                }
            }
            this.$emit('eventReceived', receive_arg);
        },
        handleEventMount: function(mount_arg: EventMountArg) {
            //If a background event with title shall be rendered, we have to check
            //if an all-day slot is present or not.
            mount_arg.el.setAttribute('title', mount_arg.event.title);
            mount_arg.event.setExtendedProp('generate-title', false);
            if (mount_arg.event.display === 'background' && mount_arg.event.title && mount_arg.event.allDay) {
                if (mount_arg.view.getOption('all-day') === true) {
                    //An all-day slot is present in the calendar.
                    if (mount_arg.isStart || mount_arg.isEnd) {
                        mount_arg.event.setExtendedProp('generate-title', true);
                    }
                } else {
                    //No all-day slot in the calendar. Display a title
                    //at the start of the day in the calendar.
                    if (!mount_arg.isStart || !mount_arg.isEnd) {
                        mount_arg.event.setExtendedProp('generate-title', true);
                    }
                }
            }
        }
    }
})
</script>
<style lang="scss">
@import '../../assets/stylesheets/fullcalendar';
</style>
