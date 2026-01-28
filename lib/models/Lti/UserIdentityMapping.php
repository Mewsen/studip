<?php
namespace Lti;

use Avatar;
use SimpleORMap;
use User;

/**
 * @property int $id
 * @property string $external_user_id
 * @property string $context
 * @property ?string $external_email
 * @property int $mkdate
 * @property int $chdate
 * @property User $user
 * @property ?Registration $registration
 */
class UserIdentityMapping extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'lti_user_identity_mappings';

        $config['belongs_to']['user'] = [
            'class_name' => User::class,
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id'
        ];

        $config['belongs_to']['registration'] = [
            'class_name' => Registration::class,
            'foreign_key' => 'registration_id',
            'assoc_foreign_key' => 'id'
        ];

        parent::configure($config);
    }

    public function transformData(): array
    {
        return [
            ...$this->toRawArray(),
            'chdate' => date('c', $this->chdate),
            'mkdate' => date('c', $this->mkdate),
            'user' => $this->user ? [
                'id'         => $this->user->id,
                'name'       => $this->user->getFullName(),
                'username'   => $this->user->username,
                'avatar_url' => Avatar::getAvatar($this->user->id)->getURL(Avatar::MEDIUM)
            ] : []
        ];
    }
}
