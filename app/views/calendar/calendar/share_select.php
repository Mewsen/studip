<?
/**
 * @var $controller AuthenticatedController
 */
?>
<section class="square-item-container">
    <div>
        <a href="<?= $controller->link_for('calendar/calendar/share') ?>"
           data-dialog="size=default">
            <?= Icon::create('group2')->asImg(50) ?>
            <?= _('Kalender mit anderen Personen teilen') ?>
        </a>
        <a href="<?= $controller->link_for('calendar/calendar/publish') ?>"
           data-dialog="size=auto">
            <?= Icon::create('globe')->asImg(50) ?>
            <?= _('Kalender weltweit veröffentlichen') ?>
        </a>
    </div>
</section>
