<?php
namespace Forum;

use ActionsWidget;
use Context;
use CoreForum;
use Icon;
use Request;
use SearchWidget;
use Sidebar;
use StudipController;

abstract class ForumBaseController extends StudipController
{
    protected $with_session = true;

    public function before_filter(&$action, &$args)
    {
        object_set_visit_module('forum');

        $this->range_id = Context::getId();
        $this->is_moderator = CoreForum::isModerator($this->range_id);
        $this->is_admin = CoreForum::isAdmin($this->range_id);

        $this->buildSidebar();

        parent::before_filter($action, $args);
    }

    protected function buildSidebar(): void
    {
        $actions = new ActionsWidget();

        $actions->addLink(
            _('Neue Diskussion starten'),
            $this->url_for('course/forum/discussions/edit'),
            Icon::create('add', Icon::ROLE_CLICKABLE, ['title' => _('Neue Diskussion starten')])
        )->asDialog('width=900;height=750');

        if ($this->is_admin) {
            $actions->addLink(
                _('Forum verwalten'),
                $this->url_for('course/forum/configs/edit'),
                Icon::create('admin', Icon::ROLE_CLICKABLE, ['title' => _('Forum verwalten')]),
                ['data-dialog' => 'width=500;height=300']
            );
        }

        Sidebar::Get()->addWidget($actions);

        $search = new SearchWidget($this->url_for('course/forum/search', [
            'begin' => Request::int('begin'),
            'end' => Request::int('end')
        ]));

        $search->addNeedle(
            _('Suche nach Diskussionen oder Beiträge'),
            'q',
            true
        );

        Sidebar::Get()->addWidget($search, 'forum_search');
    }
}
