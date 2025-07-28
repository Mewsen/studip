<?php
use Forum\DiscussionType;

class Course_Forum_DiscussionTypesController extends AuthenticatedController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        PageLayout::setTitle(_('Forum Diskussions-Typ'));

        $GLOBALS['perm']->check('root');

        Navigation::activateItem('/admin/locations/forum_discussion_types');

        $actions = new ActionsWidget();

        $actions->addLink(
            _('Neuen Diskussionstyp anlegen'),
            $this->url_for('course/forum/discussion_types/edit'),
            Icon::create('add', Icon::ROLE_CLICKABLE, ['title' => _('Neuen Diskussionstyp anlegen')])
        )->asDialog('width=700');

        Sidebar::Get()->addWidget($actions);
    }

    public function index_action()
    {
        $this->discussion_types = DiscussionType::findBySQL("TRUE ORDER BY mkdate DESC");
    }

    public function edit_action(DiscussionType $discussion_type = null)
    {
        if ($discussion_type->isNew()) {
            PageLayout::setTitle(_('Neuen Diskussionstyp anlegen'));
        } else {
            PageLayout::setTitle(_('Diskussionstyp bearbeiten'));
        }

        $icons = [];

        foreach (scandir($GLOBALS['STUDIP_BASE_PATH'] . '/public/assets/images/icons/blue') as $icon_path) {
            if (!is_dir(
                    $GLOBALS['STUDIP_BASE_PATH'] . '/public/assets/images/icons/blue/'
                ) . $icon_path && $icon_path[0] !== '.') {
                if (stripos($icon_path, '.svg')) {
                    $icon_path = substr($icon_path, 0, stripos($icon_path, '.svg'));
                }
                $icons[] = $icon_path;
            }
        }

        $this->render_vue_app(
            Studip\VueApp::create('forum/discussions_types/Edit')->withProps([
                'icons' => array_unique($icons),
                'discussion_type' => $discussion_type->toRawArray()
            ])
        );
    }

    public function save_action(DiscussionType $discussion_type = null)
    {
        CSRFProtection::verifyUnsafeRequest();

        $discussion_type->name = Request::get('name');
        $discussion_type->icon = Request::get('icon');

        $discussion_type->store();

        PageLayout::postSuccess(sprintf(_('Der Diskussionstyp „%s“ wurde gespeichert.'), $discussion_type->name));

        $this->relocate('course/forum/discussion_types/index');
    }

    public function delete_action(DiscussionType $discussion_type)
    {
        $discussion_type->delete();

        PageLayout::postSuccess(_('Der Diskussionstyp wurde gelöscht.'));

        $this->relocate('course/forum/discussion_types/index');
    }
}
