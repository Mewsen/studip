<?php
namespace JsonApi\Routes\Forum;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Forum\DiscussionType;

class DiscussionTypeStore extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);
        if (!$GLOBALS['perm']->have_perm('root', $user->id)) {
            throw new AuthorizationFailedException();
        }

        $json = $this->validate($request);
        $discussion_type = DiscussionType::create([
            'name' => self::arrayGet($json, 'data.attributes.name'),
            'icon' => self::arrayGet($json, 'data.attributes.icon')
        ]);

        return $this->getCreatedResponse($discussion_type);
    }

    protected function validateResourceDocument($json, $data)
    {
        $required_keys = [
            'data.attributes.name' => 'Missing `data.attributes.name`',
            'data.attributes.icon' => 'Missing `data.attributes.icon`',
        ];

        foreach ($required_keys as $key => $error_message) {
            if (!self::arrayHas($json, $key)) {
                return $error_message;
            }
        }

        return null;
    }
}
