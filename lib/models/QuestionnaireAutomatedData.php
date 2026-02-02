<?php

/**
 * @license GPL2 or any later version
 *
 * @property string $id alias column for question_id
 * @property string $question_id database column
 * @property string $questionnaire_id database column
 * @property string $questiontype database column
 * @property string|null $internal_name database column
 * @property JSONArrayObject $questiondata database column
 * @property int $position database column
 * @property int $chdate database column
 * @property int $mkdate database column
 * @property SimpleORMapCollection<QuestionnaireAnswer> $answers has_many QuestionnaireAnswer
 * @property Questionnaire $questionnaire belongs_to Questionnaire
 */
class QuestionnaireAutomatedData extends QuestionnaireQuestion implements QuestionType
{
    public static function isDesignElement()
    {
        return false;
    }

    public static function getIcon(bool $active = false) : Icon
    {
        return Icon::create(static::getIconShape(), $active ? Icon::ROLE_CLICKABLE : Icon::ROLE_INFO);
    }

    /**
     * Returns the shape of the icon of this QuestionType
     */
    public static function getIconShape(): string
    {
        return 'question-automation';
    }

    public static function getName(): string
    {
        return _('Automatik');
    }

    public static function getEditingComponent(): array
    {
        if ($GLOBALS['perm']->have_perm(Config::get()->QUESTIONNAIRE_AUTOMATED_DATA_PERM)) {
            return ['AutomatedDataEdit', ''];
        } else {
            //in this case the question_type is not allowed:
            return [];
        }
    }

    public function beforeStoringQuestiondata($questiondata)
    {
        return $questiondata;
    }

    public function getDisplayTemplate(): ?Flexi\Template
    {
        if ($GLOBALS['user']->id !== 'nobody') {
            $factory = new Flexi\Factory(realpath(__DIR__.'/../../app/views'));
            $template = $factory->open('questionnaire/question_types/automated_data/answer');
            $template->set_attribute('question', $this);
            return $template;
        } else {
            return null;
        }
    }

    public function createAnswer(): QuestionnaireAnswer
    {
        $answer = $this->getMyAnswer();
        $answerdata = [];
        if ($GLOBALS['user']->id !== 'nobody') {
            $user = User::findCurrent();
            if ($this['questiondata']['geschlecht']) {
                $answerdata['geschlecht'] = $user->geschlecht;
            }
            if ($this['questiondata']['studienfach']) {
                $answerdata['studienfach'] = [];
                foreach ($user->studycourses as $studycourse) {
                    $answerdata['studienfach'][] = $studycourse->studycourse_name;
                }
            }
            if ($this['questiondata']['studiengang']) {
                $answerdata['studiengang'] = [];
                foreach ($user->studycourses as $studycourse) {
                    $answerdata['studiengang'][] = $studycourse->studycourse_name . ' ' . $studycourse->degree_name;
                }
            }
            if ($this['questiondata']['studiengangfachsemester']) {
                $answerdata['studiengangfachsemester'] = [];
                foreach ($user->studycourses as $studycourse) {
                    $answerdata['studiengangfachsemester'][] = $studycourse->studycourse_name . ' ' . $studycourse->degree_name . ' ' . $studycourse['semester'];
                }
            }
            foreach ($this['questiondata']['datafields'] as $datafield_id) {
                $datafieldentry = DatafieldEntryModel::findOneBySQL('range_id = :user_id AND datafield_id = :datafield_id', [
                    'datafield_id' => $datafield_id,
                    'user_id' => $user->getId()
                ]);
                $answerdata['datafields'][$datafield_id] = $datafieldentry['content'];
            }
        }

        $answer->answerdata = $answerdata;
        return $answer;
    }

    public function getUserIdsOfFilteredAnswer($answer_option): array
    {
        $user_ids = [];
        foreach ($this->answers as $answer) {
            $answerData = $answer['answerdata']->getArrayCopy();
            if (in_array($answer_option, (array) $answerData['answers'])) {
                $user_ids[] = $answer['user_id'];
            }
        }
        return $user_ids;
    }

    public function getResultTemplate($only_user_ids = null): Flexi\Template
    {
        $answers = $this->answers;
        if ($only_user_ids !== null) {
            foreach ($answers as $key => $answer) {
                if (!in_array($answer['user_id'], $only_user_ids)) {
                    unset($answers[$key]);
                }
            }
        }
        $factory = new Flexi\Factory(realpath(__DIR__.'/../../app/views'));
        $template = $factory->open('questionnaire/question_types/automated_data/evaluation');
        $template->set_attribute('question', $this);
        $template->set_attribute('answers', $answers);
        return $template;
    }

    public function getResultArray(): array
    {
        $output = [];

        $options = [];
        if ($this['questiondata']['geschlecht']) {
            $options['geschlecht'] = _('Geschlecht');
        }
        if ($this['questiondata']['studienfach']) {
            $options['studienfach'] = _('Studienfach');
        }
        if ($this['questiondata']['studiengang']) {
            $options['studiengang'] = _('Studiengang');
        }
        if ($this['questiondata']['studiengangfachsemester']) {
            $options['studiengangfachsemester'] = _('Studiengang und Fachsemester');
        }
        foreach ($this['questiondata']['datafields'] as $datafield_id) {
            $datafield = DataField::find($datafield_id);
            if ($datafield) {
                $options[$datafield_id] = (string) $datafield['name'];
            }
        }

        $map = [
            0 => _('unbekannt'),
            1 => _('männlich'),
            2 => _('weiblich'),
            3 => _('divers')
        ];

        foreach ($options as $key => $option) {
            $answerOption = [];
            $countNobodys = 0;

            foreach ($this->answers as $answer) {
                $answerData = $answer['answerdata']->getArrayCopy();

                if ($answer['user_id'] && $answer['user_id'] != 'nobody') {
                    $userId = $answer['user_id'];
                } else {
                    $countNobodys++;
                    $userId = _('unbekannt').' '.$countNobodys;
                }

                if (isset($answerData[$key])) {
                    if (is_array($answerData[$key])) {
                        $answerOption[$userId] = implode('|', $answerData[$key]);
                    } else {
                        $answerOption[$userId] = $map[$answerData[$key]];
                    }
                } elseif(strlen($key) === 32 && isset($answerData['datafields']) && isset($answerData['datafields'][$key])) {
                    $answerOption[$userId] = $answerData['datafields'][$key];
                } else {
                    $answerOption[$userId] = '';
                }
            }

            $output[$option] = $answerOption;
        }

        return $output;
    }
}
