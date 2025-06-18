<?php
/**
 * @var string $file_ref_id
 * @var FileRef $file_ref
 * @var string $userFullname
 */
?>
<?= Studip\VueApp::create('PdfAnnotator')->withProps([
    'file-ref' => $file_ref,
    'user-fullname' => $userFullname,
    'class' => 'annotate-pdf-root'
]) ?>
<footer data-dialog-button>
    <?= Studip\Button::createAccept(
        _('Speichern'),
        'save',
        [
            'onclick' => 'STUDIP.eventBus.emit("files:save-annotated-pdf")',
        ]
    ); ?>
    <?= Studip\LinkButton::createCancel(
        _('Abbrechen'),
        ['data-dialog' => 'close']
    ); ?>
</footer>
