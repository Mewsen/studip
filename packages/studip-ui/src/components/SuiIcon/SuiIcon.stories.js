// stories/SUIIcon.stories.js
import SuiIcon from './SuiIcon.vue'
import allIcons from '../../../dist/assets/images/icons/icons-list.js'

const meta = {
    title: 'Stud.IP UI Elements/Icon',
    component: SuiIcon,
    tags: ['autodocs', 'since:6.2.0', 'new', 'beta'],
    parameters: {
        docs: {
            description: {
                component:
                    'Die **`SuiIcon`** Komponente dient zur **einheitlichen Darstellung von Icons** gemäß den Stud.IP-Konventionen. ' +
                    'Sie verwendet SVG-Sprites für eine effiziente und skalierbare Icon-Darstellung.\n\n' +
                    '* **SVG-Sprites:** Die Komponente rendert Icons als **SVG `<use>`-Elemente**, die auf vordefinierte SVG-Sprites verweisen. Dies ermöglicht eine konsistente und performante Icon-Darstellung.\n' +
                    '* **Barrierefreiheit:** Die Komponente unterstützt ARIA-Attribute wie `aria-label` für eine bessere Zugänglichkeit. Wenn kein `aria-label` angegeben ist, wird das Icon als dekorativ betrachtet und mit `aria-hidden="true"` markiert.\n\n'+ 

                    '**Achtung:** Diese Komponente rendert nur das Icon selbst. Es wird keine interaktive Funktionalität (z. B. Button, Input etc.) bereitgestellt. '
                ,
            },
        },
    },
    argTypes: {
        shape: {
            control: { type: 'select' },
            options: allIcons,
            description: 'Name des Icons aus dem Sprite',
        },
        size: { control: 'number', description: 'Größe in Pixel' },
        inline: { control: 'boolean', description: 'Ob das Icon inline dargestellt wird' },
        role: {
            control: {
                type: 'select',
            },
            options: [
                'accept',
                'attention',
                'info_alt',
                'inactive',
                'new',
                'status-green',
                'status-red',
                'status-yellow',
                'clickable',
                '',
            ],

            description: 'Farbrolle des Icons, wird nur angewendet wenn `hex` nicht gesetzt ist',
        },
        hex: {
            control: 'color',
            description: 'Exakte Farbe als Hex-Wert, überschreibt `iconRole`',
        },
        ariaLabel: { control: 'text', description: 'Optionaler ARIA-Label Text für das Icon' },
        class: { control: 'text', description: 'Zusätzliche CSS-Klasse für das Icon' },
    },
}
export default meta

export const Default = {
    args: {
        shape: 'seminar',
        size: 48,
        hex: '#28497c',
        ariaLabel: 'Stud.IP Seminar Icon',
        inline: false,
    },
}

export const InlineIcon = {
    args: {
        shape: 'accept',
        size: 48,
        inline: true,
        role: 'accept',
    },
}

export const DecorativeIcon = {
    args: {
        shape: 'info',
        size: 48,
        inline: false,
        ariaLabel: '',
    },
}

export const RoleColors = {
    render: () => ({
        components: { SuiIcon },
        template: `
            <div style="display:grid; grid-template-columns: 220px 2fr; gap:1rem; align-items:center;">
                <span>clickable</span><SuiIcon shape="seminar" role="clickable" />
                <span>accept</span><SuiIcon shape="seminar" role="accept" />
                <span>new</span><SuiIcon shape="seminar" role="new" />
                <span>attention</span><SuiIcon shape="seminar" role="attention" />
                <span>inactive</span><SuiIcon shape="seminar" role="inactive" />
            </div>
        `,
    }),
}

export const HexColors = {
    render: () => ({
        components: { SuiIcon },
        template: `
            <div style="display:grid; grid-template-columns: 220px 2fr; gap:1rem; align-items:center;">
                <span>#0C8EF4</span><SuiIcon shape="community" hex="#0C8EF4" />
                <span>#A18EE5</span><SuiIcon shape="community" hex="#A18EE5" />
                <span>#A162A1</span><SuiIcon shape="community" hex="#A162A1" />
                <span>#2B384C</span><SuiIcon shape="community" hex="#2B384C" />
                <span>#0C287B</span><SuiIcon shape="community" hex="#0C287B" />
                <span>#C7965F</span><SuiIcon shape="community" hex="#C7965F" />
            </div>
        `,
    }),
}
