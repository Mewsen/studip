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
                <th style="width: 20px" scope="col">
                    <input type="checkbox"
                           data-proxyfor="#template_pool > tbody input[type=checkbox]"
                           data-activates="#template_pool tfoot button">
                </th>
                <th data-sort="text" scope="col"><?= _('Titel') ?></th>
                <th data-sort="digit" scope="col"><?= _('Datum') ?></th>
                <th data-sort="text" scope="col"><?= _('Status') ?></th>
                <th class="actions" scope="col"><?= _('Aktionen') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($controller->templates)) : ?>
                <?php foreach ($controller->templates as $template) : ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="q[]" value="<?= htmlReady($template->id) ?>">
                        </td>
                        <?php if ($template->isEditable()) : ?>
                            <td>
                                <a href="<?= $controller->link_for('questionnaire/edit/' . $template->id,
                                    ['range_type' => 'pool']) ?>"
                                   data-dialog="size=big">
                                    <?= htmlReady($template->title) ?>
                                </a>
                            </td>
                        <?php else : ?>
                            <td title="<?= sprintf(
                                _('Mindestens eine Evaluation der Vorlage %s ist gestartet. Sie kann nicht mehr bearbeitet werden.'),
                                $template->title)
                            ?>">
                                <?= htmlReady($template->title) ?>
                            </td>
                        <?php endif ?>
                        <td data-text="<?= (int) $template->chdate ?>">
                            <?= date('d.m.Y H:i', $template->chdate) ?>
                        </td>
                        <td>
                            <?= $template->template_is_enabled ? _('Freigegeben') : _('Gesperrt') ?>
                        </td>
                        <td class="actions">
                            <? if (!$template->isEditable()) : ?>
                                <?= Icon::create('edit', Icon::ROLE_INACTIVE)->asSvg(
                                    ['title' => sprintf(
                                        _('Mindestens eine Evaluation der Vorlage %s ist gestartet. Sie kann nicht mehr bearbeitet werden.'),
                                    $template->title)]
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
                                    $controller->url_for('evaluation/pool/template_enable/' . $template->id),
                                    $template->template_is_enabled ? _('Sperren') : _('Freigeben'),
                                    Icon::create($template->template_is_enabled ? 'lock-locked' : 'lock-unlocked')
                                );
                                echo $menu->render();
                            ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            <?php else : ?>
                <tr>
                    <td colspan="5" style="text-align: center">
                        <?= _('Sie haben noch keine Vorlagen erstellt.') ?>
                    </td>
                </tr>
            <? endif ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">
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
