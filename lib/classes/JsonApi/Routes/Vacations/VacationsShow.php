<?php

namespace JsonApi\Routes\Vacations;

use JsonApi\NonJsonApiController;
use Psr\Container\ContainerInterface;
use JsonApi\JsonApiIntegration\QueryParserInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Neomerx\JsonApi\Schema\Error;
use Neomerx\JsonApi\Schema\ErrorCollection;
use Neomerx\JsonApi\Exceptions\JsonApiException;

class VacationsShow extends NonJsonApiController
{
    protected $allowed_filtering_parameters = ['year', 'month'];

    public function __construct(
        ContainerInterface $container,
        private readonly QueryParserInterface $queryParser
    ) {
        parent::__construct($container);
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $errors = new ErrorCollection();

        $filters = $this->queryParser->getFilteringParameters();
        if (isset($filters['month']) && empty($filters['year'])) {
            $errors->add(new Error(
                'invalid-filter-value',
                title: 'The month filter cannot be used without the year filter.'
            ));
        }

        if ($errors->count() > 0) {
            throw new JsonApiException($errors, JsonApiException::HTTP_CODE_BAD_REQUEST);
        }

        if (empty($filters['year'])) {
            $filters['year'] = date('Y');
        }

        $start = new \DateTime();
        $start->setTime(0,0,0);

        // Calculate the time span:
        if (!empty($filters['month'])) {
            // For one month:
            $start->setDate($filters['year'], $filters['month'], 1);
            $end = clone $start;
            $end = $end->add(new \DateInterval('P1M'))->sub(new \DateInterval('PT1S'));
        } else {
            // For a whole year:
            $start->setDate($filters['year'], 1, 1);
            $end = clone $start;
            $end = $end->add(new \DateInterval('P1Y'))->sub(new \DateInterval('PT1S'));
        }

        $vacation_objects = \SemesterHoliday::findByTimestampRange($start->getTimestamp(), $end->getTimestamp());

        $vacations = [];
        foreach ($vacation_objects as $vacation_object) {
            $vacations[$vacation_object->id] = [
                'id'          => $vacation_object->id,
                'name'        => $vacation_object->name,
                'semester_id' => $vacation_object->semester_id,
                'description' => $vacation_object->description,
                'start'       => $vacation_object->beginn,
                'end'         => $vacation_object->ende,
                'mkdate'      => $vacation_object->mkdate,
                'chdate'      => $vacation_object->chdate
            ];
        }

        $response->getBody()->write(json_encode($vacations));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
