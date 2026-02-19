<?php
/**
 * @var Evaluation_ProfilesController $controller
 */
?>

<table class="default">
    <caption><?= _('Evaluationsprofile') ?></caption>
    <thead>
        <tr>
            <th><?= _('Semester') ?></th>
            <th><?= _('Vorlage') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($controller->profiles as $profile) : ?>
            <tr>
                <td><?= htmlReady($profile->semester->name) ?></td>
                <td><?= htmlReady($profile->template->title) ?></td>
            </tr>
        <?php endforeach ?>
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
