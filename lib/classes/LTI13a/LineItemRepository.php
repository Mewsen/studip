<?php

namespace Studip\LTI13a;

use OAT\Library\Lti1p3Ags\Model\LineItem\LineItemCollection;
use OAT\Library\Lti1p3Ags\Model\LineItem\LineItemCollectionInterface;
use OAT\Library\Lti1p3Ags\Model\LineItem\LineItemInterface;
use OAT\Library\Lti1p3Ags\Repository\LineItemRepositoryInterface;
use Studip\LTIException;
use Grading\Definition;

class LineItemRepository implements LineItemRepositoryInterface
{
    /**
     * Converts the tool-ID and deployment-ID in the tool name used in the
     * Stud.IP grading context.
     *
     * @param string $tool_id The Stud.IP LTI tool ID.
     * @param string $deployment_id The Stud.IP LTI deployment ID.
     * @return string The corresponding tool name used in the Stud.IP grading context.
     */
    public static function getGradingToolName(string $tool_id, string $deployment_id) : string
    {
        return sprintf('lti-%s-%s', $tool_id, $deployment_id);
    }

    /**
     * Converts the LTI line item identifier to search parameters to retrieve
     * Stud.IP grading definitions.
     *
     * @param string $line_item_identifier The LTI line item identifier.
     *
     * @return array The search parameters for searching in the Stud.IP grading context.
     *     This is an associative array with two keys:
     *         'tool'       => The identifier of the tool in the Stud.IP grading context.
     *         'course_id'  => The Stud.IP course-ID.
     *     In case the search parameters cannot be generated, an empty array is returned.
     */
    public static function getSearchParametersFromLineItemIdentifier(string $line_item_identifier) : array
    {
        //$lineItemIdentifier contains the full URL to the line item.
        //We must extract the course-ID, tool-ID and deployment-ID
        //from the URL parameters first, before searching a grading definition.
        $url_parts = parse_url($line_item_identifier);
        $parameters = [];
        if (empty($url_parts['query'])) {
            //Nothing we can convert.
            return [];
        }
        parse_str($url_parts['query'], $parameters);
        if (empty($parameters)) {
            //Same as above.
            return [];
        }

        $search_parameters = [
            'course_id' => $parameters['cid'],
            'tool'      => self::getGradingToolName($parameters['tool_id'], $parameters['deployment_id'])
        ];
        if (!empty($parameters['definition_id'])) {
            $search_parameters['definition_id'] = $parameters['definition_id'];
        }

        return $search_parameters;
    }

    /**
     * @inheritDoc
     */
    public function find(string $lineItemIdentifier): ?LineItemInterface
    {
        $search_parameters = self::getSearchParametersFromLineItemIdentifier($lineItemIdentifier);
        if (!$search_parameters) {
            //Nothing we can search for.
            return null;
        }

        $definition = null;
        if (!empty($search_parameters['definition_id'])) {
            $definition = Definition::find($search_parameters['definition_id']);
        } else {
            $definition = Definition::findOneBySQL(
                "`course_id` = :course_id AND `tool` = :tool",
                [
                    'course_id' => $search_parameters['course_id'],
                    'tool' => $search_parameters['tool']
                ]
            );
        }
        if ($definition) {
            return $definition->toLineItem();
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
        $resource_link = \LtiResourceLink::find($resourceLinkIdentifier);
        if (!$resource_link) {
            throw new LTIException('Invalid resource link identifier.');
        }
        $tool_id       = $resource_link->deployment->tool_id ?? null;

        $sql = '';
        $sql_params = [];
        if ($tool_id && $resource_link->course_id) {
            $sql .= "`tool` = :tool AND `course_id` = :course_id";
            $sql_params['tool']      = self::getGradingToolName($tool_id, $resource_link->deployment_id);
            $sql_params['course_id'] = $resource_link->course_id;
        } else {
            //No tool-ID means no line item collection can be found.
            return $result;
        }

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
        $definitions = Definition::findBySql($sql, $sql_params);

        foreach ($definitions as $definition) {
            $result->add($definition->toLineItem());
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function save(LineItemInterface $lineItem): LineItemInterface
    {
        $resource_link_id = $lineItem->getResourceLinkIdentifier() ?? '';
        $resource_link = \LtiResourceLink::find($resource_link_id);
        if (!$resource_link) {
            throw new LTIException('Invalid resource link identifier.');
        }

        $definition            = new Definition();
        $definition->id        = $lineItem->getIdentifier();
        $definition->name      = $lineItem->getLabel();
        $definition->course_id = $resource_link->course_id;
        $definition->tool      = $resource_link->id;
        $definition->weight    = '1.0';
        if ($definition->store()) {
            return $definition->toLineItem();
        } else {
            throw new LTIException('Could not save line item.');
        }
    }

    /**
     * @inheritDoc
     */
    public function delete(string $lineItemIdentifier): void
    {
        $search_parameters = self::getSearchParametersFromLineItemIdentifier($lineItemIdentifier);
        $definition = Definition::findOneBySQL(
            '`course_id` = :course_id AND `tool` = :tool',
            $search_parameters
        );
        if ($definition) {
            $definition->delete();
        }
    }
}
