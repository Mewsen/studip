<?php
/**
 * @var AuthenticatedController $controller
 * @var LtiTool $tool
 * @var \LtiResourceLink $link
 */
?>
<form class="default" method="post"
      action="<?= $controller->link_for('course/lti/process_select_link/' . htmlReady($link->id ?? ''), ['tool_id' => $tool->id]) ?>">
    <?= CSRFProtection::tokenTag() ?>
    <?= $this->render_partial('lti/_tool_info', ['tool' => $tool, 'deployment' => $link->deployment ?? null]) ?>
    <div data-dialog-button>
        <?= \Studip\Button::create(_('Weiter'), 'continue') ?>
    </div>
</form>
