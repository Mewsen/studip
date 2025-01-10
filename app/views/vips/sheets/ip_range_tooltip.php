<?= _('Beispiele:') ?>

<dl>
    <dt>131.173.73.42</dt>
    <dd>
        <?= _('Gibt nur diese IP-Adresse frei.') ?>
    </dd>
    <dt>131.173.73 <?= _('oder') ?> 131.173.73.0/24</dt>
    <dd>
        <?= _('Gibt alle IP-Adressen frei, die so beginnen.') ?>
    </dd>
    <dt>131.173.73-131.173.75</dt>
    <dd>
        <?= _('Gibt alle IP-Adressen aus dem Bereich 131.173.73 bis 131.173.75 frei.') ?>
    </dd>
    <? if (!empty($exam_rooms)): ?>
        <dt>#94/E01</dt>
        <dd>
            <?= _('Gibt alle IP-Adressen in diesem Raum frei.') ?>
        </dd>
    <? endif?>
</dl>

<span class="smaller">
    <?= _('Außerdem können Listen aller genannten Fälle eingetragen werden (durch Komma oder Leerzeichen getrennt).') ?>
</span>
