<?php
/**
 * @var Admin_LtiController $controller
 * @var Lti\Registration[] $registrations
 */
?>

<? if ($registrations) : ?>
    <form action="" method="post">
        <?= CSRFProtection::tokenTag() ?>
        <table class="default">
            <caption><?= _('Aktuell konfigurierte LTI-Tools') ?></caption>

            <thead>
                <tr>
                    <th><?= _('Name') ?></th>
                    <th><?= _('Version') ?></th>
                    <th><?= _('Deployments') ?></th>
                    <th><?= _('Status') ?></th>
                    <th class="actions"><?= _('Aktionen') ?></th>
                </tr>
            </thead>

            <tbody>
                <? foreach ($registrations as $registration): ?>
                    <tr>
                        <td>
                            <a href="<?= $controller->link_for('lti/registrations/show/' . $registration->id) ?>" data-dialog>
                                <?= htmlReady($registration->name) ?>
                            </a>
                        </td>
                        <td><?= htmlReady($registration->version) ?></td>
                        <td>
                            <a href="<?= $controller->link_for('admin/lti/tools/deployments/' . $registration->id) ?>">
                                <?= count($registration->deployments) ?>
                            </a>
                        </td>
                        <td>
                            <?= htmlReady($registration->state) ?>
                        </td>

                        <td class="actions">
                            <a href="<?= $controller->link_for('lti/registrations/edit/' . $registration->id) ?>" title="<?= _('LTI-Tool konfigurieren') ?>"
                               aria-label="<?= _('LTI-Tool konfigurieren') ?>" data-dialog>
                                <?= Icon::create('edit') ?>
                            </a>
                            <?= Icon::create('trash')->asInput([
                                'formaction'   => $controller->url_for('lti/registrations/delete/' . $registration->id),
                                'title'        => _('LTI-Tool löschen'),
                                'data-confirm' => sprintf(_('Wollen Sie das LTI-Tool „%s“ wirklich löschen?'), htmlReady($registration->name)),
                                'aria-label'   => _('LTI-Tool löschen'),
                            ]) ?>
                        </td>
                    </tr>
                <? endforeach ?>
            </tbody>
        </table>
    </form>
<? else : ?>
    <?= MessageBox::info(_('Es sind keine globalen LTI-Tools konfiguriert.')) ?>
<? endif ?>
