<?php
/**
 * @var Admin_UserController $controller
 * @var array<int, array{Institut_id: string, Name: string, files: int}> $institutes
 * @var User $user
 * @var array $params
 */
?>
<section class="contentbox">
    <header>
        <h1>
            <a href="<?= ContentBoxHelper::href('institutes') ?>">
                <?= _('Dateiübersicht Einrichtungen') ?>
            </a>
        </h1>
    </header>
    <section>
        <table class="default">
            <colgroup>
                <col>
                <col style="width: 120px">
                <col style="width: 20px">
            </colgroup>
            <thead>
                <tr>
                    <th><?= _('Dateiname') ?></th>
                    <th><?= _('Anzahl') ?></th>
                    <th class="actions"><?= _('Aktionen') ?></th>
                </tr>
            </thead>
            <tbody>
                <? foreach ($institutes as $institute): ?>
                    <tr>
                        <td>
                            <a href="<?= URLHelper::getLink('dispatch.php/institute/overview', ['auswahl' => $institute['Institut_id']]) ?>">
                                <?= htmlReady($institute['Name']) ?>
                            </a>
                        </td>
                        <td>
                            <? if ($institute['files']) : ?>
                                <?= sprintf('%u %s', $institute['files'], _('Dokumente')) ?>
                            <? else : ?>
                                -
                            <? endif ?>
                        </td>
                        <td class="actions">
                        <? if ($institute['files']) : ?>
                            <?= ActionMenu::get()
                                ->setContext($institute['Name'])
                                ->addLink(
                                    $controller->list_filesURL($user->id, $institute['Institut_id'], $params),
                                    _('Dateien auflisten'),
                                    Icon::create('folder-full'),
                                    ['data-dialog' => 'size=50%']
                                )
                                ->addLink(
                                    $controller->download_user_filesURL($user->id, $institute['Institut_id']),
                                    _('Dateien als ZIP herunterladen'),
                                    Icon::create('download')
                                )
                            ?>
                        <? endif ?>
                        </td>
                    </tr>
                <? endforeach; ?>
            </tbody>
        </table>
    </section>
</section>
