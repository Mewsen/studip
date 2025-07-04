<?php
namespace JsonApi\Routes\Forum;

use JsonApi\Errors\RecordNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;
use Forum\ForumPostingReaction;

class ForumPostingReactionDelete extends JsonApiController
{
    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);

        $posting_reaction = ForumPostingReaction::findOneBySQL(
            "id = :reaction_id AND user_id = :user_id",
            [
                'reaction_id' => $args['reaction_id'],
                'user_id' => $user->user_id
            ]
        );

        if (!$posting_reaction) {
            throw new RecordNotFoundException();
        }

        $posting_reaction->delete();

        return $this->getCodeResponse(204);
    }
}
