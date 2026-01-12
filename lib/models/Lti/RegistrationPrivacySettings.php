<?php
namespace Lti;

use SimpleORMap;
use User;

/**
 * @property array $id alias for pk
 * @property int $registration_id database column
 * @property string $user_id database column
 * @property int $accepted database column
 * @property string $allowed_optional_fields database column
 * @property int $mkdate database column
 * @property int $chdate database column
 * @property User $user belongs_to User
 * @property Registration $registration belongs_to Registration
 */
class RegistrationPrivacySettings extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'lti_registration_privacy_settings';

        $config['belongs_to']['user'] = [
            'class_name'  => User::class,
            'foreign_key' => 'user_id'
        ];

        $config['belongs_to']['registration'] = [
            'class_name'  => Registration::class,
            'foreign_key' => 'registration_id'
        ];

        parent::configure($config);
    }
}
