<?
/**
 * @var QuestionnaireQuestion $question
 * @var QuestionnaireAnswer[] $answers
 * @var $filtered string
 * @var $description string
 * @var $answerdata array
 * @var $options array
 */

?>

<div class="description_container">
    <div class="icon_container">
        <?= Icon::create('tan2', Icon::ROLE_INFO) ?>
    </div>
    <div class="description">
        <?= formatReady($description) ?>
    </div>
</div>


<table class="default nohover">
    <tbody>
    <? $countAnswers = $question->questionnaire->countAnswers() ?>
    <? foreach ($options as $key => $answer) : ?>
        <tr>
            <? $percentage = $countAnswers && isset($answerdata[$key]) ? round(count((array) $answerdata[$key]) / $countAnswers * 100) : 0 ?>

            <td style="text-align: right; background-size: <?= $percentage ?>% 100%; background-position: right center; background-image: url('<?= Assets::image_path("vote_lightgrey.png") ?>'); background-repeat: no-repeat;" width="50%">
                <strong><?= htmlReady($answer) ?></strong>
            </td>

            <td style="white-space: nowrap;">
                <? if (!empty($filtered) && $filtered == $key) : ?>
                    <a href=""
                       title="<?= _('Zeige wieder alle Ergebnisse ohne Filterung an.') ?>"
                       onclick="STUDIP.Questionnaire.removeFilter('<?= htmlReady($question['questionnaire_id']) ?>'); return false;">
                        <?= Icon::create('filter2')->asImg(['class' => 'text-bottom']) ?>
                        (<?= $percentage ?>% | <?= (int) count((array) $answerdata[$key]) ?>/<?= $countAnswers ?>)
                    </a>
                <? else : ?>
                    <a href=""
                       onclick="STUDIP.Questionnaire.addFilter('<?= htmlReady($question['questionnaire_id']) ?>', '<?= htmlReady($question->getId()) ?>', '<?= $key ?>'); return false;"
                       title="<?= _('Zeige nur Ergebnisse von Personen an, die diese Option gewählt haben.') ?>">
                        (<?= $percentage ?>% | <?= isset($answerdata[$key]) ? count((array) $answerdata[$key]) : 0 ?>/<?= $countAnswers ?>)
                    </a>
                <? endif ?>
            </td>

            <td width="50%">
                <? if (!$question->questionnaire['anonymous'] && isset($answerdata[$key]) && $answerdata[$key]) : ?>

                    <? $users = SimpleCollection::createFromArray(
                        User::findMany($answerdata[$key])); ?>

                    <? foreach ($answerdata[$key] as $index => $user_id) : ?>

                        <? $user = $users->findOneBy('user_id', $user_id); ?>

                        <? if ($user) : ?>
                            <a href="<?= URLHelper::getLink(
                                'dispatch.php/profile',
                                ['username' => $user->username]
                            ) ?>">
                                <?= Avatar::getAvatar($user_id, $user->username)->getImageTag(
                                    Avatar::SMALL,
                                    ['title' => $user->getFullname('no_title')]
                                ) ?>
                                <? if (count($answerdata[$key]) < 4) : ?>
                                    <?= htmlReady($user->getFullname('no_title')) ?>
                                <? endif ?>
                            </a>
                        <? endif ?>
                    <? endforeach ?>
                <? endif ?>
            </td>
        </tr>
    <? endforeach ?>
    </tbody>
</table>
