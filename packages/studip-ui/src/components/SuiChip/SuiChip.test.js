import { mount, config } from '@vue/test-utils'
import { describe, it, expect } from 'vitest' 
import SuiChip from './SuiChip.vue' 

config.global.stubs = {
    SuiIcon: {
        template: '<div data-testid="sui-icon" :data-shape="$attrs.shape">Mocked Icon</div>',
        props: ['size', 'inline', 'role', 'class'] 
    }
}

describe('SuiChip.vue', () => {

    it('soll das Label korrekt rendern und die Basis-Klasse anwenden', () => {
        const wrapper = mount(SuiChip, {
            props: { label: 'Test-Chip' }
        })

        expect(wrapper.classes()).toContain('sui-chip')
        expect(wrapper.text()).toContain('Test-Chip')
    })


    it('soll das Icon rendern, wenn die "icon" Prop gesetzt ist', () => {
        const wrapper = mount(SuiChip, {
            props: { label: 'Mit Icon', icon: 'settings' }
        })

        const icon = wrapper.find('[data-testid="sui-icon"]')
        
        expect(icon.exists()).toBe(true)
        expect(icon.attributes('data-shape')).toBe('settings')
    })

    it('soll KEIN Icon rendern, wenn die "icon" Prop nicht gesetzt ist', () => {
        const wrapper = mount(SuiChip, {
            props: { label: 'Ohne Icon', icon: '' }
        })

        expect(wrapper.findAll('[data-testid="sui-icon"]').length).toBe(0) 
    })


    it('soll den Entfernen-Button rendern, wenn "removable" true ist', () => {
        const wrapper = mount(SuiChip, {
            props: { label: 'Löschbar', removable: true }
        })

        const button = wrapper.find('button.sui-chip--button-remove')
        
        expect(button.exists()).toBe(true)
        
        const buttonIcon = button.find('[data-shape="decline"]')
        expect(buttonIcon.exists()).toBe(true)
    })

    it('soll ein "remove" Event emittieren, wenn der Button geklickt wird', async () => {
        const wrapper = mount(SuiChip, {
            props: { label: 'Klick mich', removable: true }
        })

        await wrapper.find('button').trigger('click')

        expect(wrapper.emitted('remove')).toBeTruthy()
        expect(wrapper.emitted('remove').length).toBe(1)
    })

    it('soll den Entfernen-Button NICHT rendern, wenn "removable" false ist', () => {
        const wrapper = mount(SuiChip, {
            props: { label: 'Nicht löschbar', removable: false }
        })

        expect(wrapper.find('button').exists()).toBe(false)
    })


    it('soll die Klasse "disabled" anwenden und den Entfernen-Button ausblenden, wenn "disabled" true ist', () => {
        const wrapper = mount(SuiChip, {
            props: { label: 'Deaktiviert', disabled: true, removable: true }
        })

        expect(wrapper.classes()).toContain('disabled')
        
        expect(wrapper.find('button').exists()).toBe(false)
    })

    it('soll KEINE "remove" Events emittieren, wenn disabled ist (zusätzlicher Check)', async () => {
        const wrapper = mount(SuiChip, {
            props: { label: 'Deaktiviert', disabled: true, removable: true }
        })
        
        await wrapper.trigger('click') 

        expect(wrapper.emitted('remove')).toBeUndefined()
    })


    it('soll die Hintergrundfarbe basierend auf der "hex" Prop setzen', () => {
        const wrapper = mount(SuiChip, { 
            props: { label: 'Hex', hex: '#FF00FF', color: 'red' }
        })
        
        expect(wrapper.attributes('style')).toContain('background-color: rgb(255, 0, 255);')
    })
    
    it('soll die Hintergrundfarbe basierend auf der "color" Prop setzen', () => {
        const wrapper = mount(SuiChip, { 
            props: { label: 'Color Var', color: 'success-color' } 
        })
        
        expect(wrapper.attributes('style')).toContain('background-color: var(--color--success-color);')
    })

    it('soll die Standard-Hintergrundfarbe setzen, wenn weder hex noch color gesetzt sind', () => {
        const wrapper = mount(SuiChip, { 
            props: { label: 'Default' } 
        })
        
        expect(wrapper.attributes('style')).toContain('background-color: var(--color--font-primary);')
    })
})