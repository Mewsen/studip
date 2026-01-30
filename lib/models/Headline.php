<?php

class Headline extends QuestionnaireQuestion implements QuestionType
{
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
        return 'question-text';
    }

    public static function getName()
    {
        return _('Überschrift');
    }


    public function getDisplayTemplate()
    {
        $factory = new Flexi\Factory(realpath(__DIR__.'/../../app/views'));
        $template = $factory->open('questionnaire/question_types/info/info');
        $template->set_attribute('vote', $this);
        return $template;
    }

    static public function getEditingComponent()
    {
        return ['HeadlineEdit', ''];
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
        $template = $factory->open('questionnaire/question_types/info/info');
        $template->set_attribute('vote', $this);
        return $template;
    }

    public function getResultArray()
    {
        return [];
    }

}
