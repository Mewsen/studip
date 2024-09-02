<?php
/**
 * @var StudipStudyArea $node
 * @var string $semester_id An optional semester_id.
 * @var string $open
 * @var int $layer
 * @var array $dont_open
 * @var string $compulsory
 */
$layer = 0;
?>
<li>
<? if ($node->id !== 'root' && $node->required_children): ?>
    <input id='<?= htmlReady($node->id) ?>' type='checkbox' <?= $open && !in_array($layer, $dont_open) ? 'checked' : ''?>>
<? endif; ?>
    <label for='<?= htmlReady($node->id) ?>'></label>

<? if ($node->id !== 'root'): ?>
    <?
    $url_params = ['node_id' => 'StudipStudyArea_' . $node->id];
    if ($semester_id) {
        $url_params['semester'] = $semester_id;
    }
    ?>
    <a href="<?= URLHelper::getLink('dispatch.php/search/courses', $url_params, true) ?>">
        <?= htmlReady($node->name) ?>
    </a>
<? else: ?>
    <?= htmlReady($node->name) ?>
<? endif; ?>

<? if ($node->required_children): ?>
    <ul>
    <? foreach ($node->required_children as $child): ?>
        <?= $this->render_partial(
            'study_area/tree.php', ['node' => $child, 'open' => $open, 'layer' => $layer + 1, 'semester_id' => $semester_id]) ?>
    <? endforeach; ?>
    </ul>
<? endif; ?>
</li>
