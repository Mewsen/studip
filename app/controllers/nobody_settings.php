<?php

use Trails\Controller;

/**
 * nobody_settings.php - contrast and language settings for nobody
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Michaela Brückner <brueckner@data-quest.de>
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 */

class NobodySettingsController extends StudipController
{
    protected $with_session = true;

    public function store_settings_action()
    {

        $this->page = Request::get('page');

        if (Request::submitted('user_config_submitted')) {
            CSRFProtection::verifyUnsafeRequest();

            if (Request::submitted('unset_contrast')) {
                $_SESSION['contrast'] = 0;
            }
            if (Request::submitted('set_contrast')) {
                $_SESSION['contrast'] = 1;
            }

            foreach (array_keys($GLOBALS['INSTALLED_LANGUAGES']) as $language_key) {
                if (Request::get('set_language') === $language_key) {
                    $_SESSION['forced_language'] = $language_key;
                    $_SESSION['_language'] = $language_key;
                }
            }
        }

        $this->redirect($this->page); //we're too late to remove the high contrast mode, so we reload the page
    }

}
