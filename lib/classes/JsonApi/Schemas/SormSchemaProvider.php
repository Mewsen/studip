<?php

namespace JsonApi\Schemas;

use JsonApi\SORM;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\Link;

abstract class SormSchemaProvider extends SchemaProvider
{
    /**
     * @param SORM $resource
     */
    public function getId($resource): ?string
    {
        return $resource->id;
    }

    /**
     * @param SORM $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return $resource->jsonSerialize();
    }

    /**
     * @param SORM $resource
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        $isPrimary = $context->getPosition()->getLevel() === 0;
        foreach ($resource->getRelations() as $relation => $options) {
            $kebab_relation = str_replace('_', '-', $relation);
            if (!empty($options['internal'])) {
                continue;
            }

            $should_include = $this->shouldInclude($context, $kebab_relation);

            if (!$isPrimary && !$should_include) {
                continue;
            }

            $data = $resource->getValue($relation);
            if (!$should_include && !$data) {
                // we can omit the relation, if it isn't explicitly included
                continue;
            }
            $link = $this->getRelationshipRelatedLink($resource, $kebab_relation);

            if ($data) {
                if (in_array($options['type'], ['has_one', 'belongs_to'])) {
                    $link = $this->createLinkToResource($data);
                } elseif (!$should_include) {
                    $data = $data->map(function ($rel) use ($options) {
                        return $options['class']::build(['id' => $rel->id], false);
                    });
                }
            }

            $relationships[$kebab_relation] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $link,
                ],
                self::RELATIONSHIP_DATA => $data,
            ];
        }

        return $relationships;
    }

    /**
     * @param SORM $resource
     */
    public function hasResourceMeta($resource): bool
    {
        return $resource->hasI18NFields();
    }

    /**
     * @param SORM $resource
     */
    public function getResourceMeta($resource)
    {
        if (!$resource->hasI18NFields()) {
            return [];
        }

        $result = [
            'i18n' => [],
        ];
        foreach ($resource->getI18NFields() as $field) {
            $value = $resource->getValue($field);
            $result['i18n'][$field] = [];
            foreach (array_keys($GLOBALS['CONTENT_LANGUAGES']) as $lang) {
                $result['i18n'][$field][$lang] = $value->localized($lang);
            }
        }

        return $result;
    }
}
