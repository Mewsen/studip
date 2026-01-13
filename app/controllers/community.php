<?php

class CommunityController extends AuthenticatedController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        PageLayout::setTitle(_('Community'));
        PageLayout::disableSidebar(state: true);
    }
    public function index_action()
    {
        if (Navigation::hasItem('/community/overview')) {
            Navigation::activateItem('/community/overview');
        }
        PageLayout::disableSidebar(state: true);

        $this->render_vue_app(
            Studip\VueApp::create('TheCommunityOverview')
                ->withProps(
                    []
                )
        );
    }

    public function groups_action()
    {
        if (Navigation::hasItem('/community/groups')) {
            Navigation::activateItem('/community/groups');
        }
        PageLayout::disableSidebar(state: true);

        $this->render_vue_app(
            Studip\VueApp::create('TheCommunityGroups')
                ->withPlugin('CommunityGroupsPlugin', 'community-groups')
        );
    }

    public function contacts_action()
    {
        if (Navigation::hasItem('/community/contacts')) {
            Navigation::activateItem('/community/contacts');
        }

        $this->render_vue_app(
            Studip\VueApp::create('TheCommunityContacts')
        );
        
    }
}
