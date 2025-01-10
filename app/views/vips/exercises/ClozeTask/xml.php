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
        <item type="cloze-<?= $exercise->interactionType() ?>">
            <description>
                <? foreach (explode('[[]]', $exercise->task['text']) as $blank => $text): ?>
                    <text><?= htmlReady($text) ?></text>
                    <? if (isset($exercise->task['answers'][$blank])): ?>
                        <answers<? if ($exercise->isSelect($blank, false)): ?> select="true"<? endif ?>>
                            <? foreach ($exercise->task['answers'][$blank] as $answer): ?>
                                <answer score="<?= $answer['score'] ?>"><?= htmlReady($answer['text']) ?></answer>
                            <? endforeach ?>
                        </answers>
                    <? endif ?>
                <? endforeach ?>
            </description>
            <? if (isset($exercise->task['input_width'])): ?>
                <submission-hints>
                    <input type="text" width="<?= (int) $exercise->task['input_width'] ?>"/>
                </submission-hints>
            <? endif ?>
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
