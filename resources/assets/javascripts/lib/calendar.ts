import {EventImpl} from "@fullcalendar/core/internal";
import {CalendarOptions, PluginDef} from "@fullcalendar/core";
import {getLocale} from "./gettext";
import locale_de from "@fullcalendar/core/locales/de";
import locale_en_gb from "@fullcalendar/core/locales/en-gb";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import resourceTimelinePlugin from "@fullcalendar/resource-timeline";
import interactionPlugin from "@fullcalendar/interaction";

/**
 * The EventURLParameters class represents URL parameters for calendar events
 * that are passed to Stud.IP controllers to do something with the event.
 */
class EventURLParameters
{
    /**
     * The start date of the event.
     */
    start:        Date | null;

    /**
     * The end date of the event.
     */
    end:          Date | null;

    /**
     * Whether the event is an all-day event (true) or not (false).
     */
    all_day:      boolean;

    /**
     * The optional resource-ID of the event. In the Stud.IP context, this can be the same
     * as the ID of a Stud.IP resource.
     */
    resource_id: string | null;

    /**
     * Constructs an instance using a Fullcalendar Event object.
     *
     * @param event The Fullcalendar event object to construct URL parameters from.
     */
    constructor(event?: EventImpl) {
        this.resource_id = null;
        if (event) {
            this.start   = event.start;
            this.end     = event.end;
            this.all_day = event.allDay;
        } else {
            this.start   = null;
            this.end     = null;
            this.all_day = false;
        }
    }

    setStart(start: Date) {
        this.start = start;
    }

    setEnd(end: Date) {
        this.end = end;
    }

    setAllDay(all_day: boolean) {
        this.all_day = all_day;
    }

    setResourceId(resource_id: string) : void {
        this.resource_id = resource_id;
    }

    /**
     * Converts the parameters to a plain JavaScript object to be used
     * with the existing code in Stud.IP to fire requests or to open a dialog.
     */
    toObject(): object {
        return {
            start:       this.start ? this.start.toISOString() : null,
            end:         this.end ? this.end.toISOString() : null,
            all_day:     this.all_day ? '1' : '0',
            resource_id: this.resource_id,
        };
    }
}

/**
 * The StudipCalendarConfig class provides default values for the Fullcalendar
 * configuration that do not need to be set for each calendar instance.
 */
class StudipCalendarConfig
{
    fullcalendar_config: CalendarOptions = {};

    constructor(config: CalendarOptions) {
        this.fullcalendar_config = config;

        //Set fixed options:
        this.fullcalendar_config.firstDay = 1;
        this.fullcalendar_config.weekNumberCalculation = 'ISO';
        this.fullcalendar_config.height = 'auto';
        this.fullcalendar_config.contentHeight = 'auto';
        this.fullcalendar_config.schedulerLicenseKey = 'GPL-My-Project-Is-Open-Source';
        this.fullcalendar_config.timeZone = 'local';

        const all_views: Set<string> = new Set<string>();
        if (this.fullcalendar_config.views) {
            for (const view of Object.keys(this.fullcalendar_config.views)) {
                all_views.add(view);
            }
        }
        if (this.fullcalendar_config.initialView) {
            all_views.add(this.fullcalendar_config.initialView);
        }
        //Load the plugins according to the views, if they are not
        //explicitly set:
        if (!this.fullcalendar_config.plugins) {
            //Add the plugins here so that users of this component
            //do not need to add them separately.
            const active_plugins: Array<PluginDef> = [interactionPlugin];
            for (const view_name of all_views) {
                if (view_name === 'dayGridMonth') {
                    active_plugins.push(dayGridPlugin);
                } else if (view_name === 'timeGridWeek' || view_name === 'timeGridDay') {
                    active_plugins.push(timeGridPlugin);
                } else if (view_name === 'resourceTimelineWeek'
                    || view_name === 'resourceTimelineDay') {
                    active_plugins.push(resourceTimelinePlugin)
                }
            }
            this.fullcalendar_config.plugins = active_plugins;
        }

        //Fullcalendar needs a short version of the locale:
        let short_locale: string = getLocale();
        if (short_locale) {
            short_locale = short_locale.replace('_', '-');
        } else {
            short_locale = 'de-DE';
        }
        this.fullcalendar_config.locales = [locale_de, locale_en_gb];

        //Make sure the event sources item is an array:
        if (!this.fullcalendar_config.eventSources) {
            this.fullcalendar_config.eventSources = [];
        }

        //Provide defaults for options that can be altered:
        if (!this.fullcalendar_config.eventTimeFormat) {
            this.fullcalendar_config.eventTimeFormat = {
                hour12: false,
                hour:   '2-digit',
                minute: '2-digit'
            };
        }
        if (!this.fullcalendar_config.slotLabelFormat) {
            this.fullcalendar_config.slotLabelFormat = {
                hour: 'numeric',
                minute: '2-digit',
                omitZeroMinute: false
            };
        }

        if (!this.fullcalendar_config.nowIndicator) {
            this.fullcalendar_config.nowIndicator = true;
        }
        if (!this.fullcalendar_config.slotMinTime) {
            this.fullcalendar_config.slotMinTime = '08:00';
        }
        if (!this.fullcalendar_config.slotMaxTime) {
            this.fullcalendar_config.slotMaxTime = '20:00';
        }
        if (!this.fullcalendar_config.initialDate) {
            this.fullcalendar_config.initialDate = new Date();
        }
        if (!this.fullcalendar_config.initialView && all_views.has('timeGridWeek')) {
            //At the moment, there is only one good default for timeGridWeek.
            this.fullcalendar_config.initialView = 'timeGridWeek';
        }
        if (this.fullcalendar_config.allDaySlot === undefined) {
            this.fullcalendar_config.allDaySlot = false;
        }
        if (this.fullcalendar_config.allDayText === undefined) {
            this.fullcalendar_config.allDayText = '';
        }
        if (this.fullcalendar_config.weekNumbers === undefined) {
            this.fullcalendar_config.weekNumbers = true;
        }
        if (this.fullcalendar_config.headerToolbar) {
            //Check if the start and end item in the toolbar is empty.
            //In that case, navigation is not desired because it is
            //a semester calendar.
            const toolbar = this.fullcalendar_config.headerToolbar;
            if ((!toolbar.start || toolbar.start.length < 1)
                && (!toolbar.end || toolbar.end.length < 1)
                && this.fullcalendar_config.views) {

                //No navigation items present and no day header format set.
                //This is a semester calendar. Deactivate the display of
                //specific dates and display only the day(s) of the week.
                if (this.fullcalendar_config.views.timeGridWeek) {
                    this.fullcalendar_config.views.timeGridWeek.dayHeaderFormat = {
                        weekday: 'long'
                    };
                }
                if (this.fullcalendar_config.views.timeGridDay) {
                    this.fullcalendar_config.views.timeGridDay.dayHeaderFormat = {
                        weekday: 'long'
                    };
                }
            }
        } else {
            //No toolbar defined. Define a standard one:
            this.fullcalendar_config.headerToolbar = {
                start:  Array.from(all_views).join(','),
                center: 'title',
                end:    'prev,today,next'
            };
        }
    }

    getConfig() : CalendarOptions {
        return this.fullcalendar_config;
    }
}

export {EventURLParameters, StudipCalendarConfig};
