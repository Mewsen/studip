<?php
namespace JsonApi\Routes\Resources;

use JsonApi\ComplexFilter;
use JsonApi\Schemas\ResourceBookingIntervalSchema;
use JsonApi\Errors\BadRequestException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Neomerx\JsonApi\Contracts\Http\Query\BaseQueryParserInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Neomerx\JsonApi\Schema\ErrorCollection;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Resource;
use ResourceBooking;
use ResourceBookingInterval;
use Slim\Routing\RouteContext;

final class ResourceBookingIntervalIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedIncludePaths = [
        ResourceBookingIntervalSchema::REL_BOOKING,
        ResourceBookingIntervalSchema::REL_RESOURCE,
    ];
    protected $allowedSortFields = [
        'begin',
        'end',
        'mkdate',
    ];
    protected $allowedFilteringParameters = [
        'begin',
        'booking-id',
        'end',
        'takes-place',
        'resource-id',
    ];
    protected $allowedFieldSetTypes = [
        ResourceBookingIntervalSchema::TYPE => ['begin', 'end', 'takes-place', 'mkdate', 'chdate'],
    ];

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $filters = $this->getFilters();
        $order = $this->getOrder();
        [$offset, $limit] = $this->getOffsetAndLimit();

        $routeName = RouteContext::fromRequest($request)->getRoute()->getName();
        if ($routeName === 'intervals-of-booking') {
            if (isset($filters['booking-id'])) {
                throw new BadRequestException('You may not use the booking-id filter for this route.');
            }

            if (!ResourceBooking::exists($args['id'])) {
                throw new RecordNotFoundException("No resource booking found with id {$args['id']}.");
            }

            $filters['booking-id'] = $args['id'];
        } elseif ($routeName === 'intervals-of-resource') {
            if (isset($filters['resource-id'])) {
                throw new BadRequestException('You may not use the resource-id filter for this route.');
            }

            if (!Resource::exists($args['id'])) {
                throw new RecordNotFoundException("No resource found with id {$args['id']}.");
            }

            $filters['resource-id'] = $args['id'];
        }

        [$condition, $parameters] = $this->getConditionAndParameters($filters);

        $total = ResourceBookingInterval::countBySql($condition, $parameters);
        $bookings = ResourceBookingInterval::findBySQL(
            "{$condition} {$order} LIMIT {$offset}, {$limit}",
            $parameters
        );

        return $this->getPaginatedContentResponse($bookings, $total);
    }

    private function getFilters(): array
    {
        $filters = iterator_to_array($this->getQueryParameters()->getFilters());
        $errors = new ErrorCollection();

        if (array_key_exists('begin', $filters)) {
            if (ComplexFilter::detect($filters['begin'])) {
                $filters['begin'] = ComplexFilter::create($filters['begin']);
            } elseif (!is_numeric($filters['begin'])) {
                $errors->addQueryParameterError(
                    BaseQueryParserInterface::PARAM_FILTER,
                    'Filter begin must be numeric.'
                );
            } else {
                $filters['begin'] = (int)$filters['begin'];
            }
        }

        if (array_key_exists('booking-id', $filters)) {
            if (!ResourceBooking::exists($filters['booking-id'])) {
                $errors->addQueryParameterError(
                    BaseQueryParserInterface::PARAM_FILTER,
                    sprintf(
                        'Filter booking-id links to an unknown resource booking with id %s.',
                        $filters['booking-id']
                    )
                );
            }
        }

        if (array_key_exists('end', $filters)) {
            if (ComplexFilter::detect($filters['end'])) {
                $filters['end'] = ComplexFilter::create($filters['end']);
            } elseif (!is_numeric($filters['end'])) {
                $errors->addQueryParameterError(
                    BaseQueryParserInterface::PARAM_FILTER,
                    'Filter end must be numeric.'
                );
            } else {
                $filters['end'] = (int)$filters['end'];
            }
        }

        if (array_key_exists('takes-place', $filters)) {
            $filters['takes-place'] = (bool) $filters['takes-place'];
        }

        if (array_key_exists('resource-id', $filters)) {
            if (!Resource::exists($filters['resource-id'])) {
                $errors->addQueryParameterError(
                    BaseQueryParserInterface::PARAM_FILTER,
                    sprintf(
                        'Filter resource-id links to an unknown resource with id %s.',
                        $filters['resource-id']
                    )
                );
            }
        }

        if (count($errors) > 0) {
            throw new JsonApiException($errors, JsonApiException::HTTP_CODE_BAD_REQUEST);
        }

        return $filters;
    }

    private function getOrder(): string
    {
        $result = [];
        foreach ($this->getQueryParameters()->getSorts() as $column => $ascending) {
            if ($ascending) {
                $result[] = $column;
            } else {
                $result[] = "{$column} DESC";
            }
        }

        return count($result) > 0 ? 'ORDER BY ' . implode(', ', $result) : '';
    }

    private function getConditionAndParameters(array $filters): array
    {
        $conditions = [];
        $joins = [];
        $parameters = [];

        if (array_key_exists('begin', $filters)) {
            if ($filters['begin'] instanceof ComplexFilter) {
                $filters['begin']->apply($conditions, $parameters, 'begin');
            } else {
                $conditions[] = 'begin = :begin';
                $parameters[':begin'] = $filters['begin'];
            }
        }

        if (array_key_exists('booking-id', $filters)) {
            $conditions[] = 'booking_id = :booking_id';
            $parameters[':booking_id'] = $filters['booking-id'];
        }

        if (array_key_exists('end', $filters)) {
            if ($filters['end'] instanceof ComplexFilter) {
                $filters['end']->apply($conditions, $parameters, 'end');
            } else {
                $conditions[] = 'end = :end';
                $parameters[':end'] = $filters['end'];
            }
        }

        if (array_key_exists('takes-place', $filters)) {
            $conditions[] = 'takes_place = :takes_place';
            $parameters[':takes_place'] = (int) $filters['takes-place'];
        }

        if (array_key_exists('resource-id', $filters)) {
            $conditions[] = 'resource_id = :resource_id';
            $parameters[':resource_id'] = $filters['resource-id'];
        }

        $condition = implode(' ', $joins);
        if ($condition) {
            $condition .= ' WHERE ';
        }
        $condition .= '(' . implode(') AND (', $conditions ?: [1]) . ')';

        return [$condition, $parameters];
    }
}
