<?php
namespace JsonApi\Routes\Forum;

use Forum\ForumPosting;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\RangeAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;

class ForumPostingShow extends JsonApiController
{
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumPosting::REL_DISCUSSION,
        \JsonApi\Schemas\Forum\ForumPosting::REL_POSTING,
        \JsonApi\Schemas\Forum\ForumPosting::REL_OPENGRAPH_URLS,
        \JsonApi\Schemas\Forum\ForumPosting::REL_REACTIONS,
        \JsonApi\Schemas\Forum\ForumPosting::REL_REACTIONS_USER
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $posting = ForumPosting::find($args['posting_id']);
        if (!$posting) {
            throw new RecordNotFoundException();
        }

        $range = get_object_by_range_id($posting->range_id);
        if (!$range) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!RangeAuthority::canShowRange($user, $range)) {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse($posting);
    }
}
