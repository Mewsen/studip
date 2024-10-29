<form action="<?= $controller->seen() ?>" data-dialog="size=auto" method="post">
    <section class="contentbox">
        <h2>
            <?= _('Dieser Dialog dient dazu, die Änderungen seit dem letzten Update durchzusehen. Achtung, hier '
                . 'geänderte Einstellungen wirken sich auf das gesamte System aus!') ?>
        </h2>
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
                            <col style="width: 30%">
                            <col>
                        </colgroup>
                        <thead>
                        <tr>
                            <th><?= _('Name') ?></th>
                            <th><?= _('Typ') ?></th>
                            <th><?= _('Beschreibung') ?></th>
                            <th><?= _('Aktueller Wert') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <? foreach ($configurations as $configuration) : ?>
                            <tr>
                                <td><?= htmlReady($configuration->field) ?></td>
                                <td><?= htmlReady($configuration->type) ?></td>
                                <td><?= mb_strlen($configuration->description) > 120
                                        ? htmlReady(mb_substr($configuration->description, 0, 120))
                                            . '...' . tooltip2($configuration->description)
                                        : htmlReady($configuration->description) ?></td>
                                <td>
                                    <? switch ($configuration->type) {
                                        case 'string':
                                        case 'i18n':
                                            echo htmlReady($configuration->value);
                                            break;
                                        case 'integer':
                                            echo (int) $configuration->value;
                                            break;
                                        case 'boolean':
                                            echo $configuration->value
                                                ? Icon::create('accept', Icon::ROLE_STATUS_GREEN,
                                                    ['title' => _('TRUE')])
                                                : Icon::create('decline', Icon::ROLE_STATUS_RED,
                                                    ['title' => _('FALSE')]);
                                            break;
                                    } ?>
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
        <footer data-dialog-button>
            <?= Studip\Button::createAccept(_('Schließen'), 'close', ['data-dialog' => 'reload-on-close']) ?>
        </footer>
    </section>
</form>
