<?php

use Studip\Forms\Form;

class ShortUrlsController extends AuthenticatedController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        $this->current_user = User::findCurrent();
    }

    public function index_action(): void
    {
        PageLayout::setTitle(_('Meine Kurzlinks'));
        Navigation::activateItem('/contents/short_urls/overview');

        $this->render_vue_app(
            Studip\VueApp::create('short-urls/ShortUrlList')
                ->withStore('shortUrlsStore', 'useShortUrlsStore')
        );
    }

    public function create_action(): void
    {
        PageLayout::setTitle(_('Link zur aktuellen Seite erstellen'));

        $this->render_vue_app(
            Studip\VueApp::create('short-urls/ShortUrlLink')
                ->withProps(['isInContext' => Context::isCourse() && Context::get()->hasCourseSet()])
        );
    }
}
