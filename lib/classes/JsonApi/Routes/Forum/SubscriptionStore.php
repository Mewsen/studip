<?php
namespace JsonApi\Routes\Forum;

use JsonApi\Errors\BadRequestException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Forum\Discussion;
use Forum\Subscription;

class SubscriptionStore extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $user = $this->getUser($request);
        $subjectType = $this->mapSubjectType(self::arrayGet($json, 'data.relationships.subject.data.type'));

        if ($subjectType === 'discussion') {
            $discussion = Discussion::find(self::arrayGet($json, 'data.relationships.subject.data.id'));

            if (!$discussion || $discussion->closed_at) {
                throw new AuthorizationFailedException();
            }
        }

        if (!self::arrayHas($json, 'data.id')) {
            $subscription = new Subscription();
            $subscription->user_id = $user->user_id;
        } else {
            $subscription = Subscription::findOneBySQL(
                "id = :id AND user_id = :user_id",
                [
                    'id' => self::arrayGet($json, 'data.id'),
                    'user_id' => $user->user_id
                ]
            );

            if (!$subscription) {
                throw new BadRequestException();
            }
        }

        $subscription->range_id = self::arrayGet($json, 'data.relationships.range.data.id');
        $subscription->subject_id = self::arrayGet($json, 'data.relationships.subject.data.id');
        $subscription->subject = $subjectType;
        $subscription->notification_type = self::arrayGet($json, 'data.attributes.notification-type');

        $subscription->store();

        return $this->getCreatedResponse($subscription);
    }

    protected function validateResourceDocument($json, $data)
    {
        $required_keys = [
            'data' => 'Missing `data`',
            'data.attributes' => 'Missing `data.attributes`',
            'data.attributes.notification-type' => 'Missing `data.attributes.notification-type`',
            'data.relationships' => 'Missing `data.relationships`',
            'data.relationships.range.data.id' => 'Missing `data.relationships.range.data.id`',
            'data.relationships.subject.data.id' => 'Missing `data.relationships.subject.data.id`',
            'data.relationships.subject.data.type' => 'Missing `data.relationships.subject.data.type`',
        ];

        foreach ($required_keys as $key => $error_message) {
            if (!self::arrayHas($json, $key)) {
                return $error_message;
            }
        }

        return null;
    }

    private function mapSubjectType($type): string
    {
        return match ($type) {
            'forum-discussions' => 'discussion',
            'forum-topics' => 'topic'
        };
    }
}
