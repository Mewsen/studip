<?php

class OERPostUpload extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'oer_post_upload';
        $config['belongs_to']['file'] = [
            'class_name' => File::class,
            'foreign_key' => 'id',
            'on_delete' => 'delete'
        ];

        parent::configure($config);
    }


}
