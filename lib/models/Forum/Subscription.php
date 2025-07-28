<?php
namespace Forum;

use Range;
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
 * @property Discussion | Topic $subject_object
 * @property User $user
 * @property Range $range
 */

class Subscription extends SimpleORMap
{
    protected static function configure($config = [])
    {
        $config['db_table'] = 'forum_subscriptions';

        $config['belongs_to']['user'] = [
            'class_name' => User::class,
            'foreign_key' => 'user_id',
            'assoc_foreign_key' => 'user_id'
        ];

        $config['additional_fields']['range'] = [
            'set' => function (Subscription $subscription, string $field, Range $range) {
                $subscription->range_id = $range->getRangeId();
            },
            'get' => function (Subscription $subscription): Range {
                return get_object_by_range_id($subscription->range_id);
            },
        ];

        $config['additional_fields']['subject_object']['get'] = 'getSubjectObject';

        parent::configure($config);
    }

    /**
     * @return self[]
     */
    public static function getUserSubscriptions(string $range_id, string $user_id): array
    {
        return self::findBySQL(
            "range_id = :range_id AND user_id = :user_id ORDER BY mkdate DESC",
            [
                'range_id' => $range_id,
                'user_id' => $user_id
            ]
        );
    }

    public function getSubjectObject(): Discussion | Topic
    {
        return match ($this->subject) {
            'topic' => Topic::find($this->subject_id),
            'discussion' => Discussion::find($this->subject_id)
        };
    }
}
