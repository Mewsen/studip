<?php

namespace JsonApi\Schemas\Lti;

use Neomerx\JsonApi\Schema\Link;
use JsonApi\Schemas\SchemaProvider;
use Lti\PublicationUser as PublicationUserModel;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class PublicationMember extends SchemaProvider
{
    const TYPE = 'lti-publication-members';
    const REL_PUBLICATION = 'publication';
    const REL_USER = 'user';

    /**
     * @param PublicationUserModel $resource
     */
    public function getId($resource): ?string
    {
        return $resource->id;
    }

    /**
     * @param PublicationUserModel $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'mkdate' => date('c', $resource->mkdate),
            'chdate' => date('c', $resource->chdate)
        ];
    }

    /**
     * @param PublicationUserModel $resource
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        $relationships = $this->addPublicationRelationship($relationships, $resource, $this->shouldInclude($context, self::REL_PUBLICATION));
        $relationships = $this->addUserRelationship($relationships, $resource, $this->shouldInclude($context, self::REL_USER));

        return $relationships;
    }

    private function addPublicationRelationship(array $relationships, PublicationUserModel $publicationUser, bool $withPublication = false): array
    {
        if ($withPublication && $publicationUser->publication) {
            $relationships[self::REL_PUBLICATION] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($publicationUser->publication)
                ],
                self::RELATIONSHIP_DATA => $publicationUser->publication
            ];
        }

        return $relationships;
    }

    private function addUserRelationship(array $relationships, PublicationUserModel $publicationUser, bool $withUser = false): array
    {
        if ($withUser && $publicationUser->user) {
            $relationships[self::REL_USER] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($publicationUser->user)
                ],
                self::RELATIONSHIP_DATA => $publicationUser->user
            ];
        }

        return $relationships;
    }
}
