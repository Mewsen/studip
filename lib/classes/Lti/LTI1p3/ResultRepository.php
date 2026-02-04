<?php
namespace Studip\Lti\LTI1p3;

use Grading\Instance;
use OAT\Library\Lti1p3Ags\Model\Result\ResultCollection;
use OAT\Library\Lti1p3Ags\Model\Result\ResultInterface;
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
        $sqlParams = LineItemRepository::getSearchParametersFromLineItemIdentifier($lineItemIdentifier);
        if (!$sqlParams) {
            //Nothing we can search for:
            return new ResultCollection();
        }
        $sql = 'JOIN `grading_definitions` gd
               ON (`definition_id` = gd.`id`)
               WHERE gd.`course_id` = :course_id
               AND gd.`tool` = :tool';
        if ($limit) {
            $sql .= 'LIMIT :limit ';
            $sqlParams['limit'] = $limit;
        }
        if ($offset) {
            $sql .= 'OFFSET :offset ';
            $sqlParams['offset'] = $offset;
        }

        $grades = Instance::findBySQL($sql, $sqlParams);
        $results = new ResultCollection();
        foreach ($grades as $grade) {
            $results->add($grade->toResult());
        }
        return $results;
    }

    public function findByLineItemIdentifierAndUserIdentifier(
        string $lineItemIdentifier,
        string $userIdentifier
    ): ?ResultInterface
    {
        $searchParameters = LineItemRepository::getSearchParametersFromLineItemIdentifier($lineItemIdentifier);

        return Instance::findOneBySQL(
            'JOIN `grading_definitions` gd
               ON (`definition_id` = gd.`id`)
               WHERE gd.`course_id` = :course_id
               AND gd.`tool` = :tool
               AND `user_id` = :user_id',
            [
                ...$searchParameters,
                'user_id' => $userIdentifier
            ]
        )?->toResult();
    }
}
