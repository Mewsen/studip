<?php

namespace Courseware;

use Courseware\StructuralElement;
use Courseware\Unit;
use User;

final class CoursewareCopyService
{
    public static function copyStructuralElement(
        StructuralElement $source,
        User $user,
        StructuralElement $target = null,
        ?string $rangeId = null,
        ?string $rangeType = null,
        string $purpose = '',
        bool $duplicate = false,
        Unit $newUnit = null
    ): StructuralElement {
        $mapping = [
            'elements' => [],
            'containers' => [],
            'blocks' => [],
        ];

        if ($rangeId !== null && $rangeType !== null) {
            $newElement = $source->copyToRange(
                $user,
                $rangeId,
                $rangeType,
                $purpose,
                $duplicate,
                $mapping,
            );
        } elseif ($target !== null) {
            $newElement = $source->copy(
                $user,
                $target,
                $purpose,
                $mapping,
                $duplicate
            );
        } else {
            throw new \InvalidArgumentException('Entweder target oder rangeId + rangeType müssen gesetzt sein.');
        }

        $unit = $newUnit ?? $newElement->findUnit();

        self::performMapping($mapping, $unit);

        return $newElement;
    }

    private static function performMapping(array $mapping, Unit $newUnit): void
    {
        foreach ($mapping['blocks'] ?? [] as $block) {
            $block->type->performMapping($mapping, $newUnit);
        }
    }
}
