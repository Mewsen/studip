<?php
namespace Studip\Lti\LTI1p3;

use Lti\ResourceLink;
use Grading\Definition;
use Studip\LTIException;
use OAT\Library\Lti1p3Ags\Model\LineItem\LineItemInterface;
use OAT\Library\Lti1p3Ags\Model\LineItem\LineItemCollection;
use OAT\Library\Lti1p3Ags\Repository\LineItemRepositoryInterface;
use OAT\Library\Lti1p3Ags\Model\LineItem\LineItemCollectionInterface;

final class LineItemRepository implements LineItemRepositoryInterface
{
    public function find(string $lineItemIdentifier): ?LineItemInterface
    {
        $parameters = Helper::parseLineItemIdentifier($lineItemIdentifier);

        return Definition::find($parameters['line_item_id'])?->toLti1p3LineItem();
    }

    public function findCollection(
        ?string $resourceIdentifier = null,
        ?string $resourceLinkIdentifier = null,
        ?string $tag = null,
        ?int $limit = null,
        ?int $offset = null
    ): LineItemCollectionInterface
    {
        $result = new LineItemCollection();

        if (!$resourceLinkIdentifier) {
            return $result;
        }

        $sqlQuery = 'id = :id OR tool = :tool_id ORDER BY mkdate DESC';
        $sqlParams = [
            'id' => $resourceIdentifier,
            'tool_id' => $resourceLinkIdentifier
        ];

        if ($limit !== null) {
            $sqlQuery .= ' LIMIT :limit';
            $sqlParams['limit'] = $limit;
        }

        if ($offset !== null) {
            $sqlQuery .= ' OFFSET :offset';
            $sqlParams['offset'] = $offset;
        }

        $gradeDefinitions = Definition::findBySql($sqlQuery, $sqlParams);

        foreach ($gradeDefinitions as $gradeDefinition) {
            $result->add($gradeDefinition->toLti1p3LineItem());
        }
        return $result;
    }

    public function save(LineItemInterface $lineItem): LineItemInterface
    {
        $resourceLink = ResourceLink::find($lineItem->getResourceLinkIdentifier());
        if ($resourceLink === null) {
            throw new LTIException('Invalid resource link identifier.');
        }

        // TODO:: normalize weight
        $gradeDefinition = Definition::updateOrCreate(
            [
                'id' => $lineItem->getResourceIdentifier(),
                'tool' => $resourceLink->id,
                'course_id' => $resourceLink->course_id,
                'category' => 'LTI',
                'item' => ResourceLink::class
            ],
            [
                'name' => $lineItem->getLabel(),
                'weight' => $lineItem->getScoreMaximum() / 100
            ]
        );

        return $gradeDefinition->toLti1p3LineItem();
    }

    public function delete(string $lineItemIdentifier): void
    {
        $parameters = Helper::parseLineItemIdentifier($lineItemIdentifier);

        Definition::deleteBySQL(
            "id = ?",
            [$parameters['line_item_id']]
        );
    }
}
