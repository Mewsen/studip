<?php
class MyStudygroupsController extends AuthenticatedController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if (!$GLOBALS['perm']->have_perm('root')) {
            Navigation::activateItem('/browse/my_studygroups/index');
        }
    }

    public function index_action($is_widget = false)
    {
        PageLayout::setHelpKeyword('Basis.MeineStudiengruppen');
        PageLayout::setTitle(_('Meine Studiengruppen'));
        URLHelper::removeLinkParam('cid');

        $this->is_widget    = (bool)$is_widget;
        $this->studygroups  = StudygroupModel::getStudygroups();
        $this->nav_elements = MyRealmModel::calc_single_navigation($this->studygroups);

        // do not render sidebar if this is the widget
        if (!$this->is_widget) {
            $this->set_sidebar();
        }
    }

    public function set_sidebar()
    {
        if ($GLOBALS['user']->perms === 'user') {
            return;
        }

        $sidebar = Sidebar::Get();

        $actions = new ActionsWidget();
        $actions->addLink(
            _('Neue Studiengruppe anlegen'),
            URLHelper::getURL('dispatch.php/course/wizard', ['studygroup' => 1]),
            Icon::create('add')
        )->asDialog('size=auto');
        if (count($this->studygroups) > 0) {
            $actions->addLink(
                _('Farbgruppierung ändern'),
                URLHelper::getURL('dispatch.php/my_courses/groups/all/true'),
                Icon::create('group4')
            )->asDialog();
        }
        $sidebar->addWidget($actions);
    }
}
