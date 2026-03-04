<?php
/**
 * @var Course_EvaluationController $controller
 */
?>

<?php foreach ($controller->evaluations as $evaluation) : ?>

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
