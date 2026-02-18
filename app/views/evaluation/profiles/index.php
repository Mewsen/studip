<?php
/**
 * @var Evaluation_ProfilesController $controller
 */
?>

<table class="default">
    <caption><?= _('Evaluationsprofile') ?></caption>
    <thead>
        <tr>

        </tr>
    </thead>
    <tbody>

    </tbody>
</table>

<?php
$actions = new ActionsWidget();
$actions->addLink(
    _('Profil erstellen'),
    $controller->url_for('evaluation/profiles/edit'),
    Icon::create('add'),
    ['data-dialog' => 'size=big']
);
Sidebar::Get()->addWidget($actions);
