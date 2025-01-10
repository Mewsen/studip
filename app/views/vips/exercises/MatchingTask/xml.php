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
        <item type="<?= $exercise->isMultiSelect() ? 'matching-multiple' : 'matching' ?>">
            <choices>
                <? foreach ($exercise->task['groups'] as $group): ?>
                    <choice type="group">
                        <?= htmlReady($group) ?>
                    </choice>
                <? endforeach ?>
            </choices>
            <answers>
                <? foreach ($exercise->task['answers'] as $answer): ?>
                    <answer score="1" correct="<?= $answer['group'] ?>">
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
