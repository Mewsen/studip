<?
/**
 * @var AuthenticatedController $controller
 * @var string $range_id
 * @var LtiTool $tool
 * @var LtiDeployment $deployment
 */
?>
<form class="default" method="post" data-dialog="reload-on-close"
      action="<?= $controller->link_for('lti/tool/edit/' . $tool->id . '/' . $range_id) ?>">
    <?= $this->render_partial(
        'lti/_tool_form_fields',
        [
            'tool'       => $tool,
            'deployment' => $deployment
        ]
    ) ?>
</form>
