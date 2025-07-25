<?php
namespace JsonApi\Schemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;
use ResourceBookingInterval;

final class ResourceBookingIntervalSchema extends SchemaProvider
{
    const TYPE = 'resource-booking-intervals';

    const REL_BOOKING = 'booking';
    const REL_RESOURCE = 'resource';

    /**
     * @param ResourceBookingInterval $resource
     */
    public function getId($resource): ?string
    {
        return $resource->id;
    }

    /**
     * @param ResourceBookingInterval $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'begin' => date('c', $resource->begin),
            'end' => date('c', $resource->end),

            'takes-place' => (bool) $resource->takes_place,

            'mkdate' => date('c', $resource->mkdate),
            'chdate' => date('c', $resource->chdate),
        ];
    }

    /**
     * @param ResourceBookingInterval $resource
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        $isPrimary = $context->getPosition()->getLevel() === 0;
        if ($isPrimary) {
            $relationships = $this->getBookingRelationship(
                $relationships,
                $resource->booking,
                $this->shouldInclude($context, self::REL_BOOKING)
            );
            $relationships = $this->getResourceRelationship(
                $relationships,
                $resource->resource,
                $this->shouldInclude($context, self::REL_RESOURCE)
            );
        }

        return $relationships;
    }

    private function getBookingRelationship(
        array $relationships,
        \ResourceBooking $booking,
        bool $includeData
    ): array {
        $relationships[self::REL_BOOKING] = [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->createLinkToResource($booking),
            ],
            self::RELATIONSHIP_DATA => $includeData ? $booking : \ResourceBooking::build(['id' => $booking->id]),
        ];

        return $relationships;
    }

    private function getResourceRelationship(
        array $relationships,
        \Resource $resource,
        bool $includeData
    ): array {
        $relationships[self::REL_RESOURCE] = [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->createLinkToResource($resource),
            ],
            self::RELATIONSHIP_DATA => $includeData ? $resource : \Resource::build(['id' => $resource->id]),
        ];
        return $relationships;
    }
}
