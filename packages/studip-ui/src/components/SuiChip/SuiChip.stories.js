import SuiChip from './SuiChip.vue'
import { ref } from 'vue'
import { userEvent, within } from 'storybook/test'

const meta = {
    title: 'Stud.IP UI Elements/Chip',
    component: SuiChip,
    tags: ['autodocs', 'since:6.2.0', 'new', 'alpha'],
    parameters: {
        docs: {
            description: {
                component: 
                    'Der `SuiChip` dient zur **klaren und kompakten Darstellung** von Labels, Tags oder Statusinformationen innerhalb von UI-Elementen.' + 
                    'Er unterstützt optionale Icons zur visuellen Unterscheidung sowie eine Entfernungsfunktion zur Interaktion.\n\n' +
                    
                    '#### Kernfunktionen:\n\n' +
                    
                    '* **Flexibles Labeling:** Zeigt primäre Informationen über die `label`-Prop an.\n\n' +
                    '* **Visuelle Personalisierung:** Die Farbe kann entweder über den thematischen `color`-Namen oder einen spezifischen `hex`-Code gesteuert werden.\n\n' +
                    '* **Interaktion:** Kann über die `removable`-Prop zu einem interaktiven Element erweitert werden, das ein `remove`-Event emittiert, wenn es geklickt wird.\n\n' +
                    '* **Zustände:** Unterstützt den `disabled`-Zustand, um jegliche Interaktion zu blockieren.\n\n' +
                    
                    '#### Verwendung und Best Practices:\n\n' +
                    
                    'Verwenden Sie Chips, um ausgewählte Filter, Benutzer-Tags oder kurze Statusmeldungen darzustellen.\n\n' +
                    '**Achtung:** Der Chip entfernt sich bei `remove` nicht selbstständig, sondern signalisiert der Elternkomponente, dass er entfernt werden soll.'
                ,
            },
        },
    },
    argTypes: {
        color: {
            description: 'Hintergrundfarbe als DesignToken, z. B. `--color--blue-1`',
            control: 'text',
        },
        disabled: {
            description: 'Deaktiviert den Chip',
            control: 'boolean',
        },
        removable: {
            description: 'Zeigt einen Löschbutton an',
            control: 'boolean',
        },
        label: {
            description: 'Label des Chips',
            control: 'text',
        },
        remove: {
            action: 'remove',
            description: 'Wird ausgelöst, wenn der Benutzer auf den Entfernen-Button klickt.',
            table: {
                category: 'Events',
                type: { summary: null },
            },
        },
    },
}

export default meta

export const Default = {
    name: 'Standard',
    args: {
        color: 'blue-1',
        label: 'Blauer Chip',
    },
}

export const Color = {
    name: 'Farbangabe über Namen',
    tags: [],
    args: {
        color: 'green-1',
        label: 'Grüner Chip',
    },
}

export const Hex = {
    name: 'Farbangabe über HEX-Code',
    tags: [],
    args: {
        hex: '#00689f',
        label: 'Hex Chip',
    },
}

export const Icon = {
    name: 'Chip mit Icon',
    tags: [],
    args: {
        color: 'green-1',
        label: 'Approved',
        icon: 'accept',
    },
}

export const RemovableChip = {
    name: 'Löschbarer Chip',
    args: {
        label: 'Löschbarer Chip',
        removable: true,
        color: 'red-1',
        reset: () => {},
    },
    render: (args) => ({
        components: { SuiChip },
        setup() {
            const isVisible = ref(true)

            const handleRemove = () => {
                isVisible.value = false
                args.remove()
            }

            const reset = () => {
                isVisible.value = true
            }

            args.reset = reset

            return { args, isVisible, handleRemove }
        },
        template: `
            <div style="min-height: 50px;">
                <SuiChip 
                    v-if="isVisible" 
                    v-bind="args" 
                    @remove="handleRemove" 
                />
                <p v-else style="color: grey;">Chip wurde entfernt.</p>
            </div>
        `,
    }),
    play: async ({ canvasElement, storyContext }) => {
        if (storyContext.args.reset) {
            storyContext.args.reset()
        }

        await new Promise((resolve) => setTimeout(resolve, 50))

        const canvas = within(canvasElement)
        const removeButton = await canvas.findByRole('button', { name: 'Entfernen' })

        await userEvent.click(removeButton)
    },
}
