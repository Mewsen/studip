<?php
/**
 * @var int $count
 * @var array $search_filter
 */
?>
<? if ($count == 0 && empty(array_filter($search_filter))): ?>
    <div class="vips-teaser">
        <header><?= _('Aufgaben und Prüfungen') ?></header>
        <p>
            <?= _('Mit diesem Werkzeug können Übungen, Tests und Klausuren online vorbereitet und durchgeführt werden. ' .
                  'Die Lehrenden erhalten eine Übersicht darüber, welche Teilnehmenden eine Übung oder einen ' .
                  'Test mit welchem Ergebnis abgeschlossen haben. Im Gegensatz zu herkömmlichen Übungszetteln ' .
                  'oder Klausurbögen sind in Stud.IP alle Texte gut lesbar und sortiert abgelegt. Lehrende ' .
                  'erhalten sofort einen Überblick darüber, was noch zu korrigieren ist. Neben allgemein ' .
                  'üblichen Fragetypen wie Multiple Choice und Freitextantwort verfügt das Werkzeug auch über ' .
                  'ungewöhnlichere, aber didaktisch durchaus sinnvolle Fragetypen wie Lückentext und Zuordnung.') ?>
        </p>
        <?= Studip\LinkButton::create(_('Aufgabenblatt erstellen'), $controller->url_for('vips/sheets/edit_assignment')) ?>
    </div>
<? elseif ($count): ?>
    <?= $this->render_partial('vips/pool/list_assignments') ?>
<? else: ?>
    <?= MessageBox::info(_('Mit den aktuellen Sucheinstellungen sind keine Aufgabenblätter mit Zugriffsberechtigung vorhanden.')) ?>
<? endif ?>
