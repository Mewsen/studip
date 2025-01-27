<?php

class Massmail_OverviewController extends \AuthenticatedController
{

    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        if (!\MassMail\MassMailPermission::has(User::findCurrent()->id)) {
            throw new AccessDeniedException();
        }

        Navigation::activateItem('/messaging/massmail/overview');

        Sidebar::Get()->addWidget(new VueWidget('message-views'));

        $this->render_vue_app(
            Studip\VueApp::create('massmail/MassMailMessagesList')
        );
    }

    public function index_action($id = null)
    {
        PageLayout::setTitle(_('Nachrichten'));

        $this->render_vue_app(
            Studip\VueApp::create('massmail/MassMailMessagesList')
        );
    }

}
