<?
/**
 * @var AuthenticatedController $controller
 * @var LtiDeployment $deployment
 */
?>
<form class="default" method="post" action="<?= $controller->link_for('course/lti/consent/' . htmlReady($deployment->id)) ?>">
    <?= CSRFProtection::tokenTag() ?>
    <?
    $data_protection_warning = CourseConfig::get(Context::getId())->LTI_DATA_PROTECTION_COURSE_WARNING;
    if (empty($data_protection_warning)) {
        $data_protection_warning = Config::get()->LTI_DATA_PROTECTION_DEFAULT_WARNING;
    }
    ?>
    <fieldset>
        <legend><?= _('Datenschutzhinweise')  ?></legend>
        <section>
            <p><?= htmlReady($data_protection_warning) ?></p>
            <? if ($deployment->data_protection_notes) : ?>
                <p><?= formatReady($deployment->data_protection_notes) ?></p>
            <? endif ?>
        </section>
    </fieldset>
    <fieldset>
        <legend><?= _('Folgenden Daten werden übertragen') ?></legend>
        <?= _('Beim Wechsel in das LTI-Tool werden die folgenden personenbezogenen Daten übertragen:') ?>
        <label>
            <input type="checkbox" checked disabled>
            <?= _('Die ID ihres Stud.IP-Kontos') ?>
        </label>
        <label>
            <input type="checkbox" checked disabled>
            <?= _('Ihr Vor- und Nachname, sowie gegebenenfalls vorhandene Titel') ?>
        </label>
        <label>
            <input type="checkbox" checked disabled>
            <?= _('Ihre E-Mail Adresse') ?>
        </label>
        <label>
            <input type="checkbox" name="submit_optional_field[lang]" value="1">
            <?= _('Ihre in Stud.IP eingestellte Sprache') ?>
        </label>
        <label>
            <input type="checkbox" name="submit_optional_field[avatar_url]" value="1">
            <?= _('Ihr Profilbild') ?>
        </label>
    </fieldset>
    <?= $this->render_partial('lti/_deployment_user_info', ['deployment' => $deployment]) ?>
    <fieldset>
        <legend><?= _('Bestätigung') ?></legend>
        <label>
            <input type="checkbox" name="confirmed" value="1">
            <?= _(
                'Ich habe die Datenschutzhinweise zur Benutzung des LTI-Tools zur Kenntnis genommen und stimme der Weitergabe meiner personenbezogenen Daten zu. '
                . 'Mir ist bewusst, dass ich ohne die Zustimmung das LTI-Tool nicht nutzen kann.'
            ) ?>
        </label>
        <?= \Studip\Button::createAccept(_('Speichern'), 'save') ?>
        <?= \Studip\Button::createCancel(_('Abbrechen')) ?>
    </fieldset>
</form>
