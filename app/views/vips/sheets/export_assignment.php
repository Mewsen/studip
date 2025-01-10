<?php
/**
 * @var VipsAssignment $assignment
 * @var array $files
 */
?><?= '<?xml version="1.0" encoding="UTF-8"?>' ?>

<test xmlns="urn:vips:test:v1.0" id="test-<?= $assignment->id ?>" type="<?= $assignment->type ?>"
      start="<?= date('c', $assignment->start) ?>"
      <? if (!$assignment->isUnlimited()): ?>
          end="<?= date('c', $assignment->end) ?>"
      <? endif ?>
      <? if ($assignment->type === 'exam' && $assignment->options['duration']): ?>
          duration="<?= $assignment->options['duration'] ?>"
      <? endif ?>
      <? if ($assignment->block_id): ?>
          block="<?= htmlReady($assignment->block->name) ?>"
      <? endif ?>
      >
    <title>
        <?= htmlReady($assignment->test->title) ?>
    </title>
    <description>
        <?= htmlReady($assignment->test->description) ?>
    </description>
    <? if ($assignment->options['notes'] != ''): ?>
        <notes>
            <?= htmlReady($assignment->options['notes']) ?>
        </notes>
    <? endif ?>
    <limit
    <? if (isset($assignment->options['access_code'])): ?>
        access-code="<?= htmlReady($assignment->options['access_code']) ?>"
    <? endif ?>
    <? if (isset($assignment->options['ip_range'])): ?>
        ip-ranges="<?= htmlReady($assignment->options['ip_range']) ?>"
    <? endif ?>
    <? if ($assignment->options['resets'] === 0): ?>
        resets="0"
    <? endif ?>
    <? if (isset($assignment->options['max_tries'])): ?>
        tries="<?= $assignment->options['max_tries'] ?>"
    <? endif ?>
    />
    <option
    <? if ($assignment->options['evaluation_mode'] == VipsAssignment::SCORING_NEGATIVE_POINTS): ?>
        scoring-mode="negative_points"
    <? elseif ($assignment->options['evaluation_mode'] == VipsAssignment::SCORING_ALL_OR_NOTHING): ?>
        scoring-mode="all_or_nothing"
    <? endif ?>
    <? if ($assignment->isShuffled()): ?>
        shuffle-answers="true"
    <? endif ?>
    <? if ($assignment->isExerciseShuffled()): ?>
        shuffle-exercises="true"
    <? endif ?>
    >
    </option>
    <? if (isset($assignment->options['feedback'])): ?>
        <feedback-items>
            <? foreach ($assignment->options['feedback'] as $threshold => $feedback): ?>
                <feedback score="<?= (float) $threshold / 100 ?>">
                    <?= htmlReady($feedback) ?>
                </feedback>
            <? endforeach ?>
        </feedback-items>
    <? endif ?>
    <exercises>
        <? foreach ($assignment->test->exercise_refs as $exercise_ref): ?>
            <?= $this->render_partial($exercise_ref->exercise->getXMLTemplate($assignment), ['points' => $exercise_ref->points]) ?>
        <? endforeach ?>
    </exercises>
    <? if ($files): ?>
        <files>
            <? foreach ($files as $file): ?>
                <file id="file-<?= $file->id ?>" name="<?= htmlReady($file->name) ?>">
                    <?= base64_encode(file_get_contents($file->getPath())) ?>
                </file>
            <? endforeach ?>
        </files>
    <? endif ?>
</test>
