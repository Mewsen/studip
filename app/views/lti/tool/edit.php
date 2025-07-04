<?
/**
 * @var AuthenticatedController $controller
 * @var string $range_id
 * @var LtiTool $tool
 * @var ?\LtiResourceLink $link
 */
?>
<? if ($tool) : ?>
    <form class="default" method="post" data-dialog="reload-on-close"
          action="<?= $controller->link_for('lti/tool/edit/' . $range_id . '/' . $tool->id, ['link_id' => $link->id ?? '']) ?>">
        <?= CSRFProtection::tokenTag() ?>
        <?= $this->render_partial('lti/_tool_form_fields', [
            'tool'       => $tool,
            'deployment' => $link ?? null,
        ]) ?>
    </form>
<? endif ?>
