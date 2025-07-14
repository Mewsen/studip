<?php
namespace JsonApi\Routes\Forum;

use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\RangeAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Forum\Enum\SubscriptionNotificationType;
use Studip\Markup;
use Forum\ForumDiscussion;
use Forum\ForumPosting;
use Forum\ForumPostingRead;
use Forum\ForumSubscription;

class ForumPostingStore extends JsonApiController
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

        $discussion = ForumDiscussion::find(self::arrayGet($json, 'data.relationships.discussion.data.id'));
        $range = get_object_by_range_id($discussion->range_id);

        if (!$discussion || !$range) {
            throw new RecordNotFoundException();
        }

        if (
            !RangeAuthority::canShowRange($user, $range) ||
            $discussion->closed_at
        ) {
            throw new AuthorizationFailedException();
        }

        $parent_id = self::arrayGet($json, 'data.relationships.posting.data.id');

        $psoting = ForumPosting::create([
            'range_id' => $discussion->range_id,
            'parent_id' => $parent_id ?? null,
            'discussion_id' => $discussion->discussion_id,
            'content' => Markup::markAsHtml(self::arrayGet($json, 'data.attributes.content')),
            'anonymous' => (self::arrayGet($json, 'data.attributes.anonymous') && \Config::get()->FORUM_ANONYMOUS_POSTINGS),
            'user_id' => $user->user_id
        ]);

        $subscription = ForumSubscription::findOneBySQL(
            "user_id = :user_id AND subject_id IN (:subject_ids)",
            [
                'user_id' => $user->user_id,
                'subject_ids' => [$discussion->discussion_id, $discussion->topic_id]
            ]
        );

        if (!$subscription) {
            $subscription = new ForumSubscription();
            $subscription->user_id = $user->user_id;
            $subscription->range_id = $discussion->range_id;
            $subscription->subject_id = $discussion->discussion_id;
            $subscription->subject = 'discussion';
            $subscription->notification_type = SubscriptionNotificationType::All->value;
            $subscription->store();
        }

        ForumPostingRead::updateUserReadPoint($user->user_id, $discussion->discussion_id);

        return $this->getCreatedResponse($psoting);
    }

    protected function validateResourceDocument($json, $data)
    {
        $required_keys = [
            'data.attributes.content' => 'Missing `data.attributes.content`',
            'data.attributes.anonymous' => 'Missing `data.attributes.anonymous`',
            'data.relationships.discussion.data.id' => 'Missing `data.relationships.discussion.data.id`',
        ];

        foreach ($required_keys as $key => $error_message) {
            if (!self::arrayHas($json, $key)) {
                return $error_message;
            }
        }

        return null;
    }
}
