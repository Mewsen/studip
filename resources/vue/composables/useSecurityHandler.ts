import {onBeforeMount, onUnmounted} from 'vue';
import {$gettext} from '../../assets/javascripts/lib/gettext';

type PredicateType = () => boolean;
type UseSecurityHandlerReturn = {
    setPredicate: (predicate: PredicateType) => void;
    toggle: (state?: boolean|null) => void;
    activate: () => void;
    deactivate: () => void;
    registerGlobalHandler: () => void,
    unregisterGlobalHandler: () => void
};

let instances: Array<SecurityHandler> = [];
let isGlobalHandlerRegistered = false;

function securityHandler(event: BeforeUnloadEvent): void|string {
    if (instances.some((instance: SecurityHandler) => instance.shouldWarn())) {
        event.preventDefault();
        event.returnValue = $gettext('Ihre Eingaben wurden bislang noch nicht gespeichert.');
        return event.returnValue;
    }
}

function registerGlobalHandler(): void {
    if (!isGlobalHandlerRegistered) {
        window.addEventListener('beforeunload', securityHandler);

        isGlobalHandlerRegistered = true;
    }
}

function unregisterGlobalHandler(): void {
    if (isGlobalHandlerRegistered && instances.length === 0) {
        window.removeEventListener('beforeunload', securityHandler);

        isGlobalHandlerRegistered = false;
    }
}

class SecurityHandler
{
    isEnabled: boolean = true;
    predicate: PredicateType = () => true;

    constructor(predicate: PredicateType|null = null) {
        if (predicate !== null) {
            this.predicate = predicate;
        }
    }

    setPredicate(predicate: PredicateType): void
    {
        this.predicate = predicate;
    }

    shouldWarn(): boolean {
        return this.isEnabled && this.predicate();
    }

    enable(): void {
        this.isEnabled = true;
    }

    disable(): void {
        this.isEnabled = false;
    }

    toggle(state: boolean|null = null): void {
        this.isEnabled = state ?? !this.isEnabled;
    }
}

export function useSecurityHandler(
    predicate: PredicateType|null = null,
    standalone: boolean = false
): UseSecurityHandlerReturn {
    const instance = new SecurityHandler(predicate);
    instances.push(instance);

    if (!standalone) {
        onBeforeMount(() => registerGlobalHandler());

        onUnmounted(() => {
            instances = instances.filter((i: SecurityHandler) => instance !== i);
            unregisterGlobalHandler();
        });
    }

    return {
        setPredicate(predicate: PredicateType): void {
            instance.setPredicate(predicate);
        },
        toggle(state: boolean|null = null): void {
            instance.toggle(state);
        },
        activate(): void {
            instance.toggle(true);
        },
        deactivate(): void {
            instance.toggle(false);
        },
        registerGlobalHandler,
        unregisterGlobalHandler
    };
}
