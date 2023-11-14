import { $gettext } from '../../../../../assets/javascripts/lib/gettext';

export enum ProcessStatus {
    Before = 'before',
    After = 'after',
    Active = 'active',
}

export interface StatusDescriptor {
    status: ProcessStatus;
    shape: string;
    role: string;
    description: string;
}

interface StringDict {
    [key: string]: string;
}

export interface JsonApiSchema {
    id?: string;
    type: string;
    attributes: StringDict;
    meta?: StringDict;
    relationships?: StringDict;
}

export function getProcessStatus(process: JsonApiSchema): StatusDescriptor {
    const now = new Date();
    const startDate = new Date(process.attributes['review-start']);
    const endDate = new Date(process.attributes['review-end']);

    if (now < startDate) {
        return {
            status: ProcessStatus.Before,
            shape: 'span-empty',
            role: 'status-yellow',
            description: $gettext('Peer-Review-Process noch nicht aktiv'),
        };
    }

    if (endDate < now) {
        return {
            status: ProcessStatus.After,
            shape: 'span-full',
            role: 'status-red',
            description: $gettext('Peer-Review-Process beendet'),
        };
    }

    return {
        status: ProcessStatus.Active,
        shape: 'span-empty',
        role: 'status-green',
        description: $gettext('Peer-Review-Prozess aktiv'),
    };
}
