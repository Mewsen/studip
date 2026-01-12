<?php
namespace Lti;

use SimpleORMap;
use User;

/**
 * @property array $id
 * @property int $registration_id
 * @property string $user_id
 * @property int $accepted
 * @property string $allowed_optional_fields
 * @property int $mkdate
 * @property int $chdate
 * @property User $user
 * @property Registration $registration
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
