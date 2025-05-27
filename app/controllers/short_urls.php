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
        PageLayout::setTitle(_('Meine Kurz-URLs'));
        Navigation::activateItem('/contents/short_urls/overview');

        $this->render_vue_app(
            Studip\VueApp::create('short-urls/ShortUrlList')
            ->withStore('shortUrlsStore', 'useShortUrlsStore')
        );
    }

    public function create_action(): void
    {
        PageLayout::setTitle(_('Kurz-URL erstellen'));

        $clean_path = preg_replace('#^.*?dispatch\.php#', 'dispatch.php', Request::get('path'));
        $this->render_vue_app(
            Studip\VueApp::create('short-urls/ShortUrl')
                ->withProps([
                    'path'  => $clean_path,
                    'isNew' => true
                ])
        );
    }
}
