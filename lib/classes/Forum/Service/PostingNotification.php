<?php
namespace Forum\Service;

use Forum\Enum\SubscriptionNotificationType;
use Icon;
use PersonalNotifications;
use Forum\Discussion;
use Forum\Posting;
use Forum\Subscription;
use Forum\Topic;
use URLHelper;

class PostingNotification
{
    protected Posting $posting;
    protected Discussion $discussion;
    protected Topic $topic;

    public function __construct(Posting $posting)
    {
        $this->posting = $posting;
        $this->topic = $posting->discussion->topic;
        $this->discussion = $posting->discussion;
    }

    public function notifySubscribers(): void
    {
        $excludeUserId = null;
        if ($this->posting->parent_id) {
            $subscriber = $this->notifyParentPostAuthor();

            if ($subscriber) {
                $excludeUserId = $subscriber->user_id;
            }
        }

        $subscribers = $this->getSubscribers($excludeUserId);

        foreach ($subscribers as $subscriber) {
            if ($subscriber->user_id === $this->posting->user_id || $subscriber->notification_type !== SubscriptionNotificationType::All->value) {
                continue;
            }

            $this->sendNotifications($subscriber);
        }
    }

    protected function getSubscribers($excludeUserId = null): array
    {
        $query = [
            "range_id = :range_id AND subject_id IN (:subject_ids)",
            [
                'range_id' => $this->posting->range_id,
                'subject_ids' => [$this->discussion->discussion_id, $this->topic->topic_id]
            ]
        ];

        if ($excludeUserId) {
            $query[0] .= " AND user_id != :user_id";
            $query[1]['user_id'] = $excludeUserId;
        }

        $subscriptions = Subscription::findBySQL(...$query);

        /**
         * Allow only one subscription per user.
         * 'discussion' subscription has priority over 'topic' subscription
         */
        $filteredSubscriptions = [];
        foreach ($subscriptions as $subscription) {
            $userId = $subscription->user_id;

            if (isset($filtered[$userId])) {
                if ($filteredSubscriptions[$userId]->subject === 'discussion') {
                    continue;
                }

                // If current subscription is discussion, replace it with topic
                if ($subscription->subject === 'discussion') {
                    $filteredSubscriptions[$userId] = $subscription;
                }

                continue;
            }

            $filteredSubscriptions[$userId] = $subscription;
        }

        return array_values($filteredSubscriptions);
    }

    protected function sendNotifications(Subscription $subscriber): void
    {
        $url = URLHelper::getURL('dispatch.php/course/forum/discussions/show/'.$this->discussion->discussion_id, ['cid' => $this->topic->range_id], true)."#post_" . $this->posting->posting_id;

        $message = sprintf(
            _('Es gibt einen neuen Beitrag zur Diskussion „%s“.'),
            $this->discussion->title
        );

        PersonalNotifications::add(
            $subscriber->user_id,
            $url,
            $message,
            "post_" . $this->posting->posting_id,
            Icon::create('reply')
        );
    }

    protected function notifyParentPostAuthor(): ?Subscription
    {
        $parent = $this->posting->posting;

        $subscriber = Subscription::findOneBySQL(
            "range_id = :range_id AND subject_id IN (:subject_ids) AND user_id = :user_id AND notification_type != :notification_type ORDER BY subject",
            [
                'range_id' => $parent->range_id,
                'subject_ids' => [$this->discussion->discussion_id, $this->topic->topic_id],
                'user_id' => $parent->user_id,
                'notification_type' => SubscriptionNotificationType::None->value
            ]
        );

        if ($subscriber && $subscriber->user_id !== $this->posting->user_id) {
            \PersonalNotifications::add(
                $subscriber->user_id,
                \URLHelper::getURL('dispatch.php/course/forum/discussions/show/'.$this->posting->discussion_id, ['cid' => $this->topic->range_id], true)."#post_" . $this->posting->posting_id,
                sprintf(
                    _('%s hat ihren Beitrag zur Diskussion „%s“ zitiert.'),
                    $this->posting->user->getFullName(),
                    $this->discussion->title
                ),
                "post_" . $this->posting->posting_id,
                \Icon::create('quote')
            );
        }

        return $subscriber;
    }
}
