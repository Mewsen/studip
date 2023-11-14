<?php

namespace JsonApi\Routes\Courseware\PeerReview;

use Courseware\PeerReviewProcess;
use Courseware\TaskGroup;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\Courseware\Authority;
use JsonApi\Schemas\Courseware\PeerReviewProcess as ProcessSchema;
use JsonApi\JsonApiController;
use JsonApi\Routes\TimestampTrait;
use JsonApi\Routes\ValidationTrait;
use JsonApi\Schemas\Courseware\PeerReviewProcess as PeerReviewProcessSchema;
use JsonApi\Schemas\Courseware\TaskGroup as TaskGroupSchema;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use User;

/**
 * Updates one PeerReviewProcess.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class ProcessesUpdate extends JsonApiController
{
    use TimestampTrait;
    use ValidationTrait;

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param array $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        /** @var ?\Courseware\PeerReviewProcess $resource */
        $resource = PeerReviewProcess::find($args['id']);
        if (!$resource) {
            throw new RecordNotFoundException();
        }
        $json = $this->validate($request, $resource);
        $user = $this->getUser($request);
        if (!Authority::canUpdatePeerReviewProcess($user, $resource)) {
            throw new AuthorizationFailedException();
        }

        $process = $this->update($user, $resource, $json);

        return $this->getContentResponse($process);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     *
     * @param array $json
     * @param mixed $data
     *
     * @return string|void
     */
    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }
        if (PeerReviewProcessSchema::TYPE !== self::arrayGet($json, 'data.type')) {
            return 'Invalid `type` of document´s `data`.';
        }

        if (!self::arrayHas($json, 'data.attributes.configuration')) {
            return 'Missing `configuration` attribute.';
        }

        if (!self::arrayHas($json, 'data.attributes.review-start')) {
            return 'Missing `review-start` attribute.';
        }
        $startDate = self::arrayGet($json, 'data.attributes.review-start');
        if (!self::isValidTimestamp($startDate)) {
            return '`review-start` is not an ISO 8601 timestamp.';
        }

        if (!self::arrayHas($json, 'data.attributes.review-end')) {
            return 'Missing `review-end` attribute.';
        }
        $endDate = self::arrayGet($json, 'data.attributes.review-end');
        if (!self::isValidTimestamp($endDate)) {
            return '`review-end` is not an ISO 8601 timestamp.';
        }

        if (self::arrayHas($json, 'data.relationships.task-group')) {
            if (!$this->getTaskGroupFromJson($json)) {
                return 'Invalid `task-group` relationship.';
            }
        }
    }

    private function getTaskGroupFromJson(array $json): ?TaskGroup
    {
        if (!$this->validateResourceObject($json, 'data.relationships.task-group', TaskGroupSchema::TYPE)) {
            return null;
        }
        $resourceId = self::arrayGet($json, 'data.relationships.task-group.data.id');

        return TaskGroup::find($resourceId);
    }

    private function update(User $user, PeerReviewProcess $process, array $json): PeerReviewProcess
    {
        $startDate = self::fromISO8601(self::arrayGet($json, 'data.attributes.review-start'));
        $endDate = self::fromISO8601(self::arrayGet($json, 'data.attributes.review-end'));
        $configuration = self::arrayGet($json, 'data.attributes.configuration');

        $process->review_start = $startDate->getTimestamp();
        $process->review_end = $endDate->getTimestamp();
        $process->configuration = $configuration;

        $process->store();

        return $process;
    }
}
