<?php
/**
 * @var RootAssistantController $controller
 * @var array $release_notes
 */
?>
<form action="<?= $controller->seen() ?>" data-dialog="size=auto" method="post">
    <section class="contentbox">
        <?= MessageBox::warning(_('Dieser Dialog dient dazu, die Änderungen seit dem letzten Update durchzusehen. Achtung, hier '
                . 'geänderte Einstellungen wirken sich auf das gesamte System aus!')) ?>
        <? foreach ($release_notes as $release_note) : ?>
            <article class="<?= ContentBoxHelper::classes(md5($release_note['headline'])) ?>">
                <header>
                    <h1>
                        <a href="<?= ContentBoxHelper::href(md5($release_note['headline'])) ?>">
                            <?= htmlReady($release_note['headline']) ?>
                        </a>
                    </h1>
                </header>
                <section>
                    <article>
                        <?= $release_note['content'] ?>
                    </article>
                </section>
            </article>

        <? endforeach ?>


        <? if (!empty($configurations)) : ?>
            <article class="<?= ContentBoxHelper::classes('new-configurations') ?>">
                <header>
                    <h1>
                        <a href="<?= ContentBoxHelper::href('new-configurations') ?>">
                            <?= _('Neue Konfigurationen') ?>
                        </a>
                    </h1>
                </header>
                <section>
                    <table class="default">
                        <colgroup>
                            <col>
                            <col>
                            <col>
                            <col>
                            <col>
                        </colgroup>
                        <thead>
                        <tr>
                            <th><?= _('Name') ?></th>
                            <th><?= _('Typ') ?></th>
                            <th><?= _('Beschreibung') ?></th>
                            <th><?= _('Aktueller Wert') ?></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach ($configurations as $configuration) : $field = $configuration->field; ?>
                            <tr>
                                <td><?= htmlReady($field) ?></td>
                                <td><?= htmlReady($configuration->type) ?></td>
                                <td><?= mb_strlen($configuration->description) > 120
                                        ? htmlReady(mb_substr($configuration->description, 0, 120))
                                            . '...' . tooltip2($configuration->description)['title']
                                        : htmlReady($configuration->description) ?></td>
                                <td>
                                    <? switch ($configuration->type) {
                                        case 'string':
                                        case 'i18n':
                                            echo htmlReady(Config::get()->getValue($field));
                                            break;
                                        case 'integer':
                                            echo (int) Config::get()->getValue($field);
                                            break;
                                        case 'boolean':
                                            echo Config::get()->getValue($field)
                                                ? Icon::create('accept', Icon::ROLE_STATUS_GREEN,
                                                    ['title' => _('TRUE')])
                                                : Icon::create('decline', Icon::ROLE_STATUS_RED,
                                                    ['title' => _('FALSE')]);
                                            break;
                                    } ?>
                                </td>
                                <td>
                                    <a href="<?= URLHelper::getLink(
                                        'dispatch.php/admin/configuration/edit_configuration',
                                        ['field' => $configuration->field, 'from_root_assi' => 1]) ?>"
                                       title="<?= _('Diesen Eintrag bearbeiten') ?>?>"
                                       data-dialog="size=auto"
                                    >
                                        <?= Icon::create('edit') ?>
                                    </a>
                                </td>
                            </tr>
                        <? endforeach ?>
                        </tbody>
                    </table>
                </section>
            </article>
        <? endif ?>
        <section>
            <label>
                <input type="checkbox" name="seen" value="1">
                <?= _('Für alle Roots als gelesen kennzeichnen und nicht mehr anzeigen') ?>
            </label>
        </section>
    </section>
    <footer data-dialog-button>
        <?= Studip\Button::createCancel(_('Schließen'), 'close', ['data-dialog' => 'reload-on-close']) ?>
    </footer>
</form>
