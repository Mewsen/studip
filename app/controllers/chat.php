<?php

class ChatController extends AuthenticatedController
{
    public function index_action()
    {
        if (Navigation::hasItem('/community/chat')) {
            Navigation::activateItem('/community/chat');
        }
        $this->buildSidebar();
        $this->render_vue_app(
            Studip\VueApp::create('TheChat')
            ->withProps(
                [
                    'context' => 'community',
                ]
            )
        );
    }

    private function buildSidebar()
    {
        $sidebar = Sidebar::Get();
        $sidebar->addWidget(new VueWidget('chat-room-actions-widget'));
        $sidebar->addWidget(new VueWidget('chat-rooms-widget'));
    }
}