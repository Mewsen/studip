<?php
/**
 * @var AuthenticatedController $controller
 * @var LtiTool $tool
 * @var LtiDeployment $deployment
 */
?>
<form class="default" method="post"
      action="<?= $controller->link_for('course/lti/select_link/' . htmlReady($deployment->id), ['tool_id' => $tool->id]) ?>">
    <?= CSRFProtection::tokenTag() ?>
    <?= $this->render_partial('lti/_tool_info', ['tool' => $tool, 'deployment' => $deployment]) ?>
    <div data-dialog-button>
        <?= \Studip\Button::create(_('Weiter'), 'continue') ?>
    </div>
</form>
