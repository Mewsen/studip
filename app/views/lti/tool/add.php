<?php
/**
 * @var AuthenticatedController $controller
 * @var string $range_id
 * @var LtiTool $tool
 * @var ?\LtiResourceLink $link
 */
?>
<form class="default" method="post" data-dialog="reload-on-close"
      action="<?= $controller->link_for('lti/tool/add/' . $range_id . '/' . $tool->id, ['link_id' => $link->id ?? '']) ?>">
    <?= CSRFProtection::tokenTag() ?>
    <?= $this->render_partial('lti/_tool_form_fields', [
        'tool'       => $tool,
        'link'       => $link ?? null,
    ]) ?>
</form>
