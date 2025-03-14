<?php

namespace MassMail;

/**
 * @license GPL2 or any later version
 *
 * @property int $id alias column for token_id
 * @property int $token_id database column
 * @property int $message_id database column
 * @property string|null $user_id database column
 * @property string $token database column
 * @property int $mkdate database column
 * @property MassMailMessage $message belongs_to MassMailMessage
 * @property \User|null $user belongs_to \User
 */
class MassMailToken extends \SimpleORMap
{

    protected static function configure($config = [])
    {
        $config['db_table'] = 'massmail_tokens';

        $config['belongs_to']['message'] = [
            'class_name' => MassMailMessage::class,
            'foreign_key' => 'message_id'
        ];

        $config['belongs_to']['user'] = [
            'class_name' => \User::class,
            'foreign_key' => 'user_id'
        ];

        parent::configure($config);
    }

}
