<?php
/**
 * @var ClozeTask $exercise
 * @var float|int $points
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
        <item type="text-line">
            <answers>
                <? foreach ($exercise->task['answers'] as $answer): ?>
                    <answer score="<?= (float) $answer['score'] ?>">
                        <?= htmlReady($answer['text']) ?>
                    </answer>
                <? endforeach ?>
            </answers>
            <? if (!empty($exercise->task['compare'])): ?>
                <evaluation-hints>
                    <similarity type="<?= htmlReady($exercise->task['compare']) ?>"/>
                    <? if ($exercise->task['compare'] === 'numeric'): ?>
                        <input-data type="relative-epsilon">
                            <?= (float) $exercise->task['epsilon'] ?>
                        </input-data>
                    <? endif ?>
                </evaluation-hints>
            <? endif ?>
            <? if ($exercise->options['feedback'] != ''): ?>
                <feedback>
                    <?= htmlReady($exercise->options['feedback']) ?>
                </feedback>
            <? endif ?>
        </item>
    </items>
    <? if ($exercise->folder): ?>
        <file-refs<? if ($exercise->options['files_hidden']): ?> hidden="true"<? endif ?>>
            <? foreach ($exercise->folder->file_refs as $file_ref): ?>
                <file-ref ref="file-<?= $file_ref->file_id ?>"/>
            <? endforeach ?>
        </file-refs>
    <? endif ?>
</exercise>
