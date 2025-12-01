<?php
/**
 * UserInfo.php
 * model class for table user_info
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 *
 * @author      André Noack <noack@data-quest.de>
 * @copyright   2010 Stud.IP Core-Group
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPL version 2
 * @category    Stud.IP
 *
 * @property string $id alias column for user_id
 * @property string $user_id database column
 * @property I18NString $hobby database column
 * @property I18NString $lebenslauf database column
 * @property I18NString $publi database column
 * @property I18NString $schwerp database column
 * @property string $home database column
 * @property string $privatnr database column
 * @property string $privatcell database column
 * @property string $privadr database column
 * @property int $score database column
 * @property int $geschlecht database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property string $title_front database column
 * @property string $title_rear database column
 * @property string|null $preferred_language database column
 * @property int $smsforward_copy database column
 * @property string $smsforward_rec database column
 * @property int $email_forward database column
 * @property string $motto database column
 * @property string $lock_rule database column
 * @property string|null $oercampus_description database column
 */

class UserInfo extends SimpleORMap
{
    /**
     * Constants for column geschlecht
     */
    const GENDER_UNKNOWN = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    const GENDER_DIVERSE = 3;

    protected static function configure($config = [])
    {
        $config['db_table'] = 'user_info';

        $config['belongs_to']['user'] = [
            'class_name'  => \User::class,
            'foreign_key' => 'user_id',
        ];

        $config['additional_fields']['is_king']['get'] = 'getIsKing';

        $config['i18n_fields']['hobby'] = true;
        $config['i18n_fields']['lebenslauf'] = true;
        $config['i18n_fields']['schwerp'] = true;
        $config['i18n_fields']['publi'] = true;
        parent::configure($config);
    }

    public static function loadPaginatedUsersListForScores(int $limit, int $offset): array
    {
        $vis_query = get_vis_query('b');
        $query =
            "SELECT
                SQL_CALC_FOUND_ROWS a.user_id,
                username,
                score,
                geschlecht,
                {$GLOBALS['_fullname_sql']['full']} AS fullname
            FROM user_info AS a
            LEFT JOIN auth_user_md5 AS b USING (user_id)
                WHERE score > 0 AND locked = 0 AND {$vis_query}
                ORDER BY score DESC
                LIMIT ?, ?";
        $result = DBManager::get()->fetchAll($query, [$offset, $limit]);
        $total = DBManager::get()->fetchColumn("SELECT FOUND_ROWS()");

        $users = [];
        if (!empty($result)) {
            foreach ($result as $row) {
                $row['is_king'] = \StudipKing::is_king($row['user_id'], true);
                $row['title'] = \Score::getTitel($row['score'], $row['geschlecht']);
                $users[$row['user_id']] = $row;
            }
        }

        return [$users, $total];
    }
}
