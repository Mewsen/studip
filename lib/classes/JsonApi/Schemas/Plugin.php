<?php
namespace JsonApi\Schemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class Plugin extends SchemaProvider
{
    const TYPE = 'plugins';

    /**
     * @param \Plugin $resource
     */
    public function getId($resource): ?string
    {
        return $resource->id;
    }

    /**
     * @param \Plugin $resource
     */
    public function getAttributes($resource, ContextInterface $context): iterable
    {
        $plugin_admin = new \PluginAdministration();

        return [
            'name'     => $resource->pluginname,
            'class'    => $resource->pluginclassname,
            'position' => (int) $resource->navigationpos,
            'type'     => explode(',', $resource->plugintype),
            'enabled'  => $resource->enabled === 'yes',
            'core'     => str_contains($resource->plugintype, 'CorePlugin'),
            'manifest' => $resource->manifest ?? [],
            'migration_info' => !$resource->dependentonid ? ($resource->migration_info ?? []) : [],
            'dependentonid' => $resource->dependentonid,
        ];
    }

    /**
     * @param \Plugin $resource
     */
    public function getRelationships($resource, ContextInterface $context): iterable
    {
        return [];
    }

    public function hasResourceMeta($resource): bool
    {
        return true;
    }

    /**
     * @param \Plugin $resource
     */
    public function getResourceMeta($resource)
    {
        return [
            'installed' => is_dir($resource->full_plugin_path),
        ];
    }
}

