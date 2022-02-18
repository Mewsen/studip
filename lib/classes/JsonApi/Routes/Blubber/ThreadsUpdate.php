<?php

namespace JsonApi\Routes\Blubber;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\BadRequestException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use JsonApi\Errors\RecordNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Create a new private blubber thread.
 */
class ThreadsUpdate extends JsonApiController
{
    use ValidationTrait;
        /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);

        if (!($thread = \BlubberThread::find($args['id']))) {
            throw new RecordNotFoundException();
        }

        if ($thread['context_type'] !== 'course') {
            throw new BadRequestException('Only blubber threads of context-type course can be edited.');
        }

        if (!Authority::canEditCourseBlubberThread($this->getUser($request))) {
            throw new AuthorizationFailedException();
        }

        $thread['content'] = self::arrayGet($json, 'data.attributes.content');
        $thread->store();

        return $this->getCodeResponse(204);
    }

    protected function validateResourceDocument($json)
    {
        if (empty(self::arrayGet($json, 'data.attributes.content'))) {
            return 'Thread content should not be empty.';
        }
    }
}