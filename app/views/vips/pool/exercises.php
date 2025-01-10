<?php
/**
 * @var int $count
 * @var array $search_filter
 */
?>
<? if ($count == 0 && empty(array_filter($search_filter))): ?>
    <?= MessageBox::info(_('Es wurden noch keine Aufgabenblätter eingerichtet.'), [
            _('Auf dieser Seite finden Sie eine Übersicht über alle Aufgaben, auf die Sie Zugriff haben.')
        ]) ?>
<? elseif ($count): ?>
    <?= $this->render_partial('vips/pool/list_exercises') ?>
<? else: ?>
    <?= MessageBox::info(_('Mit den aktuellen Sucheinstellungen sind keine Aufgaben mit Zugriffsberechtigung vorhanden.')) ?>
<? endif ?>
