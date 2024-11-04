<?php

namespace JsonApi\Routes\Courseware;

use Courseware\Unit;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\TimestampTrait;
use JsonApi\Routes\ValidationTrait;
use JsonApi\Schemas\Courseware\Unit as UnitSchema;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Update one Unit.
 */
class UnitsUpdate extends JsonApiController
{
    use EditBlockAwareTrait;
    use TimestampTrait;
    use ValidationTrait;

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $resource = Unit::find($args['id']);
        if (!$resource) {
            throw new RecordNotFoundException();
        }
        $json = $this->validate($request, $resource);
        $user = $this->getUser($request);
        if (!Authority::canUpdateUnit($user, $resource)) {
            throw new AuthorizationFailedException();
        }
        $resource = $this->updateUnit($user, $resource, $json);

        return $this->getContentResponse($resource);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }

        if (UnitSchema::TYPE !== self::arrayGet($json, 'data.type')) {
            return 'Wrong `type` member of document´s `data`.';
        }

        if (!self::arrayHas($json, 'data.id')) {
            return 'Document must have an `id`.';
        }

        if (self::arrayHas($json, 'data.attributes.visible-start-date')) {
            $visibleStartDate = self::arrayGet($json, 'data.attributes.visible-start-date');
            if ($visibleStartDate && !self::isValidTimestamp($visibleStartDate)) {
                return '`visible-start-date` is not an ISO 8601 timestamp.';
            }
        }

        if (self::arrayHas($json, 'data.attributes.visible-end-date')) {
            $visibleEndDate = self::arrayGet($json, 'data.attributes.visible-end-date');
            if ($visibleEndDate && !self::isValidTimestamp($visibleEndDate)) {
                return '`visible-start-date` is not an ISO 8601 timestamp.';
            }
        }

        if (self::arrayHas($json, 'data.attributes.writable-start-date')) {
            $writableStartDate = self::arrayGet($json, 'data.attributes.writable-start-date');
            if ($writableStartDate && !self::isValidTimestamp($writableStartDate)) {
                return '`writable-start-date` is not an ISO 8601 timestamp.';
            }
        }

        if (self::arrayHas($json, 'data.attributes.writable-end-date')) {
            $writableEndDate = self::arrayGet($json, 'data.attributes.writable-end-date');
            if ($writableEndDate && !self::isValidTimestamp($writableEndDate)) {
                return '`writable-end-date` is not an ISO 8601 timestamp.';
            }
        }
    }

    private function updateUnit(\User $user, Unit $resource, array $json): Unit
    {
        $attributes = [
            'position',
            'public',
            'permission-scope',
            'permission-type',
            'visible',
            'visible-approval',
            'writable',
            'writable-approval',
        ];

        foreach ($attributes as $jsonKey) {
            $sormKey = strtr($jsonKey, '-', '_');
            $val = self::arrayGet($json, 'data.attributes.' . $jsonKey, '');
            if ($val) {
                $resource->$sormKey = $val;
            }
        }
        if (self::arrayHas($json, 'data.attributes.visible-all')) {
            $resource->visible_all = self::arrayGet($json, 'data.attributes.visible-all');
        }
        if (self::arrayHas($json, 'data.attributes.writable-all')) {
            $resource->writable_all = self::arrayGet($json, 'data.attributes.writable-all');
        }
        if (self::arrayHas($json, 'data.attributes.visible-start-date')) {
            $visibleStartDate = self::arrayGet($json, 'data.attributes.visible-start-date');
            if ($visibleStartDate) {
                $visibleStartDate = self::fromISO8601($visibleStartDate);
                $visibleStartDate = $visibleStartDate->getTimestamp();
            }
            $resource->visible_start_date = $visibleStartDate;
        }
        if (self::arrayHas($json, 'data.attributes.visible-end-date')) {
            $visibleEndDate = self::arrayGet($json, 'data.attributes.visible-end-date');
            if ($visibleEndDate) {
                $visibleEndDate = self::fromISO8601($visibleEndDate);
                $visibleEndDate = $visibleEndDate->getTimestamp();
            }
            $resource->visible_end_date = $visibleEndDate;
        }
        if (self::arrayHas($json, 'data.attributes.writable-start-date')) {
            $writableStartDate = self::arrayGet($json, 'data.attributes.writable-start-date');
            if ($writableStartDate) {
                $writableStartDate = self::fromISO8601($writableStartDate);
                $writableStartDate = $writableStartDate->getTimestamp();
            }
            $resource->writable_start_date = $writableStartDate;
        }
        if (self::arrayHas($json, 'data.attributes.writable-end-date')) {
            $writableEndDate = self::arrayGet($json, 'data.attributes.writable-end-date');
            if ($writableEndDate) {
                $writableEndDate = self::fromISO8601($writableEndDate);
                $writableEndDate = $writableEndDate->getTimestamp();
            }
            $resource->writable_end_date = $writableEndDate;
        }

        $resource->store();

        return $resource;
    }
}
