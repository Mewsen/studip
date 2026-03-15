<?php

namespace JsonApi\Routes\Courseware;

use Courseware\Block;
use Courseware\Container;
use Courseware\ContainerTypes\ContainerType;
use Courseware\StructuralElement;
use JsonApi\Errors\UnprocessableEntityException;
use JsonApi\Schemas\Courseware\ContainerSchema;
use JsonApi\Schemas\Courseware\StructuralElementSchema as StructuralElementSchema;
use JsonApi\SORM;
use JsonApi\SormCRUDController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use User;

/**
 * Displays all containers of a structural element.
 */
class CoursewareContainers extends SormCRUDController
{
    use EditBlockAwareTrait;

    protected $allowedPagingParameters = ['offset', 'limit'];

    protected $allowedIncludePaths = [
        ContainerSchema::REL_BLOCKS,
        ContainerSchema::REL_BLOCKS . '.edit-blocker',
        ContainerSchema::REL_BLOCKS . '.editor',
        ContainerSchema::REL_BLOCKS . '.owner',
        ContainerSchema::REL_BLOCKS . '.user-data-field',
        ContainerSchema::REL_BLOCKS . '.user-progress',
        ContainerSchema::REL_EDITOR,
        ContainerSchema::REL_EDITBLOCKER,
        ContainerSchema::REL_OWNER,
        ContainerSchema::REL_STRUCTURAL_ELEMENT,
    ];

    protected function getSORMClassName(): string
    {
        return Container::class;
    }

    /**
     * @param Container|null $current
     */
    protected function getData(Request $request, array $args, ?SORM $current = null): array
    {
        $data = [
            'position' => (int) $this->getAttribute('position', $current ? $current->position : 0),
            'site' => (int) $this->getAttribute('site', $current ? $current->site : 0),
            'container_type' => $this->getAttribute('container-type', $current ? $current->container_type : ''),
            'title' => $this->getAttribute('title', $current ? $current->type->getTitle() : ''),
            'width' => $this->getAttribute('width', $current ? $current->type->getContainerWidth() : ''),
            'visible' => $this->getAttribute('visible', $current ? $current->visible : false),
            'payload' => $this->getAttribute('payload', $current ? $current->payload->getIterator() : []),
            'mkdate' => $this->getDateAttribute('mkdate'),
            'chdate' => $this->getDateAttribute('chdate'),
        ];

        $relations = [
            ContainerSchema::REL_BLOCKS  => Block::class,
            ContainerSchema::REL_EDITOR => User::class,
            ContainerSchema::REL_EDITBLOCKER => User::class,
            ContainerSchema::REL_OWNER => User::class,
            ContainerSchema::REL_STRUCTURAL_ELEMENT => StructuralElement::class,
        ];

        foreach ($relations as $relation_field => $relation_class) {
            if ($this->hasRelation($relation_field)) {
                $data[$relation_field] = $this->resolveRelationData($relation_field, $relation_class);
            }
        }
        return $data;
    }

    public function validateResourceDocument($json, $data): ?string
    {
        if (!isset($json['data'])) {
            return 'Missing `data` member at document´s top level.';
        }
        if (ContainerSchema::TYPE !== $json['data']['type']) {
            return 'Wrong `type` member of document´s `data`.';
        }

        if (!isset($json['data']['attributes']['container-type'])) {
            return 'Missing `container-type` attribute.';
        }

        $containerType = $json['data']['attributes']['container-type'];
        if (!$this->validateContainerType($containerType)) {
            return 'Invalid `container-type` attribute.';
        }

        if (!isset($json['data']['relationships'][ContainerSchema::REL_STRUCTURAL_ELEMENT])) {
            return 'Missing `structural-element` relationship.';
        }
        $struct_el_json = $json['data']['relationships'][ContainerSchema::REL_STRUCTURAL_ELEMENT];
        if (!$this->getStructElemFromJson($struct_el_json)) {
            return 'Invalid `structural-element` relationship.';
        }
        return null;
    }

    protected function performCreate(Request $request, Response $response, array $args): SORM
    {
        $data = $this->getData($request, $args);
        $user = $this->getUser($request);

        /** @var StructuralElement $struct_el */
        $struct_el = $data[ContainerSchema::REL_STRUCTURAL_ELEMENT];
        $container = \Courseware\Container::build([
            'structural_element_id' => $struct_el->id,
            'owner_id' => $user->id,
            'editor_id' => $user->id,
            'edit_blocker_id' => '',
            'position' => $struct_el->countContainers(),
            'container_type' => $data['container_type'],
            'payload' => '',
        ]);

        if ($data['payload']) {
            if (!$container->type->validatePayload((object) $data['payload'])) {
                throw new UnprocessableEntityException('Invalid payload for this `container-type`.');
            }
            $container->type->setPayload($data['payload']);
        }
        $container->store();
        return $container;
    }

    protected function performUpdate(Request $request, Response $response, array $args): SORM
    {
        $user = $this->getUser($request);
        $resource = $this->requireItem($request, $args['id']);
        $data = $this->getData($request, $args, $resource);
        return $this->updateLockedResource($user, $resource, function ($user, $resource) use ($data) {
            if ($payload = $data['payload']) {
                if (!$resource->type->validatePayload((object) $payload)) {
                    throw new UnprocessableEntityException('Invalid payload for this `container-type`.');
                }
                $resource->type->setPayload($payload);
            }
            if (isset($data[ContainerSchema::REL_STRUCTURAL_ELEMENT])) {
                $resource->structural_element_id = $data[ContainerSchema::REL_STRUCTURAL_ELEMENT]->id;
            }
            if ($data['container_type']) {
                $resource->container_type = $data['container_type'];
            }

            $resource->position = $data['position'];

            $resource->editor_id = $user->id;
            $resource->store();

            return $resource;
        });
    }

    protected function performDelete(Request $request, Response $response, array $args): bool|int
    {
        $item = $this->requireItem($request, $args['id']);
        return $this->deleteResource($this->getUser($request), $item);
    }

    private function validateContainerType(string $containerType): bool
    {
        return ContainerType::isContainerType($containerType);
    }

    private function getStructElemFromJson($json): ?StructuralElement
    {
        if (!$this->validateResourceObject($json, ContainerSchema::REL_STRUCTURAL_ELEMENT, StructuralElementSchema::TYPE)) {
            return null;
        }

        return StructuralElement::find($json['data']['id']);
    }
}
