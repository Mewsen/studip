<?php
/**
 * @var Vips_SolutionsController $controller
 * @var int $assignment_id
 * @var string $view
 * @var string $expand
 */
?>
<form class="default" action="<?= $controller->autocorrect_solutions() ?>" method="POST">
    <?= CSRFProtection::tokenTag() ?>

    <input type="hidden" name="assignment_id" value="<?= $assignment_id ?>">
    <input type="hidden" name="view" value="<?= htmlReady($view) ?>">
    <input type="hidden" name="expand" value="<?= htmlReady($expand) ?>">

    <h4>
        <?= _('Manuell durchgeführte Korrekturen werden durch diese Aktion nicht überschrieben.') ?>
    </h4>

    <label>
        <input type="checkbox" name="corrected" value="1">
        <?= _('Unbekannte Eingaben als sicher falsch bewerten') ?>
        <?= tooltipIcon(_('Wird diese Option nicht ausgewält, bleiben die betroffenen Aufgaben als unkorrigiert markiert.')) ?>
    </label>

    <footer data-dialog-button>
        <?= Studip\Button::createAccept(_('Autokorrektur starten'), 'autocorrect_solutions') ?>
    </footer>
</form>
