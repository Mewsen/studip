<?
/**
 * @var QuestionnaireQuestion $question
 * @var QuestionnaireAnswer[] $answers
 * @var $filtered string
 */
?>

<? if ($question['questiondata']['geschlecht']) : ?>
    <? $options = [
        0 => _('unbekannt'),
        1 => _('männlich'),
        2 => _('weiblich'),
        3 => _('divers')
    ];
    $answerdata = [];
    foreach ($answers as $answer) {
        if (isset($answer['answerdata']['geschlecht'])) {
            $answerdata[$answer['answerdata']['geschlecht']][] = $answer['user_id'];
        }
    }
    ?>
    <?= $this->render_partial('questionnaire/question_types/automated_data/_evaluation_part.php', [
        'options' => $options,
        'description' => _('Geschlecht'),
        'answerdata' => $answerdata,
    ]) ?>
<? endif ?>

<? if ($question['questiondata']['studienfach']) : ?>
    <? $options = [];
    $answerdata = [];
    foreach ($answers as $answer) {
        if (isset($answer['answerdata']['studienfach'])) {
            foreach ($answer['answerdata']['studienfach'] as $studienfach) {
                if (!in_array($studienfach, $options)) {
                    $options[$studienfach] = $studienfach;
                }
                $answerdata[$studienfach][] = $answer['user_id'];
            }
        }
    }
    ?>
    <?= $this->render_partial('questionnaire/question_types/automated_data/_evaluation_part.php', [
        'options' => $options,
        'description' => _('Studiengang'),
        'answerdata' => $answerdata,
    ]) ?>
<? endif ?>

<? if ($question['questiondata']['studiengang']) : ?>
    <? $options = [];
    $answerdata = [];
    foreach ($answers as $answer) {
        if (isset($answer['answerdata']['studiengang'])) {
            foreach ($answer['answerdata']['studiengang'] as $studiengang) {
                if (!in_array($studiengang, $options)) {
                    $options[$studiengang] = $studiengang;
                }
                $answerdata[$studiengang][] = $answer['user_id'];
            }
        }
    }
    ?>
    <?= $this->render_partial('questionnaire/question_types/automated_data/_evaluation_part.php', ['options' => $options, 'description' => _('Studiengang'), 'answerdata' => $answerdata]) ?>
<? endif ?>

<? if ($question['questiondata']['studiengangfachsemester']) : ?>
    <? $options = [];
    $answerdata = [];
    foreach ($answers as $answer) {
        if (isset($answer['answerdata']['studiengangfachsemester'])) {
            foreach ($answer['answerdata']['studiengangfachsemester'] as $studiengangfachsemester) {
                if (!in_array($studiengangfachsemester, $options)) {
                    $options[$studiengangfachsemester] = $studiengangfachsemester;
                }
                $answerdata[$studiengangfachsemester][] = $answer['user_id'];
            }
        }
    }
    ?>
    <?= $this->render_partial('questionnaire/question_types/automated_data/_evaluation_part.php', [
        'options' => $options, 'description' => _('Studiengang und Fachsemester'),
        'answerdata' => $answerdata,
    ]) ?>
<? endif ?>

<? if (isset($question['questiondata']['datafields'])) :
    foreach ($question['questiondata']['datafields'] as $datafield_id) :
        $datafield = DataField::find($datafield_id);
        if ($datafield) : ?>
            <? $options = [];
            $answerdata = [];
            foreach ($answers as $answer) {
                if (isset($answer['answerdata']['datafields'][$datafield_id])) {
                    if (!in_array($answer['answerdata']['datafields'][$datafield_id], $options)) {
                        $options[$answer['answerdata']['datafields'][$datafield_id]] = $answer['answerdata']['datafields'][$datafield_id];
                    }
                    $answerdata[$answer['answerdata']['datafields'][$datafield_id]][] = $answer['user_id'];
                }
            }
            ?>
            <?= $this->render_partial('questionnaire/question_types/automated_data/_evaluation_part.php', [
                'options' => $options,
                'description' => $datafield['name'],
                'answerdata' => $answerdata,
            ]) ?>
        <? endif ?>
    <? endforeach ?>
<? endif ?>
