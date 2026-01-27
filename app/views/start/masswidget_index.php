<?php
/**
 * @var StartController $controller
 * @var \MassWidget\MassWidget[] $massWidgets
 */
?>

<table class="default sortable-table">
    <caption>
        <?= _('Zielgruppen') ?>
        <span class="actions">
            <a
                href="<?= $controller->masswidget_edit() ?>"
                data-dialog
                title="<?= _('Neue Regel hinzufügen') ?>"
                aria-label="<?= _('Neue Regel hinzufügen') ?>"
            >
                <?= Icon::create('add', Icon::DEFAULT_ROLE, ['aria-hidden' => 'true']) ?>
            </a>
        </span>
    </caption>

    <colgroup>
        <col>
        <col>
        <col>
        <col>
        <col style="width: 24px">
    </colgroup>

    <thead>
        <tr>
            <th scope="col" data-sort="name"><?= _('Name') ?></th>
            <th scope="col" data-sort="htmldata"><?= _('Widget') ?></th>
            <th scope="col" data-sort="htmldata"><?= _('Erstellt von') ?></th>
            <th scope="col" data-sort="target"><?= _('Zielgruppe') ?></th>
            <th scope="col" class="actions"><?= _('Aktionen') ?></th>
        </tr>
    </thead>

    <tbody>
    <?php foreach ($massWidgets as $massWidget) : ?>
        <tr>
            <td>
                <?= htmlReady($massWidget->name) ?>
            </td>
            <td data-sort-value="<?= htmlReady($massWidget->plugin->pluginclassname) ?>">
                <?= htmlReady($massWidget->plugin->pluginclassname) ?>
            </td>
            <td data-sort-value="<?= htmlReady($massWidget->author->getFullName()) ?>">
                <?= Avatar::getAvatarDropdownHTML($massWidget->author, true) ?>
            </td>
            <td>
                <?= htmlReady($massWidget->target) . ' ('. count($massWidget->getTargetUserIds()) . ')' ?>
            </td>
            <td class="actions">
               <?=
                   ActionMenu::get()
                       ->setContext(htmlReady($massWidget->name))
                       ->addLink(
                           $controller->masswidget_editURL($massWidget),
                           _('Bearbeiten'),
                           Icon::create('edit'),
                           ['data-dialog' => 'default']
                       )
                       ->addButton(
                           'delete',
                            _('Löschen'),
                            Icon::create('trash'),
                            [
                                'data-confirm' => sprintf(
                                    _('Wollen Sie die Regel "%s" löschen?'),
                                    $massWidget->name
                                ),
                                'form' => 'delete-mass-widget',
                                'formaction' => $controller->masswidget_deleteURL($massWidget)
                            ]
                       )
               ?>
            </td>
        </tr>
    <? endforeach ?>

    <?php if (count($massWidgets) === 0) : ?>
        <tr>
            <td colspan="5" class="text-center">
                <?= _('Es sind noch keine Regeln definiert.') ?>
            </td>
        </tr>
    <?php endif ?>
    </tbody>
</table>

<form action="" method="post" id="delete-mass-widget">
    <?= CSRFProtection::tokenTag() ?>
</form>
