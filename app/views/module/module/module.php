<?php
/**
 * @var Modul[] $module
 */
?>

<? foreach ($module as $modul) : ?>
    <? $perm = MvvPerm::get($modul) ?>
    <tbody class="<?= $modul->count_modulteile ? '' : 'empty ' ?><?= $modul_id === $modul->getId() ? 'not-collapsed' : 'collapsed' ?>">
        <? $ampel_icon = $GLOBALS['MVV_STUDIENGANG']['STATUS']['values'][$modul->stat]['icon'] ?>
        <? $ampelstatus = $GLOBALS['MVV_STUDIENGANG']['STATUS']['values'][$modul->stat]['name'] ?>
        <tr class="header-row <?= $modul_id === $modul->getId() ? 'selected' : '' ?>" id="modul_<?= $modul->getId() ?>">
            <? if ($modul->count_modulteile) : ?>
                <td style="white-space:nowrap;" class="toggle-indicator">
                    <? $details_action = $details_action ?? 'details'; ?>
                    <a class="mvv-load-in-new-row" href="<?= $controller->action_link($details_action, $modul->getId()) ?>">
                        <? if ($ampel_icon) : ?>
                            <?= $ampel_icon->asSvg(['title' => $ampelstatus, 'style' => 'vertical-align: bottom;']) ?>
                        <? endif; ?>
                        <?= htmlReady($modul->code) ?>
                    </a>
                </td>
                <td class="dont-hide toggle-indicator">
                    <a class="mvv-load-in-new-row" href="<?= $controller->action_link($details_action, $modul->getId()) ?>"
                       style="background-image: none; padding: 0; display: inline;">
                        <?= htmlReady($modul->getDisplayName(0)) ?>
                    </a>
                    <? $trails = $modul->getTrails(array('Studiengang', 'StgteilAbschnitt')); ?>
                    <? if (count($trails)) : ?>
                        <? $li = []; ?>
                        <? foreach ($modul->getPathes($trails, ' > ') as $i => $path) : ?>
                            <? $li[] = htmlReady(sprintf('%s) %s', $i+1, $path)); ?>
                        <? endforeach; ?>
                        <?= tooltipIcon(implode('<br>', $li), false, true); ?>
                    <? endif;?>
                </td>
            <? else : ?>
                <td style="white-space:nowrap; font-weight: 700; padding-left: 20px;">
                    <? if ($ampel_icon) : ?>
                        <?= $ampel_icon->asSvg(['title' => $ampelstatus, 'style' => 'vertical-align: bottom;']) ?>
                    <? endif; ?>
                    <?= htmlReady($modul->code) ?>
                </td>
                <td class="dont-hide" style="font-weight: 700;">
                    <?= htmlReady($modul->getDisplayName()) ?>
                </td>
            <? endif; ?>
            <td style="text-align:center;" class="dont-hide"><?= htmlReady($modul->fassung_nr) ?></td>
            <td style="text-align: center;" class="dont-hide"><?= $modul->count_modulteile ?></td>
            <td class="dont-hide actions" style="text-align: left;">
                <? if ($perm->havePermRead()) : ?>
                    <? $languages = $modul->deskriptoren->getAvailableTranslations($modul->original_language); ?>
                    <? $content_languages = $GLOBALS['CONTENT_LANGUAGES'] ?>
                    <? foreach ($languages as $language) : ?>
                        <a href="<?= $controller->action_link('modul/' . $modul->id . '/', ['display_language' => $language]) ?>">
                            <?= Assets::img(MVV::getContentLanguageImagePath($language),
                                ['alt' => $content_languages[$language]['name'], 'size' => 24]) ?>
                        </a>
                    <? endforeach; ?>
                <? endif; ?>
            </td>
            <td class="dont-hide actions" style="white-space: nowrap;">
                <form method="post">
                    <?= CSRFProtection::tokenTag(); ?>
                    <? $actionMenu = ActionMenu::get()->setContext($modul->getDisplayName()) ?>
                    <? if ($modul->stat === 'planung' && $perm->haveFieldPerm('stat')) : ?>
                        <? $actionMenu->addLink(
                            $controller->action_url('approve/' . $modul->id),
                            _('Modul genehmigen'),
                            Icon::create('accept', Icon::ROLE_CLICKABLE, ['title' => _('Modul genehmigen')]),
                            ['data-dialog' => 'size=auto;']
                        ) ?>
                    <? endif; ?>
                    <? if ($perm->havePermRead()) : ?>
                        <? $actionMenu->addLink(
                            $controller->action_url('description', $modul->id),
                            _('Modulbeschreibung ansehen'),
                            Icon::create('log', Icon::ROLE_CLICKABLE,['title' => _('Modulbeschreibung ansehen')]),
                            [
                                'data-dialog' => 'size=auto',
                                'title'       => $modul->getDisplayName()
                            ]
                        ) ?>
                    <? endif; ?>
                    <? if ($perm->haveFieldPerm('modulteile', MvvPerm::PERM_CREATE)) : ?>
                        <? $actionMenu->addLink(
                            $controller->action_url('modulteil', ['modul_id' => $modul->id]),
                            _('Modulteil anlegen'),
                            Icon::create('add', Icon::ROLE_CLICKABLE,['title' => _('Modulteil anlegen')])
                        ) ?>
                    <? endif; ?>
                    <? if ($perm->havePermWrite()) : ?>
                        <? $actionMenu->addLink(
                            $controller->action_url('modul/' . $modul->id),
                            _('Modul bearbeiten'),
                            Icon::create('edit', Icon::ROLE_CLICKABLE,['title' => _('Modul bearbeiten')])
                        ) ?>
                    <? endif; ?>
                    <? if ($perm->haveFieldPerm('copy_module', MvvPerm::PERM_CREATE)) : ?>
                        <? $actionMenu->addLink(
                            $controller->action_url('copy_form', $modul->id),
                            _('Modul kopieren'),
                            Icon::create('files', Icon::ROLE_CLICKABLE,['title' => _('Modul kopieren')]),
                            ['data-dialog' => '']
                        ) ?>
                    <? endif; ?>
                    <? if ($perm->haveFieldPerm('change_language', MvvPerm::PERM_CREATE)) : ?>
                        <? $actionMenu->addLink(
                            $controller->change_languageURL($modul->id),
                            _('Originalsprache ändern'),
                            Icon::create('support', Icon::ROLE_CLICKABLE,['title' => _('Originalsprache ändern')]),
                            ['data-dialog' => 'size=370x270']
                        ) ?>
                    <? endif; ?>
                    <? if ($perm->havePermCreate()) : ?>
                        <? $actionMenu->addButton(
                            'delete',
                            _('Modul löschen'),
                            Icon::create('trash', Icon::ROLE_CLICKABLE,['title' => _('Modul löschen')]),
                            [
                                'formaction'   => $controller->action_url('delete/' . $modul->id),
                                'data-confirm' => sprintf(
                                    _('Wollen Sie wirklich das Modul "%s" löschen?'),
                                    $modul->getDisplayName()
                                )
                            ]
                        ) ?>
                    <? endif; ?>
                    <?= $actionMenu->render() ?>
                </form>
            </td>
        </tr>
        <? if ($modul->count_modulteile && $modul_id === $modul->id) : ?>
            <tr class="loaded-details nohover">
                <?= $this->render_partial('module/module/details', compact('modul')) ?>
            </tr>
        <? endif; ?>
    </tbody>
<? endforeach; ?>
