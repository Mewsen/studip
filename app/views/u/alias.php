<?= MessageBox::info(_('Sie können die Bezeichnung der Kurz-URL im untenstehenden Formular ändern. Beim Klick auf „Speichern“ wird die Kurz-URL mit der neuen Bezeichnung in die Zwischenablage kopiert.')) ?>
<? if (!empty($form)) : ?>
    <?= $form->render() ?>
<? endif ?>
