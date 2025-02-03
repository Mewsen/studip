<?php
/**
 * @var Vips_AdminController $controller
 * @var array $grades
 * @var bool $grade_settings
 * @var array $percentages
 * @var string[] $comments
 */
?>
<form class="default" action="<?= $controller->link_for('vips/admin/store_grades') ?>" data-secure method="post">
    <?= CSRFProtection::tokenTag() ?>

    <table class="default">
        <caption>
            <?= _('Notenverteilung') ?>
        </caption>
        <thead>
            <tr>
                <th><?= _('Note') ?></th>
                <th><?= _('Schwellwert') ?></th>
                <th><?= _('Kommentar') ?></th>
            </tr>
        </thead>

        <tbody>
            <? for ($i = 0; $i < count($grades); ++$i): ?>
                <? $class = $grade_settings && !$percentages[$i] ? 'quiet' : '' ?>
                <tr class="<?= $class ?>">
                    <td><?= htmlReady($grades[$i]) ?></td>
                    <td>
                        <input type="text" class="percent_input" name="percentage[<?= $i ?>]" value="<?= sprintf('%g', $percentages[$i]) ?>"> %
                    </td>
                    <td>
                        <input type="text" name="comment[<?= $i ?>]" value="<?= htmlReady($comments[$i]) ?>" <?= $class ? 'disabled' : '' ?>>
                    </td>
                </tr>
            <? endfor ?>
        </tbody>

        <tfoot>
            <tr>
                <td class="smaller" colspan="3">
                    <?= _('Wenn Sie eine bestimmte Notenstufe nicht verwenden wollen, lassen Sie das Feld für den Schwellwert leer.') ?>
                </td>
            </tr>
        </tfoot>
    </table>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Speichern'), 'save') ?>
    </footer>
</form>
