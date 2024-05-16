<?php
/**
 * @var Course_LtiController $controller
 * @var LtiDeployment[] $lti_data_array
 * @var bool $edit_perm
 */
?>
<? if (empty($lti_data_array)): ?>
    <?= MessageBox::info(_('Es sind keine LTI-Tools konfiguriert.')) ?>
<? endif ?>

<? foreach ($lti_data_array as $lti_data): ?>
    <?
    $launch_url = $lti_data->getLaunchURL();
    $unfinished_deep_linking = !empty($lti_data->options['unfinished_deep_linking']);
    $no_consent = !LtiDeploymentPrivacySettings::countBySql(
        '`deployment_id` = :deployment_id AND `user_id` = :user_id',
        ['deployment_id' => $lti_data->id, 'user_id' => $GLOBALS['user']->id]
    );
    ?>

    <article class="studip">
        <header>
            <h1>
                <?= htmlReady($lti_data->title) ?>
                <?= $unfinished_deep_linking ? '(' . _('LTI Deep Linking noch nicht fertig eingerichtet') . ')' : '' ?>
            </h1>

            <? if ($edit_perm): ?>
                <nav>
                    <form action="" method="post">
                        <?= CSRFProtection::tokenTag() ?>
                        <? if ($lti_data->position > 0): ?>
                            <?= Icon::create('arr_2up', Icon::ROLE_SORT)->asInput([
                                'formaction' => $controller->url_for('course/lti/move/' . $lti_data->position . '/up')
                            ]) ?>
                        <? endif ?>
                        <? if ($lti_data->position < count($lti_data_array) - 1): ?>
                            <?= Icon::create('arr_2down', Icon::ROLE_SORT)->asInput([
                                'formaction' => $controller->url_for('course/lti/move/' . $lti_data->position . '/down')
                            ]) ?>
                        <? endif ?>

                        <?
                        $menu = ActionMenu::get();
                        $show_admin_actions = $GLOBALS['perm']->have_studip_perm('tutor', $lti_data->course_id);
                        if ($show_admin_actions) {
                            $menu->addLink(
                                $controller->url_for('lti/tool/index/' . $lti_data->course_id . '/' . $lti_data->tool->id),
                                _('Konfiguration des LTI-Tools anzeigen'),
                                Icon::create('info-circle'),
                                ['data-dialog' => 'size=default']
                            );
                        }
                        $menu->addLink(
                            $controller->url_for('course/lti/consent/' . $lti_data->id),
                            _('Datenschutzeinstellungen'),
                            Icon::create('privacy'),
                            ['data-dialog' => 'size=default']
                        );

                        if ($show_admin_actions) {
                            $menu->addLink(
                                $controller->url_for('lti/tool/edit/' . $lti_data->course_id . '/' . $lti_data->tool->id),
                                _('LTI-Tool konfigurieren'),
                                Icon::create('edit'),
                                ['data-dialog' => 'size=default']
                            );
                            $menu->addLink(
                                sprintf(
                                    'javascript:void(STUDIP.Dialog.confirmAsPost(\'%1$s\', \'%2$s\'))',
                                    sprintf(_('Wollen Sie das LTI-Tool "%s" wirklich entfernen?'), $lti_data->title),
                                    $controller->url_for('lti/tool/delete/' . $lti_data->course_id . '/' . $lti_data->tool->id)
                                ),
                                _('LTI-Tool entfernen'),
                                Icon::create('trash')
                            );
                        }
                        ?>
                        <?= $menu->render() ?>
                    </form>
                </nav>
            <? endif ?>
        </header>
        <section>
            <? if ($unfinished_deep_linking) : ?>
                <?= Studip\LinkButton::create(
                    _('Einrichtung abschließen'),
                    $controller->url_for('course/lti/select_link/' . $lti_data->id, ['tool_id' => $lti_data->tool_id]),
                    ['target' => '_blank']
                ) ?>
            <? elseif ($no_consent) : ?>
                <?= formatReady($lti_data->description) ?>
                <p><?= _('Sie haben der Datenweitergabe an das LTI-Tool noch nicht zugestimmt und können es deswegen noch nicht nutzen.') ?></p>
                <?= Studip\LinkButton::create(
                    _('Datenschutzeinstellungen öffnen'),
                    $controller->url_for('course/lti/consent/' . $lti_data->id),
                    ['data-dialog' => 'reload-on-close']
                ) ?>
            <? elseif ($launch_url) : ?>
                <?
                $document_target = $lti_data->options['document_target'] ?? '';
                ?>
                <?= formatReady($lti_data->description) ?>
                <? if ($document_target === 'iframe') : ?>
                    <iframe style="border: none; height: 640px; width: 100%;"
                            src="<?= $controller->link_for('course/lti/iframe/' . $lti_data->id) ?>"></iframe>
                <? else : ?>
                    <?= Studip\LinkButton::create(
                        _('Anwendung starten'),
                        $controller->url_for('course/lti/iframe/' . $lti_data->id),
                        ['target' => '_blank']
                    ) ?>
                <? endif ?>
            <? endif ?>
        </section>
    </article>
<? endforeach ?>
