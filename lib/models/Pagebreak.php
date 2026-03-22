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
class Pagebreak extends QuestionnaireQuestion implements QuestionType
{
    public static function isDesignElement()
    {
        return true;
    }

    public static function getIcon(bool $active = false) : Icon
    {
        return Icon::create(static::getIconShape(), $active ? 'clickable' : 'info');
    }

    /**
     * Returns the shape of the icon of this QuestionType
     * @return string
     */
    public static function getIconShape()
    {
        // TODO we need an icon
        return 'files';
    }

    public static function getName()
    {
        return _('Seitenumbruch');
    }


    public function getDisplayTemplate()
    {
        $factory = new Flexi\Factory(realpath(__DIR__.'/../../app/views'));
        $template = $factory->open('questionnaire/question_types/designelements/divider');
        $template->set_attribute('vote', $this);
        return $template;
    }

    static public function getEditingComponent()
    {
        return ['PagebreakEdit', ''];
    }

    static public function getAnsweringComponent()
    {
        return ['PagebreakAnswer', ''];
    }

    public function beforeStoringQuestiondata($questiondata)
    {
        $questiondata['description'] = \Studip\Markup::markAsHtml(
            \Studip\Markup::purifyHtml($questiondata['description'])
        );
        return $questiondata;
    }

    public function createAnswer()
    {
        return $this->getMyAnswer();
    }

    public function getUserIdsOfFilteredAnswer($answer_option)
    {
        return [];
    }

    public function getResultTemplate($only_user_ids = null)
    {
        $factory = new Flexi\Factory(realpath(__DIR__.'/../../app/views'));
        $template = $factory->open('questionnaire/question_types/designelements/pagebreak');
        $template->set_attribute('vote', $this);
        return $template;
    }

    public function getResultArray()
    {
        return [];
    }

}
