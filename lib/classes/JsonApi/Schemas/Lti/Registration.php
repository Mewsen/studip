<?php

namespace JsonApi\Schemas\Lti;

use Studip\Markup;
use Neomerx\JsonApi\Schema\Link;
use JsonApi\Schemas\SchemaProvider;
use Lti\Registration as RegistrationModel;
use Studip\Lti\Enum\RegistrationStatus;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class Registration extends SchemaProvider
{
    const TYPE = 'lti-registrations';
    const REL_RANGE = 'range';
    const REL_DEPLOYMENTS = 'deployments';

    /**
     * @param RegistrationModel $resource
     */
    public function getId($resource): ?string
    {
        return $resource->id;
    }

    /**
     * @param RegistrationModel $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'name' => $resource->name,
            'description' => Markup::markupToHtml($resource->description),
            'description-html' => formatReady($resource->description),
            'status' => RegistrationStatus::get($resource->status)['value'],
            'role' => $resource->role,
            'version' => $resource->version,
            'mkdate' => date('c', $resource->mkdate),
            'chdate' => date('c', $resource->chdate)
        ];
    }

    /**
     * @param RegistrationModel $resource
     */
    public function hasResourceMeta($resource): bool
    {
        return true;
    }

    /**
     * @param RegistrationModel $resource
     */
    public function getResourceMeta($resource)
    {
        $configs = array_combine(
            array_map(fn ($key) => str_replace('_', '-', $key), array_keys($resource->getConfigValues())),
            $resource->getConfigValues()
        );

        foreach (['allow-custom-url', 'deep-linking', 'send-lis-person'] as $key) {
            if (array_key_exists($key, $configs)) {
                $configs[$key] = (bool) $configs[$key];
            }
        }

        if (array_key_exists('data-protection-notes', $configs)) {
            $configs['data-protection-notes'] = Markup::markupToHtml($resource->description);
            $configs['data-protection-notes-html'] = formatReady($resource->description);
        }

        return [
            'configs' => $configs
        ];
    }

    /**
     * @param RegistrationModel $resource
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        $relationships = $this->addRangeRelationship($relationships, $resource, $this->shouldInclude($context, self::REL_RANGE));
        $relationships = $this->addDeploymentsRelationship($relationships, $resource, $this->shouldInclude($context, self::REL_DEPLOYMENTS));

        return $relationships;
    }

    private function addRangeRelationship(array $relationships, RegistrationModel $registration, bool $withRange = false): array
    {
        if ($withRange && $registration->range) {
            $relationships[self::REL_RANGE] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($registration->range)
                ],
                self::RELATIONSHIP_DATA => $registration->range
            ];
        }

        return $relationships;
    }

    private function addDeploymentsRelationship(array $relationships, RegistrationModel $registration, bool $withDepyloments = false): array
    {
        if ($withDepyloments) {
            $relationships[self::REL_DEPLOYMENTS] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->getRelationshipRelatedLink($registration, self::REL_DEPLOYMENTS)
                ],
                self::RELATIONSHIP_DATA => $registration->deployments
            ];
        }

        return $relationships;
    }
}
