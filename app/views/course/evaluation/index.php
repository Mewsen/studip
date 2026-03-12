<?php
/**
 * @var Course_EvaluationController $controller
 */
?>

<?php foreach ($controller->evaluations as $key => $evaluation) : ?>
    <article class="studip toggle <?= $key == 0 ? 'open' : '' ?>">
        <header>
            <h1>
                <a href="#">
                    <?= htmlReady((Semester::find($evaluation->eval_assignment->semester_id))->name . ' - ' . $evaluation->title) ?>
                </a>
            </h1>
        </header>

        <?php if ($evaluation->isStopped()) : ?>
            <?= $this->render_partial('questionnaire/evaluate.php', ['questionnaire' => $evaluation, 'range_type' => 'course', 'range_id' => Context::getId()]) ?>
        <?php elseif ($evaluation->isAnswerable()) : ?>
            <?= $this->render_partial('questionnaire/answer.php', ['questionnaire' => $evaluation, 'range_type' => 'course', 'range_id' => Context::getId()]) ?>
        <?php else : ?>
            <p><?= _('Die Evaluation ist noch nicht abgeschlossen.') ?></p>
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
