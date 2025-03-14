<?php

/**
 *
 * @property array $id 
 * @property string $sem_id 
 * @property string $user_id 
 * @property int $mkdate 
 * @property Course $course 
 * @property User $user 
 */
final class StudygroupInvitation extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'studygroup_invitations';

        $config['belongs_to']['course'] = [
            'class_name' => Course::class,
            'foreign_key' => 'sem_id',
        ];
        $config['belongs_to']['user'] = [
            'class_name' => User::class,
            'foreign_key' => 'user_id',
        ];

        parent::configure($config);
    }
}
