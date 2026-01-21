<?php

namespace JsonApi\Schemas\Lti;

use Neomerx\JsonApi\Schema\Link;
use JsonApi\Schemas\SchemaProvider;
use Lti\Publication as PublicationModel;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class Publication extends SchemaProvider
{
    const TYPE = 'lti-publication';
    const REL_RANGE = 'range';
    const REL_USER = 'user';
    const REL_MEMBERS = 'members';

    /**
     * @param PublicationModel $resource
     */
    public function getId($resource): ?string
    {
        return $resource->id;
    }

    /**
     * @param PublicationModel $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'name' => $resource->name,
            'publication-key' => $resource->publication_key,
            'status' => (bool) $resource->status,
            'version' => $resource->version,
            'mkdate' => date('c', $resource->mkdate),
            'chdate' => date('c', $resource->chdate)
        ];
    }

    /**
     * @param PublicationModel $resource
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        $relationships = $this->addRangeRelationship($relationships, $resource, $this->shouldInclude($context, self::REL_RANGE));
        $relationships = $this->addUserRelationship($relationships, $resource, $this->shouldInclude($context, self::REL_USER));
        $relationships = $this->addMembersRelationship($relationships, $resource, $this->shouldInclude($context, self::REL_MEMBERS));

        return $relationships;
    }

    private function addRangeRelationship(array $relationships, PublicationModel $publication, bool $withRange = false): array
    {
        if ($withRange && $publication->range) {
            $relationships[self::REL_RANGE] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($publication->range)
                ],
                self::RELATIONSHIP_DATA => $publication->range
            ];
        }

        return $relationships;
    }

    private function addUserRelationship(array $relationships, PublicationModel $publication, bool $withUser = false): array
    {
        if ($withUser && $publication->user) {
            $relationships[self::REL_USER] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($publication->user)
                ],
                self::RELATIONSHIP_DATA => $publication->user
            ];
        }

        return $relationships;
    }

    private function addMembersRelationship(array $relationships, PublicationModel $publication, bool $withUsers = false): array
    {
        if ($withUsers) {
            $relationships[self::REL_MEMBERS] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->getRelationshipRelatedLink($publication, self::REL_MEMBERS)
                ],
                self::RELATIONSHIP_DATA => $publication->members
            ];
        }

        return $relationships;
    }
}
