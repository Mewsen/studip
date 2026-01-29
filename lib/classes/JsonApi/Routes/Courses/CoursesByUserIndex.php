<?php

namespace JsonApi\Routes\Courses;

use Course;
use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Errors\BadRequestException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Semester;
use User;

class CoursesByUserIndex extends JsonApiController
{
    protected $allowedIncludePaths = [
        'blubber-threads',
        'end-semester',
        'events',
        'feedback-elements',
        'file-refs',
        'folders',
        'forum-categories',
        'institute',
        'memberships',
        'news',
        'participating-institutes',
        'sem-class',
        'sem-type',
        'start-semester',
        'status-groups',
        'wiki-pages',
    ];

    protected $allowedPagingParameters = ['offset', 'limit'];

    protected $allowedFilteringParameters = ['permission', 'semester'];

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $user = User::find($args['id']);
        if (!$user) {
            throw new RecordNotFoundException();
        }

        if (!Authority::canIndexMembershipsOfUser($this->getUser($request), $user)) {
            throw new AuthorizationFailedException();
        }

        $error = $this->validateFilters();
        if ($error) {
            throw new BadRequestException($error);
        }

        $courses = $this->findCoursesByUser(
            $user,
            $this->getSemesterFilter(),
            $this->getPermissionFilter()
        );
        [$offset, $limit] = $this->getOffsetAndLimit();

        return $this->getPaginatedContentResponse(
            array_slice($courses, $offset, $limit),
            count($courses)
        );
    }

    private function validateFilters()
    {
        $filtering = $this->getQueryParameters()->getFilteringParameters() ?: [];

        // semester
        if (
            !empty($filtering['semester'])
            && !Semester::exists($filtering['semester'])
        ) {
            return 'Invalid "semester".';
        }

        if (
            !empty($filtering['permission'])
            && !in_array($filtering['permission'], ['user', 'autor', 'tutor', 'dozent'])
        ) {
            return 'Invalid "permission".';
        }
    }

    private function getSemesterFilter(): ?Semester
    {
        $filtering = $this->getQueryParameters()->getFilteringParameters();

        if (!isset($filtering['semester'])) {
            return null;
        }

        return Semester::find($filtering['semester']);
    }

    private function getPermissionFilter(): ?string
    {
        $filtering = $this->getQueryParameters()->getFilteringParameters();

        return $filtering['permission'] ?? null;
    }


    /**
     * @param User $user
     * @param Semester|null $semester
     *
     * @return Course[]
     */
    private function findCoursesByUser(User $user, ?Semester $semester, ?string $permission): array
    {
        $memberships = $user->course_memberships;
        if ($permission) {
            $memberships = $memberships->filter(function (\CourseMember $membership) use ($permission): bool {
                return $membership->status === $permission;
            });
        }

        $courses = Course::findBySQL(
            'LEFT JOIN `semester_courses`
            ON `seminare`.`seminar_id` = `semester_courses`.`course_id`
            LEFT JOIN `semester_data` USING (`semester_id`)
            WHERE
            `seminare`.`seminar_id` IN ( :course_ids )
            ORDER BY `semester_data`.`beginn`, `seminare`.`name`',
            [':course_ids' => $memberships->pluck('seminar_id')]
        );

        if ($semester) {
            $courses = array_filter($courses, function (Course $course) use ($semester): bool {
                return $course->isInSemester($semester);
            });
        }

        return $courses;
    }
}
