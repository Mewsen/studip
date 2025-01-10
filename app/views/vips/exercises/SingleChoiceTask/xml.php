<?php
/**
 * @var ClozeTask $exercise
 * @var float|int $points
 * @var array $optional_answer
 */
?>
<exercise id="exercise-<?= $exercise->id ?>" points="<?= $points ?>"
<? if ($exercise->options['comment']): ?> feedback="true"<? endif ?>>
    <title>
        <?= htmlReady($exercise->title) ?>
    </title>
    <description>
        <?= htmlReady($exercise->description) ?>
    </description>
    <? if ($exercise->options['hint'] != ''): ?>
        <hint>
            <?= htmlReady($exercise->options['hint']) ?>
        </hint>
    <? endif ?>
    <items>
        <? foreach ($exercise->task as $group => $task): ?>
            <item type="choice-single">
                <? if (isset($task['description']) && $task['description'] != ''): ?>
                    <description>
                        <text><?= htmlReady($task['description']) ?></text>
                    </description>
                <? endif ?>
                <answers>
                    <? foreach ($task['answers'] + $optional_answer as $key => $answer): ?>
                        <answer score="<?= (int) $answer['score'] ?>"<? if ($key == -1): ?> default="true"<? endif ?>>
                            <?= htmlReady($answer['text']) ?>
                        </answer>
                    <? endforeach ?>
                </answers>
                <? if ($exercise->options['feedback'] != ''): ?>
                    <feedback>
                        <?= htmlReady($exercise->options['feedback']) ?>
                    </feedback>
                <? endif ?>
            </item>
        <? endforeach ?>
    </items>
    <? if ($exercise->folder): ?>
        <file-refs<? if ($exercise->options['files_hidden']): ?> hidden="true"<? endif ?>>
            <? foreach ($exercise->folder->file_refs as $file_ref): ?>
                <file-ref ref="file-<?= $file_ref->file_id ?>"/>
            <? endforeach ?>
        </file-refs>
    <? endif ?>
</exercise>
