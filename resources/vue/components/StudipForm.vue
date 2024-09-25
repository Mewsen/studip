<template>
    <form v-cloak
          method="post"
          :action="form.autostore ? null : form.url"
          @submit="submit"
          novalidate
          :data-secure="this.isSecure"
          :id="id"
          data-inputs="<?= htmlReady(json_encode($inputs)) ?>"
          data-debugmode="<?= htmlReady(json_encode($form->getDebugMode())) ?>"
          data-server_validation="<?= $server_validation ? 1 : 0?>"
          data-validation_url="<?= htmlReady($_SERVER['REQUEST_URI']) ?>"
          class="default studipform"
          :class="{collapsable: isCollapsable}">

        <input type="hidden" :name="csrf.name" :value="csrf.value">

        <article aria-live="assertive"
                 class="validation_notes studip"
                 v-if="form.required.length > 0 || validationNotes.length > 0">
            <header>
                <h1>
                    <studip-icon shape="info-circle" role="info" class="text-bottom validation_notes_icon"></studip-icon>
                    {{ $gettext('Hinweise zum Ausfüllen des Formulars') }}
                </h1>
            </header>
            <div class="required_note" v-if="form.required.length > 0">
                <div aria-hidden="true">
                    {{ $gettext('Pflichtfelder sind mit Sternchen gekennzeichnet.') }}
                </div>
                <div class="sr-only">
                    {{ $gettext('Dieses Formular enthält Pflichtfelder.') }}
                </div>

            </div>
            <div v-if="displayValidation && validationNotes.length > 0">
                {{ $gettext('Folgende Angaben müssen korrigiert werden, um das Formular abschicken zu können:') }}
                <ul>
                    <li v-for="(note, index) in ordererValidationNotes"
                        :aria-describedby="note.describedby"
                        :key="`validation-note-${index}`"
                    >
                        {{ note.label.trim() + ": " + note.description }}
                    </li>
                </ul>
            </div>
        </article>

        <div aria-live="polite">
            <slot v-for="slot in slots" :name="slot"></slot>
        </div>

        <footer data-dialog-button>
<!--            <?= \Studip\Button::create($form->getSaveButtonText(), $form->getSaveButtonName(), ['form' => $form_id]) ?>-->
<!--        <? foreach ($form->getButtons() as $button): ?>-->
<!--            <?-->
<!--                $button->attributes['form'] = $form_id;-->
<!--                echo $button;-->
<!--            ?>-->
<!--        <? endforeach ?>-->
        </footer>
    </form>
</template>
<script>
import StudipIcon from './StudipIcon.vue';

let counter = 0;

export default {
    name: 'studip-form',
    components: {StudipIcon},
    props: {
        form: {
            type: Object,
            validator(value) {
                return 'url' in value
                    && 'values' in value
                    && 'autosave' in value
                    && ('required' in value && Array.isArray(value.required));
            },
            required: true,
        },
        isCollapsable: Boolean,
        isSecure: Boolean,
        requestUrl: String,
        inputs: Array,
        debugmode: Boolean,
        serverValidation: Boolean,
        slots: Array,
        validationUrl: String,
    },
    data() {
        return {
            ...this.form.values,

            id: `studip-form-${counter++}`,
            order: Object.keys(this.form.values),
            displayValidation: false,
            validationNotes: () => [],
            validated: false,
            i18n: {},
        };
    },
    methods: {
        submit(e) {
            if (this.validated) {
                return;
            }
            this.validationNotes = [];
            this.displayValidation = true;

            //validation:
            (this.validate()).then((validated) => {
                if (!validated) {
                    this.$el.scrollIntoView({
                        behavior: 'smooth'
                    });
                    return;
                }

                if (this.form.autosave) {
                    let params = this.getFormValues();
                    params.STUDIPFORM_AUTOSTORE = 1;

                    $.post(this.requestUrl, params).done((output) => {
                        if (output !== 'STUDIPFORM_STORE_SUCCESS') {
                            //The form has not been stored successfully:
                            Report.error(this.$gettext('Es ist ein Fehler aufgetreten'), output);
                        } else if (this.form.url) {
                            window.location.href = this.form.url;
                        }
                    });
                } else {
                    this.validated = true;
                    this.$el.submit();
                }
            });
            e.preventDefault();
        },
        getFormValues() {
            let params = {
                security_token: this.$refs.securityToken.value
            };
            Object.keys(this.$data).forEach((i) => {
                if (!i.startsWith('STUDIPFORM_')) {
                    if (typeof this.$data[i] === 'boolean') {
                        params[i] = this.$data[i] ? 1 : 0;
                    } else {
                        params[i] = this.$data[i];
                    }
                }
            });
            return params;
        },
        validate() {
            this.validationNotes = [];

            return new Promise((resolve, reject) => {
                let validated = this.$el.checkValidity();

                this.$el.querySelectorAll('input, select, textarea').forEach(input => {
                    if (!input.validity.valid) {
                        let note = {
                            name: input.name,
                            label: $(input.labels[0]).find('.textlabel').text(),
                            description: input.$gettext('Fehler!'),
                            describedby: input.id
                        };
                        if ($(input).data('validation_requirement')) {
                            note.description = $(input).data('validation_requirement');
                        }
                        if (input.validity.tooShort) {
                            note.description = this.$gettextInterpolate(
                                this.$gettext('Geben Sie mindestens %{min} Zeichen ein.'),
                                {min: this.minLength}
                            );
                        }
                        if (input.validity.valueMissing) {
                            if (this.type === 'checkbox') {
                                note.description = this.$gettext('Dieses Feld muss ausgewählt sein.');
                            } else if (input.minLength > 0) {
                                note.description = this.$gettextInterpolate(
                                    this.$gettext('Hier muss ein Wert mit mindestens %{min} Zeichen eingetragen werden.'),
                                    {min: input.minLength}
                                );
                            } else {
                                note.description = this.$gettext('Hier muss ein Wert eingetragen werden.');
                            }
                        }
                        this.validationNotes.push(note);
                    }
                });

                if (this.form.serverValidation) {
                    let params = this.getFormValues();
                    if (this.form.autosave) {
                        params.STUDIPFORM_AUTOSTORE = 1;
                    }
                    params.STUDIPFORM_SERVERVALIDATION = 1;

                    $.post(this.requestUrl, params).done((output) => {
                        for (let i in output) {
                            this.validationNotes.push({
                                name: output[i].name,
                                label: output[i].label,
                                description: output[i].error,
                                describedby: null
                            });
                        }
                        validated = this.validationNotes.length < 1;
                        resolve(validated);
                    });
                } else {
                    resolve(validated);
                }
            });
        },
        setInputs(inputs) {
            for (const [key, value] of Object.entries(inputs)) {
                if (this[key] !== undefined) {
                    this[key] = value;
                }
            }
        },
        selectLanguage(input_name, language_id) {
            this.i18n = {
                ...this.i18n,
                [input_name]: language_id,
            };
        }
    },
    computed: {
        csrf() {
            return STUDIP.CSRF_TOKEN;
        },
        ordererValidationNotes() {
            let orderedNotes = [];
            for (let i in this.order) {
                for (let k in this.validationNotes) {
                    if (this.validationNotes[k].name === this.order[i]) {
                        orderedNotes.push(this.validationNotes[k]);
                    }
                }
            }
            return orderedNotes;
        }
    },
}
</script>
