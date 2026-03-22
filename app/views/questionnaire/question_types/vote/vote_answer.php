<!-- TODO vue app! -->
<?php
/**
 * @var QuestionnaireQuestion $vote
 */

$answers = $vote->questiondata['options'];
$indexMap = count($answers) ? range(0, count($answers) - 1) : [];
if ($vote->questiondata['randomize']) {
    shuffle($indexMap);
}

$response = $vote->getMyAnswer();
$responseData = $response['answerdata'] ? $response['answerdata']->getArrayCopy() : [];
?>
<div <?= isset($vote->questiondata['mandatory']) && $vote->questiondata['mandatory'] ? ' class="mandatory"' : "" ?>>
    <?= $this->render_partial('questionnaire/_answer_description_container', ['vote' => $vote, 'iconshape' => 'vote']) ?>

    <div class="hidden invalidation_notice">
        <?= _("Diese Frage muss beantwortet werden.") ?>
    </div>

    <ul class="clean">
        <? foreach ($indexMap as $index) : ?>
            <li>
                <label>
                    <? if ($vote->questiondata['multiplechoice']) : ?>

                        <input type="checkbox"
                               name="answers[<?= $vote->getId() ?>][answerdata][answers][<?= $index ?>]"
                               value="<?= $index ?>"
                               <?= isset($responseData['answers']) && in_array($index, (array) $responseData['answers']) ? 'checked' : '' ?>>

                    <? else : ?>

                        <input type="radio"
                               name="answers[<?= $vote->getId() ?>][answerdata][answers]"
                               value="<?= $index ?>"
                               <?= isset($responseData['answers']) && $index == $responseData['answers'] ? 'checked' : '' ?>>
                    <? endif ?>

                    <?= htmlReady($answers[$index]) ?>
                </label>
            </li>
        <? endforeach ?>

        <!-- if there is a free text field ... -->
        <!-- TODO Freitextfeld aktivieren, wenn checkbox angeklickt ist -->
        <? if (isset($vote->questiondata['freetextfield'])) : ?>
        <li>
            <label>
                <? if ($vote->questiondata['multiplechoice']) : ?>

                    <input type="checkbox"
                           name="answers[<?= $vote->getId() ?>][answerdata][answers][<?= $index +1 ?>]"
                           value="<?= $index +1 ?>"
                        <?= isset($responseData['answers']) && in_array($index +1, (array) $responseData['answers']) ? 'checked' : '' ?>>

                <? else : ?>

                    <input type="radio"
                           name="answers[<?= $vote->getId() ?>][answerdata][answers]"
                           value="<?= $index +1 ?>"
                        <?= isset($responseData['answers']) && $index == $responseData['answers'] ? 'checked' : '' ?>>
                <? endif ?>


                <?= _('Sonstiges') . ':' ?>
                <textarea name="answers[<?= $vote->getId() ?>][answerdata][freetext]"><?= htmlReady($responseData['freetext'] ?? '') ?></textarea>
            </label>
        </li>
        <? endif ?>

    </ul>

</div>
