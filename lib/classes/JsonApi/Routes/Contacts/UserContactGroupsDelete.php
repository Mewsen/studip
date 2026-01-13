<?php

namespace JsonApi\Routes\Contacts;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserContactGroupsDelete extends JsonApiController
{
    use ValidationTrait;
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);
        $json = $this->validate($request);

        foreach ($this->validateUserContactGroups($user, $json) as $userContactGroup) {
            $userContactGroup->delete();
        }

        return $this->getCodeResponse(204);
    }

    private function validateUserContactGroups(\User $user, $json)
    {
        $userContactGroups = [];

        foreach (self::arrayGet($json, 'data') as $groupResource) {
            if (!$group = \ContactGroup::find($groupResource['id'])) {
                throw new RecordNotFoundException();
            }

            if (!Authority::canDeleteGroups($user, $group)) {
                throw new AuthorizationFailedException();
            }

            $userContactGroups[$group->id] = $group;
        }

        return $userContactGroups;
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
            if (self::arrayGet($item, 'type') !== \JsonApi\Schemas\UserContactGroup::TYPE) {
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
}
