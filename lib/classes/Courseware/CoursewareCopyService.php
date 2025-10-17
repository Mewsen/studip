<?php

namespace Courseware;

use Courseware\StructuralElement;
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
        bool $duplicate = false
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

        self::performMapping($mapping);

        return $newElement;
    }

    private static function performMapping(array $mapping): void
    {
        ['elements' => $elements, 'containers' => $containers, 'blocks' => $blocks] = $mapping;
        foreach ($blocks as $oldBlockId => $newBlockObj) {
            if ($newBlockObj->type->getType() === \Courseware\BlockTypes\Link::getType()) {
                $payload = $newBlockObj->type->getPayload();
                if ($payload['type'] === 'internal' && '' != $payload['target']) {
                    if (in_array($payload['target'], array_keys($mapping['elements']))) {
                        $payload['target'] = $mapping['elements'][intval($payload['target'])];
                    } else {
                        $payload['target'] = '';
                    }
                    $newBlockObj->type->setPayload($payload);
                    $newBlockObj->store();
                }
            }
        }

    }
}
