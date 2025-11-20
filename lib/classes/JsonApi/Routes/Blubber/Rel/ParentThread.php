<?php

namespace JsonApi\Routes\Blubber\Rel;

use Psr\Http\Message\ServerRequestInterface as Request;
use JsonApi\Routes\Blubber\Authority as BlubberAuthority;
use JsonApi\Routes\RelationshipsController;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Schemas\BlubberThread as BlubberThreadSchema;

class ParentThread extends RelationshipsController
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \BlubberThread $related
     */
    protected function fetchRelationship(Request $request, $related)
    {
        $parent = $related->parentthread;

        return $this->getContentResponse($parent);
    }

    /**
     * @param \BlubberThread $resource
     */
    protected function authorize(Request $request, $resource)
    {
        switch ($request->getMethod()) {
            case 'GET':
                return BlubberAuthority::canShowBlubberThread($this->getUser($request), $resource);
            default:
                return false;
        }
    }

    protected function findRelated(array $args)
    {
        $thread = \BlubberThread::find($args['id']);
        if (!$thread) {
            throw new RecordNotFoundException();
        }

        return $thread;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }

        $item = self::arrayGet($json, 'data');

        if ($item !== null) {
            if (BlubberThreadSchema::TYPE !== self::arrayGet($item, 'type')) {
                return 'Wrong `type` in document´s `data`.';
            }

            if (!self::arrayGet($item, 'id')) {
                return 'Missing `id` of document´s `data`.';
            }

            if (self::arrayHas($item, 'attributes')) {
                return 'Document must not have `attributes`.';
            }
        }
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \BlubberThread $resource
     */
    protected function getRelationshipSelfLink($resource, $schema, $userData)
    {
        return $schema->getRelationshipSelfLink($resource, BlubberThreadSchema::TYPE);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param \BlubberThread $resource
     */
    protected function getRelationshipRelatedLink($resource, $schema, $userData)
    {
        return $schema->getRelationshipRelatedLink($resource, BlubberThreadSchema::REL_PARENT);
    }
}
