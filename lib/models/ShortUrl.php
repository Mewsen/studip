<?php

/**
 * ShortUrl.php
 * model class for table short_url
 *
 * @property string id The ID of the short URL.
 * @property string alias
 * @property string path The URL where the short URL leads to.
 * @property string user_id The ID of the user who created the short URL.
 * @property string mkdate The creation timestamp of the short URL.
 * @property string chdate The modification timestamp of the short URL.
 * @property User $user belongs_to User
 */
class ShortUrl extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'short_urls';

        $config['belongs_to']['user'] = [
            'class_name'  => User::class,
            'foreign_key' => 'user_id',
        ];

        parent::configure($config);
    }
}
