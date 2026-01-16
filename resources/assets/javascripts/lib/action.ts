/**
 * The Action class represents an action in Stud.IP with its usual attributes.
 */
class Action
{
    /**
     * The URL for the action.
     */
    url: string;

    label: string;

    icon_name: string;

    constructor(
        url: string,
        label: string,
        icon_name: string = ''
        ) {
        this.url        = url;
        this.label      = label;
        this.icon_name  = icon_name;
    }
}

export {Action};
