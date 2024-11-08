<?php

namespace JsonApi\Routes;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;

/**
 * List all the semesters.
 */
class SemestersIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];

    protected $allowedFilteringParameters = ['current', 'timestamp'];

    public function __invoke(Request $request, Response $response, $args)
    {
        list($offset, $limit) = $this->getOffsetAndLimit();

        $filtering = $this->getQueryParameters()->getFilteringParameters();

        if (empty($filtering)) {
            $semesters = \Semester::getAll();
        } else {
            if (array_key_exists('current', $filtering)) {
                $semester = \Semester::findCurrent();
            }
            if (isset($filtering['timestamp'])) {
                $semester = \Semester::findByTimestamp($filtering['timestamp']);
            }

            if (!$semester) {
                throw new RecordNotFoundException('Could not find semester.');
            } else {
                $semesters = [$semester];
            }
        }

        return $this->getPaginatedContentResponse(
            array_slice($semesters, $offset, $limit),
            count($semesters)
        );
    }
}
