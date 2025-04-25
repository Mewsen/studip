import BalloonEditor, { createBalloonEditorFromTextarea } from '../cke/balloon-editor.js';
import ClassicEditor, { createClassicEditorFromTextarea } from '../cke/classic-editor.js';
import { updateVoiceLabel } from '../cke/studip-a11y-dialog/a11y-dialog.js';

export {
    BalloonEditor,
    ClassicEditor,
    createBalloonEditorFromTextarea,
    createClassicEditorFromTextarea,
};

updateVoiceLabel();
