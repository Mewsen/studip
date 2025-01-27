<?php

namespace JsonApi\Routes\Blubber;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\BadRequestException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\TimestampTrait;
use JsonApi\Routes\ValidationTrait;

/**
 * Update a blubber thread.
 */
class ThreadsUpdate extends JsonApiController
{
    use TimestampTrait;
    use ValidationTrait;

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);

        $thread = \BlubberThread::find($args['id']);
        if (!$thread) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!Authority::canEditBlubberThread($user, $thread)) {
            throw new AuthorizationFailedException();
        }

        if (self::arrayHas($json, 'data.attributes.visited-at')) {
            $visitedAt = self::arrayGet($json, 'data.attributes.visited-at');
            $visitedDate = self::fromISO8601($visitedAt)->getTimestamp();
            $GLOBALS['user']->cfg->store('BLUBBERTHREAD_VISITED_' . $thread->getId(), $visitedDate);
        }

        if (self::arrayHas($json, 'data.attributes.is-followed')) {
            $isFollowed = self::arrayGet($json, 'data.attributes.is-followed');
            if ($isFollowed) {
                $thread->addFollowingByUser($user->id);
            } else {
                $thread->removeFollowingByUser($user->id);
            }
        }

        if (self::arrayGet($json, 'data.attributes.content')) {
            if ($thread['context_type'] !== 'course') {
                throw new BadRequestException('Only blubber threads of context-type course can be edited.');
            }

            $course = \Course::find($thread['context_id']);
            if (!Authority::canEditCourseBlubberThread($this->getUser($request), $course)) {
                throw new AuthorizationFailedException();
            }

            $thread['content'] = self::arrayGet($json, 'data.attributes.content');
            $thread->store();
        }

        return $this->getContentResponse($thread);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (self::arrayHas($json, 'data.attributes.visited-at')) {
            $visitedAt = self::arrayGet($json, 'data.attributes.visited-at');
            if (!self::isValidTimestamp($visitedAt)) {
                return '`visited-at` is not an ISO 8601 timestamp.';
            }
        }

        if (self::arrayHas($json, 'data.attributes.is-followed')) {
            $isFollowed = self::arrayGet($json, 'data.attributes.is-followed');
            if (!is_bool($isFollowed)) {
                return '`is-followed` is not a boolean value.';
            }
        }
    }
}
