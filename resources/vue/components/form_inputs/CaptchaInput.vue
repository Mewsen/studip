<template>
    <div class="formpart">
        <p>{{ $gettext('An dieser Stelle prüfen wir automatisch, ob Sie ein Mensch sind.') }}</p>
        <altcha-widget :challengeurl="challengeUrl" ref="widget"></altcha-widget>
    </div>
</template>
<script>
import 'altcha';
import { $gettext } from '../../../assets/javascripts/lib/gettext';

export default {
    name: 'CaptchaInput',
    emits: ['update:model-value'],
    props: {
        name: {
            type: String,
            default: 'altcha'
        },
        challengeUrl: {
            type: String,
            requird: true,
        },
        auto: {
            type: String,
            default: null,
            validator: (value) => ['onfocus', 'onload', 'onsubmit'].includes(value),
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.$refs.widget.configure({
                auto: this.auto,
                name: this.name,
                hidefooter: true,
                hidelogo: false,
                strings: {
                    error: $gettext('Überprüfung fehlgeschlagen. Versuchen Sie es später erneut.'),
                    footer: $gettext('Geschützt von <a href="https://altcha.org/" target="_blank">ALTCHA</a>'),
                    label: $gettext('Ich bin kein Bot'),
                    verified: $gettext('Überprüft'),
                    verifying: $gettext('Überprüfung...'),
                    waitAlert: $gettext('Überprüfung... Bitte warten.'),
                },
            });

            this.$refs.widget.addEventListener('statechange', (ev) => {
                if (ev.detail.state === 'verified') {
                    this.$emit('update:model-value', ev.detail.payload);
                }
            })
        });
    }
}
</script>
<style>
:root {
    --altcha-border-width: 0;
    --altcha-border-radius: 0;
    --altcha-color-base: transparent;
    --altcha-color-border: #a0a0a0;
    --altcha-color-text: currentColor;
    --altcha-color-border-focus: currentColor;
    --altcha-color-error-text: var(--red);
    --altcha-color-footer-bg: none;
    --altcha-max-width: auto;
}
.altcha-main {
    padding: 0 !important;
}
</style>
