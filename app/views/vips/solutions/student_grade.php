<?php
/**
 * @var bool $use_weighting
 * @var array $participants
 * @var array $items
 * @var string $user_id
 */
?>
<table class="default">
    <caption>
        <?= _('Note') ?>
    </caption>

    <thead>
        <tr>
            <th>
                <?= _('Titel') ?>
            </th>
            <th colspan="3" style="text-align: center; width: 1%;">
                <?= _('Punkte') ?>
            </th>
            <th style="text-align: right;">
                <?= _('Prozent') ?>
            </th>
            <? if ($use_weighting) : ?>
                <th style="text-align: right;">
                    <?= _('Gewichtung') ?>
                </th>
            <? endif ?>
        </tr>
    </thead>

    <? /* here, $participants contains only one entry */ ?>
    <? foreach ($participants as $me) : ?>

        <tbody>
            <? foreach (['tests', 'blocks', 'exams'] as $category) : ?>
                <? foreach ($items[$category] as $item) : ?>
                    <? if ($item['item']->isVisible($user_id) && $item['weighting']) : ?>
                        <tr>
                            <td>
                                <?= htmlReady($item['name']) ?>
                            </td>

                            <td style="text-align: right;">
                                <? if (isset($me['items'][$category][$item['id']]['points'])) : ?>
                                    <?= sprintf('%g', $me['items'][$category][$item['id']]['points']) ?>
                                <? else : ?>
                                    &ndash;
                                <? endif ?>
                            </td>

                            <td style="text-align: center;">
                                /
                            </td>

                            <td style="text-align: right;">
                                <?= sprintf('%g', $item['points']) ?>
                            </td>

                            <td style="text-align: right;">
                                <? if (isset($me['items'][$category][$item['id']]['percent'])) : ?>
                                    <?= sprintf('%.1f %%', $me['items'][$category][$item['id']]['percent']) ?>
                                <? else : ?>
                                    &ndash;
                                <? endif ?>
                            </td>

                            <? if ($use_weighting) : ?>
                                <td style="text-align: right;">
                                    <?= sprintf('%.1f %%', $item['weighting']) ?>
                                </td>
                            <? endif ?>
                        </tr>
                    <? endif ?>
                <? endforeach ?>
            <? endforeach ?>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="4" style="padding: 5px;">
                    <?= _('Prozent, gesamt') ?>
                </td>
                <td style="padding: 5px; text-align: right;">
                    <?= sprintf('%.1f %%', $me['overall']['weighting']) ?>
                </td>
                <? if ($use_weighting) : ?>
                    <td></td>
                <? endif ?>
            </tr>

            <tr style="font-weight: bold;">
                <td colspan="<?= $use_weighting ? 6 : 5 ?>" style="text-align: center;">
                    <?= _('Note:') ?>
                    <?= htmlReady($me['grade']) ?>
                    <? if ($me['grade_comment'] != '') : ?>
                        (<?= htmlReady($me['grade_comment']) ?>)
                    <? endif ?>
                </td>
            </tr>
        </tfoot>

    <? endforeach ?>
</table>
