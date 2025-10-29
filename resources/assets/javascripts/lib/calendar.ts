/**
 * The ColumnHeaderEvent class represents an event that spans
 * at least one day and whose title is displayed in the calendar
 * column header.
 */
class ColumnHeaderEvent {
    /**
     * The start date of the event.
     */
    start: Date;

    /**
     * The end date of the event.
     */
    end:   Date;

    /**
     * The title of the event.
     */
    title: string;

    /**
     * Extra CSS classes that shall be added to the HTML element for the event.
     */
    class_names: Array<string>;

    constructor(start: Date, end: Date, title: string, class_names: Array<string> = []) {
        this.start       = start;
        this.end         = end;
        this.title       = title;
        this.class_names = class_names;
    }
}

export default ColumnHeaderEvent;
