<?php

namespace JsonApi\Schemas\DashboardWidget;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;
use \DashboardWidget\Container as DashboardContainer;

/**
 * DashboardWidget's Container Schema.
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
class Container extends SchemaProvider
{
    /**
     * @inheritdoc
     */
    public const TYPE = 'dashboard-widget-containers';

    /**
     * @var string the owner relationship flag.
     */
    const REL_OWNER = 'owner';

    /**
     * @var string the widgets relationship flag.
     */
    const REL_WIDGETS = 'widgets';

    /**
     * @inheritdoc
     */
    public function getId($resource): ?string
    {
        return $resource->getId();
    }

    /**
     * @inheritdoc
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'payload' => $resource->payload->getArrayCopy(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function hasResourceMeta($resource): bool
    {
        return false; // TODO: we turn this here off in favor of the misc endpoint. later decision should be made.
    }

    /**
     * @inheritdoc
     */
    public function getResourceMeta($resource)
    {
        // TODO: We have these also in misc json api, do we need them here? or there? or what?
        return [
            'contexts' => DashboardContainer::ALL_CONTEXTS,
            'breakpoints' => DashboardContainer::ALL_BREAKPOINTS,
            'default-breakpoints' => DashboardContainer::DEFAULT_BREAKPOINTS,
            'breakpoints-widths' => DashboardContainer::BREAKPOINT_WIDTHS,
            'breakpoints-cols' => DashboardContainer::BREAKPOINT_COLS,
            'breakpoints-size-mapping' => DashboardContainer::BREAKPOINT_SIZE_MAPPINGS,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];
        $owner = $resource->owner;
        $relationships[self::REL_OWNER] = [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->createLinkToResource($owner),
            ],
            self::RELATIONSHIP_DATA => $owner,
        ];
        $widgets = $resource->widgets;
        $relationships[self::REL_WIDGETS] = [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->getRelationshipSelfLink($resource, self::REL_WIDGETS),
            ],
            self::RELATIONSHIP_DATA => $widgets ?? [],
        ];
        return $relationships;
    }
}
