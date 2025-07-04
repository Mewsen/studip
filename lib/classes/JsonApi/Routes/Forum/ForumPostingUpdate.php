<?php
namespace JsonApi\Routes\Forum;

use JsonApi\Errors\RecordNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Studip\Markup;
use Forum\ForumPosting;

class ForumPostingUpdate extends JsonApiController
{
    use ValidationTrait;

    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumPosting::REL_DISCUSSION,
        \JsonApi\Schemas\Forum\ForumPosting::REL_POSTING,
        \JsonApi\Schemas\Forum\ForumPosting::REL_OPENGRAPH_URLS,
        \JsonApi\Schemas\Forum\ForumPosting::REL_AUTHOR,
        \JsonApi\Schemas\Forum\ForumPosting::REL_REACTIONS,
        \JsonApi\Schemas\Forum\ForumPosting::REL_REACTIONS_USER
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $user = $this->getUser($request);

        $posting = ForumPosting::findOneBySQL(
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

        $posting->content = Markup::markAsHtml(self::arrayGet($json, 'data.attributes.content'));
        $posting->anonymous = (self::arrayGet($json, 'data.attributes.anonymous') && \Config::get()->FORUM_ANONYMOUS_POSTINGS);
        $posting->store();

        return $this->getCreatedResponse($posting);
    }

    protected function validateResourceDocument($json, $data)
    {
        $required_keys = [
            'data.attributes.content' => 'Missing `data.attributes.content`',
            'data.attributes.anonymous' => 'Missing `data.attributes.anonymous`',
        ];

        foreach ($required_keys as $key => $error_message) {
            if (!self::arrayHas($json, $key)) {
                return $error_message;
            }
        }

        return null;
    }
}
