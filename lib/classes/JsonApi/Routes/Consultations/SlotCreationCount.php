<?php
namespace JsonApi\Routes\Consultations;

use ConsultationBlock;
use JsonApi\Errors\BadRequestException;
use JsonApi\NonJsonApiController;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Neomerx\JsonApi\Schema\ErrorCollection;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class SlotCreationCount extends NonJsonApiController
{
    public function __invoke(Request $request, Response $response, array $args)
    {
        $parameters = $request->getQueryParams();

        $this->validateParameters($parameters);

        // Determine duration of a slot and pause times
        $slot_count = ConsultationBlock::countSlots(
            strtotime($parameters['start']),
            strtotime($parameters['end']),
            $parameters['dow'],
            $parameters['interval'],
            $parameters['duration'],
            $parameters['pause_time'] ?? null,
            $parameters['pause_duration'] ?? null
        );

        $response->getBody()->write((string) $slot_count);
        return $response->withAddedHeader('Content-Type', 'application/json');
    }

    private function validateParameters(array $parameters): void
    {
        $collection = new ErrorCollection();

        foreach (['start', 'end', 'dow', 'interval', 'duration'] as $key) {
            if (!isset($parameters[$key])) {
                $collection->addQueryParameterError($key, 'Parameter is missing');
            }
        }

        if (isset($parameters['start'], $parameters['end'])) {
            $start = strtotime($parameters['start']);
            $end = strtotime($parameters['end']);

            if (!$start) {
                $collection->addQueryParameterError('start', 'Parameter has invalid datetime format');
            }

            if (!$end) {
                $collection->addQueryParameterError('end', 'Parameter has invalid datetime format');
            }

            if ($start && $end && $start > $end) {
                $collection->addQueryParameterError('start', 'Datetime value of start must be before end');
            }
        }

        if (
            isset($parameters['dow'])
            && (
                !ctype_digit($parameters['dow'])
                || $parameters['dow'] < 0
                || $parameters['dow'] > 6
            )
        ) {
            $collection->addQueryParameterError('dow', 'Parameter must be a number between 0 and 6');
        }

        if (
            isset($parameters['interval'])
            && (
                !ctype_digit($parameters['interval'])
                || $parameters['interval'] < 0
                || $parameters['interval'] > 4
            )
        ) {
            $collection->addQueryParameterError('interval', 'Parameter must be a number between 0 and 4');
        }

        if (
            isset($parameters['duration'])
            && (
                !ctype_digit($parameters['duration'])
                || $parameters['duration'] <= 0
            )
        ) {
            $collection->addQueryParameterError('duration', 'Parameter must be a positive number');
        }

        if (
            isset($parameters['pause_time'], $parameters['duration'])
            && $parameters['pause_time'] < $parameters['duration']
        ) {
            $collection->addQueryParameterError('pause_time', 'The defined time to a pause is shorter than the duration of a slot.');
        }

        if (count($collection) > 0) {
            throw new JsonApiException($collection);
        }
    }
}
