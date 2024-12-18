<?php
/**
 * @var Studip\Fullcalendar $fullcalendar
 * @var array $selections
 */
?>
<?= $fullcalendar ?>
<? if (count($selections) > 0) : ?>
    <ul class="map-key-list">
        <? $color_index = 1 ?>
        <li class="map-key">
            <span style="background-color:<?= Config::get()->PERS_TERMIN_KAT[$color_index++]['bgcolor'] ?>">
                &nbsp;
            </span>
            <?= htmlReady($selections[0]->base_version->getDisplayName()) ?>
        </li>
    <? foreach ($selections as $selection) : ?>
        <? if ($selection->base_version->id !== $selection->comp_version->id) : ?>
            <li class="map-key">
                <span style="background-color:<?= Config::get()->PERS_TERMIN_KAT[$color_index++]['bgcolor'] ?>"></span>
                <?= htmlReady($selection->comp_version->getDisplayName()) ?>
            </li>
        <? endif ?>
    <? endforeach ?>
    </ul>
<? endif ?>

