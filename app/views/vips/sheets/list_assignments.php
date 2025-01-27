<?php
/**
 * @var int $num_assignments
 * @var array $assignment_data
 */
?>
<? if ($num_assignments == 0): ?>
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
<? endif ?>

<? foreach ($assignment_data as $i => $assignment_list): ?>
    <? if (count($assignment_list['assignments']) > 0 || isset($assignment_list['block']->id)): ?>
        <?= $this->render_partial('vips/sheets/list_assignments_list', ['i' => $i] + $assignment_list) ?>
    <? endif ?>
<? endforeach ?>
