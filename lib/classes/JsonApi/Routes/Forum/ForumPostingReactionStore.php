<?php
namespace JsonApi\Routes\Forum;

use Course;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\BadRequestException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\Courses\Authority as CourseAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;
use JsonApi\Routes\ValidationTrait;
use Forum\ForumPosting;
use Forum\ForumPostingReaction;

class ForumPostingReactionStore extends JsonApiController
{
    use ValidationTrait;

    protected $allowedIncludePaths = [
        \JsonApi\Schemas\Forum\ForumPostingReaction::REL_USER
    ];

    public function __invoke(Request $request, Response $response, $args)
    {
        $json = $this->validate($request);
        $user = $this->getUser($request);

        $posting = ForumPosting::find(self::arrayGet($json, 'data.relationships.posting.data.id'));
        if (!$posting) {
            throw new BadRequestException();
        }

        $course = Course::find($posting->range_id);
        if (!$course) {
            throw new RecordNotFoundException();
        }

        if (!CourseAuthority::canShowCourse($user, $course, CourseAuthority::SCOPE_BASIC)) {
            throw new AuthorizationFailedException();
        }

        $posting_reaction = ForumPostingReaction::create([
            'posting_id' => $posting->posting_id,
            'user_id' => $user->user_id,
            'emoji' => self::arrayGet($json, 'data.attributes.emoji')
        ]);

        if ($user->user_id !== $posting->user_id) {
            \PersonalNotifications::add(
                $posting->user_id,
                \URLHelper::getURL('dispatch.php/course/forum/discussions/show/'.$posting->discussion_id, ['cid' => $posting->range_id], true)."#post_" . $posting->posting_id,
                sprintf(_("%s hat auf deinen Beitrag reagiert."), $user->getFullName()),
                null,
                self::arrayGet($json, 'data.meta.emoji-icon')
            );
        }

        return $this->getCreatedResponse($posting_reaction);
    }

    protected function validateResourceDocument($json, $data)
    {
        $required_keys = [
            'data.attributes.emoji' => 'Missing `data.attributes.emoji`',
            'data.meta.emoji-icon' => 'Missing `data.meta.emoji-icon`',
            'data.relationships.posting.data.id' => 'Missing `data.relationships.posting.data.id`',
        ];

        foreach ($required_keys as $key => $error_message) {
            if (!self::arrayHas($json, $key)) {
                return $error_message;
            }
        }

        return null;
    }
}
