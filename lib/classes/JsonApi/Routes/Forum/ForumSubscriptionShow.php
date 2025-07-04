<?php
namespace JsonApi\Routes\Forum;

use JsonApi\Errors\RecordNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;
use Forum\ForumSubscription;

class ForumSubscriptionShow extends JsonApiController
{
    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumSubscription::REL_RANGE,
        \JsonApi\Schemas\Forum\ForumSubscription::REL_SUBJECT,
        \JsonApi\Schemas\Forum\ForumSubscription::REL_USER,
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $user = $this->getUser($request);

        $subscription = ForumSubscription::findOneBySQL(
            "id = :id AND user_id = :user_id",
            [
                'id' => $args['subscription_id'],
                'user_id' => $user->user_id
            ]
        );

        if (!$subscription) {
            throw new RecordNotFoundException();
        }

        return $this->getContentResponse($subscription);
    }
}
