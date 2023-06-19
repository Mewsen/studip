<table width="90%" cellpadding="2" cellspacing="2" align="center" style="font-size:10pt;">
    <?= $message ?>
    <tr>
        <td style="font-size:10pt;">
    <? if ($editable): ?>
        <? if ($is_item_admin): ?>
            <?= Studip\LinkButton::create(
                _('Neues Objekt'),
                $controller->link_for(['cmd' => 'NewItem', 'item_id' => $item_id]),
                ['title' => _('Innerhalb dieser Ebene ein neues Element einfÃŒgen')]
            ) ?>
            <?= Studip\LinkButton::create(
                _('Sortieren'),
                $controller->link_for(['cmd' => 'OrderItemsAlphabetically', 'sort_id' => $item_id]),
                ['title' => _('Sortiert die untergeordneten Elemente alphabetisch')]
            ) ?>
        <? endif; ?>
        <? if ($is_parent_admin): ?>
            <?= Studip\LinkButton::create(
                _('Bearbeiten'),
                $controller->link_for(['cmd' => 'EditItem', 'sort_id' => $item_id]),
                ['title' => _('Dieses Element bearbeiten')]
            ) ?>
            <?= Studip\LinkButton::create(
                _('LÃ¶schen'),
                $controller->link_for(['cmd' => 'AssertDeleteItem', 'sort_id' => $item_id]),
                ['title' => _('Dieses Element lÃ¶schen')]
            ) ?>

            <? if ($moving_or_copying): ?>
                <?= Studip\LinkButton::create(
                    _('Abbrechen'),
                    $controller->link_for(['cmd' => 'Cancel', 'item_id' => $item_id]),
                    ['title' => _('Verschieben / Kopieren abbrechen')]
                ) ?>
            <? else: ?>
                <?= Studip\LinkButton::create(
                    _('Verschieben'),
                    $controller->link_for(['cmd' => 'MoveItem', 'item_id' => $item_id]),
                    ['title' => _('Dieses Element in eine andere Ebene verschieben')]
                ) ?>
                <?= Studip\LinkButton::create(
                    _('Kopieren'),
                    $controller->link_for(['cmd' => 'CopyItem', 'item_id' => $item_id]),
                    ['title' => _('Dieses Element in eine andere Ebene kopieren')]
                ) ?>
            <? endif; ?>
        <? endif; ?>
    <? endif; ?>
    <? if ($item_id === 'root' && $is_item_admin): ?>
            <form action="<?= $controller->link_for(['cmd' => 'InsertFak']) ?>" method="post" class="default">
                <?= CSRFProtection::tokenTag() ?>
                <div>
                    <label>
                        <?= _('Stud.IP-FakultÃ€t einfÃŒgen') ?>
                        <select style="width: 200px" name="insert_fak">
                        <? foreach ($unassigned_faculties as $faculty): ?>
                            <option value="<?= htmlReady($faculty->id) ?>">
                                <?= htmlReady($faculty->name) ?>
                            </option>
                        <? endforeach; ?>
                        </select>
                    </label>
                </div>
                <div class="col-1">
                    <?= Studip\Button::create(_('Eintragen'), ['title' => _('FakultÃ€t einfÃŒgen')]) ?>
                </div>
            </form>
    <? endif; ?>
        </td>
    </tr>
</table>

<table border="0" width="90%" cellpadding="2" cellspacing="0" align="center" style="font-size:10pt">
<? if ($item_id === 'root') :?>
    <tr>
        <td  class="table_header_bold" align="left" style="font-size:10pt;">
            <?= htmlReady($tree->root_name) ?>
        </td>
    </tr>
    <tr>
        <td  class="table_row_even" align="left" style="font-size:10pt;">
            <?= htmlReady($tree->root_content) ?>
        </td>
    </tr>
<? else: ?>
    <? if ($tree_data[$item_id]['info']): ?>
        <tr>
            <td style="font-size:10pt;" class="table_row_even" align="left" colspan="3">
                <?= formatReady($tree_data[$item_id]['info']) ?>
            </td>
        </tr>
    <? endif; ?>
        <tr>
            <td style="font-size:10pt;" colspan="3">&nbsp;</td>
        </tr>
    <? if ($tree->getNumEntries($item_id) - $tree_data[$item_id]['lonely_sem']): ?>
        <tr>
            <td class="table_row_even" style="font-size:10pt;" align="left" colspan="3">
                <strong><?= _('EintrÃ€ge auf dieser Ebene:') ?></strong>
            </td>
        </tr>
        <?= $controller->getSemDetails($tree->getSemData($item_id), $item_id) ?>
    <? else: ?>
        <tr>
            <td class="table_row_even" style="font-size:10pt;" colspan="3">
                <?= _('Keine EintrÃ€ge auf dieser Ebene vorhanden!') ?>
            </td>
        </tr>
    <? endif; ?>
    <? if ($tree_data[$item_id]['lonely_sem']): ?>
        <tr>
            <td class="table_row_even" align="left" style="font-size:10pt;" colspan="3">
                <strong><?= _("Nicht zugeordnete Veranstaltungen auf dieser Ebene:") ?></strong>
            </td>
        </tr>
        <?= $controller->getSemDetails($tree->getLonelySemData($item_id), $item_id, true) ?>
    <? endif; ?>
<? endif; ?>
</table>
