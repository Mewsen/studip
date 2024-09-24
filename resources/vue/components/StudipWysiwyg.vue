<template>
    <ckeditor
        :editor="editor"
        :config="editorConfig"
        @ready="onReady"
        v-model="currentText"
        @input="onInput"
    />
</template>

<script>
import { ClassicEditor, BalloonEditor } from '../../assets/javascripts/chunks/wysiwyg.js';

export default {
    name: 'studip-wysiwyg',
    model: {
        prop: 'text',
        event: 'input',
    },
    props: {
        text: {
            type: String,
            required: true,
        },
        editorType: {
            type: String,
            validator: function (value) {
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
            this.currentText = this.text;

            if (this.shouldFocus) {
                this.focus();
            }
        },
        onInput(value) {
            this.currentText = value;
            this.$emit('input', value);
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
};
</script>
