<?php
/**
 * @author    Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @license   GPL2 or any later version
 * @since     4.5
 *
 * @property int $id alias column for participation_id
 * @property int $participation_id database column
 * @property string $thread_id database column
 * @property string $user_id database column
 * @property int $external_contact database column
 * @property int $mkdate database column
 * @property BlubberThread $thread belongs_to BlubberThread
 * @property User $user belongs_to User
 */

class BlubberParticipation extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'blubber_participations';

        $config['belongs_to']['thread'] = [
            'class_name'        => BlubberThread::class,
            'foreign_key'       => 'thread_id',
            'assoc_foreign_key' => 'thread_id',
        ];
        $config['belongs_to']['user'] = [
            'class_name'        => User::class,
            'foreign_key'       => 'user_id',
            'assoc_foreign_key' => 'user_id',
        ];

        parent::configure($config);
    }

    public static function findUserParticipationIn(string $thread_id, string $user_id)
    {
        return self::findOneBySQL(
            '`thread_id` = ? AND `user_id` = ?',
            [$thread_id, $user_id]
        );
    }

    public static function userParticipatesIn(string $thread_id, string $user_id)
    {
        $has_record = self::findUserParticipationIn($thread_id, $user_id);
        return !empty($has_record);
    }

    public static function getParticipantsNamesIn(string $thread_id, string $current_user_id)
    {
        $query = "SELECT IFNULL(external_users.name, CONCAT(auth_user_md5.Vorname, ' ', auth_user_md5.Nachname)) AS name
                    FROM blubber_participations
                    LEFT JOIN auth_user_md5
                    ON blubber_participations.user_id = auth_user_md5.user_id
                        AND blubber_participations.external_contact = 0
                    LEFT JOIN external_users
                    ON external_users.external_contact_id = blubber_participations.user_id
                        AND blubber_participations.external_contact = 1
                    WHERE blubber_participations.thread_id = :thread_id
                    AND blubber_participations.user_id != :me
                    ORDER BY name";
        $result = DBManager::get()->fetchFirst($query,
            [
                ':thread_id' => $thread_id,
                ':me' => $current_user_id
            ]
        );

        $names = $result ?? [];

        $names = array_map(function ($name) {
            return $name ?? _('unbekannt');
        }, $names);

        return $names;
    }

    public static function getOrderedParticipantsIn(string $thread_id)
    {
        return self::findBySQL(
            "LEFT JOIN auth_user_md5
                ON blubber_participations.user_id = auth_user_md5.user_id
                    AND blubber_participations.external_contact = 0
            LEFT JOIN external_users
                ON blubber_participations.user_id = external_users.external_contact_id
                    AND blubber_participations.external_contact = 1
            WHERE thread_id = ?
                ORDER BY IFNULL(external_users.name, CONCAT(auth_user_md5.Vorname, ' ', auth_user_md5.Nachname))",
            [$thread_id]
        );
    }

    public static function findUserParticipationByDomainIn(
        string $thread_id,
        string $user_id,
        bool $is_external = false
    ) {
        $participation = self::findUserParticipationIn($thread_id, $user_id);
        if ($participation && (bool) $participation->external_contact === $is_external) {
            return $participation;
        }
        return null;
    }

    public static function localUserParticipatesIn(string $thread_id, string $user_id)
    {
        $has_record = self::findUserParticipationByDomainIn($thread_id, $user_id);
        return !empty($has_record);
    }

    public static function externalUserParticipatesIn(string $thread_id, string $user_id)
    {
        $has_record = self::findUserParticipationByDomainIn($thread_id, $user_id, true);
        return !empty($has_record);
    }

    public static function getParticipantsIn(string $thread_id)
    {
        return self::findBySQL('`thread_id` = ?', [$thread_id]);
    }

}
