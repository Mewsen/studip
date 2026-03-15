<?php

namespace JsonApi\Routes\Courseware;

use Courseware\StructuralElement;
use JsonApi\Schemas\Courseware\ContainerSchema;
use JsonApi\Schemas\Courseware\StructuralElementSchema;
use JsonApi\SORM;
use JsonApi\SormCRUDController;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class StructuralElementsIndex.
 */
class CoursewareStructuralElements extends SormCRUDController
{
    protected $allowedPagingParameters = ['offset', 'limit'];

    protected $allowedIncludePaths = [
        StructuralElementSchema::REL_ANCESTORS,
        StructuralElementSchema::REL_CHILDREN,
        StructuralElementSchema::REL_CONTAINERS,
        StructuralElementSchema::REL_CONTAINERS . ContainerSchema::REL_BLOCKS,
        StructuralElementSchema::REL_CONTAINERS . ContainerSchema::REL_BLOCKS . 'edit-blocker',
        StructuralElementSchema::REL_CONTAINERS . ContainerSchema::REL_BLOCKS . 'editor',
        StructuralElementSchema::REL_CONTAINERS . ContainerSchema::REL_BLOCKS . 'owner',
        StructuralElementSchema::REL_CONTAINERS . ContainerSchema::REL_BLOCKS . 'user-data-field',
        StructuralElementSchema::REL_CONTAINERS . ContainerSchema::REL_BLOCKS . 'user-progress',
        StructuralElementSchema::REL_COURSE,
        StructuralElementSchema::REL_EDITOR,
        StructuralElementSchema::REL_OWNER,
        StructuralElementSchema::REL_PARENT,
    ];

    protected function getSORMClassName(): string
    {
        return StructuralElement::class;
    }

    /**
     * @param StructuralElement|null $current
     */
    protected function getData(Request $request, array $args, ?SORM $current = null): array
    {
       $user = $this->getUser($request);
        $data = [
            'position' => (int) $this->getAttribute('position', $current ? $current->position : 0),
            'title' => $this->getAttribute('title', $current ? $current->title :''),
            'purpose' => $this->getAttribute('purpose', $current ? $current->purpose : ''),
            'payload' => $this->getAttribute('payload', $current ? $current->payload->getIterator() : []),
            'public' => (int) $this->getAttribute('public', $current ? $current->public : 0),
            'permission-type' => $this->getAttribute('permission-type', $current ? $current->permission_type : ''),
            'visible' => $this->getAttribute('visible', $current ? $current->visible : ''),
            'visible_all' => (bool) $this->getAttribute('visible-all', $current ? $current->visible_all : ''),
            'visible-start-date' => $this->getDateAttribute('visible-start-date'),
            'visible-end-date' => $this->getDateAttribute('visible-end-date'),
            'writable' => (string) $this->getAttribute('writable', $current ? $current->writable : ''),
            'writable_all' => (bool) $this->getAttribute('writable-all', $current ? $current->writable_all : ''),
            'writable_start_date' => $this->getDateAttribute('writable-start-date'),
            'writable_end_date' => $this->getDateAttribute('writable-end-date'),
            'visible_approval' => json_encode($this->getAttribute('visible-approval', $current ? $current->visible_approval : '')),
            'writable_approval' => json_encode($this->getAttribute('writable-approval', $current ? $current->writable_end_date : '')),
            'content_approval' => $this->getAttribute('content-approval', $current ? $current->content_approval->getIterator() : []),
            'copy_approval' => $this->getAttribute('copy-approval', $current ? $current->copy_approval->getIterator() : []),
            'can_edit' => $current ? $current->canEdit($user) : false,
            'can_visit' => $current ? $current->canVisit($user) : false,
            'is_link' => (int) $this->getAttribute('is-link', $current ? $current->is_link : 0),
            'commentable' => (bool) $this->getAttribute('commentable', $current ? $current->commentable : false),
            'target_id' => (int)  $this->getAttribute('target-id', $current ? $current->target_id : ''),
            'external_relations' => $this->getAttribute('external-relations', $current ? $current->external_relations->getIterator() : []),
            'mkdate' => $this->getDateAttribute('mkdate'),
            'chdate' => $this->getDateAttribute('chdate'),
        ];
    }
}
