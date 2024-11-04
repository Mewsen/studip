<template>
    <studip-dialog
        :title="$gettext('Öffentlichen Link für Seite erzeugen')"
        :confirmText="$gettext('Erstellen')"
        confirmClass="accept"
        :closeText="$gettext('Abbrechen')"
        closeClass="cancel"
        class="cw-structural-element-dialog"
        @close="closePublicLinkDialog"
        @confirm="createElementPublicLink"
    >
        <template v-slot:dialogContent>
            <form class="default" @submit.prevent="">
                <label>
                    {{ $gettext('Passwort') }}
                    <input type="password" v-model="publicLink.password" />
                </label>
                <label>
                    {{ $gettext('Ablaufdatum') }}
                    <datepicker v-model="publicLink['expire-date']"  />
                </label>
            </form>
        </template>
    </studip-dialog>
</template>
<script>
import Datepicker from './../../Datepicker.vue';
import { mapActions } from 'vuex';

export default {
    name: 'courseware-structural-element-dialog-public-link',
    components: {
        Datepicker,
    },
    props: {
        structuralElement: Object,
    },
    data() {
        return {
            publicLink: {
                passsword: '',
                'expire-date': null
            },
        };
    },
    methods: {
        ...mapActions({
            companionSuccess: 'companionSuccess',
            createLink: 'createLink',
            showElementPublicLinkDialog: 'showElementPublicLinkDialog',
        }),
        async createElementPublicLink() {
            const date = this.publicLink['expire-date'];
            const publicLink = {
                attributes: {
                    password: this.publicLink.password,
                    'expire-date': date === null ? new Date(0).toISOString() : new Date(date * 1000).toISOString()
                },
                relationships: {
                    'structural-element': {
                        data: {
                            id: this.structuralElement.id,
                            type: 'courseware-structural-elements'
                        }
                    }
                }
            }

            await this.createLink({ publicLink });
            this.companionSuccess({
                info: this.$gettext('Öffentlicher Link wurde angelegt. Unter „Freigaben“ finden Sie alle Ihre öffentlichen Links.'),
            });
            this.closePublicLinkDialog();
        },
        closePublicLinkDialog() {
            this.publicLink = {
                passsword: '',
                'expire-date': ''
            };
            this.showElementPublicLinkDialog(false);
        },
    }
};
</script>
