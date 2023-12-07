<?php
namespace JsonApi\Schemas;

use Neomerx\JsonApi\Contracts\Schema\BaseLinkInterface;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;
use ResourceBooking;

final class ResourceBookingSchema extends SchemaProvider
{
    const TYPE = 'resource-bookings';

    const REL_ASSIGNED_USER = 'assigned-user';
    const REL_BOOKING_USER = 'assigned-user';
    const REL_COURSE_DATE = 'course-date';
    const REL_INTERVALS = 'intervals';
    const REL_RESOURCE = 'resource';

    /**
     * @param ResourceBooking $resource
     */
    public function getId($resource): ?string
    {
        return $resource->id;
    }

    /**
     * @param ResourceBooking $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        $stringOrNull = function ($item): ?string {
            return trim($item) ? (string) $item : null;
        };

        return [
            'booking-type'     => (int) $resource->booking_type,
            'description'      => $stringOrNull($resource->description),
            'internal-comment' => $stringOrNull($resource->internal_comment),

            'begin'            => date('c', $resource->begin),
            'end'              => date('c', $resource->end),
            'preparation-time' => (int)$resource->preparation_time,

            'repeat-end'          => $resource->repeat_end ? date('c', $resource->repeat_end) : null,
            'repeat-quantity'     => isset($resource->repeat_quantity) ? (int)$resource->repeat_quantity : null,
            'repetition-interval' => (string)$resource->repetition_interval,

            'mkdate' => date('c', $resource->mkdate),
            'chdate' => date('c', $resource->chdate),
        ];
    }

    /**
     * @param ResourceBooking $resource
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        $isPrimary = $context->getPosition()->getLevel() === 0;
        if ($isPrimary) {
            $relationships = $this->getIntervalsRelationship(
                $relationships,
                $resource,
                $this->shouldInclude($context, self::REL_INTERVALS)
            );
            $relationships = $this->getResourceRelationship(
                $relationships,
                $resource->resource,
                $this->shouldInclude($context, self::REL_RESOURCE)
            );
            // TODO: More relations
        }

        return $relationships;
    }

    private function getIntervalsRelationship(
        array $relationships,
        \ResourceBooking $booking,
        bool $includeData
    ): array
    {
        $relationships[self::REL_INTERVALS] = [
            self::RELATIONSHIP_LINKS => [
                Link::RELATED => $this->getRelationshipRelatedLink($booking, self::REL_INTERVALS),
            ],
            self::RELATIONSHIP_DATA => $booking->time_intervals->map(function (\ResourceBookingInterval $interval) use ($includeData) {
                return $includeData ? $interval : \ResourceBookingInterval::build(['id' => $interval->id]);
            }),
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
