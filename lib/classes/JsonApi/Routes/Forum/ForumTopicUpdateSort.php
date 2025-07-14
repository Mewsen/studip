<?php
namespace JsonApi\Routes\Forum;

use CoreForum;
use JsonApi\Errors\RecordNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Forum\ForumTopic;

class ForumTopicUpdateSort extends JsonApiController
{
    use ValidationTrait;

    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $range_id = self::arrayGet($json, 'data.relationships.range.data.id');

        $range = get_object_by_range_id($range_id);
        if (!$range) {
            throw new RecordNotFoundException();
        }

        if (!CoreForum::isModerator($range->id)) {
            throw new AuthorizationFailedException();
        }

        $topic_ids = self::arrayGet($json, 'data.attributes.topic-ids');

        ForumTopic::findEachBySQL(
            function (ForumTopic $topic) use ($topic_ids) {
                $topic->position = (int) array_search($topic->topic_id, $topic_ids);
                $topic->store();
            },
            "topic_id IN (:topic_ids) AND range_id = :course_id",
            [
                "topic_ids" => $topic_ids,
                "course_id" => $range->id
            ]
        );

        return $this->getCodeResponse(204);
    }

    protected function validateResourceDocument($json, $data)
    {
        $required_keys = [
            'data.attributes.topic-ids' => 'Missing `data.attributes.topic-ids`',
            'data.relationships.range.data.id' => 'Missing `data.relationships.range.data.id`',
        ];

        foreach ($required_keys as $key => $error_message) {
            if (!self::arrayHas($json, $key)) {
                return $error_message;
            }
        }

        return null;
    }
}
