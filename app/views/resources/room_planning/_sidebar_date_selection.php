<?php
if (!isset($date)) {
    $date = new DateTime();
}
?>
<?= \Studip\LinkButton::create(
        _('Heute'),
        URLHelper::getURL('', ['defaultDate' => date('Y-m-d')])
    ); ?>

<input id="booking-plan-jmpdate" type="text"
 name="booking-plan-jmpdate" value="<?= $date->format('d.m.Y') ?>">
