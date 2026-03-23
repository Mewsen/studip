<?php
class  EvaluationNavigation extends Navigation
{
    public function __construct()
    {
        parent::__construct(_('Evaluationen'));
        $this->setImage(Icon::create('question-automation', 'navigation', ["title" => _('Zentrale Evaluationsverwaltung')]));
    }

    protected function initSubNavigation()
    {
        parent::initSubNavigation();
        $navigation = new Navigation(_('Vorlagen'), 'dispatch.php/evaluation/pool');
        $this->addSubNavigation('pool', $navigation);
        $navigation = new Navigation(_('Profile'), 'dispatch.php/evaluation/profiles');
        $this->addSubNavigation('profiles', $navigation);
        $navigation = new Navigation(_('Archiv'), 'dispatch.php/evaluation/archive');
        $this->addSubNavigation('archive', $navigation);
    }
}
