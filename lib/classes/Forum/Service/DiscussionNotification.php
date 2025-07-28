<?php
namespace Forum\Service;

use Forum\Enum\SubscriptionNotificationType;
use Icon;
use PersonalNotifications;
use Forum\Discussion;
use Forum\Subscription;
use Forum\Topic;
use URLHelper;

class DiscussionNotification
{
    protected Topic $topic;
    protected Discussion $discussion;

    public function __construct(Discussion $discussion)
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
        return Subscription::findBySQL(
            "subject = :subject AND subject_id = :subject_id AND notification_type = :notification_type",
            [
                'subject' => 'topic',
                'subject_id' => $this->topic->topic_id,
                'notification_type' => SubscriptionNotificationType::All->value
            ]
        );
    }

    protected function sendNotifications(Subscription $subscriber): void
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
