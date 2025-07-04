<?php
namespace Forum;

use SimpleORMap;
use User;

/**
 * @property int $id
 * @property string $posting_id
 * @property string $user_id
 * @property string $emoji
 * @property int $mkdate
 * @property int $chdate
 *
 * @property ForumPosting $posting
 * @property User $user
 */

class ForumPostingReaction extends SimpleORMap
{
    public const thumbUp = 'THUMBS UP SIGN';
    public const thumbDown = 'THUMBS DOWN SIGN';
    public const rocket = 'ROCKET';
    public const grinningFace = 'GRINNING FACE';
    public const sunglasses = 'SMILING FACE WITH SUNGLASSES';
    public const confused = 'CONFUSED FACE';
    public const heart = 'BLACK HEART SUIT';
    public const party = 'PARTY POPPER';


    protected static function configure($config = [])
    {
        $config['db_table'] = 'forum_posting_reactions';

        $config['belongs_to']['posting'] = [
            'class_name' => ForumPosting::class,
            'foreign_key' => 'posting_id',
            'assoc_foreign_key' => 'posting_id'
        ];

        $config['belongs_to']['user'] = [
            'class_name' => User::class,
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id'
        ];

        parent::configure($config);
    }
}
