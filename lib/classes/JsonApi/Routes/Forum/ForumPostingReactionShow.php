<?php
namespace JsonApi\Routes\Forum;

use JsonApi\Errors\RecordNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;

class ForumPostingReactionShow extends JsonApiController
{
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumPostingReaction::REL_POSTING,
        \JsonApi\Schemas\Forum\ForumPostingReaction::REL_USER,
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $posting_reaction = \Forum\ForumPostingReaction::find($args['reaction_id']);

        if (!$posting_reaction) {
            throw new RecordNotFoundException();
        }

        return $this->getContentResponse($posting_reaction);
    }
}
