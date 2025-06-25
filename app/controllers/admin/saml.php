<?php

use Studip\OAuth2\Container;
use Studip\OAuth2\Models\Client;
use Studip\OAuth2\SetupInformation;

class Admin_SAMLController extends AuthenticatedController
{
    /**
     * @param string $action
     * @param string[] $args
     *
     * @return void
     */
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);

        $GLOBALS['perm']->check('root');

        Navigation::activateItem('/admin/config/saml');
        PageLayout::setTitle(_('SAML Verwaltung'));
    }

    public function index_action(): void
    {
        $this->render_vue_app(
            Studip\VueApp::create('SSOSAML')
                ->withProps([
                ])
        );
    }
}
