<?php

namespace JsonApi\Routes\Forum;

use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\Courses\Authority as CourseAuthority;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\JsonApiController;

class ForumConfigIndex extends JsonApiController
{
    public function __invoke(Request $request, Response $response, $args)
    {
        if (!$course = \Course::find($args['course_id'])) {
            throw new RecordNotFoundException();
        }

        $user = $this->getUser($request);
        if (!CourseAuthority::canShowCourse($user, $course, CourseAuthority::SCOPE_BASIC)) {
            throw new AuthorizationFailedException();
        }

        return $this->getMetaResponse([
            'is-admin' => \CoreForum::isAdmin($course->id),
            'is-moderator' => \CoreForum::isModerator($course->id),
            'anonymous-post' => (bool) \Config::get()->FORUM_ANONYMOUS_POSTINGS,
            'tile-layout' => (bool) \UserConfig::get($user->user_id)->FORUM_TILE_LAYOUT
        ]);
    }
}
