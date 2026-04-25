import {$gettext} from "./gettext";

let uniqueId: number = 0;

class CollapsibleList
{
    static instances = new WeakMap<HTMLElement, CollapsibleList>();

    static fromNode(node: HTMLElement): CollapsibleList
    {
        return CollapsibleList.instances.get(node) ?? new CollapsibleList(node);
    }

    #node: HTMLElement;
    #data: DOMStringMap;

    #label: string = $gettext('Einträge');
    #max: number = 5;
    #hiddenItems: Array<HTMLElement> = [];

    #button: HTMLButtonElement | null = null;

    constructor(node: HTMLElement) {
        if (!node.matches('ol,ul')) {
            throw new Error('Not applicable for anything else than ol/ul lists');
        }

        if (CollapsibleList.instances.has(node)) {
            throw new Error('CollapsibleList already initialized');
        }

        CollapsibleList.instances.set(node, this);

        this.#node = node;
        this.#data = node.dataset;

        this.#label = this.#data.label ?? this.#label;
        this.#max = Number.parseInt(this.#data.collapseAfter ?? String(this.#max), 10);

        this.#node.classList.add('collapsible-list');

        const items = Array.from(this.#node.children);
        this.#hiddenItems = items.slice(this.#max) as Array<HTMLElement>;

        if (this.#hiddenItems.length > 0) {
            this.#addButton();
        }
    }

    #clickHandler = (): void => {
        this.update(this.#button?.getAttribute('aria-expanded') === 'true');
    }

    #addButton(): void
    {
        const id = this.#node.getAttribute('id') ?? `collapsible-list-${uniqueId++}`;
        this.#node.setAttribute('id', id);

        this.#button = Object.assign(document.createElement('button'), {
            className: 'as-link',
            textContent: '',
            type: 'button',
        });
        this.#button.setAttribute('aria-controls', id);

        this.#button.addEventListener('click', this.#clickHandler);

        this.#node.after(this.#button);

        this.update(true);
    }

    update(collapsed: boolean): void
    {
        if (this.#button === null) {
            return;
        }

        this.#button.textContent = collapsed
            ? $gettext('%{count} weitere %{label} anzeigen', {count: String(this.#hiddenItems.length), label: this.#label})
            : $gettext('weniger %{label} anzeigen', {label: this.#label});
        this.#button.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        this.#hiddenItems.forEach((li: HTMLElement) => {
            li.style.display = collapsed ? 'none' : '';
            li.toggleAttribute('hidden', collapsed);
        });
    }

    destroy(): void {
        this.update(false);

        this.#button?.removeEventListener('click', this.#clickHandler);
        this.#button?.remove();
        this.#node.classList.remove('collapsible-list');

        CollapsibleList.instances.delete(this.#node);
    }
}

export default CollapsibleList;
