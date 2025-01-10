<?php
/**
 * @var array $assignments
 * @var Vips_SolutionsController $controller
 */
?>
<? setlocale(LC_NUMERIC, $_SESSION['_language'] . '.UTF-8') ?>

<? if (count($assignments)) : ?>
    <table class="default">
        <caption>
            <?= _('Statistik der Aufgabenblätter') ?>
        </caption>

        <thead>
            <tr>
                <th>
                    <?= _('Titel / Aufgabe') ?>
                </th>
                <th style="text-align: right;">
                    <?= _('Erreichbare Punkte') ?>
                </th>
                <th style="text-align: right;">
                    <?= _('Durchschn. Punkte') ?>
                </th>
                <th style="text-align: right;">
                    <?= _('Korrekte Lösungen') ?>
                </th>
            </tr>
        </thead>

        <tbody>
            <? foreach ($assignments as $assignment): ?>
                <? if (count($assignment['exercises'])): ?>
                    <tr style="font-weight: bold;">
                        <td style="width: 70%;">
                            <a href="<?= $controller->link_for('vips/sheets/edit_assignment', ['assignment_id' => $assignment['assignment']->id]) ?>">
                                <?= $assignment['assignment']->getTypeIcon() ?>
                                <?= htmlReady($assignment['assignment']->test->title) ?>
                            </a>
                        </td>
                        <td style="text-align: right;">
                            <?= sprintf('%.1f', $assignment['points']) ?>
                        </td>
                        <td style="text-align: right;">
                            <?= sprintf('%.1f', $assignment['average']) ?>
                        </td>
                        <td>
                        </td>
                    </tr>

                    <? foreach ($assignment['exercises'] as $exercise): ?>
                        <tr>
                            <td style="width: 70%; padding-left: 2em;">
                                <a href="<?= $controller->link_for('vips/sheets/edit_exercise', ['assignment_id' => $assignment['assignment']->id, 'exercise_id' => $exercise['id']]) ?>">
                                    <?= $exercise['position'] ?>. <?= htmlReady($exercise['name']) ?>
                                </a>
                            </td>
                            <td style="text-align: right;">
                                <?= sprintf('%.1f', $exercise['points']) ?>
                            </td>
                            <td style="text-align: right;">
                                <?= sprintf('%.1f', $exercise['average']) ?>
                            </td>
                            <td style="text-align: right;">
                                <?= sprintf('%.1f %%', $exercise['correct'] * 100) ?>
                            </td>
                        </tr>

                        <? if (count($exercise['items']) > 1): ?>
                            <? foreach ($exercise['items'] as $index => $item): ?>
                                <tr>
                                    <td style="width: 70%; padding-left: 4em;">
                                        <?= sprintf(_('Item %d'), $index + 1) ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <?= sprintf('%.1f', $exercise['points'] / count($exercise['items'])) ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <?= sprintf('%.1f', $item) ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <?= sprintf('%.1f %%', $exercise['items_c'][$index] * 100) ?>
                                    </td>
                                </tr>
                            <? endforeach ?>
                        <? endif ?>
                    <? endforeach ?>
                <? endif ?>
            <? endforeach ?>
        </tbody>
    </table>
<? endif ?>

<? setlocale(LC_NUMERIC, 'C') ?>
