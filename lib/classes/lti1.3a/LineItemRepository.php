<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Ags\Model\LineItem\LineItemCollection;
use OAT\Library\Lti1p3Ags\Model\LineItem\LineItemCollectionInterface;
use OAT\Library\Lti1p3Ags\Model\LineItem\LineItemInterface;
use OAT\Library\Lti1p3Ags\Repository\LineItemRepositoryInterface;

class LineItemRepository implements LineItemRepositoryInterface
{

    public function find(string $lineItemIdentifier): ?LineItemInterface
    {
        $definition = \Grading\Definition::find($lineItemIdentifier);
        if ($definition) {
            return $definition->toLineItem();
        }
        return null;
    }

    public function findCollection(?string $resourceIdentifier = null, ?string $resourceLinkIdentifier = null, ?string $tag = null, ?int $limit = null, ?int $offset = null): LineItemCollectionInterface
    {
        $sql = '';
        $sql_params = [];
        if ($resourceIdentifier) {
            $sql .= "`course_id` = :course_id ";
            $sql_params['course_id'] = $resourceIdentifier;
        }

        //TODO: resourceLinkIdentifier, tag

        if ($limit) {
            if (empty($sql)) {
                $sql .= "TRUE ";
            }
            $sql .= "LIMIT :limit ";
            $sql_params['limit'] = $limit;
        }
        if ($offset) {
            $sql .= "OFFSET :offset";
            $sql_params['offset'] = $offset;
        }
        $definitions = \Grading\Definition::findBySql($sql, $sql_params);
        $result = new LineItemCollection();
        foreach ($definitions as $definition) {
            $result->add($definition->toLineItem());
        }
        return $result;
    }

    public function save(LineItemInterface $lineItem): LineItemInterface
    {
        \Grading\Definition::createFromLineItem($lineItem);
        return $lineItem;
    }

    public function delete(string $lineItemIdentifier): void
    {
        $definition = \Grading\Definition::find($lineItemIdentifier);
        if ($definition) {
            $definition->delete();
        }
    }
}
