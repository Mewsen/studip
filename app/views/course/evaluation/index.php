<?php
/**
 * @var Course_EvaluationController $controller
 */
?>

<!-- TODO min responses -->
<?php foreach ($controller->evaluations as $key => $evaluation) : ?>
    <article class="studip toggle <?= $key == 0 ? 'open' : '' ?>">
        <header>
            <h1>
                <a href="#">
                    <?= htmlReady((Semester::find($evaluation->eval_assignment->semester_id))->name . ' - ' . $evaluation->title) ?>
                </a>
            </h1>
        </header>

        <?php if (EvaluationHelper::isPermittedEvaluationAccess()) : ?>
            <?= $this->render_partial('questionnaire/evaluate.php',
                ['questionnaire' => $evaluation, 'range_type' => 'course', 'range_id' => Context::getId()]) ?>
        <?php elseif (User::findCurrent()->hasPermissionLevel('tutor', Context::get())) : ?>
            <!-- TODO other views -->
            <table class="row-headers">
                <tbody>
                    <tr>
                        <th scope="row"><?= _('Evaluationsbeginn') ?></th>
                        <td><?= date('d.m.Y H:i', $evaluation->startdate) ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= _('Evaluationsende') ?></th>
                        <td><?= date('d.m.Y H:i', $evaluation->stopdate) ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= _('Anonyme Teilnahme') ?></th>
                        <td><?= $evaluation->anonymous ? _('Ja') : _('Nein') ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= _('Antworten revidierbar') ?></th>
                        <td><?= $evaluation->editanswers ? _('Ja') : _('Nein') ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?= _('Zeitpunkt Einsicht') ?></th>
                        <td>
                            <?= _(QuestionnaireEvalCentralProfile::RESULT_VISIBILITY_OPTIONS[$evaluation->resultvisibility]) ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?= _('Einsicht für') ?></th>
                        <td>
                            <?= $evaluation->result_visible_for ?
                                _(QuestionnaireEvalCentralProfile::RESULT_VISIBLE_FOR_OPTIONS[$evaluation->result_visible_for])
                                : _('Evaluations-Admins') ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?= _('Mindestrücklauf') ?></th>
                        <td><?= htmlReady($evaluation->minimum_responses) ?></td>
                    </tr>
                </tbody>
            </table>
        <?php else : ?>
            <?php if ($evaluation->isStopped()) : ?>
                <?= $this->render_partial('questionnaire/evaluate.php', ['questionnaire' => $evaluation, 'range_type' => 'course', 'range_id' => Context::getId()]) ?>
            <?php elseif ($evaluation->isAnswerable()) : ?>
                <?= $this->render_partial('questionnaire/answer.php', ['questionnaire' => $evaluation, 'range_type' => 'course', 'range_id' => Context::getId()]) ?>
            <?php else : ?>
                <p><?= _('Die Evaluation ist noch nicht abgeschlossen.') ?></p>
            <?php endif ?>
        <?php endif ?>
    </article>
<?php endforeach ?>

<?php
if (User::findCurrent()->hasPermissionLevel('tutor', Context::get())) {
    $actions = new ActionsWidget();
    $actions->addLink(
        _("QR-Code für Studierende anzeigen"),
        URLHelper::getURL('dispatch.php/course/evaluation'),
        Icon::create("code-qr", "clickable"),
        ['data-qr-code' => sprintf(_( 'Evaluation zur Veranstaltung %s'), Context::get()->getFullname('number-name'))]
    );
    Sidebar::Get()->addWidget($actions);
}
