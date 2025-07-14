<?php
/**
 * admin/layout.php - Layout for Stud.IP
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Ron Lucke <lucke@elan-ev.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 * @package     admin
 * @since       6.1
 */

class Admin_LayoutController extends AuthenticatedController
{
    public function before_filter(&$action, &$args)
    {
        parent::before_filter($action, $args);
        $GLOBALS['perm']->check('root');
        PageLayout::setTitle(_('Darstellung'));
        Navigation::activateItem('/admin/locations/layout');
    }

    public function index_action()
    {
        $this->render_vue_app(
            Studip\VueApp::create('ThemeSettings')
                ->withVuexStore(
                    'theme-settings.module.js',
                    'theme-settings-module',
                    [
                        'setUserId' => User::findCurrent()->id,
                    ]
                )
        );
    }
}
