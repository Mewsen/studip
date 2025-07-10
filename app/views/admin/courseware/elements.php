<form method="post" action="<?= URLHelper::getLink('dispatch.php/admin/courseware/container_types') ?>">
    <?= CSRFProtection::tokenTag() ?>
    <table class="default sortable-table cw-admin-container-types">
        <caption>
            <?= _('Abschnittstypen') ?>
        </caption>
        <colgroup>
            <col style="width: 5%">
            <col style="width: 35%">
            <col style="width: 60%">
        </colgroup>
        <thead>
            <tr>
                <th><?= _('Aktiv') ?></th>
                <th data-sort="text"><?= _('Container-Typ') ?></th>
                <th data-sort="text"><?= _('Beschreibung') ?></th>
            </tr>
        </thead>
        <tbody>
            <? foreach ($containerTypes as $containerType) { ?>
                <? $isActivated = $containerType::isActivated(); ?>
                <tr>
                    <td>
                        <label>
                            <? $formaction = $isActivated ? URLHelper::getURL(
                                'dispatch.php/admin/courseware/deactivate_container_types',
                                ['container_types' => [$containerType]]
                            ) :
                                URLHelper::getURL('dispatch.php/admin/courseware/activate_container_types', [
                                    'container_types' => [$containerType],
                                ])
                                ?>
                            <span
                                class="sr-only"><? printf(_('Abschnittstyp "%s" %s'), $containerType::getTitle(), $isActivated ? _('deaktivieren') : _('aktivieren')) ?></span>
                            <button class="undecorated"
                                formaction="<?= $formaction ?>"><?= Icon::create($isActivated ? 'checkbox-checked' : 'checkbox-unchecked') ?></button>
                        </label>
                    </td>
                    <td data-sort-value="<?= htmlReady(strtolower($containerType::getType())) ?>">
                        <span><?= htmlReady($containerType::getTitle()) ?></span>
                        <span>(<?= htmlReady($containerType::getType()) ?>)</span>
                    </td>
                    <td data-sort-value="<?= htmlReady(strtolower($containerType::getDescription())) ?>">
                        <p>
                            <?= htmlReady($containerType::getDescription()) ?>
                        </p>
                    </td>
                </tr>
            <? } ?>
        </tbody>
    </table>
</form>
<form method="post" action="<?= URLHelper::getLink('dispatch.php/admin/courseware/block_types') ?>">
    <?= CSRFProtection::tokenTag() ?>
    <table class="default sortable-table cw-admin-block-types">
        <caption><?= _('Blocktypen') ?></caption>
        <colgroup>
            <col style="width: 5%">
            <col style="width: 35%">
            <col style="width: 60%">
        </colgroup>
        <thead>
            <tr>
                <th><?= _('Aktiv') ?></th>
                <th data-sort="text"><?= _('Block-Typ') ?></th>
                <th data-sort="text"><?= _('Beschreibung') ?></th>
            </tr>
        </thead>
        <tbody>
            <? foreach ($blockTypes as $blockType) { ?>
                <? $isActivated = $blockType::isActivated(); ?>
                <tr>
                    <td>
                        <label>
                            <? $formaction = $isActivated ? URLHelper::getURL(
                                'dispatch.php/admin/courseware/deactivate_block_types',
                                ['block_types' => [$blockType]]
                            ) : URLHelper::getURL(
                                        'dispatch.php/admin/courseware/activate_block_types',
                                        ['block_types' => [$blockType]]
                                    ) ?>
                            <span
                                class="sr-only"><? printf(_('Blocktyp "%s" %s'), $blockType::getTitle(), $isActivated ? _('deaktivieren') : _('aktivieren')) ?></span>
                            <button class="undecorated"
                                formaction="<?= $formaction ?>"><?= Icon::create($isActivated ? 'checkbox-checked' : 'checkbox-unchecked') ?></button>
                        </label>
                    </td>
                    <td data-sort-value="<?= htmlReady(strtolower($blockType::getType())) ?>">
                        <span><?= htmlReady($blockType::getTitle()) ?></span>
                        <span>(<?= htmlReady($blockType::getType()) ?>)</span>
                    </td>
                    <td data-sort-value="<?= htmlReady(strtolower($blockType::getDescription())) ?>">
                        <p>
                            <?= htmlReady($blockType::getDescription()) ?>
                        </p>
                    </td>
                </tr>
            <? } ?>
        </tbody>
    </table>
</form>