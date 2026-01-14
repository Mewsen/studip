<?php
namespace Lti;

use Avatar;
use SimpleORMap;
use User;

/**
 * @property int $id
 * @property int $mkdate
 * @property int $chdate
 * @property Publication $publication
 * @property User $user
 */
class PublicationUser extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'lti_publication_users';

        $config['belongs_to']['publication'] = [
            'class_name' => Publication::class,
            'foreign_key' => 'publication_id',
            'assoc_foreign_key' => 'id'
        ];

        $config['belongs_to']['user'] = [
            'class_name' => User::class,
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id'
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
