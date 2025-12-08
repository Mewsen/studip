<?php
/**
 * @var Course_LtiController $controller
 * @var \LtiResourceLink[] $links
 * @var bool $edit_perm
 */
?>
<? if (empty($links)): ?>
    <?= MessageBox::info(_('Es sind keine LTI-Tools konfiguriert.')) ?>
<? endif ?>

<? foreach ($links as $link): ?>
    <?
    $launch_url = $link->getLaunchURL();
    $unfinished_deep_linking = !empty($link->options['unfinished_deep_linking']);
    $no_consent = !LtiToolPrivacySettings::countBySql(
        '`registration_id` = :registration_id AND `user_id` = :user_id',
        ['registration_id' => $link->deployment->registration_id, 'user_id' => $GLOBALS['user']->id]
    );
    ?>

    <article class="studip">
        <header>
            <h1>
                <?= htmlReady($link->title) ?>
                <?= $unfinished_deep_linking ? '(' . _('LTI Deep Linking noch nicht fertig eingerichtet') . ')' : '' ?>
            </h1>

            <? if ($edit_perm): ?>
                <nav>
                    <form action="" method="post">
                        <?= CSRFProtection::tokenTag() ?>
                        <? if ($link->position > 0): ?>
                            <?= Icon::create('arr_2up', Icon::ROLE_SORT)->asInput([
                                'formaction' => $controller->url_for('course/lti/move/' . $link->id . '/up'),
                                'title'      => _('Nach oben verschieben'),
                                'aria-label' => _('Nach oben verschieben')
                            ]) ?>
                        <? endif ?>
                        <? if ($link->position < count($links) - 1): ?>
                            <?= Icon::create('arr_2down', Icon::ROLE_SORT)->asInput([
                                'formaction' => $controller->url_for('course/lti/move/' . $link->id . '/down'),
                                'title'      => _('Nach unten verschieben'),
                                'aria-label' => _('Nach unten verschieben')
                            ]) ?>
                        <? endif ?>

                        <?
                        $menu = ActionMenu::get();
                        $show_admin_actions = $GLOBALS['perm']->have_studip_perm('tutor', $link->course_id);
                        if ($show_admin_actions) {
                            $menu->addLink(
                                $controller->url_for('lti/tool/index/' . $link->course_id . '/' . $link->deployment->registration_id, ['link_id' => $link->id]),
                                _('Konfiguration des LTI-Tools anzeigen'),
                                Icon::create('info-circle'),
                                ['data-dialog' => 'size=default']
                            );
                        }
                        $menu->addLink(
                            $controller->url_for('course/lti/consent/' . $link->id),
                            _('Datenschutzeinstellungen'),
                            Icon::create('privacy'),
                            ['data-dialog' => 'size=default']
                        );

                        if ($link->deployment->registration->isEditableByUser()) {
                            $menu->addLink(
                                $controller->url_for('lti/tool/edit/' . $link->course_id . '/' . $link->deployment->registration_id),
                                _('LTI-Tool konfigurieren'),
                                Icon::create('edit'),
                                ['data-dialog' => 'size=default']
                            );
                        }
                        if ($show_admin_actions) {
                            $menu->addLink(
                                sprintf(
                                    'javascript:void(STUDIP.Dialog.confirmAsPost(\'%s\', \'%s\'))',
                                    sprintf(_('Wollen Sie das LTI-Tool "%s" wirklich entfernen?'), $link->title),
                                    $controller->url_for('lti/tool/delete/' . $link->course_id . '/' . $link->deployment->registration_id)
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
                    $controller->url_for('course/lti/select_link/' . $link->id, ['registration_id' => $link->deployment->registration_id]),
                    ['target' => '_blank']
                ) ?>
            <? elseif ($no_consent) : ?>
                <?= formatReady($link->description) ?>
                <p><?= _('Sie haben der Datenweitergabe an das LTI-Tool noch nicht zugestimmt und können es deswegen noch nicht nutzen.') ?></p>
                <?= Studip\LinkButton::create(
                    _('Datenschutzeinstellungen öffnen'),
                    $controller->url_for('course/lti/consent/' . $link->id),
                    ['data-dialog' => 'reload-on-close']
                ) ?>
            <? elseif ($launch_url) : ?>
                <?
                $document_target = $link->options['document_target'] ?? '';
                ?>
                <?= formatReady($link->description) ?>
                <? if ($document_target === 'iframe') : ?>
                    <iframe style="border: none; height: 640px; width: 100%;"
                            src="<?= $controller->link_for('course/lti/iframe/' . $link->id) ?>"></iframe>
                <? else : ?>
                    <?= Studip\LinkButton::create(
                        _('Anwendung starten'),
                        $controller->url_for('course/lti/iframe/' . $link->id),
                        ['target' => '_blank']
                    ) ?>
                <? endif ?>
            <? endif ?>
        </section>
    </article>
<? endforeach ?>
