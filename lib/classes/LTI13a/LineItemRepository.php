<?php

namespace Studip\LTI13a;

use Lti\ResourceLink;
use OAT\Library\Lti1p3Ags\Model\LineItem\LineItemCollection;
use OAT\Library\Lti1p3Ags\Model\LineItem\LineItemCollectionInterface;
use OAT\Library\Lti1p3Ags\Model\LineItem\LineItemInterface;
use OAT\Library\Lti1p3Ags\Repository\LineItemRepositoryInterface;
use Studip\LTIException;
use Grading\Definition;

final class LineItemRepository implements LineItemRepositoryInterface
{
    /**
     * Converts the tool-ID and deployment-ID in the tool name used in the
     * Stud.IP grading context.
     *
     * @param string $toolId The Stud.IP LTI tool ID.
     * @param string $deploymentId The Stud.IP LTI deployment ID.
     * @return string The corresponding tool name used in the Stud.IP grading context.
     */
    public static function getGradingToolName(string $toolId, string $deploymentId): string
    {
        return sprintf('lti-%s-%s', $toolId, $deploymentId);
    }

    /**
     * Converts the LTI line item identifier to search parameters to retrieve
     * Stud.IP grading definitions.
     *
     * @param string $lineItemIdentifier The LTI line item identifier.
     *
     * @return array The search parameters for searching in the Stud.IP grading context.
     *     This is an associative array with two keys:
     *         'tool'       => The identifier of the tool in the Stud.IP grading context.
     *         'course_id'  => The Stud.IP course-ID.
     *     In case the search parameters cannot be generated, an empty array is returned.
     */
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

    /**
     * @inheritDoc
     */
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
            return $definition->toLtiLineItem();
        }
        return null;
    }

    /**
     * @inheritDoc
     */
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
            $result->add($definition->toLtiLineItem());
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
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

        return $definition->toLtiLineItem();
    }

    /**
     * @inheritDoc
     */
    public function delete(string $lineItemIdentifier): void
    {
        $searchParameters = self::getSearchParametersFromLineItemIdentifier($lineItemIdentifier);

        Definition::deleteBySQL(
            "`course_id` = :course_id AND `tool` = :tool",
            $searchParameters
        );
    }
}
