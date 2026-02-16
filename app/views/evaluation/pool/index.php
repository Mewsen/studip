<?php
/**
 * @var Evaluation_PoolController $controller
 */

use Studip\Button;

?>
<form action="<?= $controller->link_for("questionnaire/bulkdelete", ["range_type" => 'pool']) ?>"
      method="post">
    <?= CSRFProtection::tokenTag() ?>
    <table class="default sortable-table" id="template_pool">
        <caption><?= _('Evaluations-Vorlagen') ?></caption>
        <thead>
            <tr>
                <th style="width: 20px">
                    <input type="checkbox"
                           data-proxyfor="#template_pool > tbody input[type=checkbox]"
                           data-activates="#template_pool tfoot button">
                </th>
                <th data-sort="text"><?= _('Titel') ?></th>
                <th data-sort="digit"><?= _('Datum') ?></th>
                <th class="actions"><?= _('Aktionen') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($controller->templates)) : ?>
                <?php foreach ($controller->templates as $template) : ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="q[]" value="<?= htmlReady($template->id) ?>">
                        </td>
                        <td><?= htmlReady($template->title) ?></td>
                        <td data-text="<?= (int) $template['chdate'] ?>">
                            <?= date('d.m.Y H:i', $template['chdate']) ?>
                        </td>
                        <td class="actions">
                            <? if (!$template->isEditable()) : ?>
                                <?= Icon::create('edit', Icon::ROLE_INACTIVE)->asSvg(
                                    ['title' => _('Mindestens eine Evaluation dieser Vorlage ist gestartet.
                                     Sie kann nicht mehr bearbeitet werden.')]
                                ) ?>
                            <? else : ?>
                                <a href="<?= $controller->link_for('questionnaire/edit/' . $template->id) ?>"
                                   data-dialog="size=big"
                                   title="<?= _('Vorlage bearbeiten') ?>">
                                    <?= Icon::create('edit') ?>
                                </a>
                            <? endif ?>

                            <?php
                                $menu = ActionMenu::get()->setContext($template['title']);
                                $menu->addLink(
                                    $controller->url_for('questionnaire/copy/' . $template->id),
                                    _('Kopieren'),
                                    Icon::create('clipboard')
                                );
                            $menu->addLink(
                                $controller->url_for('' . $template->id), //TODO
                                _('Freigeben'),
                                Icon::create('lock-unlocked'),
                                ['data-dialog' => '']
                            );
                                echo $menu->render();
                            ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php else : ?>
            <tr>
                <td colspan="4" style="text-align: center">
                    <?= _('Sie haben noch keine Vorlagen erstellt.') ?>
                </td>
            </tr>
            <? endif ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="4">
                <?= Button::create(_("Löschen"), "bulkdelete", ['data-confirm' => _("Wirklich löschen?")]) ?>
            </td>
        </tr>
        </tfoot>
    </table>
</form>

<?php
$actions = new ActionsWidget();
$actions->addLink(
    _('Vorlage erstellen'),
    $controller->url_for('questionnaire/edit', ["range_type" => 'pool']),
    Icon::create('add'),
    ['data-dialog' => 'size=big']
);
Sidebar::Get()->addWidget($actions);
