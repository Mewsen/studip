<?php
namespace JsonApi\Routes\Forum;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\RangeAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;
use Forum\ForumSubscription;

class ForumSubscriptionIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumSubscription::REL_RANGE,
        \JsonApi\Schemas\Forum\ForumSubscription::REL_SUBJECT,
        \JsonApi\Schemas\Forum\ForumSubscription::REL_USER
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $range = get_object_by_range_id($args['range_id']);
        if (!$range) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!RangeAuthority::canShowRange($user, $range)) {
            throw new AuthorizationFailedException();
        }

        $subscriptions = ForumSubscription::getUserSubscriptions($range->id, $user->user_id);

        return $this->getPaginatedContentResponse(
            array_slice($subscriptions, ...$this->getOffsetAndLimit()),
            count($subscriptions)
        );
    }
}
