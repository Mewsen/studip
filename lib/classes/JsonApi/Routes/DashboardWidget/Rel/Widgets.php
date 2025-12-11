<?php

namespace JsonApi\Routes\DashboardWidget\Rel;

use DashboardWidget\Container;
use DashboardWidget\Widget;

use Psr\Http\Message\ServerRequestInterface as Request;
use JsonApi\Errors\RecordNotFoundException;
use JsonApi\Routes\DashboardWidget\Authority;
use JsonApi\Routes\RelationshipsController;

/**
 * DashboardWidget's Container Widgets relationship route handler.
 *
 * @author Farbod Zamani Boroujeni <zamani@elan-ev.de>
 * @license GPL2 or any later version
 *
 * @since   Stud.IP 6.3
 */
class Widgets extends RelationshipsController
{
    /**
     * @inheritdoc
     */
    // protected $allowedPagingParameters = ['offset', 'limit'];

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function fetchRelationship(Request $request, $related)
    {
        $widgets = $related->widgets;
        return $this->getContentResponse($widgets);
    }

    /**
     * @inheritdoc
     */
    protected function removeFromRelationship(Request $request, $related)
    {
        $json = $this->validate($request);
        foreach ($this->validateContainerWidgets($related, $json) as $widget) {
            $related->removeWidgetFromPayload($widget->id);
            $widget->delete();
        }

        $related->store();

        return $this->getCodeResponse(204);
    }

    /**
     * @inheritdoc
     */
    protected function findRelated(array $args)
    {
        if (!$container = Container::find($args['id'])) {
            throw new RecordNotFoundException();
        }

        return $container;
    }

    /**
     * @inheritdoc
     */
    protected function authorize(Request $request, $resource)
    {
        return Authority::canManageContainerWidgets($this->getUser($request), $resource);
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getRelationshipSelfLink($resource, $schema, $userData)
    {
        return $schema->getRelationshipSelfLink($resource, 'widgets');
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getRelationshipRelatedLink($resource, $schema, $userData)
    {
        return null; // TODO: do we need this?
    }

    /**
     * @inheritdoc
     */
    protected function validateResourceDocument($json, $data)
    {
        if (!self::arrayHas($json, 'data')) {
            return 'Missing `data` member at document´s top level.';
        }

        $data = self::arrayGet($json, 'data');

        if (!is_array($data)) {
            return 'Document´s ´data´ must be an array.';
        }

        foreach ($data as $item) {
            if (self::arrayGet($item, 'type') !== \JsonApi\Schemas\DashboardWidget\Widget::TYPE) {
                return 'Wrong `type` in document´s `data`.';
            }

            if (!self::arrayGet($item, 'id')) {
                return 'Missing `id` of document´s `data`.';
            }
        }

        if (self::arrayHas($json, 'data.attributes')) {
            return 'Document must not have `attributes`.';
        }
    }

    /**
     * Looks through the request json data array and tryies to find each container's widget with their id.
     * @param Container $container
     * @param mixed $json
     * @return \DashboardWidget\Widget[] the list of validated  widgets of that container
     * @throws RecordNotFoundException
     */
    private function validateContainerWidgets(Container $container, $json)
    {
        $validatedWidgetItems = [];

        foreach (self::arrayGet($json, 'data') as $item) {
            $id = self::arrayGet($item, 'id');

            if (!$widget = $container->widgets->find($id)) {
                throw new RecordNotFoundException();
            }

            $validatedWidgetItems[] = $widget;
        }

        return $validatedWidgetItems;
    }
}
