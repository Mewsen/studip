<?php

namespace JsonApi\Routes\Admission;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;

/**
 * Zeigt alle Veranstaltungen an, die keinem Anmeldeset zugeordnet sind.
 */
class AvailableCoursesIndex extends JsonApiController
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $body = $request->getParsedBody();

        $semester = \Semester::find($body['semester']);

        if (!$semester) {
            throw new RecordNotFoundException();
        }

        $courses = \CoursesetModel::getInstCourses(
            $body['institutes'],
            $body['courseset'],
            $body['exclude'],
            $semester->id,
            $body['filter']
        );

        $courses = count($courses) > 0
            ? \Course::findMany(array_keys($courses), "ORDER BY `VeranstaltungsNummer`, `Name`")
            : [];

        return $this->getContentResponse($courses);
    }
}
