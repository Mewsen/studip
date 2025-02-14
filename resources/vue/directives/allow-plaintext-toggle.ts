import {$gettext} from "../../assets/javascripts/lib/gettext";
import {DirectiveBinding, nextTick} from "vue";

const messages = {
    0: $gettext('Passwort verstecken'),
    1: $gettext('Passwort anzeigen'),
};

function initialize(el: HTMLElement): void {
    if (!el.parentElement) {
        return;
    }

    el.classList.add('allow-plaintext-toggle');

    el.parentElement.style.position = 'relative';

    const bbox = el.getBoundingClientRect();
    const parentBbox = el.parentElement.getBoundingClientRect();

    const x = (bbox.x - parentBbox.x) + bbox.width - 26;
    const y = (bbox.y - parentBbox.y) + bbox.height / 2;

    const toggle = document.createElement('button');
    toggle.setAttribute('type', 'button');
    toggle.classList.add('as-link', 'password-display-toggle', 'password-is-hidden');
    toggle.setAttribute('title', messages[1]);
    toggle.innerText = messages[1];
    toggle.style.top = `${y}px`;
    toggle.style.left = `${x}px`;

    toggle.addEventListener('click', (event) => {
        const isHidden = el.getAttribute('type') === 'text';
        toggle.classList.toggle('password-is-hidden', isHidden);
        toggle.setAttribute('title', messages[isHidden ? 1 : 0]);
        toggle.innerText = messages[isHidden ? 1 : 0];

        el.setAttribute('type', isHidden ? 'password' : 'text');

        event.preventDefault();
    });

    el.after(toggle);
}

export default (el: HTMLElement, binding?: DirectiveBinding | undefined): void => {
    if (el.nextElementSibling?.matches('button.password-display-toggle')) {
        return;
    }

    const promise = binding ? nextTick() : Promise.resolve();
    promise.then(() => initialize(el))
}
