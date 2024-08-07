<?php
/**
 * @var Admin_UserController $controller
 * @var array<string, array<string, array{course: Course, files: int}>> $course_files
 * @var array $params
 * @var User $user
 */
?>
<section class="contentbox">
    <header>
        <h1>
            <?= _('Dateiübersicht Veranstaltungen') ?>
        </h1>
    </header>
    <? foreach ($course_files as $semester_name => $file_data) : ?>
        <article id="<?= htmlReady($semester_name) ?>" class="<?= ContentBoxHelper::classes($semester_name) ?>">
            <header>
                <h1>
                    <a href="<?= ContentBoxHelper::href($semester_name) ?>">
                        <?= htmlReady($semester_name) ?>
                    </a>
                </h1>
            </header>
            <section>
                <table class="default">
                    <colgroup>
                        <col style="width: 200px">
                        <col>
                        <col style="width: 120px">
                        <col style="width: 120px">
                        <col style="width: 20px">
                    </colgroup>
                    <thead>
                        <tr>
                            <th><?= _('Veranstaltungsnummer') ?></th>
                            <th><?= _('Veranstaltung') ?></th>
                            <th><?= _('Typ') ?></th>
                            <th><?= _('Anzahl') ?></th>
                            <th class="actions"><?= _('Aktionen') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <? foreach ($file_data as $data): ?>
                            <tr>
                                <td>
                                    <a href="<?= URLHelper::getLink('seminar_main.php', ['auswahl' => $data['course']->id]) ?>">
                                        <?= htmlReady($data['course']->veranstaltungsnummer) ?>
                                    </a>
                                </td>
                                <td>
                                    <a href="<?= URLHelper::getLink('seminar_main.php', ['auswahl' => $data['course']->id]) ?>">
                                        <?= htmlReady($data['course']->name) ?>
                                    </a>
                                </td>
                                <td>
                                    <?= htmlReady($data['course']->getSemType()['name'])?>
                                </td>
                                <td>
                                    <? if ($data['files']) : ?>
                                        <?= sprintf('%u %s', $data['files'], _('Dokumente')) ?>
                                    <? else : ?>
                                        -
                                    <? endif ?>
                                </td>
                                <td class="actions">
                                <? if ($data['files']) : ?>
                                    <?= ActionMenu::get()
                                        ->setContext($data['course']->name)
                                        ->addLink(
                                            $controller->list_filesURL($user->id, $data['course']->id, $params),
                                            _('Dateien auflisten'),
                                            Icon::create('folder-full'),
                                            ['data-dialog' => 'size=50%']
                                        )
                                        ->addLink(
                                            $controller->download_user_filesURL($user->id, $data['course']->id),
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
        </article>
    <? endforeach; ?>
</section>
