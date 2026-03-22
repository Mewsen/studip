import {$gettext} from "@/assets/javascripts/lib/gettext";

(() => {
    let uniqueId = 0;

    STUDIP.ready(() => {
        document.querySelectorAll('.collapsible-list').forEach(list => {
            if (!list.matches('ol,ul')) {
                console.error('Not applicable for anything else than ol/ul lists');
            }

            const data = list.dataset;

            if (data.collapsibleListInitialized !== undefined) {
                return;
            }

            data.collapsibleListInitialized = true;

            const label = data.label ?? $gettext('Einträge');
            const max = Number.parseInt(data.collapseAfter ?? 5, 10);
            const items = Array.from(list.children);
            const hiddenItems = items.slice(max);
            const hiddenCount = hiddenItems.length;

            if (hiddenCount === 0) {
                return;
            }

            const id = list.getAttribute('id') ?? `collapsable-list-${uniqueId++}`;
            list.setAttribute('id', id);

            const button = Object.assign(document.createElement('button'), {
                className: 'as-link',
                textContent: '',
                type: 'button',
            });
            button.setAttribute('aria-controls', id);

            const update = (collapsed) => {
                button.textContent = collapsed
                    ? $gettext('%{count} weitere %{label} anzeigen', {count: hiddenCount, label})
                    : $gettext('weniger %{label} anzeigen', {label});
                button.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                hiddenItems.forEach(li => li.toggleAttribute('hidden', collapsed));
            };

            update(true);

            button.addEventListener('click', () => {
                update(button.getAttribute('aria-expanded') === 'true');
            });

            list.after(button);
        });
    });
})();
