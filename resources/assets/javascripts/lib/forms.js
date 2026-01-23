/* ------------------------------------------------------------------------
 * Forms
 * ------------------------------------------------------------------------ */

import Report from "./report";
import {$gettext} from "./gettext";
import Dialog from "./dialog";
import {useFormsStore} from '@/vue/store/pinia/formsStore';

const Forms = {
    initialized: false,
    initialize: function(scope) {
        if (scope === undefined) {
            scope = document;
        }

        $('input[required],textarea[required]', scope).attr('aria-required', true);
        $('input[pattern][title],textarea[pattern][title]', scope).each(function() {
            $(this).data('message', $(this).attr('title'));
        });

        if (!Forms.initialized) {
            // add invalid-handler to every input and textarea on the page
            $(document).on('invalid', 'input, textarea', function() {
                $(this)
                    .attr('aria-invalid', 'true')
                    .change(function() {
                        $(this).removeAttr('aria-invalid');
                    });

                // get the fieldset that contains the invalid input
                var fieldset = $(this).closest('fieldset');
                // toggle the collapsed class if the fieldset is currently collapsed
                if (fieldset.hasClass('collapsed')) {
                    fieldset.toggleClass('collapsed');
                }
            });

            $(document).on('change', 'form.default label.file-upload input[type=file]', function(ev) {
                var selected_file = ev.target.files[0],
                    filename;
                if (
                    $(this)
                        .closest('label')
                        .find('.filename').length
                ) {
                    filename = $(this)
                        .closest('label')
                        .find('.filename');
                } else {
                    filename = $('<span class="filename"/>');
                    $(this)
                        .closest('label')
                        .append(filename);
                }
                filename.text(selected_file.name + ' ' + Math.ceil(selected_file.size / 1024) + 'KB');
            });
        }

        Forms.initialized = true;
    },
    create: function(forms) {
        STUDIP.Vue.load().then(({createApp}) => {
            forms.forEach(f => {
                if (f.nodeType !== 3) {
                    f.classList.add('vueified');

                    const app = createApp({
                        data() {
                            let params = JSON.parse(f.dataset.inputs);
                            params.STUDIPFORM_REQUIRED = f.dataset.required ? JSON.parse(f.dataset.required) : [];
                            params.STUDIPFORM_SERVERVALIDATION = f.dataset.server_validation > 0;
                            params.STUDIPFORM_DISPLAYVALIDATION = false;
                            params.STUDIPFORM_VALIDATIONNOTES = [];
                            params.STUDIPFORM_AUTOSAVEURL = f.dataset.autosave;
                            params.STUDIPFORM_VALIDATION_URL = f.dataset.validation_url;
                            params.STUDIPFORM_VALIDATED = false;
                            params.STUDIPFORM_REDIRECTURL = f.dataset.url;
                            params.STUDIPFORM_INPUTS_ORDER = [];
                            params.STUDIPFORM_SELECTEDLANGUAGES = {};
                            params.STUDIPFORM_EMIT_VALUES = f.dataset.emit;
                            params.STUDIPFORM_USE_STORE = f.dataset.useStore === 'true';
                            params.STUDIPFORM_FORM_ID = f.dataset.formId;
                            for (let i in JSON.parse(f.dataset.inputs)) {
                                params.STUDIPFORM_INPUTS_ORDER.push(i);
                            }
                            return params;
                        },
                        methods: {
                            submit: function (e) {
                                if (this.STUDIPFORM_VALIDATED) {
                                    return;
                                }
                                this.STUDIPFORM_VALIDATIONNOTES = [];
                                this.STUDIPFORM_DISPLAYVALIDATION = true;

                                // validation:
                                this.validate()
                                    .then(() => {
                                        if (this.STUDIPFORM_USE_STORE) {
                                            const store = useFormsStore();
                                            store.initialize();
                                            store.setData(this.STUDIPFORM_FORM_ID, this.getFormValues());
                                        } else {
                                            if (this.STUDIPFORM_AUTOSAVEURL) {
                                                let params = this.getFormValues();
                                                params.STUDIPFORM_AUTOSTORE = 1;

                                                $.post(this.STUDIPFORM_AUTOSAVEURL, params).done((output) => {
                                                    if (output === 'STUDIPFORM_STORE_SUCCESS' && this.STUDIPFORM_REDIRECTURL) {
                                                        //The form has been stored successfully:
                                                        window.location.href = this.STUDIPFORM_REDIRECTURL;
                                                    } else if (output !== 'STUDIPFORM_STORE_SUCCESS') {
                                                        Report.error($gettext('Es ist ein Fehler aufgetreten.'), output);
                                                    }
                                                });
                                            } else {
                                                this.STUDIPFORM_VALIDATED = true;
                                                this.$el.submit();
                                            }
                                        }
                                    }).catch(errors => {
                                        this.STUDIPFORM_VALIDATIONNOTES = errors;
                                        this.$el.scrollIntoView({behavior: 'smooth'});
                                    }
                                );
                                e.preventDefault();
                            },
                            getFormValues() {
                                let params = {
                                    security_token: this.$refs.securityToken.value
                                };
                                Object.keys(this.$data).forEach(i => {
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
                            async validate() {
                                this.$el.checkValidity();

                                // Check inputs
                                const inputs = this.$el.querySelectorAll('input,select,textarea');
                                let notes = Array.from(inputs)
                                    .filter(node => !node.validity.valid)
                                    .map(node => {
                                        const note = {
                                            name: node.name,
                                            label: node.labels[0].querySelector('.textlabel').innerText,
                                            description: node.dataset.validation_requirement ?? $gettext('Fehler'),
                                            describedby: node.id
                                        };

                                        if (node.validity.tooShort) {
                                            note.description = $gettext(
                                                'Geben Sie mindestens %{min} Zeichen ein.',
                                                {min: node.minLength}
                                            );
                                        }
                                        if (node.validity.valueMissing) {
                                            if (node.type === 'checkbox') {
                                                note.description = $gettext('Dieses Feld muss ausgewählt sein.');
                                            } else if (node.minLength > 0) {
                                                note.description = $gettext(
                                                    'Hier muss ein Wert mit mindestens %{min} Zeichen eingetragen werden.',
                                                    {min: node.minLength}
                                                );
                                            } else {
                                                note.description = $gettext('Hier muss ein Wert eingetragen werden.');
                                            }
                                        }

                                        return note;
                                    });

                                // Optional server validation
                                if (this.STUDIPFORM_SERVERVALIDATION && !this.STUDIPFORM_USE_STORE) {
                                    let params = this.getFormValues();
                                    if (this.STUDIPFORM_AUTOSAVEURL) {
                                        params.STUDIPFORM_AUTOSTORE = 1;
                                    }
                                    params.STUDIPFORM_SERVERVALIDATION = 1;
                                    params.STUDIPFORM_FORM_ID = this.STUDIPFORM_FORM_ID;

                                    const output = await fetch(this.STUDIPFORM_VALIDATION_URL, {
                                        method: 'POST',
                                        body: new URLSearchParams(params),
                                        headers: {'X-Requested-With': 'XMLHttpRequest'}
                                    }).then(response => response.json());
                                    notes.push(
                                        ...output.map(item => ({
                                            name: item.name,
                                            label: item.label,
                                            description: item.error,
                                            describedby: null
                                        }))
                                    );
                                }

                                // Resolve or reject based on present error notes
                                if (notes.length > 0) {
                                    return Promise.reject(notes);
                                } else {
                                    return Promise.resolve();
                                }
                            },
                            setInputs(inputs) {
                                for (const [key, value] of Object.entries(inputs)) {
                                    if (this[key] !== undefined) {
                                        this[key] = value;
                                    }
                                }
                            },
                            selectLanguage(input_name, language_id) {
                                let languages = {
                                    ...this.STUDIPFORM_SELECTEDLANGUAGES
                                };
                                languages[input_name] = language_id;
                                this.STUDIPFORM_SELECTEDLANGUAGES = languages;
                            }
                        },
                        computed: {
                            ordererValidationNotes: function () {
                                let orderedNotes = [];
                                let inserted = [];
                                for (let i in this.STUDIPFORM_INPUTS_ORDER) {
                                    for (let k in this.STUDIPFORM_VALIDATIONNOTES) {
                                        if (this.STUDIPFORM_VALIDATIONNOTES[k].name === this.STUDIPFORM_INPUTS_ORDER[i]) {
                                            orderedNotes.push(this.STUDIPFORM_VALIDATIONNOTES[k]);
                                            inserted.push(k);
                                        }
                                    }
                                }
                                return orderedNotes.concat(
                                    this.STUDIPFORM_VALIDATIONNOTES.filter((note, index) => !inserted.includes(index))
                                );
                            }
                        },
                        mounted() {
                            if (this.$el.closest('.ui-dialog')) {
                                const cancelButton = this.$el.querySelector('footer .button.cancel:last-of-type');
                                if (cancelButton) {
                                    cancelButton.addEventListener('click', (e) => {
                                        Dialog.close();
                                        e.preventDefault();
                                    })
                                }
                            }

                            STUDIP.Vue.on('form.submit', id => {
                                if (this.STUDIPFORM_FORM_ID === id) {
                                    this.submit(new Event('submit'));
                                }
                            });
                        }
                    });
                    const instance = app.mount(f);
                    STUDIP.Vue.emit('form.mounted', {app: app, instance: instance});
                }
            });
        });
    }
};

export default Forms;
