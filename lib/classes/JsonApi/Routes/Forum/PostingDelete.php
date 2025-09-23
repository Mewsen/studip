<?php
namespace JsonApi\Routes\Forum;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;
use Forum\Posting;

class PostingDelete extends JsonApiController
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $posting = Posting::find($args['posting_id']);
        if (!$posting) {
            throw new RecordNotFoundException();
        }

        if (
            !Authority::canDeletePost($this->getUser($request), $posting, (bool) $posting->discussion->closed_at)
        ) {
            throw new AuthorizationFailedException();
        }

        $posting->delete();

        return $this->getCodeResponse(204);
    }
}
