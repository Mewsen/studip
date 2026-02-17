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
    public static function getGradingToolName(string $toolId, string $deploymentId): string
    {
        return sprintf('lti-%s-%s', $toolId, $deploymentId);
    }

    public static function getSearchParametersFromLineItemIdentifier(string $lineItemIdentifier): array
    {
        //$lineItemIdentifier contains the full URL to the line item.
        //We must extract the course-ID, tool-ID and deployment-ID
        //from the URL parameters first, before searching a grading definition.
        $urlParts = parse_url($lineItemIdentifier);
        $parameters = [];
        if (empty($urlParts['query'])) {
            //Nothing we can convert.
            return [];
        }
        parse_str($urlParts['query'], $parameters);
        if (empty($parameters)) {
            //Same as above.
            return [];
        }

        $searchParameters = [
            'course_id' => $parameters['cid'],
            'tool' => self::getGradingToolName($parameters['tool_id'], $parameters['deployment_id'])
        ];
        if (!empty($parameters['definition_id'])) {
            $searchParameters['definition_id'] = $parameters['definition_id'];
        }

        return $searchParameters;
    }

    public function find(string $lineItemIdentifier): ?LineItemInterface
    {
        $searchParameters = self::getSearchParametersFromLineItemIdentifier($lineItemIdentifier);
        if (!$searchParameters) {
            //Nothing we can search for.
            return null;
        }

        $definition = null;
        if (!empty($searchParameters['definition_id'])) {
            $definition = Definition::find($searchParameters['definition_id']);
        } else {
            $definition = Definition::findOneBySQL(
                "`course_id` = :course_id AND `tool` = :tool",
                [
                    'course_id' => $searchParameters['course_id'],
                    'tool' => $searchParameters['tool']
                ]
            );
        }
        if ($definition) {
            return $definition->toLti1p3LineItem();
        }
        return null;
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
            //Nothing we can search for.
            return $result;
        }

        //Find the LTI resource link by its ID:
        $resourceLink = ResourceLink::find($resourceLinkIdentifier);
        if (!$resourceLink) {
            throw new LTIException('Invalid resource link identifier.');
        }
        $toolId = $resourceLink->deployment->tool_id ?? null;

        $sqlQuery = ['', []];
        if ($toolId && $resourceLink->course_id) {
            $sqlQuery[0] .= "`tool` = :tool AND `course_id` = :course_id";
            $sqlQuery[1]['tool'] = self::getGradingToolName($toolId, $resourceLink->deployment_id);
            $sqlQuery[1]['course_id'] = $resourceLink->course_id;
        } else {
            //No tool-ID means no line item collection can be found.
            return $result;
        }

        if ($limit) {
            if (empty($sqlQuery[0])) {
                $sqlQuery[0] .= "TRUE ";
            }
            $sqlQuery[0] .= "LIMIT :limit ";
            $sqlQuery[1]['limit'] = $limit;
        }
        if ($offset) {
            $sqlQuery[0] .= "OFFSET :offset";
            $sqlQuery[1]['offset'] = $offset;
        }
        $definitions = Definition::findBySql(...$sqlQuery);

        foreach ($definitions as $definition) {
            $result->add($definition->toLti1p3LineItem());
        }
        return $result;
    }

    public function save(LineItemInterface $lineItem): LineItemInterface
    {
        $resourceLink = ResourceLink::find($lineItem->getResourceLinkIdentifier());
        if (!$resourceLink) {
            throw new LTIException('Invalid resource link identifier.');
        }

        $definition = Definition::create([
            'id' => $lineItem->getIdentifier(),
            'name' => $lineItem->getLabel(),
            'course_id' => $resourceLink->course_id,
            'tool' => $resourceLink->id,
            'weight' => '1.0'
        ]);

        return $definition->toLti1p3LineItem();
    }

    public function delete(string $lineItemIdentifier): void
    {
        $searchParameters = self::getSearchParametersFromLineItemIdentifier($lineItemIdentifier);

        Definition::deleteBySQL(
            "`course_id` = :course_id AND `tool` = :tool",
            $searchParameters
        );
    }
}
