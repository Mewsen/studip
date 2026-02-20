<?php
/**
 * @var Evaluation_ProfilesController $controller
 */

use Studip\Button;

?>

<form action="<?= $controller->link_for("evaluation/profiles/bulkdelete") ?>" method="post">
    <?= CSRFProtection::tokenTag() ?>
    <table class="default sortable-table" id="eval_profiles_table">
        <caption><?= _('Evaluationsprofile') ?></caption>
        <thead>
            <tr>
                <th style="width: 20px">
                    <input type="checkbox"
                           data-proxyfor="#eval_profiles_table > tbody input[type=checkbox]"
                           data-activates="#eval_profiles_table tfoot button">
                </th>
                <th data-sort="digit"><?= _('Semester') ?></th>
                <th data-sort="text"><?= _('Vorlage') ?></th>
                <th data-sort="text"><?= _('Alternative Vorlagen') ?></th>
                <th data-sort="digit"><?= _('Start') ?></th>
                <th data-sort="digit"><?= _('Ende') ?></th>
                <th data-sort="text"><?= _('Anonym') ?></th>
                <th data-sort="text"><?= _('Revidierbar') ?></th>
                <th data-sort="text"><?= _('Zeitpunkt Einsicht') ?></th>
                <th data-sort="text"><?= _('Einsicht für') ?></th>
                <th data-sort="digit"><?= _('Mindestrücklauf') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($controller->profiles)) : ?>
                <?php foreach ($controller->profiles as $profile) : ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="profiles[]" value="<?= htmlReady($profile->semester_id) ?>">
                        </td>
                        <td data-text="<?= $profile->semester->beginn ?>">
                            <?= htmlReady($profile->semester->name) ?>
                        </td>
                        <td><?= htmlReady($profile->template->title) ?></td>
                        <td>
                            <?php foreach (Questionnaire::findMany(explode(',', $profile->optional_templates)) as $opt_template) : ?>
                                <?= htmlReady($opt_template->title) ?></br>
                            <?php endforeach ?>
                        </td>
                        <td data-text="<?= $profile->startdate ?>">
                            <?= (new DateTime())->setTimestamp($profile->startdate)->format('d.m.Y H:i') ?>
                        </td>
                        <td data-text="<?= $profile->stopdate ?>">
                            <?= (new DateTime())->setTimestamp($profile->stopdate)->format('d.m.Y H:i') ?>
                        </td>
                        <td><?= $profile->anonymous ? _('Ja') : _('Nein') ?></td>
                        <td><?= $profile->editanswers ? _('Ja') : _('Nein') ?></td>
                        <td><?= _(QuestionnaireEvalCentralProfile::RESULT_VISIBILITY_OPTIONS[$profile->resultvisibility]) ?></td>
                        <td>
                            <?=
                                $profile->result_visible_for ?
                                _(QuestionnaireEvalCentralProfile::RESULT_VISIBLE_FOR_OPTIONS[$profile->result_visible_for]) :
                                _('Admins')
                            ?>
                        </td>
                        <td><?= $profile->minimum_responses ?></td>
                    </tr>
                <?php endforeach ?>
            <?php else : ?>
                <tr>
                    <td colspan="11" style="text-align: center">
                        <?= _('Sie haben noch keine Profile erstellt.') ?>
                    </td>
                </tr>
            <?php endif ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="11">
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
