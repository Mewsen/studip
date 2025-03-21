<?php
namespace JsonApi\Schemas;

use JsonApi\Routes\Tree\Helpers\TreeNodeCourse as Model;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

final class TreeNodeCourse extends SchemaProvider
{
    const TYPE = 'tree-node-course';

    /**
     * @param Model $resource
     */
    public function getId($resource): ?string
    {
        return $resource->getId();
    }

    /**
     * @param Model $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        $schema = $this->schemaContainer->getSchema($resource->getCourse());

        return array_merge(
            (array) $schema->getAttributes($resource->getCourse(), $context),
            [
                'semester' => $resource->getSemesterText(),
                'lecturers' => $resource->getLecturers(),
                'admissionstate' => $resource->getAdmissionState(),
                'dates' => $resource->getDates(),
            ]
        );
    }

    public function getRelationships($resource, ContextInterface $context): iterable
    {
        return [];
    }
}
