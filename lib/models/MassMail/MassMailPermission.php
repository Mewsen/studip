<?php

namespace MassMail;

/**
 * @license GPL2 or any later version
 *
 * @property int $id alias column for permission_id
 * @property int $permission_id database column
 * @property string $institute_id database column
 * @property string $min_perm database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property \Institute $institute belongs_to \Institute
 * @property \SimpleORMapCollection<\Degree> $allowed_degrees has_and_belongs_to_many \Degree
 * @property \SimpleORMapCollection<\StudyCourse> $allowed_subjects has_and_belongs_to_many \StudyCourse
 * @property \SimpleORMapCollection<\Institute> $allowed_institutes has_and_belongs_to_many \Institute
 * @property-read mixed $institute_name additional field
 */
class MassMailPermission extends \SimpleORMap
{

    public const MASSMAIL_ROOT_ROLE = 'Massenmail-Root';

    protected static function configure($config = [])
    {
        $config['db_table'] = 'massmail_permissions';

        $config['belongs_to']['institute'] = [
            'class_name' => \Institute::class,
            'foreign_key' => 'institute_id',
            'assoc_foreign_key' => 'institut_id'
        ];

        $config['has_and_belongs_to_many']['allowed_degrees'] = [
            'class_name' => \Degree::class,
            'thru_table' => 'massmail_permission_degree',
            'thru_key' => 'permission_id',
            'thru_assoc_key' => 'degree_id',
            'on_store' => 'store',
            'on_delete' => 'delete'
        ];

        $config['has_and_belongs_to_many']['allowed_subjects'] = [
            'class_name' => \StudyCourse::class,
            'thru_table' => 'massmail_permission_subject',
            'thru_key' => 'permission_id',
            'thru_assoc_key' => 'subject_id',
            'on_store' => 'store',
            'on_delete' => 'delete'
        ];

        $config['has_and_belongs_to_many']['allowed_institutes'] = [
            'class_name' => \Institute::class,
            'thru_table' => 'massmail_permission_institute',
            'thru_key' => 'permission_id',
            'thru_assoc_key' => 'institute_id',
            'on_store' => 'store',
            'on_delete' => 'delete'
        ];

        $config['additional_fields']['institute_name']['get'] = function($p) {
            return $p->institute->name;
        };

        parent::configure($config);
    }

    /**
     * Check if the given user has permissions to write mass mails. The result is cached for performance reasons.
     *
     * @param string $user_id user to check
     * @param bool $unrestricted check for unrestricted permissions
     * @return bool
     */
    public static function has(string $user_id, bool $unrestricted = false) : bool
    {
        $cached = \Studip\Cache\Factory::getCache()->read('massmail-permission-' . $user_id);

        if ($cached !== false) {
            $perm = (int) $cached;
        } else {

            $perm = 0;

            // Root and users with the massmeil root role are always allowed to do anything.
            if (
                $GLOBALS['perm']->have_perm('root', $user_id)
                || \RolePersistence::isAssignedRole($user_id, static::MASSMAIL_ROOT_ROLE)
            ) {
                $perm = 2;

                // Everyone else needs at least one institute assignment with existing permissions.
            } else {
                // Institute memberships with existing mass mail permission settings.
                $relevant = static::findBySQL(
                    "JOIN `user_inst` ON (`user_inst`.`institut_id` = `massmail_permissions`.`institute_id`)
                    WHERE `user_inst`.`inst_perms` != 'user' AND `user_inst`.`user_id` = :user",
                    ['user' => $user_id]
                );
                foreach ($relevant as $one) {
                    if ($GLOBALS['perm']->have_studip_perm($one->min_perm, $one->institute_id, $user_id)) {
                        $perm = 1;
                        break;
                    }
                }
            }

            \Studip\Cache\Factory::getCache()->write('massmail-permission-' . $user_id, $perm);
        }

        return $unrestricted ? $perm === 2 : $perm >= 1;
    }

    /**
     * @return array{
     *     allowed_degrees: array,
     *     allowed_subjects: array,
     *     allowed_institutes: array
     * }
     */
    public static function getForUser(\User $user, bool $withNames = false): array
    {
        // Get user's institutes with at least autor permission.
        $institutes = $user->institute_memberships->filter(function ($membership) {
            return in_array($membership->inst_perms, ['autor', 'tutor', 'dozent', 'admin']);
        })->pluck($withNames ? 'institut_id institute_name' : 'institut_id');

        // Get permission configuration for these institutes.
        $permissions = static::findBySQL("`institute_id` IN (:institutes)", ['institutes' => $institutes]);
        $config = [
            'allowed_degrees' => [],
            'allowed_subjects' => [],
            'allowed_institutes' => $institutes
        ];
        foreach ($permissions as $permission) {
            $config['allowed_degrees'] = array_merge(
                $config['allowed_degrees'],
                $permission->allowed_degrees->pluck($withNames ? 'id name' : 'id')
            );
            $config['allowed_subjects'] = array_merge(
                $config['allowed_subjects'],
                $permission->allowed_subjects->pluck($withNames ? 'id name' : 'id')
            );
            $config['allowed_institutes'] = array_merge(
                $config['allowed_institutes'],
                $permission->allowed_institutes->pluck($withNames ? 'id name' : 'id')
            );
        }

        return $config;
    }

}
