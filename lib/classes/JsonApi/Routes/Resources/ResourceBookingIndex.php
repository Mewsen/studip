<?php
namespace JsonApi\Routes\Resources;

use CourseDate;
use JsonApi\ComplexFilter;
use JsonApi\Schemas\ResourceBookingSchema;
use JsonApi\Errors\BadRequestException;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\JsonApiController;
use Neomerx\JsonApi\Contracts\Http\Query\BaseQueryParserInterface;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Neomerx\JsonApi\Schema\ErrorCollection;
use Psr\Http\Message\{
    RequestInterface as Request,
    ResponseInterface as Response
};
use Resource;
use ResourceBooking;
use Slim\Routing\RouteContext;
use User;

final class ResourceBookingIndex extends JsonApiController
{
    protected $allowedPagingParameters = ['offset', 'limit'];
    protected $allowedIncludePaths = [
        ResourceBookingSchema::REL_INTERVALS,
        ResourceBookingSchema::REL_RESOURCE,
    ];
    protected $allowedSortFields = [
        'begin',
        'end',
        'mkdate',
    ];
    protected $allowedFilteringParameters = [
        'assigned-course-date-id',
        'assigned-user-id',
        'begin',
        'booking-type',
        'booking-user-id',
        'end',
        'range-id',
        'resource-id',
    ];

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $filters = $this->getFilters();
        $order = $this->getOrder();
        [$offset, $limit] = $this->getOffsetAndLimit();

        $routeName = RouteContext::fromRequest($request)->getRoute()->getName();
        if ($routeName === 'bookings-of-resource') {
            if (isset($filters['resource-id'])) {
                throw new BadRequestException('You may not use the resource-id filter for this route.');
            }

            if (!Resource::exists($args['id'])) {
                throw new RecordNotFoundException("No resource found with id {$args['id']}.");
            }

            $filters['resource-id'] = $args['id'];
        }

        [$condition, $parameters] = $this->getConditionAndParameters($filters);

        $total = ResourceBooking::countBySql($condition, $parameters);
        $bookings = ResourceBooking::findBySQL(
            "{$condition} {$order} LIMIT {$offset}, {$limit}",
            $parameters
        );

        return $this->getPaginatedContentResponse($bookings, $total);
    }

    private function getFilters(): array
    {
        $filters = iterator_to_array($this->getQueryParameters()->getFilters());
        $errors = new ErrorCollection();

        if (array_key_exists('assigned-course-date-id', $filters)) {
            if (!CourseDate::exists($filters['assigned-course-date-id'])) {
                $errors->addQueryParameterError(
                    BaseQueryParserInterface::PARAM_FILTER,
                    sprintf(
                        'Filter assigned-course-date-id links to an unknown course date with id %s.',
                        $filters['assigned-course-date-id']
                    )
                );
            }
        }

        if (array_key_exists('assigned-user-id', $filters)) {
            if (!User::exists($filters['assigned-user-id'])) {
                $errors->addQueryParameterError(
                    BaseQueryParserInterface::PARAM_FILTER,
                    sprintf(
                        'Filter assigned-user-id links to an unknown user with id %s.',
                        $filters['assigned-user-id']
                    )
                );
            }
        }

        if (array_key_exists('begin', $filters)) {
            if (ComplexFilter::detect($filters['begin'])) {
                $filters['begin'] = ComplexFilter::create($filters['begin']);
            } elseif (!is_numeric($filters['begin'])) {
                $errors->addQueryParameterError(
                    BaseQueryParserInterface::PARAM_FILTER,
                    'Filter begin must be numeric.'
                );
            } else {
                $filters['begin'] = (int) $filters['begin'];
            }
        }

        if (array_key_exists('booking-type', $filters)) {
            if (!is_numeric($filters['booking-type'])) {
                $errors->addQueryParameterError(
                    BaseQueryParserInterface::PARAM_FILTER,
                    'Filter booking-type must be numeric.'
                );
            } else {
                $filters['booking-type'] = (int) $filters['booking-type'];
            }
        }

        if (array_key_exists('booking-user-id', $filters)) {
            if (!User::exists($filters['booking-user-id'])) {
                $errors->addQueryParameterError(
                    BaseQueryParserInterface::PARAM_FILTER,
                    sprintf(
                        'Filter booking-user-id links to an unknown user with id %s.',
                        $filters['booking-user-id']
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
                $filters['end'] = (int) $filters['end'];
            }
        }

        if (array_key_exists('range-id', $filters)) {
            if (
                !CourseDate::exists($filters['range-id'])
                && !User::exists($filters['range-id'])
            ) {
                $errors->addQueryParameterError(
                    BaseQueryParserInterface::PARAM_FILTER,
                    sprintf(
                        'Filter range-id links to an unknown course date or user with id %s.',
                        $filters['range-id']
                    )
                );
            }
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

        return count($result)  > 0 ? 'ORDER BY ' . implode(', ', $result) : '';
    }

    private function getConditionAndParameters(array $filters): array
    {
        $conditions = [];
        $joins = [];
        $parameters = [];

        if (array_key_exists('assigned-course-date-id', $filters)) {
            $conditions[] = 'range_id = :assigned_course_date_id';
            $parameters[':assigned_course_date_id'] = $filters['assigned-course-date-id'];
        }

        if (array_key_exists('assigned-user-id', $filters)) {
            $conditions[] = 'range_id = :assigned_user_id';
            $parameters[':assigned_user_id'] = $filters['assigned-user-id'];
        }

        if (array_key_exists('begin', $filters)) {
            if ($filters['begin'] instanceof ComplexFilter) {
                $filters['begin']->apply($conditions, $parameters, 'begin');
            } else {
                $conditions[] = 'begin = :begin';
                $parameters[':begin'] = $filters['begin'];
            }
        }
        
        if (array_key_exists('booking-type', $filters)) {
            $conditions[] = 'booking_type = :booking_type';
            $parameters[':booking_type'] = $filters['booking-type'];
        }

        if (array_key_exists('booking-user-id', $filters)) {
            $conditions[] = 'booking_user_id = :booking_user_id';
            $parameters[':booking_user_id'] = $filters['booking-user-id'];
        }

        if (array_key_exists('end', $filters)) {
            if ($filters['end'] instanceof ComplexFilter) {
                $filters['end']->apply($conditions, $parameters, 'end');
            } else {
                $conditions[] = 'end = :end';
                $parameters[':end'] = $filters['end'];
            }
        }

        if (array_key_exists('range-id', $filters)) {
            $conditions[] = 'range_id = :range_id';
            $parameters[':range_id'] = $filters['range_id'];
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


    private function decomposeFilter(string $key, $value)
    {

    }
}
