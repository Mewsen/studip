<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Ags\Model\Result\Result;
use OAT\Library\Lti1p3Ags\Model\Result\ResultCollection;
use OAT\Library\Lti1p3Ags\Model\Result\ResultCollectionInterface;
use OAT\Library\Lti1p3Ags\Model\Result\ResultInterface;
use OAT\Library\Lti1p3Ags\Repository\ResultRepositoryInterface;

class ResultRepository implements ResultRepositoryInterface
{
    public function findCollectionByLineItemIdentifier(
        string $lineItemIdentifier,
        ?int $limit = null,
        ?int $offset = null
    ) : ResultCollectionInterface {
        $sql_params = LineItemRepository::getSearchParametersFromLineItemIdentifier($lineItemIdentifier);
        if (!$sql_params) {
            //Nothing we can search for:
            return new ResultCollection();
        }
        $sql = 'JOIN `grading_definitions` gd
               ON (`definition_id` = gd.`id`)
               WHERE gd.`course_id` = :course_id
               AND gd.`tool` = :tool';
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

    public function findByLineItemIdentifierAndUserIdentifier(
        string $lineItemIdentifier,
        string $userIdentifier
    ) : ?ResultInterface {
        $search_parameters = LineItemRepository::getSearchParametersFromLineItemIdentifier($lineItemIdentifier);
        $search_parameters['user_id'] = $userIdentifier;

        $grade = \Grading\Instance::findOneBySQL(
            'JOIN `grading_definitions` gd
               ON (`definition_id` = gd.`id`)
               WHERE gd.`course_id` = :course_id
               AND gd.`tool` = :tool
               AND `user_id` = :user_id',
            $search_parameters
        );
        if ($grade) {
            return $grade->toResult();
        }
        return null;
    }
}
