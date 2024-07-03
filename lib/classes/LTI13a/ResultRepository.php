<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Ags\Model\Result\Result;
use OAT\Library\Lti1p3Ags\Model\Result\ResultCollection;
use OAT\Library\Lti1p3Ags\Model\Result\ResultCollectionInterface;
use OAT\Library\Lti1p3Ags\Model\Result\ResultInterface;
use OAT\Library\Lti1p3Ags\Repository\ResultRepositoryInterface;

class ResultRepository implements ResultRepositoryInterface
{

    public function findCollectionByLineItemIdentifier(string $lineItemIdentifier, ?int $limit = null, ?int $offset = null): ResultCollectionInterface
    {
        $sql = '`definition_id` = :definition_id ';
        $sql_params = ['definition_id' => $lineItemIdentifier];
        if ($limit) {
            $sql .= 'LIMIT :limit ';
            $sql_params['limit'] = $limit;
        }
        if ($offset) {
            $sql .= 'OFFSET :offset ';
            $sql_params['offset'] = $offset;
        }

        $grades = \Grading\Instance::findBySQL($sql, $sql_params);
        $results = new ResultCollection();
        foreach ($grades as $grade) {
            $results->add($grade->toResult());
        }
        return $results;
    }

    public function findByLineItemIdentifierAndUserIdentifier(string $lineItemIdentifier, string $userIdentifier): ?ResultInterface
    {
        $grade = \Grading\Instance::findOneBySQL(
            '`definition_id` = :definition_id` AND `user_id` = :user_id',
            ['definition_id' => $lineItemIdentifier, 'user_id' => $userIdentifier]
        );
        if ($grade) {
            return $grade->toResult();
        }
        return null;
    }
}
