<?php

namespace MassMail;

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
