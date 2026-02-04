<?php

namespace JsonApi\Schemas\Lti;

use Neomerx\JsonApi\Schema\Link;
use JsonApi\Schemas\SchemaProvider;
use Lti\Deployment as DeploymentModel;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class Deployment extends SchemaProvider
{
    const TYPE = 'lti-deployment';
    const REL_REGISTRATION = 'registration';

    /**
     * @param DeploymentModel $resource
     */
    public function getId($resource): ?string
    {
        return $resource->id;
    }

    /**
     * @param DeploymentModel $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return [
            'name' => $resource->name,
            'is-default' => (bool) $resource->is_default,
            'deployment-key' => $resource->deployment_key,
            'client-id' => $resource->client_id,
            'mkdate' => date('c', $resource->mkdate),
            'chdate' => date('c', $resource->chdate)
        ];
    }

    /**
     * @param DeploymentModel $resource
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        $relationships = $this->addRegistrationRelationship($relationships, $resource, $this->shouldInclude($context, self::REL_REGISTRATION));

        return $relationships;
    }

    private function addRegistrationRelationship(array $relationships, DeploymentModel $deployment, bool $withRegistration = false): array
    {
        if ($withRegistration) {
            $relationships[self::REL_REGISTRATION] = [
                self::RELATIONSHIP_LINKS => [
                    Link::RELATED => $this->createLinkToResource($deployment->registration)
                ],
                self::RELATIONSHIP_DATA => $deployment->registration
            ];
        }

        return $relationships;
    }
}
