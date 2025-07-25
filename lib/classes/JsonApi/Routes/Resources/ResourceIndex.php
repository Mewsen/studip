<?php
namespace JsonApi\Routes\Resources;

use JsonApi\Schemas\ResourceSchema;
use JsonApi\Errors\BadRequestException;
use JsonApi\JsonApiController;
use Psr\Http\Message\{
    RequestInterface as Request,
    ResponseInterface as Response
};

final class ResourceIndex extends JsonApiController
{
    protected $allowedFilteringParameters = ['level', 'class'];
    protected $allowedIncludePaths = [ResourceSchema::REL_CATEGORY];
    protected $allowedPagingParameters = ['offset', 'limit'];

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        [$offset, $limit] = $this->getOffsetAndLimit();
        [$condition, $parameters] = $this->getConditionAndParameters(
            $this->getFilters()
        );

        $total = \Resource::countBySql($condition, $parameters);
        $resources = \Resource::findBySQL(
            "{$condition} LIMIT {$offset}, {$limit}",
            $parameters
        );

        return $this->getPaginatedContentResponse($resources, $total);
    }

    private function getFilters()
    {
        $filters = [];

        $filtering = $this->getQueryParameters()->getFilteringParameters() ?? [];

        if (array_key_exists('level', $filtering)) {
            if (!ctype_digit($filtering['level'])) {
                throw new BadRequestException('Level filter must be an int.');
            }

            $filters['level'] = (int) $filtering['level'];
        }

        if (array_key_exists('class', $filtering)) {
            if (empty($filtering['class'])) {
                throw new BadRequestException('Class filter must be not be empty.');
            }

            $filters['class'] = $filtering['class'];
        }

        return $filters;
    }

    private function getConditionAndParameters(array $filters): array
    {
        $joins = [];
        $conditions = [];
        $parameters = [];

        if (array_key_exists('level', $filters)) {
            $conditions[] = '`resources`.`level` = :level';
            $parameters[':level'] = $filters['level'];
        }

        if (array_key_exists('class', $filters)) {
            $joins[] = 'JOIN `resource_categories`
                        ON `resources`.`category_id` = `resource_categories`.`id`';
            $conditions[] = '`resource_categories`.`class_name` = :class';
            $parameters[':class'] = $filters['class'];
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
