<?php
/**
 * logout.php - logout
 *
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      André Noack <noack@data-quest.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */
class LogoutController extends AuthenticatedController
{
    protected $allow_nobody = true;

    public function index_action()
    {
        if (
            !Request::isPost()
            && !(
                isset($_SESSION['logout_ticket'])
                && check_ticket($_SESSION['logout_ticket'])
            )
        ) {
            $this->redirect(URLHelper::getURL('dispatch.php/start'));
            return;
        }

        if ($GLOBALS['user']->id !== 'nobody') {
            $my_messaging_settings = $GLOBALS['user']->cfg->MESSAGING_SETTINGS;

            //Wenn Option dafuer gewaehlt, alle ungelsesenen Nachrichten als gelesen speichern
            if (!empty($my_messaging_settings['logout_markreaded'])) {
                Message::markAllAs();
            }

            $_language = $_SESSION['_language'];
            $contrast = UserConfig::get($GLOBALS['user']->id)->USER_HIGH_CONTRAST;

            // Get auth plugin of user before logging out since the $auth object will
            // be modified by the logout
            $auth_plugin = StudipAuthAbstract::getInstance($GLOBALS['user']->auth_plugin);

            sess()->destroy();
            //Session changed zuruecksetzen
            $timeout=(time()-(15 * 60));
            $GLOBALS['user']->set_last_action($timeout);

            // Perform logout from auth plugin (if possible)
            if ($auth_plugin instanceof StudipAuthSSO) {
                $auth_plugin->logout();
            }

            sess()->start();
            $_SESSION['_language'] = $_language;
            if ($contrast) {
                $_SESSION['contrast'] = $contrast;
            }
            NotificationCenter::addObserver(function() {
                throw new NotificationVetoException();
            }, '__invoke', 'PageCloseWillExecute');
            PageLayout::postSuccess(
                _('Sie sind nun aus dem System abgemeldet.'),
                array_filter([$GLOBALS['UNI_LOGOUT_ADD']])
            );
        }

        $this->redirect(URLHelper::getURL('dispatch.php/start'));
    }
}
