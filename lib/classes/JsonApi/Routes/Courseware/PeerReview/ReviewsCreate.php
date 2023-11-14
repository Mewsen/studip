<?php

namespace JsonApi\Routes\Courseware\PeerReview;

use Courseware\PeerReview;
use Courseware\PeerReviewProcess;
use Courseware\Task;
use Courseware\TaskGroup;
use InvalidArgumentException;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use JsonApi\Routes\Courseware\Authority;
use JsonApi\Routes\TimestampTrait;
use JsonApi\Routes\ValidationTrait;
use JsonApi\Schemas\Courseware\PeerReview as PeerReviewSchema;
use JsonApi\Schemas\Courseware\PeerReviewProcess as PeerReviewProcessSchema;
use JsonApi\Schemas\StatusGroup as StatusGroupSchema;
use JsonApi\Schemas\User as UserSchema;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Statusgruppen;
use User;

/**
 * Create a PeerReview.
 *
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class ReviewsCreate extends JsonApiController
{
    use ValidationTrait;

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param array $args
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $process = $this->getProcessFromJson($json);
        $user = $this->getUser($request);

        if (!Authority::canCreatePeerReviews($user, $process)) {
            throw new AuthorizationFailedException();
        }

        $resource = $this->create($json);

        return $this->getCreatedResponse($resource);
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
        if (PeerReviewSchema::TYPE !== self::arrayGet($json, 'data.type')) {
            return 'Invalid `type` of document´s `data`.';
        }
        if (self::arrayHas($json, 'data.id')) {
            return 'New document must not have an `id`.';
        }

        // process
        if (!self::arrayHas($json, 'data.relationships.process')) {
            return 'Missing `process` relationship.';
        }
        if (!$this->getProcessFromJson($json)) {
            return 'Invalid `process` relationship.';
        }

        // submitter
        if (!self::arrayHas($json, 'data.relationships.submitter')) {
            return 'Missing `submitter` relationship.';
        }
        if (!$this->getSubmitterFromJson($json)) {
            return 'Invalid `submitter` relationship.';
        }

        // reviewer
        if (!self::arrayHas($json, 'data.relationships.reviewer')) {
            return 'Missing `reviewer` relationship.';
        }
        if (!$this->getReviewerFromJson($json)) {
            return 'Invalid `reviewer` relationship.';
        }
    }

    private function create(array $json): PeerReview
    {
        $process = $this->getProcessFromJson($json);
        $reviewer = $this->getReviewerFromJson($json);
        $submitter = $this->getSubmitterFromJson($json);

        $task = $process['task_group']->findTaskBySolver($submitter);
        $reviewerType = $this->getReviewerType($reviewer);

        /** @var PeerReview $review */
        $review = PeerReview::create([
            'process_id' => $process->id,
            'task_id' => $task->id,
            'submitter_id' => $submitter->id,
            'reviewer_id' => $reviewer->id,
            'reviewer_type' => $reviewerType,
        ]);

        return $review;
    }

    /**
     * @return User|Statusgruppen|null
     */
    private function getActorFromJson(array $json, string $relation)
    {
        $relationship = 'data.relationships.' . $relation;
        if (
            !(
                $this->validateResourceObject($json, $relationship, UserSchema::TYPE) ||
                $this->validateResourceObject($json, $relationship, StatusGroupSchema::TYPE)
            )
        ) {
            return null;
        }
        $resourceId = self::arrayGet($json, $relationship . '.data.id');

        switch (self::arrayGet($json, $relationship . '.data.type')) {
            case UserSchema::TYPE:
                return User::find($resourceId);
            case StatusGroupSchema::TYPE:
                return Statusgruppen::find($resourceId);
        }

        throw new InvalidArgumentException();
    }

    private function getProcessFromJson(array $json): ?PeerReviewProcess
    {
        if (!$this->validateResourceObject($json, 'data.relationships.process', PeerReviewProcessSchema::TYPE)) {
            return null;
        }
        $resourceId = self::arrayGet($json, 'data.relationships.process.data.id');

        return PeerReviewProcess::find($resourceId);
    }

    /**
     * @return User|Statusgruppen|null
     */
    private function getReviewerFromJson(array $json)
    {
        return $this->getActorFromJson($json, 'reviewer');
    }

    private function getReviewerType($reviewer): string
    {
        if ($reviewer instanceof User) {
            return 'autor';
        }
        if ($reviewer instanceof Statusgruppen) {
            return 'group';
        }

        throw new InvalidArgumentException();
    }

    /**
     * @return User|Statusgruppen|null
     */
    private function getSubmitterFromJson(array $json)
    {
        return $this->getActorFromJson($json, 'submitter');
    }
}
