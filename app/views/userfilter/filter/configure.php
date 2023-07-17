<?php
use Studip\Button;

/**
 * @var Userfilter_FieldController $controller
 * @var string $containerID
 */
?>
<div id="conditionfields">
    <?= $this->render_partial('userfilter/field/configure.php', ['is_first' => true]); ?>
</div>
<br/>
<a href="#" onclick="return STUDIP.UserFilter.addConditionField('conditionfields', '<?= $controller->url_for('userfilter/field/configure') ?>')">
    <?= Icon::create('add')->asImg(['alt' => _('Auswahlfeld hinzufügen')]) ?>
    <?= _('Auswahlfeld hinzufügen') ?>
</a>
<br/><br/>
<div class="submit_wrapper" data-dialog-button>
    <?= Button::createAccept(_('Speichern'), 'submit', ['onclick' => "STUDIP.UserFilter.addCondition('".$containerId."', '".$controller->url_for('userfilter/filter/add')."');"]) ?>
    <?= Button::createCancel(_('Abbrechen')) ?>
</div>
