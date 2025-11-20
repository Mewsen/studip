<?php

namespace JsonApi\Routes\Blubber;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\BadRequestException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Create a new sub thread.
 */
class SubThreadsCreate extends JsonApiController
{
    use ValidationTrait;

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);

        $user = $this->getUser($request);

        $parentId = self::arrayGet($json, 'data.attributes.parent-id', '');

        $parentThread = \BlubberThread::find($parentId);

        if (empty($parentThread)) {
            throw new RecordNotFoundException();
        }

        // TODO: make sure the context type check is still needed here!?
        if (!$parentThread->isOfContextType([\BlubberThread::CTX_TYPE_PRIVATE, \BlubberThread::CTX_TYPE_COURSE])) {
            throw new BadRequestException('Only blubber threads of context-type private or course can be created.');
        }

        if ($parentThread->isOfContextType(\BlubberThread::CTX_TYPE_PRIVATE)) {
            if (!Authority::canCreatePrivateBlubberThread($user)) {
                throw new AuthorizationFailedException();
            }
        } else {
            if (!Authority::canCreateCourseBlubberThread($user)) {
                throw new AuthorizationFailedException();
            }
        }

        // TODO: Do we need custom content (title) for the sub-thread???

        $customFields = [
            'user_id' => $user->id
        ];

        $subThread = $parentThread->createSubThread($customFields);

        return $this->getCreatedResponse($subThread);
    }

    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data.attributes.parent-id')) {
            return 'Attribute \'parent-id\' is required.';
        }
    }
}
