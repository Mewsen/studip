<?php
namespace Studip\Lti\LTI1p3;

use Grading\Instance;
use OAT\Library\Lti1p3Ags\Model\Result\ResultInterface;
use OAT\Library\Lti1p3Ags\Model\Result\ResultCollection;
use OAT\Library\Lti1p3Ags\Repository\ResultRepositoryInterface;
use OAT\Library\Lti1p3Ags\Model\Result\ResultCollectionInterface;

final class ResultRepository implements ResultRepositoryInterface
{
    public function findCollectionByLineItemIdentifier(
        string $lineItemIdentifier,
        ?int $limit = null,
        ?int $offset = null
    ): ResultCollectionInterface
    {
        [$sqlQuery, $sqlParams]  = $this->resolveBaseSqlQuery($lineItemIdentifier);

        if ($limit !== null) {
            $sqlQuery .= ' LIMIT :limit';
            $sqlParams['limit'] = $limit;
        }
        if ($offset !== null) {
            $sqlQuery .= ' OFFSET :offset';
            $sqlParams['offset'] = $offset;
        }

        $gradeInstances = Instance::findBySQL($sqlQuery, $sqlParams);

        $results = new ResultCollection();
        foreach ($gradeInstances as $gradeInstance) {
            $results->add($gradeInstance->toLti1p3Result());
        }

        return $results;
    }

    public function findByLineItemIdentifierAndUserIdentifier(
        string $lineItemIdentifier,
        string $userIdentifier
    ): ?ResultInterface
    {
        [$sqlQuery, $sqlParams] = $this->resolveBaseSqlQuery($lineItemIdentifier);

        $sqlQuery .= ' AND grading_instances.user_id = :user_id';
        $sqlParams['user_id'] = $userIdentifier;

        return Instance::findOneBySQL($sqlQuery, $sqlParams)?->toLti1p3Result();
    }

    private function resolveBaseSqlQuery(string $lineItemIdentifier): array
    {
        $parameters = Helper::parseLineItemIdentifier($lineItemIdentifier);

        return [
            'JOIN grading_definitions ON grading_instances.definition_id = grading_definitions.id
            WHERE grading_instances.definition_id = :definition_id
            AND grading_definitions.tool = :tool_id ',
            [
                'definition_id' => $parameters['line_item_id'],
                'tool_id' => $parameters['resource_link_id']
            ]
        ];
    }
}
