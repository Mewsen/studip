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
        ['elements' => $elements, 'containers' => $containers, 'blocks' => $blocks] = $mapping;
        foreach ($blocks as $oldBlockId => $newBlockObj) {
            $payload = $newBlockObj->type->getPayload();
            if ($newBlockObj->type->getType() === \Courseware\BlockTypes\Link::getType()) {
                if ($payload['type'] === 'internal' && '' != $payload['target']) {
                    if (in_array($payload['target'], array_keys($elements))) {
                        $payload['target'] = $elements[intval($payload['target'])];
                    } else {
                        $payload['target'] = '';
                    }
                    $newBlockObj->type->setPayload($payload);
                    $newBlockObj->store();
                }
            }

            if ($newBlockObj->type->getType() === \Courseware\BlockTypes\Text::getType()) {
                if ($payload['text']) {
                    $document = new \DOMDocument();
                    @$document->loadHTML(
                        mb_convert_encoding($payload['text'], 'HTML-ENTITIES', 'UTF-8')
                    );

                    $anchors = $document->getElementsByTagName('a');
                    $updated = false;

                    foreach ($anchors as $anchor) {
                        $href = $anchor->getAttribute('href');
                        if (preg_match('#/structural_element/(\d+)#', $href, $matches)) {
                            $oldId = (int) $matches[1];
                            if (isset($elements[$oldId])) {
                                $newId = $elements[$oldId];
                                $newHref = preg_replace('#(/structural_element/)\d+#', '${1}' . $newId, $href);
                                $updated = true;
                            }
                        }

                        if (preg_match('/cid=[^&#]+/', $href)) {
                            $newHref = preg_replace('/(cid=)([^&#]+)/', '${1}' . $newUnit->range_id, $newHref);
                            $updated = true;
                        }

                        if (preg_match('#(/courseware/courseware/)\d+#', $href)) {
                            $newHref = preg_replace('#(/courseware/courseware/)\d+#', '${1}' . $newUnit->id, $newHref);
                            $updated = true;
                        }

                        if ($updated && $newHref !== $href) {
                            $anchor->setAttribute('href', $newHref);
                        }
                    }

                    if ($updated) {
                        $body = $document->getElementsByTagName('body')->item(0);
                        $newHtml = '';
                        foreach ($body->childNodes as $child) {
                            $newHtml .= $document->saveHTML($child);
                        }
                        $payload['text'] = $newHtml;
                        $newBlockObj->type->setPayload($payload);
                        $newBlockObj->store();
                    }
                }
            }

        }
    }
}
