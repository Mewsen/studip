<?php

namespace JsonApi\Routes\Files;

use JsonApi\Schemas\Folder as FolderSchema;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\BadRequestException;
use JsonApi\Errors\InternalServerError;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;

class RangeFoldersCreate extends JsonApiController
{
    use RangeHelperTrait, ValidationTrait;

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        if (!$this->validateResourceType($args['type'])) {
            throw new BadRequestException('Bad resource type.');
        }

        if (!$range = $this->findResource($args['type'], $args['id'])) {
            throw new RecordNotFoundException();
        }

        $folder = $this->validateAndCreateFolder($request, $range);

        return $this->getCreatedResponse($folder);
    }

    protected function validateAndCreateFolder(Request $request, \SimpleORMap $range)
    {
        if (!Authority::canShowFileArea($user = $this->getUser($request), $range)) {
            throw new AuthorizationFailedException();
        }

        $json = $this->validate($request);

        if (!$parent = $this->getRelationshipParent($json)) {
            throw new RecordNotFoundException('Bad `parent` folder.');
        }

        return $this->validateAndCreateSubfolder($range, $user, $json, $parent);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data.attributes')) {
            return 'Missing `attributes` member of document´s `data`.';
        }

        if (!self::arrayHas($json, 'data.attributes.name')) {
            return 'Missing `data.name`.';
        }

        // Attribute: name must not be empty if present
        if (self::arrayHas($json, 'data.attributes.name')
            && !mb_strlen(trim(self::arrayGet($json, 'data.attributes.name', '')))) {
            return '`name` must not be empty.';
        }

        // Relationship: parent
        if (self::arrayHas($json, 'data.relationships.parent')) {
            $parent = self::arrayGet($json, 'data.relationships.parent');
            if (!self::arrayHas($parent, 'data')) {
                return 'Missing `data` member at document´s top level.';
            }

            // type
            if (self::arrayGet($parent, 'data.type') !== FolderSchema::TYPE) {
                return 'Missing `type` member of document´s `data`.';
            }

        } else {
            return 'Missing `parent` relationship.';
        }

        return '';
    }

    protected function validateAndCreateSubfolder(
        \SimpleORMap $range,
        \User $user,
        array $json,
        \Folder $parentFolder
    ) {
        if ($parentFolder->range_id !== $range->id) {
            throw new BadRequestException('Parent folder does not belong to this file area.');
        }

        if (!$parent = $parentFolder->getTypedFolder()) {
            throw new InternalServerError();
        }

        if (!Authority::canCreateSubfolder($user, $parent)) {
            throw new AuthorizationFailedException();
        }

        return $this->createFolder($user, $json, $parent);
    }

    protected function getRelationshipParent($json)
    {
        if (!$parentId = $this->getRelationshipParentId($json)) {
            return null;
        }

        return \Folder::find($parentId);
    }

    protected function getRelationshipParentId($json)
    {
        return self::arrayGet($json, 'data.relationships.parent.data.id', false);
    }

    protected function createFolder(
        \User $user,
        array $json,
        \FolderType $parentFolder
    ) {
        $getTrimmed = function ($key, $default = '') use ($json) {
            return trim(self::arrayGet($json, $key, $default));
        };

        $name = $getTrimmed('data.attributes.name');
        $description = $getTrimmed('data.attributes.description');
        $folderType = $getTrimmed('data.attributes.folder-type', 'StandardFolder');

        $result = \FileManager::createSubFolder(
            $parentFolder,
            $user,
            $folderType,
            $name,
            $description
        );

        if (is_array($result)) {
            throw new BadRequestException($result[0]);
        }

        return $result;
    }
}
