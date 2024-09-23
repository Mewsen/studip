<script>
import { ClassicEditor, BalloonEditor } from '../../assets/javascripts/chunks/wysiwyg.js';
import {h, resolveComponent} from "vue";

export default {
    compatConfig: {
        COMPONENT_V_MODEL: false,
        RENDER_FUNCTION: false,
    },
    name: 'studip-wysiwyg',
    emits: ['update:modelValue'],
    props: {
        modelValue: {
            type: String,
            required: true,
        },
        editorType: {
            type: String,
            validator(value) {
                return ['classic', 'balloon'].includes(value);
            },
            default: 'classic',
        },
        autofocus: Boolean,
    },
    data() {
        return {
            currentText: '',
            editorConfig: {},

            createdEditor: null,
            shouldFocus: this.autofocus,
        };
    },
    computed: {
        editor() {
            switch (this.editorType) {
                case 'classic':
                    return ClassicEditor;
                case 'balloon':
                    return BalloonEditor;
            }
            throw new Error('Unknown `editorType`');
        },
    },
    methods: {
        onReady(editor) {
            this.createdEditor = editor;
            this.currentText = this.modelValue;

            if (this.shouldFocus) {
                this.focus();
            }
        },
        onInput(value) {
            this.currentText = value;
            this.$emit('update:modelValue', value);
        },
        focus() {
            if (this.createdEditor) {
                this.createdEditor.focus();
            } else {
                this.shouldFocus = true;
            }
        }
    },
    created() {
        STUDIP.loadChunk('mathjax');
    },
    render() {
        return h(resolveComponent('ckeditor'), {
            compatConfig: {
                COMPONENT_V_MODEL: false,
                RENDER_FUNCTION: false,
            },
            editor: this.editor,
            config: this.editorConfig,
            modelValue: this.modelValue,
            onInput: this.onInput,
            onReady: this.onReady,
        })
    }
};
</script>
