<?php
namespace JsonApi\Routes\Forum;

use Forum\DiscussionType;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;

class DiscussionTypeDelete extends JsonApiController
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);
        if (!$GLOBALS['perm']->have_perm('root', $user->id)) {
            throw new AuthorizationFailedException();
        }

        $discussion_type = DiscussionType::find($args['type_id']);
        if (!$discussion_type) {
            throw new RecordNotFoundException();
        }

        $discussion_type->delete();

        return $this->getCodeResponse(204);
    }
}
