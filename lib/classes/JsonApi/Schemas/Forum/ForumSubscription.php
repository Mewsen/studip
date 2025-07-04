<?php

namespace JsonApi\Schemas\Forum;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

class ForumSubscription extends SchemaProvider
{
    const TYPE = 'forum-subscriptions';
    const REL_USER = 'user';
    const REL_RANGE = 'range';
    const REL_SUBJECT = 'subject';

    public function getId($subscription): ?string
    {
        return $subscription->id;
    }

    public function getAttributes($subscription, ContextInterface $context): iterable
    {
        return [
            'notification-type' => $subscription->notification_type,
            'mkdate' => date('c', $subscription->mkdate),
            'chdate' => date('c', $subscription->chdate)
        ];
    }


    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getRelationships($subscription, ContextInterface $context): iterable
    {
        $isPrimary = $context->getPosition()->getLevel() === 0;
        $includeList = $context->getIncludePaths();

        $relationships = [];
        if ($isPrimary) {
            $relationships = $this->addUserRelationship($relationships, $subscription, $includeList);
            $relationships = $this->addRangeRelationship($relationships, $subscription, $includeList);
            $relationships = $this->addSubjectRelationship($relationships, $subscription, $includeList);
        }

        return $relationships;
    }

    private function addUserRelationship(array $relationships, $subscription, array $includeList)
    {
        $relationships[self::REL_USER] = [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->createLinkToResource($subscription->user)
            ],
            self::RELATIONSHIP_DATA => $subscription->user
        ];

        return $relationships;
    }

    private function addSubjectRelationship(array $relationships, $subscription, array $includeList)
    {
        $relationships[self::REL_SUBJECT] = [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->createLinkToResource($subscription->subject_object)
            ],
            self::RELATIONSHIP_DATA => $subscription->subject_object
        ];

        return $relationships;
    }

    private function addRangeRelationship(array $relationships, $subscription, $includeList)
    {
        $relationships[self::REL_RANGE] = [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->createLinkToResource($subscription->range),
            ],
            self::RELATIONSHIP_DATA => $subscription->range,
        ];

        return $relationships;
    }
}
