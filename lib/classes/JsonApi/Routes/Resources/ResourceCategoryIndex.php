<?php
namespace JsonApi\Routes\Resources;

use JsonApi\Errors\BadRequestException;
use JsonApi\JsonApiController;
use Psr\Http\Message\{
    RequestInterface as Request,
    ResponseInterface as Response
};

final class ResourceCategoryIndex extends JsonApiController
{
    protected $allowedFilteringParameters = ['class_name', 'system'];
    protected $allowedPagingParameters = ['offset', 'limit'];

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        [$offset, $limit] = $this->getOffsetAndLimit();
        [$condition, $parameters] = $this->getConditionAndParameters(
            $this->getFilters()
        );

        $total = \ResourceCategory::countBySql($condition, $parameters);
        $resources = \ResourceCategory::findBySQL(
            "{$condition} LIMIT {$offset}, {$limit}",
            $parameters
        );

        return $this->getPaginatedContentResponse($resources, $total);
    }

    private function getFilters()
    {
        $filters = [];

        $filtering = $this->getQueryParameters()->getFilteringParameters() ?? [];

        if (array_key_exists('class_name', $filtering)) {
            if (empty($filtering['class_name'])) {
                throw new BadRequestException('Class name filter must be not be empty.');
            }

            $filters['class_name'] = $filtering['class_name'];
        }

        if (array_key_exists('system', $filtering)) {
            $filters['system'] = (bool) $filtering['system'];
        }

        return $filters;
    }

    private function getConditionAndParameters(array $filters): array
    {
        $joins = [];
        $conditions = [];
        $parameters = [];

        if (array_key_exists('class_name', $filters)) {
            $conditions[] = '`class_name` = :class';
            $parameters[':class'] = $filters['class'];
        }

        if (array_key_exists('system', $filters)) {
            $conditions[] = '`system` = :system';
            $parameters[':system'] = (int) $filters['system'];
        }

        // Build condition
        $condition = implode(' ', $joins);
        if ($condition) {
            $condition .= ' WHERE ';
        }

        if (count($conditions) === 0) {
            $conditions[] = '1';
        }

        $condition .= implode(' AND ', array_map(
            function ($condition): string {
                return "({$condition})";
            },
            $conditions
        ));

        return [
            $condition,
            $parameters,
        ];
    }
}
