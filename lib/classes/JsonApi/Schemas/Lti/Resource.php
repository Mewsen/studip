<?php

namespace JsonApi\Schemas\Lti;

use Neomerx\JsonApi\Schema\Link;
use JsonApi\Schemas\SchemaProvider;
use Lti\ResourceLink;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class Resource extends SchemaProvider
{
    const TYPE = 'lti-resources';
    const REL_RANGE = 'range';
    const REL_REGISTRATION = 'registration';
    const REL_DEPLOYMENT = 'deployment';

    /**
     * @param ResourceLink $resource
     */
    public function getId($resource): ?string
    {
        return $resource->id;
    }

    /**
     * @param ResourceLink $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'title' => $resource->title,
            'description' => $resource->description,
            'position' => (int) $resource->position,
            'color' => $resource->color,
            'icon' => $resource->icon,
            'launch-url' => $resource->launch_url,
            'options' => $resource->options,
            'custom-parameters' => $resource->custom_parameters,
            'launch-container' => $resource->launch_container,
            'mkdate' => date('c', $resource->mkdate),
            'chdate' => date('c', $resource->chdate)
        ];
    }

    /**
     * @param ResourceLink $resource
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        $relationships = $this->addRangeRelationship($relationships, $resource, $this->shouldInclude($context, self::REL_RANGE));
        $relationships = $this->addRegistrationRelationship($relationships, $resource, $this->shouldInclude($context, self::REL_REGISTRATION));
        $relationships = $this->addDeploymentRelationship($relationships, $resource, $this->shouldInclude($context, self::REL_DEPLOYMENT));

        return $relationships;
    }

    private function addRangeRelationship(array $relationships, ResourceLink $resource, bool $withRange = false): array
    {
        if ($withRange && $resource->course) {
            $relationships[self::REL_RANGE] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($resource->course)
                ],
                self::RELATIONSHIP_DATA => $resource->course
            ];
        }

        return $relationships;
    }

    private function addRegistrationRelationship(array $relationships, ResourceLink $resource, bool $withRegistration = false): array
    {
        if ($withRegistration && $resource->registration) {
            $relationships[self::REL_REGISTRATION] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($resource->registration)
                ],
                self::RELATIONSHIP_DATA => $resource->registration
            ];
        }

        return $relationships;
    }

    private function addDeploymentRelationship(array $relationships, ResourceLink $resource, bool $withDeployment = false): array
    {
        if ($withDeployment) {
            $relationships[self::REL_DEPLOYMENT] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($resource->deployment)
                ],
                self::RELATIONSHIP_DATA => $resource->deployment
            ];
        }

        return $relationships;
    }
}
