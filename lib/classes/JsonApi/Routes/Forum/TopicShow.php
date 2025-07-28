<?php
namespace JsonApi\Routes\Forum;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Forum\Topic;

class TopicShow extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];

    public function __invoke(Request $request, Response $response, $args)
    {
        $topic = Topic::find($args['topic_id']);
        if (!$topic) {
            throw new RecordNotFoundException();
        }

        $range = get_object_by_range_id($topic->range_id);
        if (!$range) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!Authority::canShowForum($user, $range)) {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse($topic);
    }
}
