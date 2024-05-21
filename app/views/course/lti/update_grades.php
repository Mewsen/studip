<?
/**
 * @var AuthenticatedController $controller
 * @var bool $update_all
 * @var LtiDeployment[] $deployments
 * @var string[] $selected_deployment_ids
 */
?>
<form class="default" method="post" action="<?= $controller->link_for('course/lti/update_grades') ?>">
    <?= CSRFProtection::tokenTag() ?>
    <fieldset>
        <legend><?= _('Auswahl des konfigurierten LTI-Tools') ?></legend>
        <? foreach ($deployments as $deployment) : ?>
            <label>
                <input type="checkbox" name="deployment_ids[]" class="update_grades_lti_deployment_list"
                       value="<?= htmlReady($deployment->id) ?>"
                    <?= in_array($deployment->id, $selected_deployment_ids) ? 'checked' : '' ?>>
                <?= htmlReady($deployment->title) ?>
            </label>
        <? endforeach ?>
        <label>
            <input type="hidden" name="update_all" value="0">
            <input type="checkbox" name="update_all" value="1" <?= $update_all ? 'checked' : '' ?>
                   data-deactivates=".update_grades_lti_deployment_list">
            <?= _('Alle Noten von allen in der Veranstaltung konfigurierten LTI-Tools aktualisieren.') ?>
        </label>
    </fieldset>
    <div data-dialog-button>
        <?= \Studip\Button::create(_('Aktualisieren'), 'update') ?>
        <?= \Studip\Button::createCancel(_('Abbrechen')) ?>
    </div>
</form>
