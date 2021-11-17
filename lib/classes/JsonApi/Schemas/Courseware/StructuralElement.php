<?php

namespace JsonApi\Schemas\Courseware;

use JsonApi\Schemas\SchemaProvider;
use Neomerx\JsonApi\Document\Link;
use Neomerx\JsonApi\Schema\Identifier;

class StructuralElement extends SchemaProvider
{
    const TYPE = 'courseware-structural-elements';

    const REL_ANCESTORS = 'ancestors';
    const REL_CHILDREN = 'children';
    const REL_CONTAINERS = 'containers';
    const REL_COURSE = 'course';
    const REL_DESCENDANTS = 'descendants';
    const REL_EDITBLOCKER = 'edit-blocker';
    const REL_EDITOR = 'editor';
    const REL_IMAGE = 'image';
    const REL_OWNER = 'owner';
    const REL_PARENT = 'parent';
    const REL_USER = 'user';

    protected $resourceType = self::TYPE;

    /**
     * {@inheritdoc}
     */
    public function getId($resource)
    {
        return $resource->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($resource)
    {
        $user = $this->getDiContainer()->get('studip-current-user');

        return [
            'position' => (int) $resource['position'],
            'title' => (string) $resource['title'],
            'purpose' => (string) $resource['purpose'],
            'payload' => $resource['payload']->getIterator(),
            'public' => (int) $resource['public'],
            'release-date' => $resource['release_date'] ? date('Y-m-d', (int) $resource['release_date']) : null,
            'withdraw-date' => $resource['withdraw_date'] ? date('Y-m-d', (int) $resource['withdraw_date']) : null,
            'read-approval' => $resource['read_approval']->getIterator(),
            'write-approval' => $resource['write_approval']->getIterator(),
            'copy-approval' => $resource['copy_approval']->getIterator(),
            'can-edit' => $resource->canEdit($user),

            'external-relations' => $resource['external_relations']->getIterator(),
            'mkdate' => date('c', $resource['mkdate']),
            'chdate' => date('c', $resource['chdate']),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @param \Courseware\Models\StructuralElement $resource
     * @param bool                                 $isPrimary
     * @param array                                $includeList
     */
    public function getRelationships($resource, $isPrimary, array $includeList)
    {
        $relationships = [];

        $shouldInclude = function ($key) use ($includeList) {
            return in_array($key, $includeList);
        };

        $relationships = $this->addChildrenRelationship(
            $relationships,
            $resource,
            $shouldInclude(self::REL_CHILDREN)
        );

        $relationships = $this->addContainersRelationship(
            $relationships,
            $resource,
            $shouldInclude(self::REL_CONTAINERS)
        );

        $relationships = $this->addRangeRelationship(
            $relationships,
            $resource,
            $includeList
        );

        $relationships = $this->addOwnerRelationship(
            $relationships,
            $resource,
            $shouldInclude(self::REL_OWNER)
        );

        $relationships = $this->addEditorRelationship(
            $relationships,
            $resource,
            $shouldInclude(self::REL_EDITOR)
        );

        $relationships = $this->addEditBlockerRelationship(
            $relationships,
            $resource,
            $shouldInclude(self::REL_EDITBLOCKER)
        );

        $relationships = $this->addParentRelationship(
            $relationships,
            $resource,
            $shouldInclude(self::REL_PARENT)
        );

        $relationships = $this->addAncestorsRelationship(
            $relationships,
            $resource,
            $shouldInclude(self::REL_ANCESTORS)
        );

        $relationships = $this->addDescendantsRelationship(
            $relationships,
            $resource,
            $shouldInclude(self::REL_DESCENDANTS)
        );

        $relationships = $this->addImageRelationship(
            $relationships,
            $resource,
            $shouldInclude(self::REL_IMAGE)
        );

        return $relationships;
    }

    private function addAncestorsRelationship(array $relationships, $resource, $includeData)
    {
        $relation = [
            self::LINKS => [
                Link::RELATED => $this->getRelationshipRelatedLink($resource, self::REL_ANCESTORS),
            ],
        ];

        if ($includeData) {
            $user = $this->getDiContainer()->get('studip-current-user');
            $related = $resource->findAncestors($user);
            $relation[self::DATA] = $related;
        }

        $relationships[self::REL_ANCESTORS] = $relation;

        return $relationships;
    }

    private function addChildrenRelationship(array $relationships, $resource, $includeData)
    {
        $relation = [
            self::LINKS => [
                Link::RELATED => $this->getRelationshipRelatedLink($resource, self::REL_CHILDREN),
            ]
        ];

        if ($includeData) {
            $user = $this->getDiContainer()->get('studip-current-user');
            $relation[self::DATA] = array_filter(
                $resource->children,
                function ($child) use ($user) {
                    return $child->canRead($user);
                }
            );
        }

        $relationships[self::REL_CHILDREN] = $relation;

        return $relationships;
    }

    private function addContainersRelationship(array $relationships, $resource, $includeData)
    {
        $relation = [
            self::LINKS => [
                Link::RELATED => $this->getRelationshipRelatedLink($resource, self::REL_CONTAINERS),
            ],
        ];

        if ($includeData) {
            $relation[self::DATA] = $resource->containers;
        } else {
            $sql = 'SELECT id FROM cw_containers WHERE structural_element_id = ?';
            $containers = \DBManager::get()->fetchAll($sql, [$resource->id], function ($container) {
                return new Identifier($container['id'], \JsonApi\Schemas\Courseware\Container::TYPE);
            });
            $relation[self::DATA] = $containers;
        }
        $relationships[self::REL_CONTAINERS] = $relation;

        return $relationships;
    }

    private function addDescendantsRelationship(array $relationships, $resource, $includeData)
    {
        $relation = [
            self::LINKS => [
                Link::RELATED => $this->getRelationshipRelatedLink($resource, self::REL_DESCENDANTS),
            ],
        ];

        if ($includeData) {
            $user = $this->getDiContainer()->get('studip-current-user');
            $related = $resource->findDescendants($user);
            $relation[self::DATA] = $related;
        }

        $relationships[self::REL_DESCENDANTS] = $relation;

        return $relationships;
    }

    private function addEditBlockerRelationship(array $relationships, $resource, $includeData)
    {
        $relation = [
            self::SHOW_SELF => true,
        ];
        if ($resource['edit_blocker_id']) {
            $relation[self::LINKS] = [
                Link::RELATED => $this->createLinkToUser($resource['edit_blocker_id']),
            ];
            $relation[self::DATA] = $includeData ? $resource->edit_blocker : new Identifier($resource['edit_blocker_id'], \JsonApi\Schemas\User::TYPE);
        } else {
            $relation[self::DATA] = null;
        }
        $relationships[self::REL_EDITBLOCKER] = $relation;

        return $relationships;
    }

    private function addEditorRelationship(array $relationships, $resource, $includeData)
    {
        $relation = [];
        if ($resource['editor_id']) {
            $relation[self::LINKS] = [
                Link::RELATED => $this->createLinkToUser($resource['editor_id']),
            ];
            $relation[self::DATA] = $includeData ? $resource->editor : new Identifier($resource['editor_id'], \JsonApi\Schemas\User::TYPE);
        } else {
            $relation[self::DATA] = null;
        }
        $relationships[self::REL_EDITOR] = $relation;

        return $relationships;
    }

    private function addImageRelationship(array $relationships, $resource, $includeData)
    {
        $image = $resource->image;
        $relation = [
            self::DATA => $image ?: null,
        ];

        if ($image) {
            $relation[self::META] = [
                'download-url' => $resource->getImageUrl(),
            ];
        }

        $relationships[self::REL_IMAGE] = $relation;

        return $relationships;
    }

    private function addOwnerRelationship(array $relationships, $resource, $includeData)
    {
        $relation = [];
        if ($resource['owner_id']) {
            $relation[self::LINKS] = [
                Link::RELATED => $this->createLinkToUser($resource['owner_id']),
            ];
            $relation[self::DATA] = $includeData ? $resource->owner : new Identifier($resource['owner_id'], \JsonApi\Schemas\User::TYPE);
        } else {
            $relation[self::DATA] = null;
        }
        $relationships[self::REL_OWNER] = $relation;

        return $relationships;
    }

    private function addParentRelationship(array $relationships, $resource, $includeData)
    {
        $relation = [];

        if ($resource['parent_id']) {
            $relation[self::LINKS] = [
                Link::RELATED => $this->createLinkToStructuralElement($resource['parent_id']),
            ];
            $relation[self::DATA] = $includeData ? $resource->parent : new Identifier($resource['parent_id'], self::TYPE);
        } else {
            $relation[self::DATA] = null;
        }
        $relationships[self::REL_PARENT] = $relation;

        return $relationships;
    }

    private function addRangeRelationship(array $relationships, $resource, $includeList)
    {
        if ($resource['range_type'] === 'course') {
            $includeData = in_array(self::REL_COURSE, $includeList);
            $relationships[self::REL_COURSE] = [
                self::LINKS => [
                    Link::RELATED => $this->createLinkToCourse($resource['range_id']),
                ],
                self::DATA => $includeData ? $resource->course : new Identifier($resource['range_id'], \JsonApi\Schemas\Course::TYPE),
            ];
        } elseif ($resource['range_type'] === 'user') {
            $includeData = in_array(self::REL_USER, $includeList);
            $relationships[self::REL_USER] = [
                self::LINKS => [
                    Link::RELATED => $this->createLinkToUser($resource['range_id']),
                ],
                self::DATA => $includeData ? $resource->user : new Identifier($resource['range_id'], \JsonApi\Schemas\User::TYPE)
            ];
        }

        return $relationships;
    }

    private static $memo = [];

    private function createLinkToCourse($rangeId)
    {
        if (isset(self::$memo['course' . $rangeId])) {
            return self::$memo['course' . $rangeId];
        }

        $course = \Course::build(['id' => $rangeId], false);
        $link = $this->getSchemaContainer()
            ->getSchema($course)
            ->getSelfSubLink($course);
        self::$memo['course' . $rangeId] = $link;

        return $link;
    }

    private function createLinkToStructuralElement($structuralElementId)
    {
        if (isset(self::$memo['structuralelement' . $structuralElementId])) {
            return self::$memo['structuralelement' . $structuralElementId];
        }

        $structuralElement = \Courseware\StructuralElement::build(['id' => $structuralElementId], false);
        $link = $this->getSchemaContainer()
            ->getSchema($structuralElement)
            ->getSelfSubLink($structuralElement);
        self::$memo['structuralelement' . $structuralElementId] = $link;

        return $link;
    }

    private function createLinkToUser($rangeId)
    {
        if (isset(self::$memo['user' . $rangeId])) {
            return self::$memo['user' . $rangeId];
        }

        $user = \User::build(['id' => $rangeId], false);
        $link = $this->getSchemaContainer()
            ->getSchema($user)
            ->getSelfSubLink($user);
        self::$memo['user' . $rangeId] = $link;

        return $link;
    }
}
