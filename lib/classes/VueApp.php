<?php
namespace Studip;

use Flexi\Template;
use Stringable;

/**
 * PHP abstraction of vue app
 *
 * The VueApp is used to create a Vue app in a general way. Just create it
 * using the name of the case component and pass in any required props or
 * stores including initial data.
 *
 * The store data is passed as an associative array where the key is the name
 * of the mutation to call with the given value as data.
 *
 * All methods are written in fluid manner so that you can create the app like this:
 *
 * <code>
 *     <?= Studip\VueApp::create('ExampleComponent')
 *         ->withProps(['foo' => 'bar'])
 *         ->withStore('exampleStore', data: ['setBar' => 'baz']) ?>
 * </code>
 *
 * All with* methods will always create a new cloned instance so the original
 * instance is immutable.
 *
 * @author Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @since Stud.IP 6.0
 */
final class VueApp implements Stringable
{
    /**
     * Creates a vue app with the given base component.
     */
    public static function create(string $base_component): VueApp
    {
        return new self($base_component);
    }

    private array $plugins = [];
    private array $props = [];
    private array $slots = [];
    private array $stores = [];
    private array $storeData = [];
    private array $components = [];

    private array $vuexStores = [];
    private array $vuexStoreData = [];

    /**
     * Private constructor since we want to enforce the use of VueApp::create().
     */
    private function __construct(
        private readonly string $base_component
    ) {
        $this->components[] = $base_component;
    }

    /**
     * Returns the base component
     */
    public function getBaseComponent(): string
    {
        return $this->base_component;
    }

    /**
     * Add props
     *
     * You may choose to overwrite the defined props
     */
    public function withProps(array $props, bool $overwrite = false): VueApp
    {
        $clone = clone $this;
        $clone->props = [...$overwrite ? [] : $clone->props, ...$props];
        return $clone;
    }

    /**
     * Returns all props
     */
    public function getProps(): array
    {
        return $this->props;
    }

    /**
     * Set the content of a slot.
     */
    public function setSlot(string $name, string|Template $content): VueApp
    {
        $this->slots[$name] = $content instanceof Template ? $content->render() : $content;
        return $this;
    }

    /**
     * Add a slot with the given name
     *
     * If you pass a flexi template as the content, it will be rendered.
     */
    public function withSlot(string $name, string|Template $content): VueApp
    {
        $clone = clone $this;
        $clone->slots[$name] = $content instanceof Template ? $content->render() : $content;
        return $clone;
    }

    /**
     * Returns all slots
     */
    public function getSlots(): array
    {
        return $this->slots;
    }

    /**
     * Adds a store
     */
    public function withStore(string $store, ?string $command = null, ?array $data = null): VueApp
    {
        $clone = clone $this;

        if ($command === null) {
            $command = 'use' . strtopascalcase($store) . 'Store';
        }

        $clone->stores[$store] = $command;

        if ($data !== null) {
            $clone->storeData[$store] = $data;
        }

        return $clone;
    }

    /**
     * Returns all stores
     */
    public function getStores(): array
    {
        return $this->stores;
    }

    /**
     * Returns all store data
     */
    public function getStoreData(): array
    {
        return $this->storeData;
    }

    /**
     * Adds a vuex store
     */
    public function withVuexStore(string $store, ?string $index = null, ?array $data = null): VueApp
    {
        $clone = clone $this;

        $clone->vuexStores[$index ?? $store] = $store;

        if ($data !== null) {
            $clone->vuexStoreData[$index ?? $store] = $data;
        }

        return $clone;
    }

    /**
     * Returns all vuex stores
     */
    public function getVuexStores(): array
    {
        return $this->vuexStores;
    }

    /**
     * Returns all vuex store data
     */
    public function getVuexStoreData(): array
    {
        return $this->vuexStoreData;
    }

    /**
     * Adds a plugin
     *
     * You may specify a different filename for the plugin.
     */
    public function withPlugin(string $plugin, string $filename = null): VueApp
    {
        $clone = clone $this;
        $clone->plugins[$plugin] = $filename ?? $plugin;
        return $clone;
    }

    /**
     * Returns all plugins
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }

    /**
     * Registers a component for use e.g. in slots.
     */
    public function withComponent(string $component): VueApp
    {
        $clone = clone $this;
        $clone->components[] = $component;
        return $clone;
    }

    /**
     * Returns all components
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    /**
     * Returns the template to render the vue app
     */
    public function getTemplate(): Template
    {
        $data = [
            'components' => $this->components,
        ];

        if (count($this->stores) > 0) {
            $data['stores'] = $this->stores;
        }

        if (count($this->vuexStores) > 0) {
            $data['vuexStores'] = $this->vuexStores;
        }

        if (count($this->plugins) > 0) {
            $data['plugins'] = $this->plugins;
        }

        $template = $GLOBALS['template_factory']->open('vue-app.php');
        $template->baseComponent = basename($this->base_component);
        $template->attributes = ['data-vue-app' => json_encode($data)];
        $template->props = $this->getPreparedProps();
        $template->storeData = $this->storeData;
        $template->vuexStoreData = $this->vuexStoreData;
        $template->slots = $this->getSlots();
        return $template;
    }

    /**
     * Returns the props as required to include them in the html
     */
    private function getPreparedProps(): array
    {
        $result = [];
        foreach ($this->props as $name => $value) {
            $name = ltrim($name, ':');
            $name = strtokebabcase($name);
            $result[":{$name}"] = json_encode($value);
        }
        return $result;
    }

    /**
     * Renders the vue app
     */
    public function render(): string
    {
        if (Debug\DebugBar::isActivated()) {
            $debugbar = app()->get(\DebugBar\DebugBar::class);
            $collector = new Debug\VueCollector($this);
            $debugbar->addCollector($collector);
        }

        \NotificationCenter::postNotification('VueAppWillRender', $this);

        $content = $this->getTemplate()->render();

        \NotificationCenter::postNotification('VueAppDidRender', $this);

        return $content;
    }

    /**
     * Returns a string representation of the vue app by rendering it.
     */
    public function __toString(): string
    {
        return $this->render();
    }
}
