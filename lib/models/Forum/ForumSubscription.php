<?php
namespace Forum;

use Course;
use SimpleORMap;
use User;
use Forum\Enum\SubscriptionNotificationType;

/**
 * @property int $id
 * @property string $subject_id
 * @property string $range_id
 * @property string $subject
 * @property SubscriptionNotificationType $notification_type
 * @property int $mkdate
 * @property int $chdate
 *
 * @property ForumDiscussion | ForumTopic $subject_object
 * @property User $user
 * @property Course $range
 */

class ForumSubscription extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'forum_subscriptions';

        $config['belongs_to']['user'] = [
            'class_name' => User::class,
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id'
        ];

        $config['belongs_to']['range'] = [
            'class_name' => Course::class,
            'foreign_key' => 'range_id',
            'assoc_foreign_key' => 'Seminar_id'
        ];

        $config['additional_fields']['subject_object']['get'] = 'getSubjectObject';

        parent::configure($config);
    }

    public function getSubjectObject(): ForumDiscussion | ForumTopic
    {
        return match ($this->subject) {
            'topic' => ForumTopic::find($this->subject_id),
            'discussion' => ForumDiscussion::find($this->subject_id)
        };
    }
}
