// SuiIcon.test.js
import { vi } from 'vitest'

window.fetch = vi.fn().mockResolvedValue({
    text: () => Promise.resolve('<svg><symbol id="icon-mocked"></symbol></svg>'),
    ok: true,
})
window.__STUDIP_ICON_SPRITE_LOADED__ = true

window.STUDIP = {
    URLHelper: {
        getURL: vi.fn((path) => `http://mocked-url/${path}`),
    },
}

import { mount } from '@vue/test-utils'
import SuiIcon from './SuiIcon.vue'
import { describe, it, expect, beforeEach } from 'vitest'

beforeEach(() => {
    vi.clearAllMocks()
    window.__STUDIP_ICON_SPRITE_LOADED__ = true
})

describe('SuiIcon.vue', () => {
    const renderAndWait = (props = {}) => {
        const wrapper = mount(SuiIcon, { props })
        return wrapper.vm.$nextTick().then(() => wrapper)
    }

    it('soll das SVG rendern, wenn Sprite geladen wurde', async () => {
        const wrapper = mount(SuiIcon, {
            props: { shape: 'courseware' },
        })

        await wrapper.vm.$nextTick()

        expect(wrapper.find('svg').exists()).toBe(true)

        expect(window.fetch).not.toHaveBeenCalled()
    })

    it('soll fetch aufrufen, wenn der Sprite-Lade-Flag nicht gesetzt ist', async () => {
        window.__STUDIP_ICON_SPRITE_LOADED__ = false
        document.getElementById = vi.fn().mockReturnValue(null)

        const wrapper = mount(SuiIcon, {
            props: { shape: 'courseware' },
        })

        await wrapper.vm.$nextTick()
        expect(window.fetch).toHaveBeenCalled()

        await wrapper.vm.$nextTick()
        expect(window.__STUDIP_ICON_SPRITE_LOADED__).toBe(true)

        await wrapper.vm.$nextTick()
        expect(wrapper.find('svg').exists()).toBe(true)
    })

    it('soll Größe, Klasse und href korrekt aus Props anwenden', async () => {
        const wrapper = await renderAndWait({
            shape: 'check',
            size: 42,
            class: 'my-custom-class',
            inline: true,
        })
        const svg = wrapper.find('svg')

        expect(svg.attributes('width')).toBe('42px')
        expect(svg.attributes('height')).toBe('42px')
        expect(svg.attributes('class')).toContain('sui-icon--inline')
        expect(svg.attributes('class')).toContain('my-custom-class')
        expect(wrapper.find('use').attributes('href')).toBe('#icon-check')
    })

    it('soll als informativ (role=img) markiert werden, wenn ariaLabel gesetzt ist', async () => {
        const wrapper = await renderAndWait({ shape: 'user', ariaLabel: 'Benutzer-Icon' })
        const svg = wrapper.find('svg')

        expect(svg.attributes('aria-hidden')).toBe('false')
        expect(svg.attributes('role')).toBe('img')
        expect(svg.attributes('aria-label')).toBe('Benutzer-Icon')
    })

    it('soll als dekorativ (aria-hidden=true) markiert werden, wenn ariaLabel ein Leerstring ist', async () => {
        const wrapper = await renderAndWait({ shape: 'user', ariaLabel: ' ' })
        const svg = wrapper.find('svg')

        expect(svg.attributes('aria-hidden')).toBe('true')
        expect(svg.attributes('role')).toBeUndefined()
    })

    it('soll die Farbe mit einem gültigen HEX-Wert überschreiben', async () => {
        const wrapper = await renderAndWait({ shape: 'color-hex', hex: '#AABBCC', role: 'attention' })
        const svg = wrapper.find('svg')

        // Vitest wandelt die Farbe in rgb() um
        expect(svg.attributes('style')).toContain('color: rgb(170, 187, 204);')
    })

    it('soll die Farbe basierend auf einem gültigen ROLE setzen', async () => {
        const wrapper = await renderAndWait({ shape: 'color-role', role: 'attention' })
        const svg = wrapper.find('svg')

        expect(svg.attributes('style')).toContain('color: var(--color--warning);')
    })

    it('soll die Standardfarbe setzen, wenn role ungültig ist und kein hex gesetzt ist', async () => {
        const wrapper = await renderAndWait({ shape: 'default-color', role: 'ungueltige-rolle' })
        const svg = wrapper.find('svg')

        const styleAttr = svg.attributes('style')

        if (styleAttr) {
            expect(styleAttr).toContain('color: var(--color--font-primary);')
        }
        else {
            expect(styleAttr).toBeUndefined()
        }
    })
})
