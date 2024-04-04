<?php

/**
 * WikiOnlineEditingUser.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      Rasmus Fuhse <fuhse@data-quest.de>
 * @copyright   2023 Stud.IP Core-Group
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 *
 * @property int $id database column
 * @property string $user_id database column
 * @property int $page_id database column
 * @property int $editing database column
 * @property int $editing_request database column
 * @property int $chdate database column
 * @property int $mkdate database column
 * @property WikiPage $page belongs_to WikiPage
 * @property User $user belongs_to User
 */
class WikiOnlineEditingUser extends SimpleORMap
{
    public static $threshold = 60 * 1;

    protected static function configure($config = [])
    {
        $config['db_table'] = 'wiki_online_editing_users';
        $config['belongs_to']['page'] = [
            'class_name'  => WikiPage::class,
            'foreign_key' => 'page_id'
        ];
        $config['belongs_to']['user'] = [
            'class_name'  => User::class,
            'foreign_key' => 'user_id'
        ];
        parent::configure($config);
    }
}
