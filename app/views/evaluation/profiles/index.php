<?php
/**
 * @var Evaluation_ProfilesController $controller
 */

use Studip\Button;

?>

<form action="<?= $controller->link_for("evaluation/profiles/bulkdelete") ?>" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <table class="default" id="eval_profiles_table">
        <caption><?= _('Evaluationsprofile') ?></caption>
        <thead>
            <tr>
                <th style="width: 20px">
                    <input type="checkbox"
                           data-proxyfor="#eval_profiles_table > tbody input[type=checkbox]"
                           data-activates="#eval_profiles_table tfoot button">
                </th>
                <th><?= _('Semester') ?></th>
                <th><?= _('Vorlage') ?></th>
                <th><?= _('Alternative Vorlagen') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($controller->profiles as $profile) : ?>
                <tr>
                    <td>
                        <input type="checkbox" name="profiles[]" value="<?= htmlReady($profile->semester_id) ?>">
                    </td>
                    <td><?= htmlReady($profile->semester->name) ?></td>
                    <td><?= htmlReady($profile->template->title) ?></td>
                    <td>
                        <?php foreach (Questionnaire::findMany(explode(',', $profile->optional_templates)) as $opt_template) : ?>
                            <?= htmlReady($opt_template->title) ?></br>
                        <?php endforeach ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">
                    <?= Button::create(_("Löschen"), "bulkdelete", ['data-confirm' => _("Wirklich löschen?")]) ?>
                </td>
            </tr>
        </tfoot>
    </table>
</form>

<?php
$actions = new ActionsWidget();
$actions->addLink(
    _('Profil erstellen'),
    $controller->url_for('evaluation/profiles/edit'),
    Icon::create('add'),
    ['data-dialog' => 'size=big']
);
Sidebar::Get()->addWidget($actions);
