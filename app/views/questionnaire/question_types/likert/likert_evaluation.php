<?php
/**
 * @var QuestionnaireQuestion $vote
 * @var QuestionnaireAnswer[] $answers
 * @var $filtered string
 */
$options = $vote->questiondata['options'];
?>

<div class="description_container">
    <div class="icon_container">
        <?= Icon::create('question-likert', Icon::ROLE_INFO)->asImg(20) ?>
    </div>
    <div class="description">
        <?= formatReady(isset($vote->questiondata) && isset($vote->questiondata['description']) ? $vote->questiondata['description'] : '') ?>
    </div>
</div>

<table class="default nohover">
    <thead>
        <tr>
            <th><?= _('Aussage') ?></th>
            <? foreach ($options as $option) : ?>
                <th class="option-cell"><?= htmlReady($option) ?></th>
            <? endforeach ?>
        </tr>
    </thead>
    <tbody>
        <? $countAnswers = $vote->questionnaire->countAnswers() ?>
        <? if (!empty($vote->questiondata['statements'])) : ?>
            <? foreach ($vote->questiondata['statements'] as $key => $statement) : ?>
            <tr>
                <td>
                    <strong><?= htmlReady($statement) ?></strong>
                </td>

                <?
            $average = 0;
            if (count($answers) > 0) {
                $countableAnswers = 0;
                foreach ($answers as $answer) {
                    $average += $answer['answerdata']['answers'][$key];
                    if ($answer['answerdata']['answers'][$key] !== null) {
                        $countableAnswers++;
                    }
                }
                if ($countableAnswers > 0) {
                    $average /= $countableAnswers;
                }
            }
            ?>

            <? foreach($options as $option_index => $option) : ?>
                <? if ($countAnswers) : ?>
                    <?
                    $hits = 0;
                    $names = [];
                    foreach ($answers as $answer) {
                        if ($answer['answerdata']['answers'][$key] === $option_index) {
                            $hits++;
                            if ($answer['user_id'] && $answer['user_id'][0] !== 'q' && $answer['user_id'][0] !== 'n') {
                                $names[] = $answer->user->getFullName('full');
                            }
                        }
                    }
                    $color = $countableAnswers > 0 ? 'hsl(0 0% '.round(70 + (30 * (1 - ($hits / $countableAnswers)) )).'%)' : 'transparent';
                    ?>
                    <td style="background-color: <?= $color ?>;" <?= count($names) > 0 ? 'title="'.htmlReady(implode(', ', $names)).'"' : ''?>>
                        <? if ($filtered !== null && $filtered == $key.'_'.$option_index) : ?>
                            <a href=""
                               onclick="STUDIP.Questionnaire.removeFilter('<?= htmlReady($vote['questionnaire_id']) ?>'); return false;"
                               title="<?= _('Zeige wieder alle Ergebnisse ohne Filterung an.') ?>">
                                <?= Icon::create('filter2', Icon::ROLE_CLICKABLE)->asImg(16, ['class' => 'text-bottom']) ?>
                                <?= round(100 * $hits / $countableAnswers) ?>%
                            </a>
                        <? else : ?>
                            <a href=""
                               onclick="STUDIP.Questionnaire.addFilter('<?= htmlReady($vote['questionnaire_id']) ?>', '<?= htmlReady($vote->getId()) ?>', '<?= $key.'_'.$option_index ?>'); return false;"
                               title="<?= _('Zeige nur Ergebnisse von Personen an, die diese Option gewählt haben.') ?>">
                                <?= $countableAnswers > 0 ? round(100 * $hits / $countableAnswers) : 0  ?>%
                            </a>
                        <? endif ?>
                    </td>
                <? else : ?>
                    <td>
                        0%
                    </td>
                <? endif ?>
                <? endforeach ?>
            </tr>
        <? endforeach ?>
    <? endif ?>
    </tbody>
</table>
