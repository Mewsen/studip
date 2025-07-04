<?php
namespace Forum\Service;

use Forum\Enum\SubscriptionNotificationType;
use Icon;
use PersonalNotifications;
use Forum\ForumDiscussion;
use Forum\ForumSubscription;
use Forum\ForumTopic;
use URLHelper;

class DiscussionNotification
{
    protected ForumTopic $topic;
    protected ForumDiscussion $discussion;

    public function __construct(ForumDiscussion $discussion)
    {
        $this->discussion = $discussion;
        $this->topic = $discussion->topic;
    }

    public function notifySubscribers(): void
    {
        $subscribers = $this->getSubscribers();

        foreach ($subscribers as $subscriber) {
            $this->sendNotifications($subscriber);
        }
    }

    protected function getSubscribers(): array
    {
        return ForumSubscription::findBySQL(
            "subject = :subject AND subject_id = :subject_id AND notification_type = :notification_type",
            [
                'subject' => 'topic',
                'subject_id' => $this->topic->topic_id,
                'notification_type' => SubscriptionNotificationType::All->value
            ]
        );
    }

    protected function sendNotifications(ForumSubscription $subscriber): void
    {
        $url = URLHelper::getURL('dispatch.php/course/forum/discussions/show/'.$this->discussion->discussion_id, ['cid' => $this->topic->range_id], true);

        $message = sprintf(
            _('Es gibt eine neue Diskussion „%1$s“ zum Thema „%2$s“.'),
            $this->discussion->title,
            $this->topic->name
        );

        PersonalNotifications::add(
            $subscriber->user_id,
            $url,
            $message,
            null,
            Icon::create('forum')
        );
    }
}
