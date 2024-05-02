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
                        <a href="<?= $controller->link_for('lti/tool/index/' . $lti_data->course_id . '/' . $lti_data->tool->id) ?>"
                           title="<?= _('Konfiguration des LTI-Tools anzeigen') ?>" data-dialog>
                            <?= Icon::create('info-circle') ?>
                        </a>
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

                        <a href="<?= $controller->link_for('lti/tool/edit/' . $lti_data->course_id . '/' . $lti_data->tool->id) ?>"
                           title="<?= _('LTI-Tool konfigurieren') ?>" data-dialog>
                            <?= Icon::create('edit') ?>
                        </a>
                        <a href="<?= htmlReady(sprintf(
                                'javascript:void(STUDIP.Dialog.confirmAsPost(\'%1$s\', \'%2$s\'))',
                                sprintf(_('Wollen Sie wirklich den Abschnitt "%s" löschen?'), $lti_data->title),
                                $controller->url_for('lti/tool/delete/' . $lti_data->course_id . '/' . $lti_data->tool->id)
                        )) ?>"
                           title="<?= _('Abschnitt löschen') ?>">
                            <?= Icon::create('trash') ?>
                        </a>
                    </form>
                </nav>
            <? endif ?>
        </header>
        <?
        $document_target = $lti_data->options['document_target'] ?? '';
        ?>
        <section>
            <?= formatReady($lti_data->description) ?>

            <? if ($launch_url && $document_target === 'iframe'): ?>
                <iframe style="border: none; height: 640px; width: 100%;"
                        src="<?= $controller->link_for('course/lti/iframe', $lti_data->position) ?>"></iframe>
            <? endif ?>
        </section>

        <? if ($launch_url && $document_target !== 'iframe'): ?>
            <footer>
                <?= Studip\LinkButton::create(
                    _('Anwendung starten'),
                    $controller->link_for('course/lti/iframe', $lti_data->position),
                    ['target' => '_blank']
                ) ?>
            </footer>
        <? endif ?>
    </article>
<? endforeach ?>
