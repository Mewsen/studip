<?php

namespace JsonApi\Routes\Contacts\Rel;

use Psr\Http\Message\ServerRequestInterface as Request;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\Contacts\Authority;
use JsonApi\Routes\RelationshipsController;

class ContactGroupItems extends RelationshipsController
{
    protected $allowedPagingParameters = ['offset', 'limit'];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function fetchRelationship(Request $request, $related)
    {
        $items = $related->items;
        $total = count($items);
        list($offset, $limit) = $this->getOffsetAndLimit();

        return $this->getPaginatedIdentifiersResponse(
            $items->limit($offset, $limit),
            $total,
            $this->getRelationshipLinks($related)
        );
    }

    protected function addToRelationship(Request $request, $related)
    {
        $json = $this->validate($request);

        foreach ($this->validateGroupUsers($related, $json) as $userId) {
            if (!\ContactGroupItem::countBySQL('group_id = ? AND user_id = ?', [$related->id, $userId])) {
                \ContactGroupItem::create(['group_id' => $related->id, 'user_id' => $userId]);
            }
        }

        return $this->getCodeResponse(201);
    }

    protected function removeFromRelationship(Request $request, $related)
    {
        $json = $this->validate($request);
        $userIds = $this->validateGroupUsers($related, $json);
        foreach ($userIds as $userId) {
            \ContactGroupItem::deleteItemFromGroup($related->id, $userId);
        }

        return $this->getCodeResponse(204);
    }

    protected function replaceRelationship(Request $request, $related)
    {
        parent::replaceRelationship($request, $related);
    }

    protected function findRelated(array $args)
    {
        if (!$group = \ContactGroup::find($args['id'])) {
            throw new RecordNotFoundException();
        }

        return $group;
    }

    protected function authorize(Request $request, $resource)
    {
        return Authority::canManageGroups($this->getUser($request), $resource);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getRelationshipSelfLink($resource, $schema, $userData)
    {
        return $schema->getRelationshipSelfLink($resource, 'group-users');
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getRelationshipRelatedLink($resource, $schema, $userData)
    {
        // return $schema->getRelationshipRelatedLink($resource, 'group-users');
        return null;
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }

        $data = self::arrayGet($json, 'data');

        if (!is_array($data)) {
            return 'Document´s ´data´ must be an array.';
        }

        foreach ($data as $item) {
            if (self::arrayGet($item, 'type') !== \JsonApi\Schemas\User::TYPE) {
                return 'Wrong `type` in document´s `data`.';
            }

            if (!self::arrayGet($item, 'id')) {
                return 'Missing `id` of document´s `data`.';
            }
        }

        if (self::arrayHas($json, 'data.attributes')) {
            return 'Document must not have `attributes`.';
        }
    }

    private function validateGroupUsers(\ContactGroup $group, $json)
    {
        $validatedUserIds = [];

        foreach (self::arrayGet($json, 'data') as $groupResource) {
            if (!$extractedUser = \User::find($groupResource['id'])) {
                throw new RecordNotFoundException();
            }

            if (!Authority::canAddUsersToGroup($group->owner, $extractedUser)) {
                throw new RecordNotFoundException();
            }

            $validatedUserIds[$extractedUser->id] = $extractedUser->id;
        }

        return $validatedUserIds;
    }
}
