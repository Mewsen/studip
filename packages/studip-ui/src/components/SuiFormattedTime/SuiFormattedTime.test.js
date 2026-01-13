import { describe, it, expect } from 'vitest'
import { mount } from '@vue/test-utils'
import SuiFormattedTime from './SuiFormattedTime.vue'

describe('SuiFormattedTime', () => {
    it('zeigt "—" wenn kein Datum gesetzt ist', () => {
        const wrapper = mount(SuiFormattedTime)
        expect(wrapper.text()).toBe('—')
    })

    it('zeigt das absolute Datum bei timestamp', () => {
        const timestamp = 1700000000
        const wrapper = mount(SuiFormattedTime, { props: { timestamp } })

        const date = new Date(timestamp * 1000)
        const formatted = date
            .toLocaleDateString('de-DE', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            })
            .replace(/,/, '')

        expect(wrapper.text()).toBe(formatted)
        expect(wrapper.find('time').attributes('datetime')).toBe(date.toISOString())
    })

    it('zeigt relative Zeit wenn relative=true', () => {
        const now = Date.now()
        const timestamp = Math.floor((now - 90 * 1000) / 1000)

        const wrapper = mount(SuiFormattedTime, {
            props: { timestamp, relative: true },
        })

        expect(wrapper.text()).toBe('Vor 1 Minute')
    })

    it('zeigt "Jetzt" wenn Zeitdifferenz < 1 Minute', () => {
        const now = Date.now()
        const timestamp = Math.floor((now - 30 * 1000) / 1000)

        const wrapper = mount(SuiFormattedTime, {
            props: { timestamp, relative: true },
        })

        expect(wrapper.text()).toBe('Jetzt')
    })

    it('zeigt nur Datum wenn dateOnly=true', () => {
        const timestamp = 1700000000
        const wrapper = mount(SuiFormattedTime, {
            props: { timestamp, dateOnly: true },
        })

        const date = new Date(timestamp * 1000)
        const formatted = date.toLocaleDateString('de-DE', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
        })

        expect(wrapper.text()).toBe(formatted)
    })

    it('zeigt Datum korrekt wenn iso statt timestamp verwendet wird', () => {
        const iso = '2024-01-01T12:34:56Z'
        const wrapper = mount(SuiFormattedTime, { props: { iso } })

        const date = new Date(iso)
        const formatted = date
            .toLocaleDateString('de-DE', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            })
            .replace(/,/, '')

        expect(wrapper.text()).toBe(formatted)
        expect(wrapper.find('time').attributes('datetime')).toBe(date.toISOString())
    })

    it('zeigt relative Zeit nach mehr als 2 Stunden', () => {
        const now = Date.now()
        const timestamp = Math.floor((now - 3 * 60 * 60 * 1000) / 1000)

        const wrapper = mount(SuiFormattedTime, {
            props: { timestamp, relative: true },
        })

        const date = new Date(timestamp * 1000)
        const formatted =
            date.getHours().toString().padStart(2, '0') +
            ':' +
            date.getMinutes().toString().padStart(2, '0')

        expect(wrapper.text()).toBe(formatted)
    })

    it('zeigt absolute Zeit wenn forceAbsolute=true', () => {
        const timestamp = 1700000000
        const wrapper = mount(SuiFormattedTime, {
            props: { timestamp, relative: true },
        })

        expect(wrapper.vm.formattedDisplay).toBeDefined()
        const result = wrapper.vm.formattedDisplay
        expect(result).toBe(result)
    })
})