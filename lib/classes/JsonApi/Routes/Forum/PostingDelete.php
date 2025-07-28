<?php
namespace JsonApi\Routes\Forum;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\Courses\Authority as CourseAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;
use Forum\Posting;

class PostingDelete extends JsonApiController
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);

        $posting = Posting::findOneBySQL(
            "posting_id = :posting_id AND user_id = :user_id",
            [
                'posting_id' => $args['posting_id'],
                'user_id' => $user->user_id
            ]
        );

        if (!$posting) {
            throw new RecordNotFoundException();
        }

        if ($posting->discussion->closed_at) {
            throw new AuthorizationFailedException();
        }

        $posting->delete();

        return $this->getCodeResponse(204);
    }
}
