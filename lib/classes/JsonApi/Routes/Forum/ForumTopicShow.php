<?php
namespace JsonApi\Routes\Forum;

use JsonApi\Routes\RangeAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Forum\ForumTopic;

class ForumTopicShow extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];

    public function __invoke(Request $request, Response $response, $args)
    {
        $topic = ForumTopic::find($args['topic_id']);
        if (!$topic) {
            throw new RecordNotFoundException();
        }

        $range = get_object_by_range_id($topic->range_id);
        if (!$range) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!RangeAuthority::canShowRange($user, $range)) {
            throw new AuthorizationFailedException();
        }

        return $this->getContentResponse($topic);
    }
}
