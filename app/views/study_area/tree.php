<?php
/**
 * @var StudipStudyArea $node
 * @var string $open
 * @var int $layer
 * @var array $dont_open
 * @var string $compulsory
 * @var string|null $semester
 */
$layer = 0;

$url_params = [];
if ($semester && $semester !== Semester::findCurrent()) {
    $url_params['semester'] = $semester->id;
}
$url_params['node_id'] = 'StudipStudyArea_' . $node->id;

?>
<li>
<? if ($node->id !== 'root' && $node->required_children): ?>
    <input id='<?= htmlReady($node->id) ?>' type='checkbox' <?= $open && !in_array($layer, $dont_open) ? 'checked' : ''?>>
<? endif; ?>
    <label for='<?= htmlReady($node->id) ?>'></label>

<? if ($node->id !== 'root'): ?>
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
            'study_area/tree.php',
            ['node' => $child, 'open' => $open, 'layer' => ((int)$layer + 1), 'semester' => $semester]
        ) ?>
    <? endforeach; ?>
    </ul>
<? endif; ?>
</li>
