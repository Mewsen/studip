<?php
namespace JsonApi\Routes\Lti;

use JsonApi\Errors\RecordNotFoundException;
use Lti\ResourceLink;
use LtiToolModule;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;

class ResourceUpdateSort extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args): Response
    {
        $json = $this->validate($request);
        $rangeId = self::arrayGet($json, 'data.relationships.range.data.id');

        $range = get_object_by_range_id($rangeId);
        if (!$range) {
            throw new RecordNotFoundException();
        }

        if (!LtiToolModule::isModerator($range->id)) {
            throw new AuthorizationFailedException();
        }

        $resourceIds = self::arrayGet($json, 'data.attributes.resource-ids');

        ResourceLink::findEachBySQL(
            function (ResourceLink $resourceLink) use ($resourceIds) {
                $resourceLink->position = (int) array_search($resourceLink->id, $resourceIds);
                $resourceLink->store();
            },
            "id IN (:resource_ids) AND course_id = :course_id",
            [
                'resource_ids' => $resourceIds,
                'course_id' => $range->id
            ]
        );

        return $this->getCodeResponse(204);
    }

    protected function validateResourceDocument($json, $data): ?string
    {
        $requiredKeys = [
            'data.attributes.resource-ids' => 'Missing `data.attributes.resource-ids`',
            'data.relationships.range.data.id' => 'Missing `data.relationships.range.data.id`',
        ];

        foreach ($requiredKeys as $key => $errorMessage) {
            if (!self::arrayHas($json, $key)) {
                return $errorMessage;
            }
        }

        return null;
    }
}
