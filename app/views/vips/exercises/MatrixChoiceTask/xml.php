<?php
/**
 * @var ClozeTask $exercise
 * @var float|int $points
 * @var array $optional_choice
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
        <item type="choice-multiple">
            <choices>
                <? foreach ($exercise->task['choices'] + $optional_choice as $key => $choice): ?>
                    <choice type="<?= $key == 0 ? 'yes' : ($key == 1 ? 'no' : ($key == -1 ? 'none' : 'group')) ?>">
                        <?= htmlReady($choice) ?>
                    </choice>
                <? endforeach ?>
            </choices>
            <answers>
                <? foreach ($exercise->task['answers'] as $answer): ?>
                    <answer score="<?= $answer['choice'] ? 0 : 1 ?>" correct="<?= (int) $answer['choice'] ?>">
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
    </items>
    <? if ($exercise->folder): ?>
        <file-refs<? if ($exercise->options['files_hidden']): ?> hidden="true"<? endif ?>>
            <? foreach ($exercise->folder->file_refs as $file_ref): ?>
                <file-ref ref="file-<?= $file_ref->file_id ?>"/>
            <? endforeach ?>
        </file-refs>
    <? endif ?>
</exercise>
